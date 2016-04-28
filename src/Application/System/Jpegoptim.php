<?php

namespace Application\System;

class Jpegoptim extends AbstractSystem implements InterfaceSystem
{
    public function checkDependency()
    {
        $command     = 'jpegoptim';
        $exec        = '/usr/bin/jpegoptim';
        $checkMethod = self::DEPENDENCY_CHECK_METHOD_EXECUTABLE;

        return $this->checkDependencyHelper($command, $exec, $checkMethod);
    }

    public function optimize($filename)
    {
        $format = '/usr/bin/env jpegoptim --strip-all --all-progressive %s > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($filename));
        exec($exec);
    }
}