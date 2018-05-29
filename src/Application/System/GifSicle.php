<?php
declare(strict_types=1);

namespace Application\System;

use Symfony\Component\Filesystem\Filesystem;

class GifSicle extends AbstractSystem implements InterfaceSystem
{
    const EXEC = '/usr/bin/gifsicle';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $filesystem = new Filesystem();

        $tempFilename = $this->getTempFilename();

        $format = '%s -O3 %s -o %s > /dev/null 2>&1';
        $exec   = sprintf($format
            , escapeshellcmd(self::EXEC)
            , escapeshellarg($filename)
            , escapeshellarg($tempFilename));
        exec($exec);

        $filesystem->rename($tempFilename, $filename, true);

        return true;
    }
}
