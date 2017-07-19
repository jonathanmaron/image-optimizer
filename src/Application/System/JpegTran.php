<?php

namespace Application\System;

class JpegTran extends AbstractSystem implements InterfaceSystem
{
    const EXEC = '/usr/bin/jpegtran';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize($filename)
    {
        $tempFilename = $this->getTempFilename();

        $format = '%s -optimize -progressive -outfile %s %s > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellcmd(self::EXEC), escapeshellarg($tempFilename), escapeshellarg($filename));
        exec($exec);

        rename($tempFilename, $filename);
    }
}