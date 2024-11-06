<?php

namespace Tax16\SystemCheckBundle\Infrastructure\Services\Health\Checker;

use Tax16\SystemCheckBundle\Core\Domain\Service\ServiceCheckInterface;
use Tax16\SystemCheckBundle\Core\Domain\Model\CheckInfo;

abstract class AbstractChecker implements ServiceCheckInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $icon;

    /**
     * @var bool
     */
    protected $allowChildren = false;

    public function __construct(string $name, ?string $icon = null, bool $allowChildren = false)
    {
        $this->name = $name;
        $this->icon = $icon;
        $this->allowChildren = $allowChildren;
    }

    /**
     * Get the name of the health check.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the icon for the health check.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Checks if this service checker allows children.
     */
    public function isAllowedToHaveChildren(): bool
    {
        return $this->allowChildren;
    }

    /**
     * Perform the health check.
     */
    abstract public function check(bool $withNetwork = false): CheckInfo;
}
