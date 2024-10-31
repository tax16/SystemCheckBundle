<?php

namespace Tax16\SystemCheckBundle\Services\Health\Transformer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Enum\ResultState;
use Tax16\SystemCheckBundle\Services\Health\Checker\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\ValueObject\SystemNetwork;
use Tax16\SystemCheckBundle\ValueObject\SystemNode;
use Tax16\SystemCheckBundle\ValueObject\SystemNodeEdge;

class NodeTransformer implements TransformerInterface
{
    /**
     * @var array<SystemNode>
     */
    private array $nodes = [];
    /**
     * @var array<SystemNodeEdge>
     */
    private array $edges = [];

    /**
     * Transforms an array of HealthCheckDTO objects into a SystemNetwork.
     *
     * @param array<HealthCheckDTO> $results
     */
    public function transform(array $results, string $prefix = '', ?string $primaryNodeIndex = null): SystemNetwork
    {
        $primaryNodeIndex ??= $prefix.$this->getPrimaryNodeIndex($results);

        foreach ($results as $index => $result) {
            $currentIndex = $prefix.$index;
            $this->nodes[] = $this->transformHealthCheckToNode($result, $currentIndex);

            $children = $result->getResult()->getChildren();
            $length = empty($children) ? SystemNodeEdge::EDGE_LENGTH_SUB : SystemNodeEdge::EDGE_LENGTH_MAIN;

            if (!empty($children)) {
                $this->transform($children, $currentIndex.'_', $currentIndex);
            }

            if ($currentIndex !== $primaryNodeIndex) {
                $this->edges[] = $this->transformHealthCheckToEdges($result, $currentIndex, $primaryNodeIndex, $length);
            }
        }

        return new SystemNetwork($this->nodes, $this->edges);
    }

    /**
     * Transforms a HealthCheckDTO object into a SystemNodeEdge.
     */
    private function transformHealthCheckToEdges(
        HealthCheckDTO $checkDTO,
        string $index,
        string $primaryNodeIndex,
        int $length = SystemNodeEdge::EDGE_LENGTH_SUB,
    ): SystemNodeEdge {
        return new SystemNodeEdge(
            $primaryNodeIndex,
            $index,
            $length,
            $this->determineState($checkDTO)->getEdgeStyle(),
            $checkDTO->getResult()->isSuccess() ? null : $checkDTO->getResult()->getMessage(),
            !$checkDTO->getResult()->isSuccess()
        );
    }

    /**
     * Gets the index of the primary node from the results.
     *
     * @param array<HealthCheckDTO> $results
     *
     * @throws NotFoundHttpException
     */
    private function getPrimaryNodeIndex(array $results): int
    {
        foreach ($results as $index => $result) {
            if (CriticalityLevel::HEAD === $result->getPriority()) {
                return $index;
            }
        }

        throw new NotFoundHttpException('The primary node was not found.');
    }

    /**
     * Transforms a HealthCheckDTO object into a SystemNode.
     */
    private function transformHealthCheckToNode(HealthCheckDTO $checkDTO, string $index): SystemNode
    {
        return new SystemNode(
            $index,
            $checkDTO->getIcon() ?? CheckerIcon::UNKNOWN,
            $checkDTO->getLabel(),
            $this->determineState($checkDTO)->getStyle()
        );
    }

    /**
     * Determines the ResultState based on the HealthCheckDTO.
     */
    private function determineState(HealthCheckDTO $checkDTO): ResultState
    {
        return match (true) {
            $checkDTO->getResult()->isSuccess() => ResultState::SUCCESS,
            $checkDTO->getResult()->isSuccess() === null => ResultState::NO_CHECK,
            CriticalityLevel::LOW === $checkDTO->getPriority() => ResultState::WARNING,
            default => ResultState::ERROR,
        };
    }
}
