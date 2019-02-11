<?php
declare(strict_types=1);

namespace ApplicationTest\Component\Config\Loader\FileLoader;

use Application\Component\Config\Loader\FileLoader\Loader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;

class LoaderTest extends TestCase
{
    private $loader;

    protected function setUp(): void
    {
        $paths = [
            APPLICATION_ROOT . '/config',
        ];

        $locator      = new FileLocator($paths);
        $this->loader = new Loader($locator);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->loader);

        parent::tearDown();
    }

    function testSupports(): void
    {
        $actual = $this->loader->supports('application.xxx');
        $this->assertFalse($actual);

        $actual = $this->loader->supports('application.yaml.xxx');
        $this->assertFalse($actual);

        $actual = $this->loader->supports('application.yaml.dist.xxx');
        $this->assertFalse($actual);

        $actual = $this->loader->supports('application.yaml');
        $this->assertTrue($actual);

        $actual = $this->loader->supports('application.yaml.dist');
        $this->assertTrue($actual);
    }
}
