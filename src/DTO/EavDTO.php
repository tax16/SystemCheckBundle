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

        if (!isset($data['label'], $data['value'])) {
            throw new \InvalidArgumentException('Missing required fields in EavDTO data array.');
        }

        return new self($data['label'], $data['value']);
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
            'value' => $this->getValue(),
        ];
    }
}
