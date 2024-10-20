<?php

namespace Tax16\SystemCheckBundle\DTO;

class HealthCheckDTO
{
    private CheckResult $result;

    private string $label;

    private string $description;

    private int $priority;

    public function __construct(CheckResult $result, string $label, string $description, int $priority)
    {
        $this->result = $result;
        $this->label = $label;
        $this->description = $description;
        $this->priority = $priority;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getResult(): CheckResult
    {
        return $this->result;
    }
}
