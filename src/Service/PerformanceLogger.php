<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class PerformanceLogger
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function measure(callable $callback, string $context): mixed
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        try {
            $result = $callback();
        } finally {
            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);

            $duration = round(($endTime - $startTime) * 1000, 2);
            $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2);

            $this->logger->info('⏱️ Benchmark', [
                'context' => $context,
                'duration_ms' => $duration,
                'memory_mb' => $memoryUsed,
            ]);
        }

        return $result;
    }
}
