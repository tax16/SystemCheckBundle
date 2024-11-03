<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

interface HttpServiceCheckInterface extends ServiceCheckInterface
{
    public function isToTrace(): bool;

    public function setToTrace(bool $toTrace): HttpServiceCheckInterface;

    public function getHttpClient(): HttpClientInterface;

    public function setHttpClient(HttpClientInterface $httpClient): HttpServiceCheckInterface;

    public function getResponseData(): ?ResponseInterface;
}
