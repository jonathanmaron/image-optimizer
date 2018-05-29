<?php
declare(strict_types=1);

namespace Application\Optimizer;

use Application\Exception\RuntimeException;
use Application\System\GifSicle;
use Application\System\JpegOptim;
use Application\System\JpegTran;
use Application\System\PngCrush;
use Application\System\PngOut;

class Optimizer
{
    public function optimizeImage(string $filename): bool
    {
        $mode      = fileperms($filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        switch ($extension) {
            case 'png':
                $optimizer = new PngOut();
                $optimizer->optimize($filename);
                $optimizer = new PngCrush();
                $optimizer->optimize($filename);
                break;
            case 'jpg':
            case 'jpeg':
                $optimizer = new JpegTran();
                $optimizer->optimize($filename);
                $optimizer = new JpegOptim();
                $optimizer->optimize($filename);
                break;
            case 'gif':
                $optimizer = new GifSicle();
                $optimizer->optimize($filename);
                break;
            default:
                throw new RuntimeException("Unknown image file type - {$filename}");
                break;
        }
        chmod($filename, $mode);

        return true;
    }
}
