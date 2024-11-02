<?php

namespace Tax16\SystemCheckBundle\Core\Domain\ValueObject;

class SystemNode
{
    private const PATH_ICON = '/bundles/systemcheck/images/icon/';

    private $id;

    private $image;

    private $label;

    private $shape;

    /**
     * @var array<string, mixed>
     */
    private $color;

    /**
     * @param array<string, mixed> $color
     */
    public function __construct(
        string $id,
        string $image,
        string $label,
        array $color,
        string $shape = 'image'
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
            'image' => self::PATH_ICON.$this->image,
            'label' => $this->label,
            'shape' => $this->shape,
            'color' => $this->color,
            'shapeProperties' => [
                'useImageSize' => false,
                'useBorderWithImage' => false,
            ],
        ];
    }
}
