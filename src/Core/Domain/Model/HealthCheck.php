<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Model;

use Tax16\SystemCheckBundle\Core\Domain\Constant\CheckerIcon;
use Tax16\SystemCheckBundle\Core\Domain\Enum\CriticalityLevel;

class HealthCheck
{
    /**
     * @var CheckInfo
     */
    private $result;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var string|null
     */
    private $icon;

    /**
     * @var string|null
     */
    private $parent;

    public function __construct(CheckInfo $result, string $id, string $label, string $description, int $priority, ?string $icon = CheckerIcon::UNKNOWN, ?string $parent = null)
    {
        if (!CriticalityLevel::isValid($priority)) {
            throw new \InvalidArgumentException('Invalid priority level');
        }

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

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getResult(): CheckInfo
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
            'priority' => $this->getPriority(),
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

        if (!$result = CheckInfo::fromArray($data['result'])) {
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
