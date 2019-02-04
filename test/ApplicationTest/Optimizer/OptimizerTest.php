<?php
declare(strict_types=1);

namespace ApplicationTest\Optimizer;

use Application\Component\Finder\Finder;
use Application\Optimizer\Optimizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class OptimizerTest extends TestCase
{
    protected $optimizer;

    protected function setUp(): void
    {
        $this->optimizer = new Optimizer();

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->optimizer);

        parent::tearDown();
    }

    private function getTestPath(): string
    {
        $finder     = new Finder();
        $filesystem = new Filesystem();

        $rand       = (string) random_int(PHP_INT_MIN, PHP_INT_MAX);
        $targetDir  = 'phpunit_' . hash('sha256', $rand);
        $targetPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $targetDir;

        if ($filesystem->exists($targetPath)) {
            $filesystem->remove($targetPath);
        }

        $filesystem->mkdir($targetPath);

        $originPath = dirname(__FILE__, 3) . '/asset';
        $finder     = $finder->in($originPath);

        array_map(function ($originFile) use ($filesystem, $targetPath) {
            $targetFile = $targetPath . DIRECTORY_SEPARATOR . basename($originFile);
            $filesystem->copy($originFile, $targetFile);
        }, $finder->getFilenames());

        return $targetPath;
    }

    public function testOptimizeImage(): void
    {
        $finder     = new Finder();
        $filesystem = new Filesystem();

        $path   = $this->getTestPath();
        $finder = $finder->in($path);

        array_map(function ($filename) use ($path) {
            $mode     = fileperms($filename);
            $filesize = filesize($filename);
            $actual   = $this->optimizer->optimizeImage($filename);
            $this->assertTrue($actual);
            clearStatCache();
            $this->assertEquals($mode, fileperms($filename));
            $this->assertLessThanOrEqual($filesize, filesize($filename));
        }, $finder->getFilenames());
        $filesystem->remove($path);
    }
}
