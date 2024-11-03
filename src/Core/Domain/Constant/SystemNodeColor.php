<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Constant;

class SystemNodeColor
{
    public const WARNING = [
        'background' => '#FFF3CD',
        'border' => '#FFC107',
        'textColor' => '#856404',
        'highlight' => [
            'border' => '#FFA000',
            'background' => '#FFD54F',
        ],
        'hover' => [
            'border' => '#FFA000',
            'background' => '#FFE082',
        ],
    ];

    public const SUCCESS = [
        'background' => '#D4EDDA',
        'border' => '#28A745',
        'textColor' => '#155724',
        'highlight' => [
            'border' => '#218838',
            'background' => '#C3E6CB',
        ],
        'hover' => [
            'border' => '#218838',
            'background' => '#B1DFBB',
        ],
    ];

    public const ERROR = [
        'background' => '#F8D7DA',
        'border' => '#DC3545',
        'textColor' => '#721C24',
        'highlight' => [
            'border' => '#C82333',
            'background' => '#F5C6CB',
        ],
        'hover' => [
            'border' => '#C82333',
            'background' => '#F1B0B7',
        ],
    ];

    public const NO_CHECK = [
        'background' => '#E2E3E5',
        'border' => '#6C757D',
        'textColor' => '#495057',
        'highlight' => [
            'border' => '#5A6268',
            'background' => '#CED4DA',
        ],
        'hover' => [
            'border' => '#5A6268',
            'background' => '#D3D3D3',
        ],
    ];
}
