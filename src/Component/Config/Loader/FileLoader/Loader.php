<?php
declare(strict_types=1);

namespace Application\Component\Config\Loader\FileLoader;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class Loader extends FileLoader
{
    public function load(mixed $resource, string $type = null): array
    {
        assert(is_string($resource));

        $buffer = file_get_contents($resource);
        assert(is_string($buffer));

        $array = Yaml::parse($buffer);
        assert(is_array($array));

        return $array;
    }

    public function supports($resource, string $type = null): bool
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
