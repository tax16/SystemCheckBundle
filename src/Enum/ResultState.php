<?php

namespace Tax16\SystemCheckBundle\Enum;

enum ResultState: string
{
    case WARNING = 'warning';
    case SUCCESS = 'success';
    case ERROR = 'error';

    /**
     * @return array<mixed>
     */
    public function getStyle(): array
    {
        return match ($this) {
            self::WARNING => [
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
            ],
            self::SUCCESS => [
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
            ],
            self::ERROR => [
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
            ],
        };
    }

    /**
     * @return array<mixed>
     */
    public function getEdgeStyle(): array
    {
        return match ($this) {
            self::WARNING => [
                'color' => '#FFC107',
                'highlight' => '#FFA000',
            ],
            self::SUCCESS => [
                'color' => '#D4EDDA',
                'highlight' => '#218838',
            ],
            self::ERROR => [
                'color' => '#DC3545',
                'highlight' => '#C82333',
            ],
        };
    }
}
