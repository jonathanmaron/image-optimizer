<?php
declare(strict_types=1);

namespace Application\System;

class PngOut extends AbstractSystem implements InterfaceSystem
{
    /**
     * Full path to installed executable 'pngout'
     *
     * @var string
     */
    protected const EXEC = '/usr/bin/pngout';

    /**
     * PngOut constructor
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
            $filename,
            '-s0',
            '-v',
        ];


        dump(__METHOD__);

        $ret = $this->execute($command);

        dump(__METHOD__);

        return $ret;
    }
}
