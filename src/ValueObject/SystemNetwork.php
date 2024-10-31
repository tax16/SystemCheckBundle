<?php

namespace Tax16\SystemCheckBundle\ValueObject;

class SystemNetwork
{
    /** @var array<SystemNode> */
    private array $nodes;

    /** @var array<SystemNodeEdge> */
    private array $edges;

    /**
     * @param SystemNode[]     $nodes
     * @param SystemNodeEdge[] $edges
     */
    public function __construct(array $nodes, array $edges)
    {
        $this->nodes = $nodes;
        $this->edges = $edges;
    }

    /**
     * @return SystemNode[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return SystemNodeEdge[]
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'nodes' => array_map(fn (SystemNode $node) => $node->toArray(), $this->nodes),
            'edges' => array_map(fn (SystemNodeEdge $nodeEdge) => $nodeEdge->toArray(), $this->edges),
        ];
    }
}
