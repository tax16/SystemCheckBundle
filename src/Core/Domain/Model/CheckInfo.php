<?php
declare(strict_types=1);
namespace Tax16\SystemCheckBundle\Core\Domain\Model;

class CheckInfo
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool|null
     */
    private $success;
    /**
     * @var string|null
     */
    private $message;
    /**
     * @var string|null
     */
    private $stack;

    /**
     * @var Eav[]|null
     */
    private $eav;

    /**
     * @var array<HealthCheck>|null
     */
    private $children = [];

    /**
     * @param array<Eav>|null $eav
     */
    public function __construct(
        string $name,
        ?bool $success,
        ?string $message = null,
        ?string $stack = null,
        ?array $eav = []
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
     * @return array|Eav[]|null
     */
    public function getEav(): ?array
    {
        return $this->eav;
    }

    public function addEav(Eav $eavDTO): self
    {
        $this->eav[] = $eavDTO;

        return $this;
    }

    /**
     * @return HealthCheck[]|null
     */
    public function getChildren(): ?array
    {
        return $this->children;
    }

    /**
     * @param HealthCheck[] $children
     *
     * @return $this
     */
    public function setChildren(array $children): CheckInfo
    {
        $this->children = array_merge($this->children ?? [], $children);

        return $this;
    }

    public function addChildren(HealthCheck $children): CheckInfo
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
            'eav' => $this->eav ? array_map(static function ($eavDTO) {
                return $eavDTO->toArray();
            }, $this->eav) : null,
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
                array_map(static function ($eav) {
                    return Eav::fromArray($eav);
                }, $data['eav']),
                static function ($item) {
                    return null !== $item;
                }
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
