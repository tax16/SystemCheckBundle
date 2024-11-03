<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Application\Transformer;

use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;

interface TransformerInterface
{
    /**
     * @param array<HealthCheck> $results
     *
     * @return mixed|mixed[
     */
    public function transform(array $results);
}
