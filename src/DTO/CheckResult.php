<?php

namespace Tax16\SystemCheckBundle\DTO;

class CheckResult
{
    private string $name;
    private bool $success;
    private ?string $message;
    private ?string $stack;

    public function __construct(
        string $name,
        bool $success,
        ?string $message = null,
        ?string $stack = null,
    ) {
        $this->name = $name;
        $this->success = $success;
        $this->message = $message;
        $this->stack = $stack;
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
}
