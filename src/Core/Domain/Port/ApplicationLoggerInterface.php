<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Port;

interface ApplicationLoggerInterface
{
    /**
     * @param string $message
     * @param array<mixed> $context
     * @return void
     */
    public function info(string $message, array $context = []): void;

    /**
     * @param string $message
     * @param array<mixed> $context
     * @return void
     */
    public function error(string $message, array $context = []): void;

    /**
     * @param string $message
     * @param array<mixed> $context
     * @return void
     */
    public function warning(string $message, array $context = []): void;
}