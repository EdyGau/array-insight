<?php

namespace App\Helper;

class ArrayHelper
{
    /**
     * @return int[]
     */
    public static function parseNumberString(string $input): array
    {
        return array_map(
            'intval',
            array_filter(
                array_map('trim', explode(',', $input)),
                fn (string $v): bool => '' !== $v
            )
        );
    }
}
