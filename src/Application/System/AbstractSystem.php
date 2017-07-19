<?php

namespace Application\System;

use Symfony\Component\Console\Exception\RuntimeException;

abstract class AbstractSystem
{
    public function isInstalled($exec)
    {
        if (!is_executable($exec)) {
            $message = sprintf("The required command '%s' is not installed.", $exec);
            throw new RuntimeException($message);
        }

        return true;
    }

    public function getTempFilename()
    {
        $path = sys_get_temp_dir();

        return tempnam($path, 'image_optimizer_');
    }
}