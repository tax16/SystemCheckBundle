<?php

declare(strict_types=1);

namespace Tax16\SystemCheckBundle\Core\Domain\Enum;

abstract class Enum
{
    /**
     * Get all constant values from the child class.
     *
     * @return mixed[]
     */
    public static function values(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return array_values($reflection->getConstants());
    }

    /**
     * Check if a given value is valid in the enum.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return in_array($value, static::values(), true);
    }

    /**
     * Get the enum value if it exists, or throw an exception if it does not.
     *
     * @param mixed $value
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function from($value): string
    {
        $reflection = new \ReflectionClass(static::class);
        $constants = $reflection->getConstants();

        $name = array_search($value, $constants, true);

        if ($name === false) {
            throw new \InvalidArgumentException("Invalid value '$value' for enum " . static::class);
        }

        return $name;
    }
}