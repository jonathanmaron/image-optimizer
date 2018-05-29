<?php
declare(strict_types=1);

namespace Application\Component\Console\Command;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

class CommandFactory
{
    public function __invoke(
        ?ContainerInterface $container = null, ?string $requestedName = null, ?array $options = null
    ): Command {

        $command = new $requestedName;

        return $command;
    }
}
