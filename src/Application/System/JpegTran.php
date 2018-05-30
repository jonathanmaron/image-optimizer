<?php
declare(strict_types=1);

namespace Application\System;

class JpegTran extends AbstractSystem implements InterfaceSystem
{
    private const EXEC = '/usr/bin/jpegtran';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $tempFilename = $this->getTempFilename();

        $command = [
            self::EXEC,
            '-optimize',
            '-progressive',
            '-verbose',
            '-outfile',
            $tempFilename,
            $filename,
        ];

        $ret1 = $this->execute($command);
        $ret2 = $this->rename($tempFilename, $filename, true);

        return ($ret1 && $ret2);
    }
}
