<?php
declare(strict_types=1);

namespace Application\System;

interface InterfaceSystem
{
    /**
     * Return true, if executable is installed. False otherwise.
     *
     * @param string $exec
     *
     * @return bool
     */
    public function isInstalled(string $exec): bool;

    /**
     * Pass the image filename to installed executable and optimize image
     *
     * @param string $filename
     *
     * @return bool
     */
    public function optimize(string $filename): bool;
}
