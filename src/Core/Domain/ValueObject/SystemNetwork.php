<?php

namespace Tax16\SystemCheckBundle\Core\Domain\ValueObject;

class SystemNetwork
{
    /** @var array<SystemNode> */
    private $nodes;

    /** @var array<SystemNodeEdge> */
    private $edges;

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
            'nodes' => array_map(static function (SystemNode $node) {
                return $node->toArray();
            }, $this->nodes),
            'edges' => array_map(static function (SystemNodeEdge $nodeEdge) {
                return $nodeEdge->toArray();
            }, $this->edges),
        ];
    }
}
