<?php

namespace App\Tests\Service;

use App\Service\ArrayAnalyzerService;
use App\Service\Strategy\ArrayOperationStrategyInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ArrayAnalyzerServiceTest extends TestCase
{
    private LoggerInterface $loggerMock;
    private array $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->config = [
            'max_size' => 100_000,
            'max_unique_range' => 100_000,
            'max_completeness_range' => 1_000_000,
            'max_completeness_count' => 10_000,
        ];
    }

    public function testAnalyzeWithValidUniquenessData(): void
    {
        $strategyMock = $this->createMock(ArrayOperationStrategyInterface::class);
        $strategyMock->method('execute')->willReturn(['value' => 3]);
        $strategyMock->method('getKey')->willReturn('uniqueness');

        $service = new ArrayAnalyzerService([$strategyMock], [], $this->loggerMock, $this->config);
        $numbers = [1, 2, 3, 1, 2];

        $result = $service->analyze($numbers, 'uniqueness');

        $this->assertSame('uniqueness', $result['strategy']);
        $this->assertSame(3, $result['value']);
        $this->assertSame(5, $result['size']);
    }

    public function testAnalyzeWithUnknownStrategy(): void
    {
        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with('Nieznana strategia analizy.', ['strategy' => 'unknown']);

        $service = new ArrayAnalyzerService([], [], $this->loggerMock, $this->config);
        $numbers = [1, 2, 3];

        $result = $service->analyze($numbers, 'unknown');

        $this->assertArrayHasKey('error', $result);
        $this->assertSame('Nieznana strategia analizy', $result['error']);
    }

    public function testAnalyzeWithEmptyArray(): void
    {
        $this->loggerMock
            ->expects($this->once())
            ->method('warning')
            ->with('Pusta lub niepoprawna tablica do analizy.', ['numbers' => []]);

        $service = new ArrayAnalyzerService([], [], $this->loggerMock, $this->config);
        $result = $service->analyze([], 'uniqueness');

        $this->assertArrayHasKey('error', $result);
        $this->assertSame('Tablica nie może być pusta lub zawiera niepoprawne wartości', $result['error']);
    }

    public function testPrepareArrayForCompleteness(): void
    {
        $service = new ArrayAnalyzerService([], [], $this->loggerMock, $this->config);
        $data = ['strategy' => 'completeness', 'arraySize' => 5];

        $result = $service->prepareArray($data);

        $this->assertCount(5, $result);
        foreach ($result as $num) {
            $this->assertGreaterThanOrEqual(1, $num);
            $this->assertLessThanOrEqual($this->config['max_completeness_range'], $num);
        }
    }

    public function testPrepareArrayForUniqueness(): void
    {
        $service = new ArrayAnalyzerService([], [], $this->loggerMock, $this->config);
        $data = ['strategy' => 'uniqueness', 'arraySize' => 5];

        $result = $service->prepareArray($data);

        $this->assertCount(5, $result);
        foreach ($result as $num) {
            $this->assertGreaterThanOrEqual(-$this->config['max_unique_range'], $num);
            $this->assertLessThanOrEqual($this->config['max_unique_range'], $num);
        }
    }
}
