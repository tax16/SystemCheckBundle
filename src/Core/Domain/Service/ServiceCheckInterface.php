<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Service;

use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;

interface ServiceCheckInterface
{
    public function check(bool $withNetwork = false): CheckInfo;

    public function getName(): string;

    public function getIcon(): ?string;

    public function isAllowedToHaveChildren(): bool;
}
