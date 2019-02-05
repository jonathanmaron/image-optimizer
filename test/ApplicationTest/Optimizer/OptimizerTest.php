<?php
declare(strict_types=1);

namespace ApplicationTest\Optimizer;

use Application\Component\Finder\Finder;
use Application\Exception\RuntimeException;
use Application\Optimizer\Optimizer;
use Symfony\Component\Filesystem\Filesystem;

class OptimizerTest extends AbstractTestCase
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

    private function getTestAssetWorkingPath(): string
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

        $originPath = $this->getTestAssetPath();
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

        $workingPath = $this->getTestAssetWorkingPath();
        $finder      = $finder->in($workingPath);

        array_map(function ($filename) use ($workingPath) {
            $mode     = fileperms($filename);
            $filesize = filesize($filename);
            $actual   = $this->optimizer->optimizeImage($filename);
            $this->assertTrue($actual);
            clearStatCache();
            $this->assertEquals($mode, fileperms($filename));
            $this->assertLessThanOrEqual($filesize, filesize($filename));
        }, $finder->getFilenames());

        $filesystem->remove($workingPath);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testOptimizeImageExceptionIsThrownOnNonExistentFilename(): void
    {
        $this->optimizer->optimizeImage('invalid.abc');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testOptimizeImageExceptionIsThrownOnUnsupportedFileType(): void
    {
        $filesystem = new Filesystem();
        $filename   = sys_get_temp_dir() . '/invalid.abc';
        $filesystem->touch($filename);
        $this->optimizer->optimizeImage($filename);
        $filesystem->remove($filename);
    }
}