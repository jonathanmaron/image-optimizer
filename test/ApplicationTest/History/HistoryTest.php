<?php
declare(strict_types=1);

namespace ApplicationTest\History;

use Application\History\History;

class HistoryTest extends AbstractTestCase
{
    protected History $history;

    protected function setUp(): void
    {
        $this->history = new History();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->history);

        parent::tearDown();
    }

    public function testGetBasePath(): void
    {
        $expected1 = '/home';
        $expected2 = '.image_optimizer_3.0';
        $actual    = $this->history->getBasePath();

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testGetEntityFilename(): void
    {
        $filename = $this->getTestAssetFilename();

        $expected1 = '.image_optimizer_3.0';
        $expected2 = '/48/d1/04/48d10460971376c6fe3d5bbf5d3af2443aff9aade89d2f036eb1a879eea2d15e.serialized';
        $actual    = $this->history->getEntityFilename($filename);

        self::assertStringContainsString($expected1, $actual);
        self::assertStringContainsString($expected2, $actual);
    }

    public function testImageStatus(): void
    {
        $filename = $this->getTestAssetFilename();

        $actual = $this->history->isOptimized($filename);
        self::assertFalse($actual);

        $actual = $this->history->setAsOptimized($filename);
        self::assertTrue($actual);

        $actual = $this->history->isOptimized($filename);
        self::assertTrue($actual);

        $actual = $this->history->setAsUnoptimized($filename);
        self::assertTrue($actual);

        $actual = $this->history->isOptimized($filename);
        self::assertFalse($actual);
    }
}
