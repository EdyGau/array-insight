<?php

namespace App\Service\Strategy\Validation;

interface ValidationStrategyInterface
{
    public function supports(string $strategy): bool;

    /**
     * @param int[] $numbers
     */
    public function validate(array $numbers): ?string;
}
