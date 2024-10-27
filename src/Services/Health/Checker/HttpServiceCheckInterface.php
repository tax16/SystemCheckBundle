<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface HttpServiceCheckInterface extends ServiceCheckInterface
{
    public function isToTrace(): bool;

    public function setToTrace(bool $toTrace): HttpServiceCheckInterface;

    public function getHttpClient(): HttpClientInterface;

    public function setHttpClient(HttpClientInterface $httpClient): HttpServiceCheckInterface;

    public function getResponseData(): ?string;
}
