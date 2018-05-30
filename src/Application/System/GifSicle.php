<?php
declare(strict_types=1);

namespace Application\System;

class GifSicle extends AbstractSystem implements InterfaceSystem
{
    const EXEC = '/usr/bin/gifsicle';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $tempFilename = $this->getTempFilename();

        $command = [
            self::EXEC,
            $filename,
            '--optimize=3',
            '--verbose',
            '--output',
            $tempFilename,
        ];

        $ret1 = $this->execute($command);
        $ret2 = $this->rename($tempFilename, $filename);

        return ($ret1 && $ret2);
    }
}
