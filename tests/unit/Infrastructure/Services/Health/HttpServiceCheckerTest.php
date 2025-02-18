<?php

namespace unit\Infrastructure\Services\Health;

use Codeception\Test\Unit;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker\HttpServiceChecker;

class HttpServiceCheckerTest extends Unit
{
    /**
     * @var HttpClientInterface
     */
    private  $httpClientMock;
    /**
     * @var ResponseInterface
     */
    private  $responseMock;

    protected function _before()
    {
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->responseMock = $this->createMock(ResponseInterface::class);
    }

    public function testServiceAvailableWithExpectedStatusCode()
    {
        $this->responseMock->method('getStatusCode')->willReturn(200);
        $this->httpClientMock->method('request')->willReturn($this->responseMock);

        $checker = new HttpServiceChecker('http://example.com', 200, $this->httpClientMock);
        $result = $checker->check();

        $this->assertInstanceOf(CheckInfo::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertStringContainsString('The service at http://example.com is available with status code 200.', $result->getMessage());
    }

    public function testServiceAvailableWithDifferentStatusCode()
    {
        $this->responseMock->method('getStatusCode')->willReturn(404);
        $this->httpClientMock->method('request')->willReturn($this->responseMock);

        $checker = new HttpServiceChecker('http://example.com', 200, $this->httpClientMock);
        $result = $checker->check();

        $this->assertInstanceOf(CheckInfo::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('Expected status code 200 but received 404.', $result->getMessage());
    }

    public function testServiceTransportError()
    {
        $this->httpClientMock->method('request')->willThrowException(
            $this->createMock(TransportExceptionInterface::class)
        );

        $checker = new HttpServiceChecker('http://example.com', 200, $this->httpClientMock);
        $result = $checker->check();

        $this->assertInstanceOf(CheckInfo::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('Transport error', $result->getMessage());
    }

    public function testServiceUnexpectedError()
    {
        $this->httpClientMock->method('request')->willThrowException(new \Exception('Some unexpected error'));

        $checker = new HttpServiceChecker('http://example.com', 200, $this->httpClientMock);
        $result = $checker->check();

        $this->assertInstanceOf(CheckInfo::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('An unexpected error occurred: Some unexpected error', $result->getMessage());
    }
}
