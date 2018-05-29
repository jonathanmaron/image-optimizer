<?php
declare(strict_types=1);

namespace Application\System;

use Symfony\Component\Filesystem\Filesystem;

class JpegTran extends AbstractSystem implements InterfaceSystem
{
    const EXEC = '/usr/bin/jpegtran';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $filesystem = new Filesystem();

        $tempFilename = $this->getTempFilename();

        $format = '%s -optimize -progressive -outfile %s %s > /dev/null 2>&1';
        $exec   = sprintf($format
            , escapeshellcmd(self::EXEC)
            , escapeshellarg($tempFilename)
            , escapeshellarg($filename)
        );
        exec($exec);

        $filesystem->rename($tempFilename, $filename, true);

        return true;
    }
}
