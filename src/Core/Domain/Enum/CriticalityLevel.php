<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Enum;

class CriticalityLevel extends Enum
{
    public const LOW = 3;
    public const MEDIUM = 2;
    public const HIGH = 1;
    public const HEAD = 0;
}