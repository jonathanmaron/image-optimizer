<?php
declare(strict_types=1);

namespace Application\Component\Console\Command;

use Application\Component\Config\Loader\FileLoader\Loader;
use Interop\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;

class CommandFactory
{
    private const CONFIG_FILE = 'application.yaml';

    public function __invoke(
        ?ContainerInterface $container = null,
        ?string $requestedName = null,
        ?array $options = null
    ): Command {

        $paths = [
            realpath(APPLICATION_ROOT . '/config'),
        ];

        $locator = new FileLocator($paths);
        $loader  = new Loader($locator);

        $filename = $locator->locate(self::CONFIG_FILE, null, true);
        $config   = $loader->load($filename);

        $command = new $requestedName;
        $command->setConfig($config);

        return $command;
    }
}
