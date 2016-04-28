<?php

namespace Application\System;

class Jpegoptim extends AbstractSystem implements InterfaceSystem
{
    public function checkDependency()
    {
        return $this->checkDependencyHelper('jpegoptim', '/usr/bin/jpegoptim', self::DEPENDENCY_CHECK_METHOD_EXECUTABLE);
    }

    public function optimize($filename)
    {
        $format = '/usr/bin/env jpegoptim --strip-all --all-progressive %s > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($filename));
        exec($exec);
    }
}