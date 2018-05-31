<?php
declare(strict_types=1);

namespace Application\System;

class GifSicle extends AbstractSystem implements InterfaceSystem
{
    /**
     * Full path to installed executable 'gifsicle'
     *
     * @var string
     */
    const EXEC = '/usr/bin/gifsicle';

    /**
     * GifSicle constructor
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
