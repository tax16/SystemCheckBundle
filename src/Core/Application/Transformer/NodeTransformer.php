<?php
declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Application\Transformer;

use Tax16\SystemCheckBundle\Core\Application\Exception\NotFoundException;
use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Core\Domain\Enum\ResultState;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\ValueObject\SystemNetwork;
use Tax16\SystemCheckBundle\Core\Domain\ValueObject\SystemNode;
use Tax16\SystemCheckBundle\Core\Domain\ValueObject\SystemNodeEdge;

class NodeTransformer implements TransformerInterface
{
    /**
     * @var array<SystemNode>
     */
    private $nodes = [];
    /**
     * @var array<SystemNodeEdge>
     */
    private $edges = [];

    /**
     * {inheritdoc}
     * Transforms an array of HealthCheckDTO objects into a SystemNetwork.
     *
     * @param array<HealthCheck> $results
     */
    public function transform(array $results, string $prefix = '', ?string $primaryNodeIndex = null): SystemNetwork
    {
        $primaryNodeIndex = $primaryNodeIndex ?? $prefix.$this->getPrimaryNodeIndex($results);

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
        HealthCheck $checkDTO,
        string      $index,
        string      $primaryNodeIndex,
        int         $length = SystemNodeEdge::EDGE_LENGTH_SUB
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
     * @param array<HealthCheck> $results
     *
     * @throws NotFoundException
     */
    private function getPrimaryNodeIndex(array $results): int
    {
        foreach ($results as $index => $result) {
            if (CriticalityLevel::HEAD === $result->getPriority()) {
                return $index;
            }
        }

        throw new NotFoundException('The primary node was not found.');
    }

    /**
     * Transforms a HealthCheckDTO object into a SystemNode.
     */
    private function transformHealthCheckToNode(HealthCheck $checkDTO, string $index): SystemNode
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
    private function determineState(HealthCheck $checkDTO): ResultState
    {
        if ($checkDTO->getResult()->isSuccess()) {
            return new ResultState(ResultState::SUCCESS);
        }
        if ($checkDTO->getResult()->isSuccess() === null) {
            return new ResultState(ResultState::NO_CHECK);
        }

        if (CriticalityLevel::LOW === $checkDTO->getPriority()) {
            return new ResultState(ResultState::WARNING);
        }

        return new ResultState(ResultState::ERROR);
    }
}
