<?php

namespace unit\Services\Health;

use Codeception\Test\Unit;
use Tax16\SystemCheckBundle\DTO\CheckResult;
use Tax16\SystemCheckBundle\Services\Health\Checker\PhpVersionChecker;

class PhpVersionCheckerTest extends Unit
{
    public function phpVersionProvider(): array
    {
        return [
            ['7.4.0', '>=', '7.4.0', true],
            ['7.4.0', '>=', '7.3.0', true],
            ['7.4.0', '>=', '7.4.1', false],
            ['7.4.0', '=', '7.4.0', true],
            ['7.4.0', '=', '7.3.0', false],
            ['7.4.0', '<=', '7.4.0', true],
            ['7.4.0', '<=', '7.4.1', true],
            ['7.4.0', '<=', '7.3.0', false],
            ['7.4.0', '<', '7.4.1', true],
            ['7.4.0', '<', '7.4.0', false],
            ['7.4.0', '>', '7.3.0', true],
            ['7.4.0', '>', '7.4.0', false],
        ];
    }

    /**
     * @dataProvider phpVersionProvider
     */
    public function testPhpVersionCheck($currentVersion, $operator, $versionToCheck, $expectedResult): void
    {
        $checker = $this->getMockBuilder(PhpVersionChecker::class)
            ->setConstructorArgs([$versionToCheck, $operator])
            ->setMethods(['getPhpVersion'])
            ->getMock();

        $checker->method('getPhpVersion')->willReturn($currentVersion);

        $result = $checker->check();

        $this->assertInstanceOf(CheckResult::class, $result);
        $this->assertEquals($expectedResult, $result->isSuccess());
    }

    public function invalidOperatorProvider(): array
    {
        return [
            ['7.4.0', '=='],
            ['7.4.0', '!='],
            ['7.4.0', '==='],
            ['7.4.0', '!=='],
        ];
    }

    /**
     * @dataProvider invalidOperatorProvider
     */
    public function testInvalidOperatorThrowsException($versionToCheck, $operator): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PhpVersionChecker($versionToCheck, $operator);
    }
}