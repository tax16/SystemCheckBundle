<?php

namespace Tax16\SystemCheckBundle\DTO;

class CheckResult
{
    private string $name;
    private bool $success;
    private ?string $message;
    private ?string $stack;

    /**
     * @var EavDTO[]|null
     */
    private ?array $eav;

    /**
     * @param array<EavDTO>|null $eav
     */
    public function __construct(
        string $name,
        bool $success,
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

    public function isSuccess(): bool
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
}
