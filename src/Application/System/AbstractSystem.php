<?php
declare(strict_types=1);

namespace Application\System;

use Application\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractSystem
{
    public function isInstalled(string $exec): bool
    {
        if (!is_executable($exec)) {
            $format  = 'The required command "%s" is not installed.';
            $message = sprintf($format, $exec);
            throw new RuntimeException($message);
        }

        return true;
    }

    public function getTempFilename(): string
    {
        $filesystem = new Filesystem();

        $path   = sys_get_temp_dir();
        $prefix = 'image_optimizer_';

        $ret = $filesystem->tempnam($path, $prefix);

        return $ret;
    }
}
