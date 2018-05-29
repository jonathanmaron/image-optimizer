<?php
declare(strict_types=1);

namespace Application\System;

class PngCrush extends AbstractSystem implements InterfaceSystem
{
    const EXEC = '/usr/bin/pngcrush';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $tempFilename = $this->getTempFilename();

        $format = '%s -rem alla -brute -reduce %s %s > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellcmd(self::EXEC), escapeshellarg($filename), escapeshellarg($tempFilename));
        exec($exec);

        rename($tempFilename, $filename);

        return true;
    }
}
