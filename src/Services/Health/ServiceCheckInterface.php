<?php

namespace Tax16\SystemCheckBundle\Service\Health;

use Tax16\SystemCheckBundle\Service\Health\DTO\CheckResult;

interface ServiceCheckInterface
{
    public function check(): CheckResult;

    public function getName(): string;
}