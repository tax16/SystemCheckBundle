<?php

namespace Tax16\SystemCheckBundle\ValueObject;

class SystemNodeEdge
{
    public const EDGE_LENGTH_MAIN = 400;

    public const EDGE_LENGTH_SUB = 400;

    public const LABEL_LENGTH_MAX = 150;

    private string $from;

    private string $to;

    private int $length;

    /** @var array<mixed> */
    private array $color;

    private ?string $label;

    private ?bool $dashes = false;

    /**
     * @param array<mixed> $color
     */
    public function __construct(string $from, string $to, int $length = self::EDGE_LENGTH_SUB, array $color = [], ?string $label = null, ?bool $dashes = false)
    {
        $this->from = $from;
        $this->to = $to;
        $this->length = $length;
        $this->color = $color;
        $this->setLabel($label);
        $this->dashes = $dashes;
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
            'dashes' => $this->dashes,
            'width' => 3,
            'font' => [
                'align' => 'right',
            ],
        ];
    }
}
