<?php
declare(strict_types=1);

namespace ApplicationTest;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function getTestAssetPath(): string
    {
        return dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'asset';
    }

    protected function getTestAssetFilename(): string
    {
        return $this->getTestAssetPath() . DIRECTORY_SEPARATOR . 'wave.jpg';
    }
}
