<?php
declare(strict_types=1);

namespace Application\System;

class JpegOptim extends AbstractSystem implements InterfaceSystem
{
    const EXEC = '/usr/bin/jpegoptim';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $format = '%s --strip-all --all-progressive %s > /dev/null 2>&1';
        $exec   = sprintf($format
            , escapeshellcmd(self::EXEC)
            , escapeshellarg($filename)
        );
        exec($exec);

        return true;
    }
}
