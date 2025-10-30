<?php

namespace App\Tests\Service\Strategy;

use App\Service\Strategy\UniquenessCountStrategy;
use PHPUnit\Framework\TestCase;

class UniquenessCountStrategyTest extends TestCase
{
    public function testCountsUniqueValuesCorrectly(): void
    {
        $strategy = new UniquenessCountStrategy();
        $numbers = [1, 2, 1, 4, 2];

        $result = $strategy->execute($numbers);

        $this->assertEquals(3, $result['value']);
    }

    public function testAllUniqueValues(): void
    {
        $strategy = new UniquenessCountStrategy();
        $numbers = [1, 2, 3, 4, 5];

        $result = $strategy->execute($numbers);

        $this->assertEquals(5, $result['value']);
    }

    public function testSingleElement(): void
    {
        $strategy = new UniquenessCountStrategy();
        $numbers = [10];

        $result = $strategy->execute($numbers);

        $this->assertEquals(1, $result['value']);
    }
}
