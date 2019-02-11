<?php
declare(strict_types=1);

namespace ApplicationTest\Optimizer;

use Application\Component\Finder\Finder;
use Application\Exception\RuntimeException;
use Application\Optimizer\Optimizer;
use Application\System\GifSicle;
use Application\System\JpegOptim;
use Application\System\JpegTran;
use Application\System\PngCrush;
use Application\System\PngOut;
use Application\System\Tinify;
use Symfony\Component\Filesystem\Filesystem;

class OptimizerTest extends AbstractTestCase
{
    protected static $config
        = [
            'system'     => [
                GifSicle::class  => [
                    'active' => true,
                ],
                JpegOptim::class => [
                    'active' => true,
                ],
                JpegTran::class  => [
                    'active' => true,
                ],
                PngCrush::class  => [
                    'active' => true,
                ],
                PngOut::class    => [
                    'active' => true,
                ],
                Tinify::class    => [
                    'active'  => true,
                    'api_key' => null,
                ],
            ],
        ];

    protected $optimizer;

    protected function setUp(): void
    {
        $this->optimizer = new Optimizer(['config' => self::$config]);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->optimizer);

        parent::tearDown();
    }

    private function getTestAssetWorkingPath(): string
    {
        $finder     = new Finder(['config' => self::$config]);
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
        $finder     = new Finder(['config' => self::$config]);
        $filesystem = new Filesystem();

        $workingPath = $this->getTestAssetWorkingPath();
        $finder      = $finder->in($workingPath);

        array_map(function ($filename) use ($workingPath) {
            $mode     = fileperms($filename);
            $filesize = filesize($filename);
            $actual   = $this->optimizer->optimize($filename);
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
        $this->optimizer->optimize('invalid.abc');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testOptimizeImageExceptionIsThrownOnUnsupportedFileType(): void
    {
        $filesystem = new Filesystem();
        $filename   = sys_get_temp_dir() . '/invalid.abc';
        $filesystem->touch($filename);
        $this->optimizer->optimize($filename);
        $filesystem->remove($filename);
    }
}
