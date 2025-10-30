<?php

namespace App\Service\Strategy;

interface ArrayOperationStrategyInterface
{
    public function getKey(): string;

    /**
     * @param int[] $numbers
     *
     * @return array{value: int|bool}
     */
    public function execute(array $numbers): array;
}
