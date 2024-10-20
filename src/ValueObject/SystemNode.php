<?php

namespace Tax16\SystemCheckBundle\ValueObject;

class SystemNode
{
    private int $id;

    private string $image;

    private string $label;

    private string $shape;

    /**
     * @var array<string, mixed>
     */
    private array $color;

    /**
     * @param array<string, mixed> $color
     */
    public function __construct(
        int $id,
        string $image,
        string $label,
        array $color,
        string $shape = 'circularImage',
    ) {
        $this->id = $id;
        $this->image = $image;
        $this->label = $label;
        $this->shape = $shape;
        $this->color = $color;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image.'....',
            'label' => $this->label,
            'shape' => $this->shape,
            'color' => $this->color,
        ];
    }
}
