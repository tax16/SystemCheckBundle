<?php

namespace Tax16\SystemCheckBundle\Services\Health\Checker;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\DTO\HealthCheckDTO;
use Tax16\SystemCheckBundle\Services\Health\Checker\Constant\CheckerIcon;

class HttpServiceChecker implements ServiceCheckInterface, HttpServiceCheckInterface
{
    private string $url;
    private int $statusCode;
    private HttpClientInterface $httpClient;
    private bool $toTrace = false;
    private ?ResponseInterface $response = null;

    /**
     * @var HealthCheckDTO[]
     */
    private array $childrenProcess = [];

    /**
     * @param string                   $url        the full URL of the HTTP service to check
     * @param int|null                 $statusCode the expected HTTP status code (default is 200)
     * @param HttpClientInterface|null $httpClient the HTTP client to use
     */
    public function __construct(string $url, ?int $statusCode = 200, ?HttpClientInterface $httpClient = null)
    {
        $this->url = $url;
        $this->statusCode = $statusCode ?: 200;
        $this->httpClient = $httpClient ?: HttpClient::create();
    }

    /**
     * @return CheckResult the result of the HTTP service check
     */
    public function check(): CheckResult
    {
        try {
            $response = $this->httpClient->request('GET', $this->url.'?trace='.$this->isToTrace());

            $statusCode = $response->getStatusCode();
            if ($this->toTrace) {
                $this->response = $response;
            }

            if ($statusCode !== $this->statusCode) {
                return new CheckResult(
                    $this->getName(),
                    false,
                    sprintf('Expected status code %d but received %d.', $this->statusCode, $statusCode),
                    null
                );
            }

            return new CheckResult(
                $this->getName(),
                true,
                sprintf('The service at %s is available with status code %d.', $this->url, $statusCode),
                null
            );
        } catch (TransportExceptionInterface $e) {
            return new CheckResult(
                $this->getName(),
                false,
                sprintf('Transport error: %s', $e->getMessage()),
                $e->getTraceAsString()
            );
        } catch (\Exception $e) {
            return new CheckResult(
                $this->getName(),
                false,
                sprintf('An unexpected error occurred: %s', $e->getMessage()),
                $e->getTraceAsString()
            );
        }
    }

    public function getName(): string
    {
        return 'HTTP Services';
    }

    public function getIcon(): ?string
    {
        return CheckerIcon::WEBSITE;
    }

    public function isToTrace(): bool
    {
        return $this->toTrace;
    }

    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    public function setHttpClient(HttpClientInterface $httpClient): HttpServiceCheckInterface
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    public function setToTrace(bool $toTrace): HttpServiceCheckInterface
    {
        $this->toTrace = $toTrace;

        return $this;
    }

    public function getResponseData(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param array<HealthCheckDTO> $childrenChecker
     */
    public function setChildren(array $childrenChecker): void
    {
        $this->childrenProcess = $childrenChecker;
    }

    /**
     * @return HealthCheckDTO[]
     */
    public function getChildrenProcess(): array
    {
        return $this->childrenProcess;
    }
}
