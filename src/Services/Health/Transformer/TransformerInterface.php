<?php

namespace Tax16\SystemCheckBundle\Services\Health\Transformer;

use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;

interface TransformerInterface
{
    /**
     * @param array<HealthCheckDTO> $results
     */
    public function transform(array $results): object;
}
