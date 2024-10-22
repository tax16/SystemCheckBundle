<?php

namespace Tax16\SystemCheckBundle\ValueObject;

class SystemNodeEdge
{
    public const EDGE_LENGTH_MAIN = 150;

    public const EDGE_LENGTH_SUB = 300;

    public const LABEL_LENGTH_MAX = 150;

    private int $from;

    private int $to;

    private int $length;

    /** @var array<mixed> */
    private array $color;

    private ?string $label;

    /**
     * @param array<mixed> $color
     */
    public function __construct(int $from, int $to, int $length = self::EDGE_LENGTH_SUB, array $color = [], ?string $label = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->length = $length;
        $this->color = $color;
        $this->setLabel($label);
    }

    private function setLabel(?string $label): void
    {
        if (null !== $label && strlen($label) > self::LABEL_LENGTH_MAX) {
            $this->label = substr($label, 0, self::LABEL_LENGTH_MAX).'...';
        } else {
            $this->label = $label;
        }
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'length' => $this->length,
            'color' => $this->color,
            'label' => $this->label,
            'labelHighlightBold' => true,
            'widthConstraint' => 100,
            'width' => 5,
        ];
    }
}
