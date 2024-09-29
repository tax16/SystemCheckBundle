<?php

namespace Tax16\SystemCheckBundle\Services\Health;

use Tax16\SystemCheckBundle\Services\Health\DTO\CheckResult;

class PhpVersionChecker implements ServiceCheckInterface
{
    private string $versionToCheck;
    private string $operator;

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
     * @return CheckResult the result of the check, including the status, message, and criticality level
     */
    public function check(): CheckResult
    {
        $currentVersion = $this->getPhpVersion();

        if (version_compare($currentVersion, $this->versionToCheck, $this->operator)) {
            return new CheckResult(
                $this->getName(),
                true,
                "The current PHP version ($currentVersion) meets the required version ({$this->operator} {$this->versionToCheck}).",
                null
            );
        }

        return new CheckResult(
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
        return 'PHP Version Check';
    }
}
