<?php
declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Constant;

class SystemEdgeColor
{
    public const WARNING = [
        'color' => '#FFC107',
        'highlight' => '#FFA000',
    ];

    public const SUCCESS = [
        'color' => '#28A745',
        'highlight' => '#218838',
    ];

    public const ERROR = [
        'color' => '#DC3545',
        'highlight' => '#C82333',
    ];

    public const NO_CHECK = [
        'color' => '#6C757D',
        'highlight' => '#5A6268',
    ];
}