<?php

namespace unit\Services\Health;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Tax16\SystemCheckBundle\Services\Health\Checker\DoctrineDbConnectionChecker;

class DoctrineDbConnectionCheckerTest extends TestCase
{
    public function testConnectionIsSuccessful(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $connection = $this->createMock(Connection::class);

        $entityManager->method('getConnection')->willReturn($connection);
        $connection->method('isConnected')->willReturn(true);
        $connection->expects($this->never())->method('executeQuery');

        $checker = new DoctrineDbConnectionChecker($entityManager);
        $result = $checker->check();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Connection to the database "default" is successful.', $result->getMessage());
    }

    public function testConnectionIsNotConnectedInitially(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $connection = $this->createMock(Connection::class);

        $connection->method('isConnected')->willReturn(false);
        $connection->method('executeQuery');
        $entityManager->method('getConnection')->willReturn($connection);

        $checker = new DoctrineDbConnectionChecker($entityManager);
        $result = $checker->check();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Connection to the database "default" is successful.', $result->getMessage());
    }

    public function testConnectionFails(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $connection = $this->createMock(Connection::class);

        $entityManager->method('getConnection')->willReturn($connection);
        $connection->method('isConnected')->willReturn(false);
        $connection->method('executeQuery')->will($this->throwException(new \Exception('Connection error')));

        $checker = new DoctrineDbConnectionChecker($entityManager);
        $result = $checker->check();

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('Failed to connect to the database "default": Connection error', $result->getMessage());
    }

    public function testCustomConnectionNameIsUsed(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $connection = $this->createMock(Connection::class);

        $entityManager->method('getConnection')->willReturn($connection);
        $connection->method('isConnected')->willReturn(true);
        $connection->expects($this->never())->method('executeQuery');

        $checker = new DoctrineDbConnectionChecker($entityManager, 'custom_connection');
        $result = $checker->check();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('Connection to the database "custom_connection" is successful.', $result->getMessage());
    }
}
