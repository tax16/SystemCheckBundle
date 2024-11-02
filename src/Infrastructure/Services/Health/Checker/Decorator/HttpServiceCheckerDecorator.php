<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker\Decorator;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Port\ConfigurationProviderInterface;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\HttpServiceCheckInterface;

class HttpServiceCheckerDecorator implements HttpServiceCheckInterface
{
    private $httpServiceCheck;

    private $applicationId;

    public function __construct(
        HttpServiceCheckInterface $httpServiceCheck,
        ConfigurationProviderInterface $parameterBag,
        RequestStack $requestStack
    ) {
        $request = $requestStack->getCurrentRequest();

        $this->applicationId = $request
            ? $request->headers->get('X-Trace-Id', $parameterBag->get('system_check.id'))
            : $parameterBag->get('system_check.id');

        $this->httpServiceCheck = $httpServiceCheck;
    }

    public function isToTrace(): bool
    {
        return false;
    }


    public function check(): CheckInfo
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
                array_map(static function ($data) {
                    return HealthCheck::fromArray($data);
                }, $result ?? []),
                static function ($item) {
                    return null !== $item;
                }
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

    public function isAllowedToHaveChildren(): bool
    {
        return $this->httpServiceCheck->isAllowedToHaveChildren();
    }
}