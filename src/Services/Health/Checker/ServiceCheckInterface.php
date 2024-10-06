<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker;

use Tax16\SystemCheckBundle\DTO\CheckResult;

interface ServiceCheckInterface
{
    public function check(): CheckResult;

    public function getName(): string;

    public function getIcon(): ?string;
}
