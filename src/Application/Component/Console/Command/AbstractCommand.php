<?php
declare(strict_types=1);

namespace Application\Component\Console\Command;

use Application\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command as ParentCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends ParentCommand
{
    private const BANNER_START = 'banner_start';

    private const BANNER_END   = 'banner_end';

    private $path      = '';

    private $indexOnly = false;

    private $force     = false;

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getIndexOnly(): bool
    {
        return $this->indexOnly;
    }

    public function setIndexOnly(bool $indexOnly): self
    {
        $this->indexOnly = $indexOnly;

        return $this;
    }

    public function getForce(): bool
    {
        return $this->force;
    }

    public function setForce(bool $force): self
    {
        $this->force = $force;

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output): self
    {
        $this->banner($input, $output, self::BANNER_START);
        $this->main($input, $output);
        $this->banner($input, $output, self::BANNER_END);

        return $this;
    }

    private function banner(InputInterface $input, OutputInterface $output, $type): self
    {
        $timestamp = time();
        $execution = microtime(true) - REQUEST_MICROTIME;

        switch ($type) {
            case self::BANNER_START:
                $command  = $input->getArgument('command');
                $messages = [
                    '',
                    sprintf('## Command    : %s', $command),
                    sprintf('## Start time : %s', date('r', $timestamp)),
                    '',
                ];
                $output->writeln($messages);
                break;
            case self::BANNER_END:
                $messages = [
                    '',
                    sprintf('## End time       : %s', date('r', $timestamp)),
                    sprintf('## Execution time : %0.4f s', $execution),
                    '',
                ];
                $output->writeln($messages);
                break;
            default:
                $message = 'Invalid banner type';
                throw new RuntimeException($message);
                break;
        }

        return $this;
    }
}
