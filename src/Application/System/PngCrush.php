<?php

namespace Application\System;

class PngCrush extends AbstractSystem implements InterfaceSystem
{
    public function checkDependency()
    {
        $command     = 'pngcrush';
        $exec        = '/usr/bin/env pngcrush > /dev/null 2>&1';
        $checkMethod = self::DEPENDENCY_CHECK_METHOD_RETURN_VALUE;

        return $this->checkDependencyHelper($command, $exec, $checkMethod);
    }

    public function optimize($filename)
    {
        $tempFilename = $this->getTempFilename();
        
        $format = '/usr/bin/env pngcrush -rem alla -brute -reduce %s %s > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($filename), escapeshellarg($tempFilename));
        exec($exec);

        rename($tempFilename, $filename);
    }
}