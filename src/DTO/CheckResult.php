<?php

namespace Tax16\SystemCheckBundle\DTO;

class CheckResult
{
    private string $name;
    private ?bool $success;
    private ?string $message;
    private ?string $stack;

    /**
     * @var EavDTO[]|null
     */
    private ?array $eav;

    /**
     * @var array<HealthCheckDTO>|null
     */
    private ?array $children = [];

    /**
     * @param array<EavDTO>|null $eav
     */
    public function __construct(
        string $name,
        ?bool $success,
        ?string $message = null,
        ?string $stack = null,
        ?array $eav = [],
    ) {
        $this->name = $name;
        $this->success = $success;
        $this->message = $message;
        $this->stack = $stack;
        $this->eav = $eav;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSuccess(): ?bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStack(): ?string
    {
        return $this->stack;
    }

    /**
     * @return array|EavDTO[]|null
     */
    public function getEav(): ?array
    {
        return $this->eav;
    }

    public function addEav(EavDTO $eavDTO): self
    {
        $this->eav[] = $eavDTO;

        return $this;
    }

    /**
     * @return HealthCheckDTO[]|null
     */
    public function getChildren(): ?array
    {
        return $this->children;
    }

    /**
     * @param HealthCheckDTO[] $children
     *
     * @return $this
     */
    public function setChildren(array $children): CheckResult
    {
        $this->children = array_merge($this->children ?? [], $children);

        return $this;
    }

    public function addChildren(HealthCheckDTO $children): CheckResult
    {
        $this->children[] = $children;

        return $this;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'success' => $this->isSuccess(),
            'message' => $this->getMessage(),
            'stack' => $this->getStack(),
            'eav' => $this->eav ? array_map(fn ($eavDTO) => $eavDTO->toArray(), $this->eav) : null,
        ];
    }

    /**
     * Create an instance from an associative array.
     *
     * @param mixed[] $data the data array
     *
     * @throws \InvalidArgumentException if required fields are missing
     */
    public static function fromArray(array $data): ?self
    {
        if (empty($data)) {
            return null;
        }

        if (!isset($data['name'])) {
            throw new \InvalidArgumentException('Missing required fields in CheckResult data array.');
        }

        $eav = [];
        if (isset($data['eav']) && is_array($data['eav'])) {
            $eav = array_filter(
                array_map(fn ($eav) => EavDTO::fromArray($eav), $data['eav']),
                fn ($item) => null !== $item
            );
        }

        return new self(
            $data['name'],
            $data['success'],
            $data['message'] ?? null,
            $data['stack'] ?? null,
            $eav
        );
    }
}
