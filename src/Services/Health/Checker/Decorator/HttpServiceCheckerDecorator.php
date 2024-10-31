<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker\Decorator;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Services\Health\Checker\HttpServiceCheckInterface;

class HttpServiceCheckerDecorator implements HttpServiceCheckInterface
{
    private HttpServiceCheckInterface $httpServiceCheck;

    private string $applicationId;

    public function __construct(
        HttpServiceCheckInterface $httpServiceCheck,
        ParameterBagInterface $parameterBag,
        Request $request
    ) {
        $this->applicationId = $request->headers->get('X-Trace-Id', $parameterBag->get('system_check.id'));
        $this->httpServiceCheck = $httpServiceCheck;
    }

    public function isToTrace(): bool
    {
        return false;
    }


    public function check(): CheckResult
    {
        $this->setToTrace(true);

        $currentHttpClient = $this->getHttpClient()->withOptions([
            'headers' => [
                'X-Trace-Id' => $this->applicationId,
            ],
        ]);
        $this->httpServiceCheck->setHttpClient($currentHttpClient);
        $response = $this->httpServiceCheck->check();

        if ($this->getResponseData()) {
            $result = json_decode($this->getResponseData()->getContent() ?? '[]', true);

            $healthCheckChildren = array_filter(
                array_map(fn ($data) => HealthCheckDTO::fromArray($data), $result ?? []),
                fn ($item) => null !== $item
            );

            $response->setChildren($healthCheckChildren);
        }


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

    public function getResponseData(): ?ResponseInterface
    {
        return $this->httpServiceCheck->getResponseData();
    }
}
