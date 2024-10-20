<?php

namespace Tax16\SystemCheckBundle\Services\Health\Transformer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Enum\ResultState;
use Tax16\SystemCheckBundle\ValueObject\SystemNetwork;
use Tax16\SystemCheckBundle\ValueObject\SystemNode;
use Tax16\SystemCheckBundle\ValueObject\SystemNodeEdge;

class NodeTransformer implements TransformerInterface
{
    /**
     * @param array<HealthCheckDTO> $results
     */
    public function transform(array $results): SystemNetwork
    {
        $primaryNodeIndex = $this->getPrimaryNodeIndex($results);
        $nodes = [];
        $edges = [];

        foreach ($results as $index => $result) {
            $nodes[] = $this->transformHealthCheckToNode($result, $index);

            if ($index !== $primaryNodeIndex) {
                $edges[] = $this->transformHealthCheckToEdges($result, $index, $primaryNodeIndex);
            }
        }

        return new SystemNetwork($nodes, $edges);
    }

    private function transformHealthCheckToEdges(HealthCheckDTO $checkDTO, int $index, int $primaryNodeIndex): SystemNodeEdge
    {
        return new SystemNodeEdge(
            $primaryNodeIndex,
            $index,
            SystemNodeEdge::EDGE_LENGTH_SUB,
            $this->determineState($checkDTO)->getEdgeStyle(),
            !$checkDTO->getResult()->isSuccess() ? $checkDTO->getResult()->getMessage() : null
        );
    }

    /**
     * @param array<HealthCheckDTO> $results
     */
    private function getPrimaryNodeIndex(array $results): int
    {
        foreach ($results as $index => $result) {
            if ($result->getPriority() === CriticalityLevel::HEAD) {
                return $index;
            }
        }

        throw new NotFoundHttpException('The primary node was not found.');
    }

    private function transformHealthCheckToNode(HealthCheckDTO $checkDTO, int $index): SystemNode
    {
        return new SystemNode(
            $index,
            $checkDTO->getIcon() ?? '', // Default empty icon if null
            $checkDTO->getLabel(),
            $this->determineState($checkDTO)->getStyle()
        );
    }

    private function determineState(HealthCheckDTO $checkDTO): ResultState
    {
        return match (true) {
            $checkDTO->getResult()->isSuccess() => ResultState::SUCCESS,
            $checkDTO->getPriority() === CriticalityLevel::LOW => ResultState::WARNING,
            default => ResultState::ERROR,
        };
    }
}
