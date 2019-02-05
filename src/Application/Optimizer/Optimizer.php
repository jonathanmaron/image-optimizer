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
use Application\Utility\ConfigTrait;
use Symfony\Component\Filesystem\Filesystem;

class Optimizer
{
    use ConfigTrait;

    /**
     * PNG image filename extension
     *
     * @var string
     */
    private const EXTENSION_PNG = 'png';

    /**
     * JPG image filename extension
     *
     * @var string
     */
    private const EXTENSION_JPG = 'jpg';

    /**
     * JPEG image filename extension
     *
     * @var string
     */
    private const EXTENSION_JPEG = 'jpeg';

    /**
     * GIF image filename extension
     *
     * @var string
     */
    private const EXTENSION_GIF = 'gif';

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
            $format  = '"%s" does not exist';
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
                $format  = 'Unsupported image file type "%s"';
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
    private function optimizePng(string $filename, Filesystem $filesystem): bool
    {
        $optimizer = new PngOut();
        $optimizer->optimize($filename);

        $optimizer = new PngCrush();
        $optimizer->optimize($filename);

        $config = $this->getConfig();
        $apiKey = $config['credentials']['tinify']['api_key'] ?? null;
        if (is_string($apiKey)) {
            $optimizer = new Tinify(['api_key' => $apiKey]);
            $optimizer->optimize($filename);
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
    private function optimizeJpg(string $filename, Filesystem $filesystem): bool
    {
        $optimizer = new JpegTran();
        $optimizer->optimize($filename);

        $optimizer = new JpegOptim();
        $optimizer->optimize($filename);

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
    private function optimizeGif(string $filename, Filesystem $filesystem): bool
    {
        $optimizer = new GifSicle();
        $optimizer->optimize($filename);

        return $filesystem->exists($filename);
    }
}
