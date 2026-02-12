<?php

namespace App\Infrastructure\Service;

use MongoDB\Client;
use MongoDB\Database;
use Psr\Log\LoggerInterface;

class MongoDBService
{
    private Client $client;
    private Database $database;

    public function __construct(
        private LoggerInterface $logger,
        string $mongodbUrl,
        string $mongodbDatabase
    ) {
        try {
            $this->client = new Client($mongodbUrl);
            $this->database = $this->client->selectDatabase($mongodbDatabase);
        } catch (\Exception $e) {
            $this->logger->error('MongoDB connection failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function ping(): bool
    {
        try {
            $result = $this->database->command(['ping' => 1]);
            return isset($result['ok']) && $result['ok'] === 1;
        } catch (\Exception $e) {
            $this->logger->error('MongoDB ping failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function insert(string $collection, array $document): ?string
    {
        try {
            $result = $this->database->selectCollection($collection)->insertOne($document);
            return $result->getInsertedId();
        } catch (\Exception $e) {
            $this->logger->error('MongoDB insert failed: ' . $e->getMessage());
            return null;
        }
    }

    public function find(string $collection, array $filter = [], array $options = []): array
    {
        try {
            $cursor = $this->database->selectCollection($collection)->find($filter, $options);
            return $cursor->toArray();
        } catch (\Exception $e) {
            $this->logger->error('MongoDB find failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getServerInfo(): array
    {
        try {
            $result = $this->database->command(['serverStatus' => 1]);
            return [
                'version' => $result['version'] ?? 'unknown',
                'uptime' => $result['uptime'] ?? 0,
                'connections' => $result['connections'] ?? [],
            ];
        } catch (\Exception $e) {
            $this->logger->error('MongoDB server info failed: ' . $e->getMessage());
            return [];
        }
    }
}
