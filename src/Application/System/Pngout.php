<?php

namespace Application\System;

class Pngout extends AbstractSystem implements InterfaceSystem
{
    public function checkDependency()
    {
        $command     = 'pngout';
        $exec        = '/usr/bin/env pngout > /dev/null 2>&1';
        $checkMethod = self::DEPENDENCY_CHECK_METHOD_RETURN_VALUE;

        return $this->checkDependencyHelper($command, $exec, $checkMethod);
    }

    public function optimize($filename)
    {
        $format = '/usr/bin/env pngout %s -s0 > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($filename));
        exec($exec);
    }
}