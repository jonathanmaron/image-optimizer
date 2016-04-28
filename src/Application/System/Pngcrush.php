<?php

namespace Application\System;

class Pngcrush extends AbstractSystem implements InterfaceSystem
{
    public function checkDependency()
    {
        return $this->checkDependencyHelper('pngcrush', '/usr/bin/env pngcrush > /dev/null 2>&1', self::DEPENDENCY_CHECK_METHOD_RETURN_VALUE);
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