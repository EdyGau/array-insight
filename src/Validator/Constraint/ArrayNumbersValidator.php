<?php

namespace App\Validator\Constraint;

use App\Helper\ArrayValidatorHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ArrayNumbersValidator extends ConstraintValidator
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_string($value) || '' === $value) {
            return;
        }

        $form = $this->context->getRoot();
        if (!$form instanceof FormInterface) {
            return;
        }

        $manualInput = $form->get('manualInput')->getData();
        $strategy = $form->get('strategy')->getData() ?: 'uniqueness';

        if (!$manualInput) {
            return;
        }

        $parts = ArrayValidatorHelper::parseNumbers($value);

        if (0 === count($parts)) {
            $this->logger->warning('Brak liczb do analizy.', [
                'strategy' => $strategy,
                'input' => $value,
            ]);

            return;
        }

        foreach ($parts as $p) {
            if (!ArrayValidatorHelper::validateNumberString($p)) {
                $message = sprintf('Niepoprawna liczba: %s', $p);
                $this->context->buildViolation($message)
                    ->setParameter('{{ value }}', $p)
                    ->addViolation();

                $this->logger->warning('Walidacja tablicy: niepoprawna liczba', [
                    'strategy' => $strategy,
                    'invalid_value' => $p,
                ]);

                return;
            }
        }

        $intParts = ArrayValidatorHelper::toIntArray($parts);
        $count = count($intParts);
        if (0 === $count) {
            return;
        }

        $minVal = min($intParts);
        $maxVal = max($intParts);

        if ('uniqueness' === $strategy && ($count > 100_000 || $minVal < -100_000 || $maxVal > 100_000)) {
            $message = 'Liczby dla unikalności muszą mieścić się w zakresie -100000 do 100000, max 100000 elementów';
            $this->context->buildViolation($message)->addViolation();
            $this->logger->warning('Walidacja unikalności nie powiodła się', [
                'count' => $count,
                'min' => $minVal,
                'max' => $maxVal,
            ]);
        } elseif ('completeness' === $strategy && ($count > 10_000 || $minVal < 1 || $maxVal > 1_000_000)) {
            $message = 'Liczby dla kompletności muszą mieścić się w zakresie 1 do 1000000, max 10000 elementów';
            $this->context->buildViolation($message)->addViolation();
            $this->logger->warning('Walidacja kompletności nie powiodła się', [
                'count' => $count,
                'min' => $minVal,
                'max' => $maxVal,
            ]);
        }
    }
}
