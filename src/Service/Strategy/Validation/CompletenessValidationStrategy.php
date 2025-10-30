<?php

namespace App\Service\Strategy\Validation;

class CompletenessValidationStrategy implements ValidationStrategyInterface
{
    public function supports(string $strategy): bool
    {
        return 'completeness' === $strategy;
    }

    /**
     * @param int[] $numbers
     */
    public function validate(array $numbers): ?string
    {
        $count = count($numbers);
        if ($count < 1 || $count > 10000) {
            return 'Rozmiar tablicy dla kompletności: 1–10000.';
        }

        $min = min($numbers);
        $max = max($numbers);
        if ($min < 1 || $max > 1000000) {
            return 'Wartości muszą być z zakresu 1–1000000.';
        }

        return null;
    }
}
