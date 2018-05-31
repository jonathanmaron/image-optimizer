<?php
declare(strict_types=1);

namespace Application\System;

class JpegOptim extends AbstractSystem implements InterfaceSystem
{
    /**
     * Full path to installed executable 'jpegoptim'
     *
     * @var string
     */
    private const EXEC = '/usr/bin/jpegoptim';

    /**
     * JpegOptim constructor
     *
     * Check the executable is installed
     */
    public function __construct()
    {
        $this->isInstalled(self::EXEC);
    }

    /**
     * Pass the image filename to installed executable and optimize image
     *
     * @param string $filename
     *
     * @return bool
     */
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
