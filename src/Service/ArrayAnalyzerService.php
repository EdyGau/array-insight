<?php

namespace App\Service;

use App\Helper\ArrayHelper;
use App\Service\Strategy\ArrayOperationStrategyInterface;
use App\Service\Strategy\Validation\ValidationStrategyInterface;
use Psr\Log\LoggerInterface;
use Random\RandomException;

class ArrayAnalyzerService
{
    /** @var array<string, ArrayOperationStrategyInterface> */
    private array $strategies;

    /** @var ValidationStrategyInterface[] */
    private array $validationStrategies;

    private LoggerInterface $logger;

    /**
     * @var array{
     *   max_size: int,
     *   max_unique_range: int,
     *   max_completeness_range: int,
     *   max_completeness_count: int
     * }
     */
    private array $config;

    /**
     * @param iterable<ArrayOperationStrategyInterface>                                                             $strategies
     * @param iterable<ValidationStrategyInterface>                                                                 $validationStrategies
     * @param array{max_size: int, max_unique_range: int, max_completeness_range: int, max_completeness_count: int} $config
     */
    public function __construct(
        iterable $strategies,
        iterable $validationStrategies,
        LoggerInterface $logger,
        array $config,
        private readonly ?PerformanceLogger $performanceLogger = null,
    ) {
        $this->strategies = [];
        foreach ($strategies as $strategy) {
            $this->strategies[$strategy->getKey()] = $strategy;
        }

        $this->validationStrategies = [];
        foreach ($validationStrategies as $validator) {
            $this->validationStrategies[] = $validator;
        }

        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return int[]
     *
     * @throws RandomException
     */
    public function prepareArray(array $data): array
    {
        if (!empty($data['manualInput']) && !empty($data['numbers']) && is_string($data['numbers'])) {
            $this->logger->info('Użytkownik wprowadził własne liczby.', ['input' => $data['numbers']]);

            return ArrayHelper::parseNumberString($data['numbers']);
        }

        $strategy = $data['strategy'] ?? 'uniqueness';
        $size = isset($data['arraySize']) && is_numeric($data['arraySize'])
            ? max(1, min((int) $this->config['max_size'], (int) $data['arraySize']))
            : 10;

        $config = $this->config; //TODO validation do random
        $this->logger->info('Generowanie tablicy liczb losowo.', ['strategy' => $strategy, 'size' => $size]);

        return match ($strategy) {
            'completeness' => array_map(
                static fn () => random_int(1, (int) $config['max_completeness_range']),
                range(1, min($size, (int) $config['max_completeness_count']))
            ),
            'uniqueness' => array_map(
                static fn () => random_int(-(int) $config['max_unique_range'], (int) $config['max_unique_range']),
                range(1, $size)
            ),
            default => [],
        };
    }

    /**
     * @param int[] $numbers
     *
     * @return array{strategy: string, value: int|bool|null, size: int}|array{error: string}
     */
    public function analyze(array $numbers, string $strategyKey): array
    {
        $cleanNumbers = array_map('intval', array_filter($numbers, 'is_numeric'));

        if ([] === $cleanNumbers) {
            $this->logger->warning('Pusta lub niepoprawna tablica do analizy.', ['numbers' => $numbers]);

            return ['error' => 'Tablica nie może być pusta lub zawiera niepoprawne wartości'];
        }

        $analyzeFn = function () use ($cleanNumbers, $strategyKey): array {
            return $this->performAnalysis($cleanNumbers, $strategyKey);
        };

        if ($this->performanceLogger) {
            /** @var array{strategy: string, value: int|bool|null, size: int}|array{error: string} $result */
            $result = $this->performanceLogger->measure($analyzeFn, "ArrayAnalyzerService::$strategyKey");

            return $result;
        }

        return $this->performAnalysis($cleanNumbers, $strategyKey);
    }

    /**
     * @param int[] $cleanNumbers
     *
     * @return array{strategy: string, value: int|bool|null, size: int}|array{error: string}
     */
    private function performAnalysis(array $cleanNumbers, string $strategyKey): array
    {
        foreach ($this->validationStrategies as $validator) {
            if ($validator->supports($strategyKey)) {
                $error = $validator->validate($cleanNumbers);
                if (null !== $error) {
                    $this->logger->warning('Błąd walidacji tablicy.', ['strategy' => $strategyKey, 'error' => $error]);

                    return ['error' => $error];
                }
            }
        }

        if (!isset($this->strategies[$strategyKey])) {
            $this->logger->error('Nieznana strategia analizy.', ['strategy' => $strategyKey]);

            return ['error' => 'Nieznana strategia analizy'];
        }

        $strategy = $this->strategies[$strategyKey];
        $result = $strategy->execute($cleanNumbers);
        $value = $result['value'];

        $this->logger->info('Analiza zakończona pomyślnie.', [
            'strategy' => $strategyKey,
            'value' => $value,
            'size' => count($cleanNumbers),
        ]);

        return [
            'strategy' => $strategyKey,
            'value' => $value,
            'size' => count($cleanNumbers),
        ];
    }
}
