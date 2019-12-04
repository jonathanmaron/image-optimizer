<?php
declare(strict_types=1);

namespace Application\Component\Console\Command\ImageOptimizerCommand;

use Application\Component\Config\Loader\FileLoader\Loader;
use Application\Component\Finder\Finder;
use Application\History\History;
use Application\Optimizer\Optimizer;
use Application\Statistics\Statistics;
use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use NumberFormatter;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Command\Command;

class Factory
{
    protected const CONFIG_FILES
        = [
            'application.yaml.dist',
            'application.yaml',
        ];

    public function __invoke(
        ?ContainerInterface $container = null,
        ?string $requestedName = null,
        ?array $options = null
    ): Command {

        $locale = locale_get_default();
        $config = $this->getConfig();

        $finder          = new Finder();
        $history         = new History();
        $numberFormatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $optimizer       = new Optimizer(['config' => $config]);
        $statistics      = new Statistics();

        $command = new $requestedName;
        $command->setConfig($config);
        $command->setFinder($finder);
        $command->setHistory($history);
        $command->setNumberFormatter($numberFormatter);
        $command->setOptimizer($optimizer);
        $command->setStatistics($statistics);

        return $command;
    }

    protected function getConfig(): array
    {
        $ret = [];

        $paths = [
            APPLICATION_ROOT . '/config',
        ];

        $locator = new FileLocator($paths);
        $loader  = new Loader($locator);

        foreach (self::CONFIG_FILES as $name) {
            $config = [];
            try {
                $filename = $locator->locate($name, null, true);
                $config   = $loader->load($filename);
            } catch (InvalidArgumentException | FileLocatorFileNotFoundException $e) {
            }
            if (count($config) > 0) {
                $ret = array_merge($ret, $config);
            }
        }

        return $ret;
    }
}
