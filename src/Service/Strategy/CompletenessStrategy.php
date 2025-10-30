<?php

namespace App\Service\Strategy;

class CompletenessStrategy implements ArrayOperationStrategyInterface
{
    public function getKey(): string
    {
        return 'completeness';
    }

    /**
     * @param int[] $numbers
     *
     * @return array{value: bool}
     */
    public function execute(array $numbers): array
    {
        $n = count($numbers);
        $map = array_flip($numbers);
        for ($i = 1; $i <= $n; ++$i) {
            if (!isset($map[$i])) {
                return ['value' => false];
            }
        }

        return ['value' => true];
    }
}
