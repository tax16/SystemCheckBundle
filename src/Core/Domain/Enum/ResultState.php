<?php

namespace Tax16\SystemCheckBundle\Core\Domain\Enum;


use Tax16\SystemCheckBundle\Core\Domain\Constant\SystemEdgeColor;
use Tax16\SystemCheckBundle\Core\Domain\Constant\SystemNodeColor;

class ResultState extends Enum
{
    public const WARNING = 'warning';
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const NO_CHECK = 'no_check';

    private $state;

    private const NODE_STYLES = [
        self::WARNING => SystemNodeColor::WARNING,
        self::SUCCESS => SystemNodeColor::SUCCESS,
        self::ERROR => SystemNodeColor::ERROR,
        self::NO_CHECK => SystemNodeColor::NO_CHECK,
    ];

    private const EDGE_STYLES = [
        self::WARNING => SystemEdgeColor::WARNING,
        self::SUCCESS => SystemEdgeColor::SUCCESS,
        self::ERROR => SystemEdgeColor::ERROR,
        self::NO_CHECK => SystemEdgeColor::NO_CHECK,
    ];


    public function __construct(string $state)
    {
        if (!self::isValid($state)) {
            throw new \InvalidArgumentException("Invalid state: $state");
        }
        $this->state = $state;
    }

    /**
     * Get the style based on the current state.
     *
     * @return array<mixed>
     */
    public function getStyle(): array
    {
        return self::NODE_STYLES[$this->state] ?? SystemNodeColor::NO_CHECK;
    }

    /**
     * Get the edge style based on the current state.
     *
     * @return array<mixed>
     */
    public function getEdgeStyle(): array
    {
        return self::EDGE_STYLES[$this->state] ?? SystemNodeColor::NO_CHECK;
    }
}