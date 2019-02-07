<?php
declare(strict_types=1);

namespace ApplicationTest\Statistics;

use Application\Statistics\Statistics;

class StatisticsTest extends AbstractTestCase
{
    protected $statistics;

    protected function setUp()
    {
        $this->statistics = new Statistics();

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->statistics);

        parent::tearDown();
    }

    public function testIncrementSkipped(): void
    {
        $expected = 0;
        $actual   = $this->statistics->getSkipped();
        $this->assertEquals($expected, $actual);

        $expected = 1;
        $this->statistics->incrementSkipped();
        $actual = $this->statistics->getSkipped();
        $this->assertEquals($expected, $actual);

        $expected = 2;
        $this->statistics->incrementSkipped();
        $actual = $this->statistics->getSkipped();
        $this->assertEquals($expected, $actual);

        $expected = 3;
        $this->statistics->incrementSkipped();
        $actual = $this->statistics->getSkipped();
        $this->assertEquals($expected, $actual);

        $expected = 4;
        $this->statistics->incrementSkipped();
        $actual = $this->statistics->getSkipped();
        $this->assertEquals($expected, $actual);

        $expected = 5;
        $this->statistics->incrementSkipped();
        $actual = $this->statistics->getSkipped();
        $this->assertEquals($expected, $actual);
    }

    public function testIncrementOptimized(): void
    {
        $expected = 0;
        $actual   = $this->statistics->getOptimized();
        $this->assertEquals($expected, $actual);

        $expected = 1;
        $this->statistics->incrementOptimized();
        $actual = $this->statistics->getOptimized();
        $this->assertEquals($expected, $actual);

        $expected = 2;
        $this->statistics->incrementOptimized();
        $actual = $this->statistics->getOptimized();
        $this->assertEquals($expected, $actual);

        $expected = 3;
        $this->statistics->incrementOptimized();
        $actual = $this->statistics->getOptimized();
        $this->assertEquals($expected, $actual);

        $expected = 4;
        $this->statistics->incrementOptimized();
        $actual = $this->statistics->getOptimized();
        $this->assertEquals($expected, $actual);

        $expected = 5;
        $this->statistics->incrementOptimized();
        $actual = $this->statistics->getOptimized();
        $this->assertEquals($expected, $actual);
    }

    public function testIncrementIndexed(): void
    {
        $expected = 0;
        $actual   = $this->statistics->getIndexed();
        $this->assertEquals($expected, $actual);

        $expected = 1;
        $this->statistics->incrementIndexed();
        $actual = $this->statistics->getIndexed();
        $this->assertEquals($expected, $actual);

        $expected = 2;
        $this->statistics->incrementIndexed();
        $actual = $this->statistics->getIndexed();
        $this->assertEquals($expected, $actual);

        $expected = 3;
        $this->statistics->incrementIndexed();
        $actual = $this->statistics->getIndexed();
        $this->assertEquals($expected, $actual);

        $expected = 4;
        $this->statistics->incrementIndexed();
        $actual = $this->statistics->getIndexed();
        $this->assertEquals($expected, $actual);

        $expected = 5;
        $this->statistics->incrementIndexed();
        $actual = $this->statistics->getIndexed();
        $this->assertEquals($expected, $actual);
    }

    public function testBytesInOutCalculation(): void
    {
        $this->statistics->setBytesIn(100);
        $this->statistics->setBytesOut(75);

        $expected = 25;
        $actual   = $this->statistics->getBytesDifference();
        $this->assertEquals($expected, $actual);

        $expected = 25.0;
        $actual   = $this->statistics->getBytesDifferenceAsPercentage();
        $this->assertEquals($expected, $actual);

        $this->statistics->resetBytesIn();
        $this->statistics->resetBytesOut();
    }

    public function testTotalBytesInOutCalculation(): void
    {
        for ($x = 0; $x <= 10; $x++) {
            $this->statistics->setBytesIn(100);
            $this->statistics->setBytesOut(75);
        }

        $expected = 275;
        $actual   = $this->statistics->getTotalBytesDifference();
        $this->assertEquals($expected, $actual);

        $expected = 25.0;
        $actual   = $this->statistics->getTotalBytesDifferenceAsPercentage();
        $this->assertEquals($expected, $actual);
    }
}
