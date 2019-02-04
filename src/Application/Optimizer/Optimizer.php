<?php
declare(strict_types=1);

namespace Application\Optimizer;

use Application\Exception\RuntimeException;
use Application\System\GifSicle;
use Application\System\JpegOptim;
use Application\System\JpegTran;
use Application\System\PngCrush;
use Application\System\PngOut;
use Symfony\Component\Filesystem\Filesystem;

class Optimizer
{
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
     * Using the installed executables, optimize the passed image file
     *
     * @param string $filename
     *
     * @return bool
     */
    public function optimizeImage(string $filename): bool
    {
        $filesystem = new Filesystem();

        $mode      = fileperms($filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        switch ($extension) {
            case self::EXTENSION_PNG:
                $optimizer = new PngOut();
                $optimizer->optimize($filename);
                $optimizer = new PngCrush();
                $optimizer->optimize($filename);
                break;
            case self::EXTENSION_JPG:
            case self::EXTENSION_JPEG:
                $optimizer = new JpegTran();
                $optimizer->optimize($filename);
                $optimizer = new JpegOptim();
                $optimizer->optimize($filename);
                break;
            case self::EXTENSION_GIF:
                $optimizer = new GifSicle();
                $optimizer->optimize($filename);
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
}
