<?php
declare(strict_types=1);

namespace Application\Component\Console\Command;

use Application\Component\Config\Loader\FileLoader\Loader;
use Application\Component\Finder\Finder;
use Application\History\History;
use Application\Optimizer\Optimizer;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;

class CommandFactory
{
    private const CONFIG_FILES
        = [
            'application.yaml.dist',
            'application.yaml',
        ];

    public function __invoke(
        ?ContainerInterface $container = null,
        ?string $requestedName = null,
        ?array $options = null
    ): Command {

        $paths = [
            APPLICATION_ROOT . '/config',
        ];

        $locator = new FileLocator($paths);
        $loader  = new Loader($locator);

        $config = [];
        foreach (self::CONFIG_FILES as $name) {
            $loaded = [];
            try {
                $filename = $locator->locate($name, null, true);
                $loaded   = $loader->load($filename);
            } catch (InvalidArgumentException | FileLocatorFileNotFoundException $e) {
            }
            if (count($loaded) > 0) {
                $config = array_merge($config, $loaded);
            }
        }

        $optimizer = new Optimizer(['config' => $config]);
        $finder    = new Finder();
        $history   = new History();

        $command = new $requestedName;
        $command->setConfig($config);
        $command->setOptimizer($optimizer);
        $command->setFinder($finder);
        $command->setHistory($history);

        return $command;
    }
}
