<?php
declare(strict_types=1);

namespace Application\Optimizer;

use Application\Exception\InvalidArgumentException;
use Application\Exception\RuntimeException;
use Application\System\GifSicle;
use Application\System\JpegOptim;
use Application\System\JpegTran;
use Application\System\PngCrush;
use Application\System\PngOut;
use Application\System\Tinify;
use Symfony\Component\Filesystem\Filesystem;

class Optimizer extends AbstractOptimizer
{
    /**
     * Optimizer constructor
     *
     * @param $options
     */
    public function __construct(array $options)
    {
        if (!array_key_exists('config', $options)) {
            $format  = "Missing 'config' key in 'options' array at '%s'";
            $message = sprintf($format, __METHOD__);
            throw new InvalidArgumentException($message);
        }

        $this->setConfig($options['config']);
    }

    /**
     * Using the installed executables, optimize the passed image file
     *
     * @param string $filename
     *
     * @return bool
     */
    public function optimize(string $filename): bool
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists($filename)) {
            $format  = "'%s' does not exist";
            $message = sprintf($format, $filename);
            throw new RuntimeException($message);
        }

        $mode      = fileperms($filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        switch ($extension) {
            case self::EXTENSION_PNG:
                $this->optimizePng($filename, $filesystem);
                break;
            case self::EXTENSION_JPG:
            case self::EXTENSION_JPEG:
                $this->optimizeJpg($filename, $filesystem);
                break;
            case self::EXTENSION_GIF:
                $this->optimizeGif($filename, $filesystem);
                break;
            default:
                $format  = "Unsupported image file type '%s'";
                $message = sprintf($format, $filename);
                throw new RuntimeException($message);
                break;
        }

        $filesystem->chmod($filename, $mode);

        return $filesystem->exists($filename);
    }

    /**
     * Using the installed executables, optimize the passed PNG file
     *
     * @param string     $filename
     * @param Filesystem $filesystem
     *
     * @return bool
     */
    protected function optimizePng(string $filename, Filesystem $filesystem): bool
    {
        $classNames = [
            PngOut::class,
            PngCrush::class,
        ];

        foreach ($classNames as $className) {
            if ($this->isActive($className)) {
                $optimizer = new $className();
                $optimizer->optimize($filename);
            }
        }

        $className = Tinify::class;
        if ($this->isActive($className)) {
            $config = $this->getConfig();
            $apiKey = $config['system'][$className]['api_key'] ?? null;
            if (is_string($apiKey)) {
                $optimizer = new Tinify(['api_key' => $apiKey]);
                $optimizer->optimize($filename);
            }
        }

        return $filesystem->exists($filename);
    }

    /**
     * Using the installed executables, optimize the passed JPG or JPEG file
     *
     * @param string     $filename
     * @param Filesystem $filesystem
     *
     * @return bool
     */
    protected function optimizeJpg(string $filename, Filesystem $filesystem): bool
    {
        $classNames = [
            JpegTran::class,
            JpegOptim::class,
        ];

        foreach ($classNames as $className) {
            if ($this->isActive($className)) {
                $optimizer = new $className();
                $optimizer->optimize($filename);
            }
        }

        return $filesystem->exists($filename);
    }

    /**
     * Using the installed executables, optimize the passed GIF file
     *
     * @param string     $filename
     * @param Filesystem $filesystem
     *
     * @return bool
     */
    protected function optimizeGif(string $filename, Filesystem $filesystem): bool
    {
        $classNames = [
            GifSicle::class,
        ];

        foreach ($classNames as $className) {
            if ($this->isActive($className)) {
                $optimizer = new $className();
                $optimizer->optimize($filename);
            }
        }

        return $filesystem->exists($filename);
    }
}
