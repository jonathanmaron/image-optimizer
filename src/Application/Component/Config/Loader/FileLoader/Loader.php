<?php
declare(strict_types=1);

namespace Application\Component\Config\Loader\FileLoader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class Loader extends FileLoader
{
    public function load($resource, $type = null): array
    {
        $buffer = file_get_contents($resource);

        return (array) Yaml::parse($buffer);
    }

    public function supports($resource, $type = null): bool
    {
        if (!is_string($resource)) {
            return false;
        }

        if (1 === preg_match('/(.*)\.yaml(\.dist)?$/', $resource)) {
            return true;
        }

        return false;
    }
}
