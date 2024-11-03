<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Port;

interface ApplicationLoggerInterface
{
    /**
     * @param array<mixed> $context
     */
    public function info(string $message, array $context = []): void;

    /**
     * @param array<mixed> $context
     */
    public function error(string $message, array $context = []): void;

    /**
     * @param array<mixed> $context
     */
    public function warning(string $message, array $context = []): void;
}
