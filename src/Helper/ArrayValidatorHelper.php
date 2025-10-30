<?php

namespace App\Helper;

class ArrayValidatorHelper
{
    /**
     * @return string[]
     */
    public static function parseNumbers(string $value): array
    {
        $parts = array_map('trim', explode(',', $value));

        return array_filter($parts, fn ($p) => '' !== $p);
    }

    public static function validateNumberString(string $number): bool
    {
        return 1 === preg_match('/^-?\d+$/', $number);
    }

    /**
     * @param string[] $numbers
     *
     * @return int[]
     */
    public static function toIntArray(array $numbers): array
    {
        return array_map('intval', $numbers);
    }
}
