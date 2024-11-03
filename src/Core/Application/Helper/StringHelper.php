<?php

namespace Tax16\SystemCheckBundle\Core\Application\Helper;

class StringHelper
{
    /**
     * Static function to validate that a value is a non-empty string.
     *
     * @param mixed  $value     the value to validate
     * @param string $fieldName the name of the field for error messages
     *
     * @throws \InvalidArgumentException if the value is not a non-empty string
     */
    public static function validateNonEmptyString($value, string $fieldName = 'Field'): string
    {
        if (!is_string($value) || '' === trim($value)) {
            throw new \InvalidArgumentException("$fieldName is required and must be a non-empty string.");
        }

        return $value;
    }
}
