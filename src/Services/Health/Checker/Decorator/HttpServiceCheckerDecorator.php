<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker\Decorator;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Services\Health\Checker\HttpServiceCheckInterface;

class HttpServiceCheckerDecorator implements HttpServiceCheckInterface
{
    private HttpServiceCheckInterface $httpServiceCheck;

    private string $applicationId;

    public function __construct(
        HttpServiceCheckInterface $httpServiceCheck,
        ?string $applicationId = null,
    ) {
        $this->httpServiceCheck = $httpServiceCheck;
        $this->applicationId = $applicationId ?: uniqid();
    }

    public function isToTrace(): bool
    {
        return false;
    }

    public function check(): CheckResult
    {
        $this->setToTrace(true);

        $currentHttpClient = $this->getHttpClient();
        $currentHttpClient->withOptions([
            'headers' => [
                'x-trace-id' => $this->applicationId,
            ],
        ]);
        $response = $this->httpServiceCheck->check();

        $result = json_decode($this->getResponseData() ?? '[]', true);

        $healthCheckChildren = array_filter(
            array_map(fn ($data) => HealthCheckDTO::fromArray($data), $result ?? []),
            fn ($item) => null !== $item
        );

        $response->setChildren($healthCheckChildren);

        return $response;
    }

    public function getName(): string
    {
        return $this->httpServiceCheck->getName();
    }

    public function getIcon(): ?string
    {
        return $this->httpServiceCheck->getIcon();
    }

    public function getHttpClient(): HttpClientInterface
    {
        if (method_exists($this->httpServiceCheck, 'getHttpClient')) {
            return $this->httpServiceCheck->getHttpClient();
        }

        throw new \RuntimeException('The decorated service does not provide a HttpClient.');
    }

    public function setHttpClient(HttpClientInterface $httpClient): HttpServiceCheckInterface
    {
        $this->httpServiceCheck->setHttpClient($httpClient);

        return $this->httpServiceCheck;
    }

    public function setToTrace(bool $toTrace): HttpServiceCheckInterface
    {
        $this->httpServiceCheck->setToTrace($toTrace);

        return $this;
    }

    public function getResponseData(): ?string
    {
        return $this->httpServiceCheck->getResponseData();
    }
}
