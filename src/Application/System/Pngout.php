<?php

namespace Application\System;

class Pngout extends AbstractSystem implements InterfaceSystem
{
    public function checkDependency()
    {
        return $this->checkDependencyHelper('pngout', '/usr/bin/env pngout > /dev/null 2>&1', self::DEPENDENCY_CHECK_METHOD_RETURN_VALUE);
    }

    public function optimize($filename)
    {
        $format = '/usr/bin/env pngout %s -s0 > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($filename));
        exec($exec);
    }
}