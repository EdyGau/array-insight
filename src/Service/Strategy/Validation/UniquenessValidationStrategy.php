<?php

namespace App\Service\Strategy\Validation;

class UniquenessValidationStrategy implements ValidationStrategyInterface
{
    public function supports(string $strategy): bool
    {
        return 'uniqueness' === $strategy;
    }

    /**
     * @param int[] $numbers
     */
    public function validate(array $numbers): ?string
    {
        $count = count($numbers);
        if ($count < 1 || $count > 100000) {
            return 'Rozmiar tablicy dla unikalności: 1–100000.';
        }

        $min = min($numbers);
        $max = max($numbers);
        if ($min < -100000 || $max > 100000) {
            return 'Wartości muszą być z zakresu -100000 do 100000.';
        }

        return null;
    }
}
