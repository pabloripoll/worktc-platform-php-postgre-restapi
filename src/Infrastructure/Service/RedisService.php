<?php

namespace App\Infrastructure\Service;

use Predis\Client;
use Psr\Log\LoggerInterface;

class RedisService
{
    private Client $redis;

    public function __construct(
        private LoggerInterface $logger,
        string $redisDsn
    ) {
        try {
            $this->redis = new Client($redisDsn);
        } catch (\Exception $e) {
            $this->logger->error('Redis connection failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function ping(): bool
    {
        try {
            $response = $this->redis->ping();
            return $response === 'PONG' || (is_array($response) && in_array('PONG', $response, true));
        } catch (\Exception $e) {
            $this->logger->error('Redis ping failed: ' . $e->getMessage());
            return false;
        }
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        try {
            if ($ttl) {
                $this->redis->setex($key, $ttl, serialize($value));
            } else {
                $this->redis->set($key, serialize($value));
            }
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Redis set failed: ' . $e->getMessage());
            return false;
        }
    }

    public function get(string $key): mixed
    {
        try {
            $value = $this->redis->get($key);
            return $value ? unserialize($value) : null;
        } catch (\Exception $e) {
            $this->logger->error('Redis get failed: ' . $e->getMessage());
            return null;
        }
    }

    public function delete(string $key): bool
    {
        try {
            $this->redis->del([$key]);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Redis delete failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getInfo(): array
    {
        try {
            return $this->redis->info();
        } catch (\Exception $e) {
            $this->logger->error('Redis info failed: ' . $e->getMessage());
            return [];
        }
    }
}
