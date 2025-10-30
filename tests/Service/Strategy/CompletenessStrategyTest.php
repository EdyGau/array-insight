<?php

namespace App\Tests\Service\Strategy;

use App\Service\Strategy\CompletenessStrategy;
use PHPUnit\Framework\TestCase;

class CompletenessStrategyTest extends TestCase
{
    public function testCompleteArrayReturnsTrue(): void
    {
        $strategy = new CompletenessStrategy();
        $numbers = [1, 2, 3, 4, 5];

        $result = $strategy->execute($numbers);

        $this->assertTrue($result['value']);
    }

    public function testIncompleteArrayReturnsFalse(): void
    {
        $strategy = new CompletenessStrategy();
        $numbers = [1, 3, 4];

        $result = $strategy->execute($numbers);

        $this->assertFalse($result['value']);
    }

    public function testDuplicateNumbersReturnFalse(): void
    {
        $strategy = new CompletenessStrategy();
        $numbers = [1, 2, 2, 3];

        $result = $strategy->execute($numbers);

        $this->assertFalse($result['value']);
    }

    public function testNumberOutOfRangeReturnsFalse(): void
    {
        $strategy = new CompletenessStrategy();
        $numbers = [1, 2, 5];

        $result = $strategy->execute($numbers);

        $this->assertFalse($result['value']);
    }
}
