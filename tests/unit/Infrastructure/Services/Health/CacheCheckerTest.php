<?php

namespace unit\Infrastructure\Services\Health;

use Error;
use PHPUnit\Framework\TestCase;
use Redis;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CacheType;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Cache\CacheFactory;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\Cache\CacheInterface;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\CacheChecker;

class CacheCheckerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|CacheFactory
     */
    private $cacheFactory;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the CacheFactory so that we can control its behavior
        $this->cacheFactory = $this->createMock(CacheFactory::class);
    }

    public function testRedisConnectionIsSuccessful(): void
    {
        $redis = $this->createMock(CacheInterface::class);
        $redis->method('ping');

        $this->cacheFactory->method('createClient')
            ->with(CacheType::REDIS, 'redis://localhost:6379')
            ->willReturn($redis);

        $checker = $this->getMockBuilder(CacheChecker::class)
            ->setConstructorArgs(['redis://localhost:6379', CacheType::REDIS])
            ->onlyMethods(['getCacheFactory'])
            ->getMock();

        $checker->method('getCacheFactory')->willReturn($this->cacheFactory);

        $result = $checker->check();
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Connection to redis cache is successful.', $result->getMessage());
    }

    public function testRedisConnectionFails(): void
    {
        $checker = new CacheChecker("redis://localhost_test_not_work:6379", CacheType::REDIS);

        $result = $checker->check();

        $this->assertFalse($result->isSuccess());
    }

}
