<?php

namespace Application\System;

use Symfony\Component\Console\Exception\RuntimeException;

abstract class AbstractSystem
{
    /**
     * Check a dependency by examining its return value
     */
    const DEPENDENCY_CHECK_METHOD_RETURN_VALUE = 1;

    /**
     * Check a dependency by checking whether its exists on the file system and is executable
     */
    const DEPENDENCY_CHECK_METHOD_EXECUTABLE = 2;

    public function __construct()
    {
        return $this->checkDependency();
    }

    public function checkDependencyHelper($command, $exec, $checkMethod)
    {
        $pass = false;

        switch ($checkMethod) {
            
            case self::DEPENDENCY_CHECK_METHOD_RETURN_VALUE:
                exec($exec, $output, $returnVar);
                if (1 == $returnVar) {
                    $pass = true;
                }
            break;
            
            case self::DEPENDENCY_CHECK_METHOD_EXECUTABLE:
                if (is_executable($exec)) {
                    $pass = true;
                }
            break;

            default:
                throw new RuntimeException(
                    "Invalid dependency check method."
                );
            break;

        }

        if (false === $pass) {
            throw new RuntimeException(
                "The required command '{$command}' is not installed."
            );
        }

        return true;
    }

    public function getTempFilename()
    {
        $path = sys_get_temp_dir();

        return tempnam($path, 'image_optimizer_');
    }
}