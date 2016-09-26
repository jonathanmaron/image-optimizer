<?php

namespace Application\Component\Console;

use Application\Component\Console\Command\Command;
use Symfony\Component\Console\Application as ApplicationConsoleComponentSymfony;
use Symfony\Component\Console\Input\InputInterface;

class Application extends ApplicationConsoleComponentSymfony
{
    protected $config;

    protected function getCommandName(InputInterface $input)
    {
        return 'image-optimizer.php';
    }

    protected function getDefaultCommands()
    {
        $command = new Command();

        $defaultCommands = parent::getDefaultCommands();

        array_push($defaultCommands, $command);

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

}