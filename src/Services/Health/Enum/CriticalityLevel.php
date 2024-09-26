<?php

namespace Tax16\SystemCheckBundle\Service\Health\Enum;

enum CriticalityLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public static function isValid(string $level): bool
    {
        return in_array($level, array_column(self::cases(), 'value'), true);
    }
}
