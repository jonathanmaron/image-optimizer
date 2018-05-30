<?php
declare(strict_types=1);

namespace Application\System;

class PngOut extends AbstractSystem implements InterfaceSystem
{
    private const EXEC = '/usr/bin/pngout';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $command = [
            self::EXEC,
            $filename,
            '-s0',
            '-v',
        ];

        $ret = $this->execute($command);

        return $ret;
    }
}
