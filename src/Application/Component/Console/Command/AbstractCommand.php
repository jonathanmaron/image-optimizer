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
        $lines     = [];

        switch ($type) {
            case self::BANNER_START:
                $command = $input->getArgument('command');
                $lines[] = '';
                $lines[] = sprintf('## Command    : %s', $command);
                $lines[] = sprintf('## Start time : %s', date('r', $timestamp));
                $lines[] = '';
                $output->writeln($lines);
                break;
            case self::BANNER_END:
                $lines[] = '';
                $lines[] = sprintf('## End time       : %s', date('r', $timestamp));
                $lines[] = sprintf('## Execution time : %0.4f s', $execution);
                $lines[] = '';
                $output->writeln($lines);
                break;
            default:
                $message = 'Invalid banner type';
                throw new RuntimeException($message);
                break;
        }

        return $this;
    }
}
