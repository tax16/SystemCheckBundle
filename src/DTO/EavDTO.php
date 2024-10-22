<?php

namespace Tax16\SystemCheckBundle\DTO;

class EavDTO
{
    private string $label;

    private mixed $value;

    public function __construct(string $label, mixed $value)
    {
        $this->label = $label;
        $this->value = $value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}
