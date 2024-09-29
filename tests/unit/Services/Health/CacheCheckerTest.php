<?php

namespace unit\Services\Health;

use Redis;
use Tax16\SystemCheckBundle\Services\Health\CacheChecker;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Services\Health\Enum\CacheType;

class CacheCheckerTest extends TestCase
{
    public function testRedisConnectionIsSuccessful(): void
    {
        $redis = $this->createMock(Redis::class);
        $redis->method('ping')->willReturn(true);

        $checker = new CacheChecker($redis, CacheType::Redis);
        $result = $checker->check();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Connection to redis cache is successful.', $result->getMessage());
    }

    public function testRedisConnectionFails(): void
    {
        $redis = $this->createMock(Redis::class);
        $redis->method('ping')->will($this->throwException(new \Exception('Connection error')));

        $checker = new CacheChecker($redis, CacheType::Redis);
        $result = $checker->check();

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('Failed to connect to redis cache: Connection error', $result->getMessage());
    }

    public function testInvalidRedisClientThrowsException(): void
    {
        $checker = new CacheChecker(new \stdClass(), CacheType::Redis);
        $result = $checker->check();

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('Failed to connect to redis cache: Invalid Redis client.', $result->getMessage());
    }
}
