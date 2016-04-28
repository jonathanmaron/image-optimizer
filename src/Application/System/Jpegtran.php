<?php

namespace Application\System;

class Jpegtran extends AbstractSystem implements InterfaceSystem
{
    public function checkDependency()
    {
        $command     = 'jpegtran';
        $exec        = '/usr/bin/env jpegtran --version > /dev/null 2>&1';
        $checkMethod = self::DEPENDENCY_CHECK_METHOD_RETURN_VALUE;

        return $this->checkDependencyHelper($command, $exec, $checkMethod);
    }

    public function optimize($filename)
    {
        $tempFilename = $this->getTempFilename();

        $format = '/usr/bin/env jpegtran -optimize -progressive -outfile %s %s > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($tempFilename), escapeshellarg($filename));
        exec($exec);

        rename($tempFilename, $filename);
    }
}