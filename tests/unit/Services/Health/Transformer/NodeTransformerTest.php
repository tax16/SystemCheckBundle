<?php

namespace unit\Services\Health\Transformer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Services\Health\Transformer\NodeTransformer;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\ValueObject\SystemNetwork;


class NodeTransformerTest extends TestCase
{
    private NodeTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new NodeTransformer();
    }

    public function testTransformWithAllSuccessChecks(): void
    {
        $results = [
            new HealthCheckDTO(new CheckResult("Check 1", true),"fake_id_1",  "Label 1", "Description 1", CriticalityLevel::HEAD),
            new HealthCheckDTO(new CheckResult("Check 2", true), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::MEDIUM),
        ];

        $network = $this->transformer->transform($results);

        $this->assertInstanceOf(SystemNetwork::class, $network);
        $this->assertCount(2, $network->getNodes());
        $this->assertCount(1, $network->getEdges());
    }

    public function testTransformWithMixedChecks(): void
    {
        $results = [
            new HealthCheckDTO(new CheckResult("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HEAD),
            new HealthCheckDTO(new CheckResult("Check 2", false), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::HIGH), // failed
            new HealthCheckDTO(new CheckResult("Check 3", false), "fake_id_3", "Label 3", "Description 3", CriticalityLevel::LOW), // warning
        ];

        $network = $this->transformer->transform($results);

        $this->assertCount(3, $network->getNodes());
        $this->assertCount(2, $network->getEdges());
    }

    public function testTransformWithHeadPriority(): void
    {
        $results = [
            new HealthCheckDTO(new CheckResult("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HEAD),
            new HealthCheckDTO(new CheckResult("Check 2", false), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::HIGH),
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
            new HealthCheckDTO(new CheckResult("Check 1", true), "fake_id_1", "Label 1", "Description 1", CriticalityLevel::HIGH),
            new HealthCheckDTO(new CheckResult("Check 2", true), "fake_id_2", "Label 2", "Description 2", CriticalityLevel::LOW),
        ];

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The primary node was not found.');

        $this->transformer->transform($results);
    }

    public function testTransformWithEmptyArray(): void
    {
        $results = [];

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The primary node was not found.');

        $this->transformer->transform($results);
    }
}
