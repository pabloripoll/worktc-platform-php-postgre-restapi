<?php

namespace App\Infrastructure\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Infrastructure\Messaging\Message\NotifyUserMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use MongoDB\Client as MongoClient;
use Predis\Client as RedisClient;

class ApiTestController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/api/v1/test', name: 'installation_test_api_v1')]
    public function testApiV1(): JsonResponse
    {
        $data = [
            'message' => 'REST API version 1.',
            'datetime' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        return $this->json($data, JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/test/database', name: 'installation_test_database')]
    public function testPostgreSQL(): JsonResponse
    {
        $success = false;
        $error = null;
        /** @var array<string, mixed> $details */
        $details = [];

        try {
            // Get database connection from EntityManager
            $connection = $this->entityManager->getConnection();

            // Test connection
            $connection->connect();

            // Get PostgreSQL version
            $versionResult = $connection->executeQuery('SELECT version()')->fetchOne();

            // Get current database name
            $databaseResult = $connection->executeQuery('SELECT current_database()')->fetchOne();

            // Get current user
            $userResult = $connection->executeQuery('SELECT current_user')->fetchOne();

            // Get server settings
            $maxConnections = $connection->executeQuery("SHOW max_connections")->fetchOne();
            $sharedBuffers = $connection->executeQuery("SHOW shared_buffers")->fetchOne();

            // Create a test table (temporary)
            $tableName = 'test_connection_' . time();
            $connection->executeStatement("
                CREATE TEMPORARY TABLE {$tableName} (
                    id SERIAL PRIMARY KEY,
                    test_message VARCHAR(255),
                    test_data JSONB,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");

            // Insert test data with JSONB
            $testMessage = 'Symfony connection test';
            $testData = json_encode(['symfony' => true, 'timestamp' => time()]);
            $connection->executeStatement(
                "INSERT INTO {$tableName} (test_message, test_data) VALUES (?, ?::jsonb)",
                [$testMessage, $testData]
            );

            // Query test data
            $result = $connection->executeQuery(
                "SELECT * FROM {$tableName} WHERE test_message = ?",
                [$testMessage]
            )->fetchAssociative();

            // Count rows
            $count = $connection->executeQuery("SELECT COUNT(*) FROM {$tableName}")->fetchOne();

            // Get table info
            $tablesCount = $connection->executeQuery(
                "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public'"
            )->fetchOne();

            // Get database size
            $dbSize = $connection->executeQuery(
                "SELECT pg_size_pretty(pg_database_size(current_database()))"
            )->fetchOne();

            // Test transaction
            $connection->beginTransaction();
            $connection->executeStatement(
                "INSERT INTO {$tableName} (test_message) VALUES (?)",
                ['Transaction test']
            );
            $connection->commit();

            // Drop temporary table
            $connection->executeStatement("DROP TABLE IF EXISTS {$tableName}");

            $success = true;
            $details = [
                'connected' => $connection->isConnected(),
                'database' => $databaseResult,
                'user' => $userResult,
                'version' => $versionResult,
                'max_connections' => $maxConnections,
                'shared_buffers' => $sharedBuffers,
                'database_size' => $dbSize,
                'test_table' => $tableName,
                'test_message' => $testMessage,
                'row_inserted' => $result !== false,
                'inserted_id' => $result['id'] ?? null,
                'test_data_json' => json_decode($result['test_data'] ?? '{}', true),
                'rows_count' => (int)$count,
                'public_tables_count' => (int)$tablesCount,
                //'driver' => $connection->getDriver()->getName(),
                'transaction_test' => 'passed',
            ];

        } catch (\Doctrine\DBAL\Exception\ConnectionException $e) {
            $error = 'Connection error: ' . $e->getMessage();
        } catch (\Doctrine\DBAL\Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        } catch (\Throwable $e) {
            $error = 'Unexpected error: ' . $e->getMessage();
        }

        $data = [
            'service' => 'PostgreSQL',
            'host' => $_ENV['DATABASE_HOST'] ?? $_ENV['POSTGRES_HOST'] ?? 'localhost',
            'port' => $_ENV['DATABASE_PORT'] ?? $_ENV['POSTGRES_PORT'] ?? 5432,
            'database' => $_ENV['DATABASE_NAME'] ?? $_ENV['POSTGRES_DB'] ?? 'unknown',
            'message' => $success ? 'PostgreSQL connection successful' : 'PostgreSQL connection failed',
            'success' => $success,
            'error' => $error,
            'details' => $details,
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
        ];

        return $this->json($data, $success ? JsonResponse::HTTP_OK : JsonResponse::HTTP_SERVICE_UNAVAILABLE);
    }

    #[Route('/api/v1/test/mailer', name: 'installation_test_mailer')]
    public function testMailer(MailerInterface $mailer): JsonResponse
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to('dev@example.com')
            ->subject('Welcome to My App')
            ->text('Testing email');

        $success = false;
        $error = null;

        try {
            $mailer->send($email);
            $success = true;
        } catch (TransportExceptionInterface $e) {
            // Transport errors (connection, timeouts, TLS, auth)
            $error = $e->getMessage();
        } catch (\Throwable $e) {
            // Any other unexpected error
            $error = $e->getMessage();
        }

        $data = [
            'message' => $success ? 'Email sent' : 'Email failed',
            'success' => $success,
            'error' => $error,
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
        ];

        return $this->json($data, JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/v1/test/broker', name: 'installation_test_broker')]
    public function testBroker(): JsonResponse
    {
        $userId = 1;
        $success = false;
        $error = null;
        /** @var array<string, mixed> $details */
        $details = [];

        try {
            // Debug: Show what DSN Symfony is reading
            $dsn = $_ENV['MESSENGER_TRANSPORT_DSN'] ?? 'NOT_SET';
            $details['dsn'] = $dsn;
            $details['dsn_parsed'] = parse_url($dsn);

            // Dispatch returns an Envelope; dispatch itself does not mean the job was processed,
            // only that it was accepted for transport.
            $envelope = $this->messageBus->dispatch(new NotifyUserMessage($userId, 'Welcome!'));

            $success = true;
            $details['message_dispatched'] = true;
            $details['message_dispatch_envelope'] = $envelope;

        } catch (\Symfony\Component\Messenger\Exception\TransportException $e) {
            $error = 'Transport error: ' . $e->getMessage();
        } catch (\Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }

        $data = [
            'message' => $success ? 'Message sent successfully' : 'Message failed',
            'success' => $success,
            'error' => $error,
            'details' => $details,
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
        ];

        return $this->json($data, $success ? JsonResponse::HTTP_OK : JsonResponse::HTTP_SERVICE_UNAVAILABLE);
    }

    #[Route('/api/v1/test-redis', name: 'installation_redis_test')]
    public function testRedis(): JsonResponse
    {
        $success = false;
        $error = null;
        /** @var array<string, mixed> $details */
        $details = [];

        try {
            // Option 1: Use DSN (simpler)
            $dsn = sprintf(
                'redis://%s:%s@%s:%d/%d',
                $_ENV['REDIS_USER'] ?? 'some-user',
                $_ENV['REDIS_PASSWORD'] ?? 'AppPassword123!',
                $_ENV['REDIS_HOST'] ?? '192.168.1.41',
                (int)($_ENV['REDIS_PORT'] ?? 7701),
                (int)($_ENV['REDIS_DATABASE'] ?? 0)
            );

            $redis = new RedisClient($dsn);

            // Test connection with PING
            $pong = $redis->ping();

            // Set a test key
            $testKey = 'test:connection:' . time();
            $testValue = 'Symfony test at ' . (new \DateTime())->format(\DateTime::ATOM);
            $redis->setex($testKey, 60, $testValue);

            // Get the test key back
            $retrieved = $redis->get($testKey);

            // Get server info
            $info = $redis->info('server');

            // Delete test key
            $redis->del([$testKey]);

            $success = true;
            $details = [
                'ping' => $pong,
                'test_key' => $testKey,
                'test_value' => $testValue,
                'retrieved_value' => $retrieved,
                'redis_version' => $info['redis_version'] ?? 'unknown',
                'redis_mode' => $info['redis_mode'] ?? 'unknown',
                'authenticated_as' => $_ENV['REDIS_APP_USER'] ?? 'social',
            ];

        } catch (\Predis\Connection\ConnectionException $e) {
            $error = 'Connection error: ' . $e->getMessage();
        } catch (\Predis\Response\ServerException $e) {
            $error = 'Redis server error: ' . $e->getMessage();
        } catch (\Throwable $e) {
            $error = 'Unexpected error: ' . $e->getMessage();
        }

        $data = [
            'service' => 'Redis',
            'host' => $_ENV['REDIS_HOST'] ?? '192.168.1.41',
            'port' => $_ENV['REDIS_PORT'] ?? 7701,
            'database' => $_ENV['REDIS_DATABASE'] ?? 0,
            'message' => $success ? 'Redis connection successful' : 'Redis connection failed',
            'success' => $success,
            'error' => $error,
            'details' => $details,
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
        ];

        return $this->json($data, $success ? JsonResponse::HTTP_OK : JsonResponse::HTTP_SERVICE_UNAVAILABLE);
    }

    #[Route('/api/v1/test/event-db', name: 'installation_test_event_db')]
    public function testMongoDB(): JsonResponse
    {
        $success = false;
        $error = null;
        /** @var array<string, mixed> $details */
        $details = [];

        try {
            $uri = $_ENV['MONGODB_URL'];
            $database = $_ENV['MONGODB_DATABASE'];

            // Create MongoDB client
            $client = new MongoClient($uri, [
                'username' => $_ENV['MONGODB_USERNAME'] ?? 'rootuser',
                'password' => $_ENV['MONGODB_PASSWORD'] ?? 'rootpass',
                'authSource' => 'admin',
            ], [
                'serverSelectionTimeoutMS' => 30000,  // Increase to 30 seconds
                'connectTimeoutMS' => 30000,
                'socketTimeoutMS' => 30000,
            ]);

            // Select database
            $db = $client->selectDatabase($database);

            // Test connection with ping command
            $pingCommand = $db->command(['ping' => 1]);
            $pingResultArray = $pingCommand->toArray()[0] ?? null;

            // Get server info
            $buildCommand = $client->selectDatabase('admin')->command(['buildInfo' => 1]);
            $buildInfoArray = $buildCommand->toArray()[0] ?? null;

            // Create a test collection and document
            $testCollection = 'test_connection';
            $collection = $db->selectCollection($testCollection);

            $testDoc = [
                'test' => true,
                'message' => 'Symfony connection test',
                'timestamp' => new \MongoDB\BSON\UTCDateTime(),
            ];

            // Insert test document
            $insertResult = $collection->insertOne($testDoc);
            $insertedId = (string)$insertResult->getInsertedId();

            // Find the document
            $foundDoc = $collection->findOne(['_id' => $insertResult->getInsertedId()]);

            // Delete test document
            $deleteResult = $collection->deleteOne(['_id' => $insertResult->getInsertedId()]);

            // List collections
            $collections = iterator_to_array($db->listCollections());
            $collectionNames = array_map(fn($col) => $col->getName(), $collections);

            /** @var bool $pingOk */
            $pingOk = isset($pingResultArray['ok']) && $pingResultArray['ok'] === 1;
            /** @var string $mongoVersion */
            $mongoVersion = $buildInfoArray['version'] ?? 'unknown';

            $success = true;
            $details = [
                'ping' => $pingOk,
                'mongodb_version' => $mongoVersion,
                'database' => $database,
                'test_collection' => $testCollection,
                'inserted_id' => $insertedId,
                'document_found' => $foundDoc !== null,
                'document_deleted' => $deleteResult->getDeletedCount() > 0,
                'collections_count' => count($collectionNames),
                'collections' => $collectionNames,
            ];

        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            $error = 'Connection timeout: ' . $e->getMessage();
        } catch (\MongoDB\Driver\Exception\AuthenticationException $e) {
            $error = 'Authentication failed: ' . $e->getMessage();
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            $error = 'MongoDB driver error: ' . $e->getMessage();
        } catch (\Throwable $e) {
            $error = 'Unexpected error: ' . $e->getMessage();
        }

        $data = [
            'service' => 'MongoDB',
            'host' => $_ENV['MONGODB_HOST'] ?? 'localhost',
            'port' => $_ENV['MONGODB_PORT'] ?? 27017,
            'database' => $database ?? 'unknown',
            'message' => $success ? 'MongoDB connection successful' : 'MongoDB connection failed',
            'success' => $success,
            'error' => $error,
            'details' => $details,
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
        ];

        return $this->json($data, $success ? JsonResponse::HTTP_OK : JsonResponse::HTTP_SERVICE_UNAVAILABLE);
    }

    #[Route('/api/v1/test/all', name: 'installation_test_all')]
    public function testAll(MailerInterface $mailer): JsonResponse
    {
        $results = [];

        // Test PostgreSQL
        $postgresResponse = $this->testPostgreSQL();
        /** @var array{success: bool} $postgresData */
        $postgresData = json_decode($postgresResponse->getContent(), true);
        $results['postgresql'] = $postgresData;

        // Test Redis
        $redisResponse = $this->testRedis();
        /** @var array{success: bool} $redisData */
        $redisData = json_decode($redisResponse->getContent(), true);
        $results['redis'] = $redisData;

        // Test MongoDB
        $mongoResponse = $this->testMongoDB();
        /** @var array{success: bool} $mongoData */
        $mongoData = json_decode($mongoResponse->getContent(), true);
        $results['mongodb'] = $mongoData;

        // Test Mailer
        $mailerResponse = $this->testMailer($mailer);
        /** @var array{success: bool} $mailerData */
        $mailerData = json_decode($mailerResponse->getContent(), true);
        $results['mailer'] = $mailerData;

        // Test Broker
        $brokerResponse = $this->testBroker();
        /** @var array{success: bool} $brokerData */
        $brokerData = json_decode($brokerResponse->getContent(), true);
        $results['broker'] = $brokerData;

        $allSuccess = ($postgresData['success'] ?? false)
            && ($redisData['success'] ?? false)
            && ($mongoData['success'] ?? false)
            && ($mailerData['success'] ?? false)
            && ($brokerData['success'] ?? false);

        $data = [
            'message' => $allSuccess ? 'All services operational' : 'Some services failed',
            'all_success' => $allSuccess,
            'results' => $results,
            'summary' => [
                'postgresql' => ($postgresData['success'] ?? false) ? 'healthy' : 'failed',
                'redis' => ($redisData['success'] ?? false) ? 'healthy' : 'failed',
                'mongodb' => ($mongoData['success'] ?? false) ? 'healthy' : 'failed',
                'mailer' => ($mailerData['success'] ?? false) ? 'healthy' : 'failed',
                'broker' => ($brokerData['success'] ?? false) ? 'healthy' : 'failed',
            ],
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
        ];

        return $this->json($data, $allSuccess ? JsonResponse::HTTP_OK : JsonResponse::HTTP_MULTI_STATUS);
    }
}
