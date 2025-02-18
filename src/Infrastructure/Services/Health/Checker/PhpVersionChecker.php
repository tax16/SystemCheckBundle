<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;

class PhpVersionChecker extends AbstractChecker
{
    /**
     * @var string
     */
    private $versionToCheck;

    /**
     * @var string
     */
    private $operator;

    /**
     * @param string $versionToCheck the PHP version to check against
     * @param string $operator       The comparison operator (default is '>=').
     *                               Valid values: '=', '>=', '<=', '<', '>'.
     */
    public function __construct(string $versionToCheck, string $operator = '>=')
    {
        parent::__construct('PHP Version', CheckerIcon::PHP);

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
     * @return CheckInfo the result of the check, including the status, message, and criticality level
     */
    public function check(bool $withNetwork = false): CheckInfo
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
}
