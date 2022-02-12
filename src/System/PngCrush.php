<?php
declare(strict_types=1);

namespace Application\System;

class PngCrush extends AbstractSystem implements InterfaceSystem
{
    /**
     * Full path to installed executable 'pngcrush'
     *
     * @var string
     */
    protected const EXEC = '/usr/bin/pngcrush';

    /**
     * PngCrush constructor
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

        return $ret1 && $ret2;
    }
}
