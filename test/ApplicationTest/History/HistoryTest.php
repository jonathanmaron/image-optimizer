<?php
declare(strict_types=1);

namespace ApplicationTest\History;

use Application\History\History;

class HistoryTest extends AbstractTestCase
{
    protected $history;

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

    public function testGetHash(): void
    {
        $filename = $this->getTestAssetFilename();

        $expected = '48d10460971376c6fe3d5bbf5d3af2443aff9aade89d2f036eb1a879eea2d15e';
        $actual   = $this->history->getHash($filename);

        $this->assertEquals($expected, $actual);
    }

    public function testGetHashFile(): void
    {
        $filename = $this->getTestAssetFilename();

        $expected = '0a645e6aff6887af8a2bdeb877e5304a4815340e743cb2d52a134acdd57a8c57';
        $actual   = $this->history->getHashFile($filename);

        $this->assertEquals($expected, $actual);
    }

    public function testGetHashFilename(): void
    {
        $filename = $this->getTestAssetFilename();
        $hash     = $this->history->getHash($filename);

        $expected = '.image_optimizer/48/d1/04/48d10460971376c6fe3d5bbf5d3af2443aff9aade89d2f036eb1a879eea2d15e';
        $actual   = $this->history->getHashFilename($hash);

        $this->assertContains($expected, $actual);
    }

    public function testGetBasePath()
    {
        $expected1 = '/home';
        $expected2 = '/.image_optimizer';
        $actual    = $this->history->getBasePath();

        $this->assertContains($expected1, $actual);
        $this->assertContains($expected2, $actual);
    }

    public function testImageStatus(): void
    {
        $filename = $this->getTestAssetFilename();

        $actual = $this->history->isOptimizedImage($filename);
        $this->assertFalse($actual);

        $actual = $this->history->setImageAsOptimized($filename);
        $this->assertTrue($actual);

        $actual = $this->history->isOptimizedImage($filename);
        $this->assertTrue($actual);

        $actual = $this->history->setImageAsUnoptimized($filename);
        $this->assertTrue($actual);

        $actual = $this->history->isOptimizedImage($filename);
        $this->assertFalse($actual);
    }
}
