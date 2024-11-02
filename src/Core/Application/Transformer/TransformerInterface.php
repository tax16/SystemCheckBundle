<?php

namespace Tax16\SystemCheckBundle\Core\Application\Transformer;

use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

interface TransformerInterface
{
    /**
     * @param array<HealthCheck> $results
     */
    public function transform(array $results);
}
