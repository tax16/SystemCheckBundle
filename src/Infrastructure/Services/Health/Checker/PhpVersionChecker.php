<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;
use Tax16\SystemCheckBundle\Core\Domain\Model\HealthCheck;
use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;

class PhpVersionChecker implements ServiceCheckInterface
{
    private $versionToCheck;
    private $operator;

    /**
     * @param string $versionToCheck the PHP version to check against
     * @param string $operator       The comparison operator (default is '>=').
     *                               Valid values: '=', '>=', '<=', '<', '>'.
     */
    public function __construct(string $versionToCheck, string $operator = '>=')
    {
        // Validate the operator
        $validOperators = ['=', '>=', '<=', '<', '>'];
        if (!in_array($operator, $validOperators)) {
            throw new \InvalidArgumentException('Invalid comparison operator');
        }

        $this->versionToCheck = $versionToCheck;
        $this->operator = $operator;
    }

    /**
     * Check the current PHP version against the provided version.
     *
     * @param bool $execute
     * @return CheckInfo the result of the check, including the status, message, and criticality level
     */
    public function check(): CheckInfo
    {
        $currentVersion = $this->getPhpVersion();

        if (version_compare($currentVersion, $this->versionToCheck, $this->operator)) {
            return new CheckInfo(
                $this->getName(),
                true,
                "The current PHP version ($currentVersion) meets the required version ({$this->operator} {$this->versionToCheck}).",
                null
            );
        }

        return new CheckInfo(
            $this->getName(),
            false,
            "The current PHP version ($currentVersion) does not meet the required version ({$this->operator} {$this->versionToCheck}).",
            null
        );
    }

    protected function getPhpVersion(): string
    {
        return phpversion();
    }

    public function getName(): string
    {
        return 'PHP Version';
    }

    public function getIcon(): ?string
    {
        return CheckerIcon::PHP;
    }

    public function isAllowedToHaveChildren(): bool
    {
        return false;
    }
}
