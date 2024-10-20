<?php

namespace Tax16\SystemCheckBundle\DTO;

use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Services\Health\Checker\Constant\CheckerIcon;

class HealthCheckDTO
{
    private CheckResult $result;

    private string $label;

    private string $description;

    private CriticalityLevel $priority;

    private ?string $icon;

    public function __construct(CheckResult $result, string $label, string $description, CriticalityLevel $priority, ?string $icon = CheckerIcon::UNKOWN)
    {
        $this->result = $result;
        $this->label = $label;
        $this->description = $description;
        $this->priority = $priority;
        $this->icon = $icon;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPriority(): CriticalityLevel
    {
        return $this->priority;
    }

    public function getResult(): CheckResult
    {
        return $this->result;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }
}
