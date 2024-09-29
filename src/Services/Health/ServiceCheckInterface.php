<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use Tax16\SystemCheckBundle\Services\Health\DTO\CheckResult;

interface ServiceCheckInterface
{
    public function check(): CheckResult;

    public function getName(): string;
}
