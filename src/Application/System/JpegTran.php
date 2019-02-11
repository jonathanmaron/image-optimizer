<?php
declare(strict_types=1);

namespace Application\System;

class JpegTran extends AbstractSystem implements InterfaceSystem
{
    /**
     * Full path to installed executable 'jpegtran'
     *
     * @var string
     */
    protected const EXEC = '/usr/bin/jpegtran';

    /**
     * JpegTran constructor
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
