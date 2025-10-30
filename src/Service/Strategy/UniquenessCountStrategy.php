<?php

namespace App\Service\Strategy;

class UniquenessCountStrategy implements ArrayOperationStrategyInterface
{
    public function getKey(): string
    {
        return 'uniqueness';
    }

    /**
     * @param int[] $numbers
     *
     * @return array{value: int}
     */
    public function execute(array $numbers): array
    {
        return ['value' => count(array_flip($numbers))];
    }
}
