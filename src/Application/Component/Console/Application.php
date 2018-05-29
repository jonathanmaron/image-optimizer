<?php
declare(strict_types=1);

namespace Application\Component\Console;

use Application\Component\Console\Command\ACommand;
use Application\Component\Console\Command\CCommand;
use Application\Component\Console\Command\CommandFactory;
use Application\Component\Console\Command\ImageOptimizerCommand;
use Symfony\Component\Console\Application as ParentApplication;

class Application extends ParentApplication
{
    protected function getDefaultCommands(): array
    {
        $ret = parent::getDefaultCommands();

        $commands = [
            ImageOptimizerCommand::class,
        ];

        $container = null;
        $options   = [];

        foreach ($commands as $requestedName) {
            $instance = new CommandFactory();
            $command  = $instance($container, $requestedName, $options);
            array_push($ret, $command);
        }

        return $ret;
    }
}
