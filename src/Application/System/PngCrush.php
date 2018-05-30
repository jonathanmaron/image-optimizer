<?php
declare(strict_types=1);

namespace Application\System;

class PngCrush extends AbstractSystem implements InterfaceSystem
{
    private const EXEC = '/usr/bin/pngcrush';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $tempFilename = $this->getTempFilename();

        $command = [
            self::EXEC,
            '-rem',
            'alla',
            '-brute',
            '-reduce',
            '-v',
            $filename,
            $tempFilename,
        ];

        $ret1 = $this->execute($command);
        $ret2 = $this->rename($tempFilename, $filename);

        return ($ret1 && $ret2);
    }
}
