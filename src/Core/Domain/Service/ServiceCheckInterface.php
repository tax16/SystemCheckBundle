<?php

namespace Tax16\SystemCheckBundle\Core\Domain\Service;

use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;

interface ServiceCheckInterface
{
    public function check(): CheckInfo;

    public function getName(): string;

    public function getIcon(): ?string;

    public function isAllowedToHaveChildren(): bool;
}
