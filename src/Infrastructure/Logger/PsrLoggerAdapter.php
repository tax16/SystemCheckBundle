<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Logger;

use Psr\Log\LoggerInterface;
use Tax16\SystemCheckBundle\Core\Domain\Port\ApplicationLoggerInterface;

class PsrLoggerAdapter implements ApplicationLoggerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }
}