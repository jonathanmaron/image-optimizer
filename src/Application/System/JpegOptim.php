<?php
declare(strict_types=1);

namespace Application\System;

class JpegOptim extends AbstractSystem implements InterfaceSystem
{
    private const EXEC = '/usr/bin/jpegoptim';

    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    public function optimize(string $filename): bool
    {
        $command = [
            self::EXEC,
            '--all-progressive',
            '--strip-all',
            '--totals',
            '--verbose',
            $filename,
        ];

        $ret = $this->execute($command);

        return $ret;
    }
}
