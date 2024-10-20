<?php

namespace Tax16\SystemCheckBundle\Enum;

enum CriticalityLevel: int
{
    case LOW = 3;
    case MEDIUM = 2;
    case HIGH = 1;
    case HEAD = 0;

    public static function isValid(int $level): bool
    {
        return in_array($level, array_column(self::cases(), 'value'), true);
    }
}
