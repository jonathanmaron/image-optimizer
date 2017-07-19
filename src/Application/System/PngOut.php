<?php

namespace Application\System;

class PngOut extends AbstractSystem implements InterfaceSystem
{
    const EXEC = '/usr/bin/pngout';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize($filename)
    {
        $format = '%s %s -s0 > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellcmd(self::EXEC), escapeshellarg($filename));
        exec($exec);
    }
}