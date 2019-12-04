<?php
declare(strict_types=1);

namespace Application\Component\Console;

use Application\Component\Console\Command\ImageOptimizerCommand\Factory;
use Application\Component\Console\Command\ImageOptimizerCommand\Command;
use Symfony\Component\Console\Application as ParentApplication;

class Application extends ParentApplication
{
    protected function getDefaultCommands(): array
    {
        $ret = parent::getDefaultCommands();

        $commands = [
            Command::class,
        ];

        $container = null;
        $options   = [];

        foreach ($commands as $requestedName) {
            $instance = new Factory();
            $ret[]    = $instance($container, $requestedName, $options);
        }

        return $ret;
    }
}
