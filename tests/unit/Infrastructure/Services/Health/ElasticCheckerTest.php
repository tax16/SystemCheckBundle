<?php

namespace unit\Infrastructure\Services\Health;

use Elastica\Client;
use Elastica\Cluster;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\ElasticChecker;

class ElasticCheckerTest extends TestCase
{
    /**
     * @var Client
     */
    private $clientMock;
    /**
     * @var Cluster
     */
    private $clusterMock;
    /**
     * @var ElasticChecker
     */
    private $elasticChecker;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->clusterMock = $this->createMock(Cluster::class);

        $this->clientMock
            ->method('getCluster')
            ->willReturn($this->clusterMock);

        $this->elasticChecker = new ElasticChecker($this->clientMock);
    }

    public function testCheckReturnsSuccessfulCheckInfoWhenStatusIsGreen(): void
    {
        $data = $this->createMock(Cluster\Health::class);
        $data
            ->method('getData')
            ->willReturn(['status' => 'green']);

        $this->clusterMock
            ->method('getHealth')
            ->willReturn($data);

        $result = $this->elasticChecker->check();

        $this->assertInstanceOf(CheckInfo::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Elastic client connected successfully', $result->getMessage());
    }

    public function testCheckReturnsSuccessfulCheckInfoWhenStatusIsYellow(): void
    {
        $data = $this->createMock(Cluster\Health::class);
        $data
            ->method('getData')
            ->willReturn(['status' => 'yellow']);

        $this->clusterMock
            ->method('getHealth')
            ->willReturn($data);

        $result = $this->elasticChecker->check();

        $this->assertInstanceOf(CheckInfo::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Elastic client connected successfully', $result->getMessage());
    }

    public function testCheckReturnsFailureCheckInfoWhenStatusIsRed(): void
    {
        $data = $this->createMock(Cluster\Health::class);
        $data
            ->method('getData')
            ->willReturn(['status' => 'red']);

        $this->clusterMock
            ->method('getHealth')
            ->willReturn($data);

        $result = $this->elasticChecker->check();

        $this->assertInstanceOf(CheckInfo::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertEquals(
            'Failed to connect to the elastic client, status: red',
            $result->getMessage()
        );
    }

    public function testCheckReturnsFailureCheckInfoOnException(): void
    {
        $data = $this->createMock(Cluster\Health::class);
        $data
            ->method('getData')
            ->willThrowException(new \Exception('Connection error'));

        $this->clusterMock
            ->method('getHealth')
            ->willReturn($data);

        $result = $this->elasticChecker->check();

        $this->assertInstanceOf(CheckInfo::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('Failed to connect to the elastic client', $result->getMessage());
        $this->assertStringContainsString('Connection error', $result->getMessage());
    }
}