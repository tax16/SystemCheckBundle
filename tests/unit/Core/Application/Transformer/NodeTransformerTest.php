<?php

namespace unit\Core\Application\Transformer;

use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Core\Application\Exception\NotFoundException;
use Tax16\SystemCheckBundle\Core\Application\Transformer\NodeTransformer;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\ValueObject\SystemNetwork;


class NodeTransformerTest extends TestCase
{
    private $transformer;

    protected function setUp(): void
    {
        $this->transformer = new NodeTransformer();
    }

    public function testTransformWithAllSuccessChecks(): void
    {
        $results = [
            new HealthCheck(new CheckInfo("Check 1", true),"fake_id_1",  "Label 1", "Description 1", CriticalityLevel::HEAD),
            new HealthCheck(new CheckInfo("Check 2", true), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::MEDIUM),
        ];

        $network = $this->transformer->transform($results);

        $this->assertInstanceOf(SystemNetwork::class, $network);
        $this->assertCount(2, $network->getNodes());
        $this->assertCount(1, $network->getEdges());
    }

    public function testTransformWithMixedChecks(): void
    {
        $results = [
            new HealthCheck(new CheckInfo("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HEAD),
            new HealthCheck(new CheckInfo("Check 2", false), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::HIGH), // failed
            new HealthCheck(new CheckInfo("Check 3", false), "fake_id_3", "Label 3", "Description 3", CriticalityLevel::LOW), // warning
        ];

        $network = $this->transformer->transform($results);

        $this->assertCount(3, $network->getNodes());
        $this->assertCount(2, $network->getEdges());
    }

    public function testTransformWithHeadPriority(): void
    {
        $results = [
            new HealthCheck(new CheckInfo("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HEAD),
            new HealthCheck(new CheckInfo("Check 2", false), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::HIGH),
        ];

        $network = $this->transformer->transform($results);

        $this->assertCount(2, $network->getNodes());
        $this->assertCount(1, $network->getEdges());
        $this->assertEquals("0", $network->getEdges()[0]->toArray()['from']);
        $this->assertEquals("1", $network->getEdges()[0]->toArray()['to']);
    }

    public function testTransformWithNoHeadNode(): void
    {
        $results = [
            new HealthCheck(new CheckInfo("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HIGH),
            new HealthCheck(new CheckInfo("Check 2", true), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::LOW),
        ];

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('The primary node was not found.');

        $this->transformer->transform($results);
    }

    public function testTransformWithEmptyArray(): void
    {
        $results = [];

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('The primary node was not found.');

        $this->transformer->transform($results);
    }
}
