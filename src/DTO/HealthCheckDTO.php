<?php

namespace Tax16\SystemCheckBundle\DTO;

use Tax16\SystemCheckBundle\Enum\CriticalityLevel;
use Tax16\SystemCheckBundle\Services\Health\Checker\Constant\CheckerIcon;

class HealthCheckDTO
{
    private CheckResult $result;

    private string $label;
    private string $id;

    private string $description;

    private CriticalityLevel $priority;

    private ?string $icon;
    private ?string $parent;

    public function __construct(CheckResult $result, string $id, string $label, string $description, CriticalityLevel $priority, ?string $icon = CheckerIcon::UNKNOWN, ?string $parent = null)
    {
        $this->result = $result;
        $this->id = $id;
        $this->label = $label;
        $this->description = $description;
        $this->priority = $priority;
        $this->icon = $icon;
        $this->parent = $parent;
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * Convert the DTO to an associative array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'priority' => $this->getPriority()->value,
            'icon' => $this->getIcon(),
            'result' => $this->getResult()->toArray(),
        ];
    }

    /**
     * Create an instance from an associative array.
     *
     * @param array<mixed> $data
     *
     * @throws \InvalidArgumentException if required fields are missing
     */
    public static function fromArray(array $data): ?self
    {
        if (empty($data)) {
            return null;
        }

        if (!isset($data['label'], $data['description'], $data['priority'], $data['result'])) {
            throw new \InvalidArgumentException('Missing required fields in data array.');
        }

        if (!$result = CheckResult::fromArray($data['result'])) {
            throw new \InvalidArgumentException('Result value not valid.');
        }

        $priority = CriticalityLevel::from($data['priority']);

        return new self(
            $result,
            $data['label'],
            $data['id'],
            $data['description'],
            $priority,
            $data['icon'] ?? CheckerIcon::UNKNOWN
        );
    }
}
