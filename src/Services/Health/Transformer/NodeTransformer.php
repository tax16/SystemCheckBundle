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
        $primaryNodeIndex = $this->getThePrimaryNode($results);
        $nodes = [];
        $edges = [];
        foreach ($results as $index => $result) {
            $nodes[] = $this->transformHealthCheckToNode($result, $index + 1);

            if ($index === $primaryNodeIndex) {
                continue;
            }

            $edges[] = $this->transformHealthCheckToEdges($result, $index, $primaryNodeIndex);
        }

        return new SystemNetwork(
            $nodes,
            $edges
        );
    }

    private function transformHealthCheckToEdges(HealthCheckDTO $checkDTO, int $index, int $idPrimary): SystemNodeEdge
    {
        return new SystemNodeEdge(
            $idPrimary,
            $index,
            SystemNodeEdge::EDGE_LENGTH_SUB,
            $this->determineState($checkDTO)->getEdgeStyle()
        );
    }

    /**
     * @param array<HealthCheckDTO> $results
     *
     **/
    private function getThePrimaryNode(array $results): int
    {
        foreach ($results as $index => $result) {
            if (CriticalityLevel::HEAD === $result->getPriority()) {
                return $index;
            }
        }

        throw new NotFoundHttpException('The primary node not found');
    }

    private function transformHealthCheckToNode(HealthCheckDTO $checkDTO, int $index): SystemNode
    {
        if (CriticalityLevel::HEAD === $checkDTO->getPriority()) {
            $index = 0;
        }

        return new SystemNode(
            $index,
            $checkDTO->getIcon() ?? '',
            $checkDTO->getLabel(),
            $this->determineState($checkDTO)->getStyle()
        );
    }

    private function determineState(HealthCheckDTO $checkDTO): ResultState
    {
        if ($checkDTO->getResult()->isSuccess()) {
            return ResultState::SUCCESS;
        }

        if (CriticalityLevel::LOW === $checkDTO->getPriority()) {
            return ResultState::WARNING;
        }

        return ResultState::ERROR;
    }
}
