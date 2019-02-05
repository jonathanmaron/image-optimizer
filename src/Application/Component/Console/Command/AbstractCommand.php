<?php
declare(strict_types=1);

namespace Application\Component\Console\Command;

use Application\Exception\RuntimeException;
use Application\Utility\ArgumentsTrait;
use Application\Utility\ConfigTrait;
use Application\Utility\DependenciesTrait;
use Symfony\Component\Console\Command\Command as ParentCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends ParentCommand
{
    use ArgumentsTrait;
    use ConfigTrait;
    use DependenciesTrait;

    private const BANNER_START = 'banner_start';

    private const BANNER_END   = 'banner_end';

    protected function execute(InputInterface $input, OutputInterface $output): self
    {
        $this->banner($input, $output, self::BANNER_START);
        $this->main($input, $output);
        $this->banner($input, $output, self::BANNER_END);

        return $this;
    }

    protected function banner(InputInterface $input, OutputInterface $output, string $type): self
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

    protected function bannerGrandTotals(
        InputInterface $input,
        OutputInterface $output,
        array $totals,
        int $count
    ): self {

        $noun = function ($count) {
            return (1 == $count) ? 'file' : 'files';
        };

        $totals['diff'] = $totals['in'] - $totals['out'];
        if ($totals['out'] > 0 && $totals['in'] > 0) {
            $totals['diff_pct'] = 100 - (($totals['out'] / $totals['in']) * 100);
        }

        $messages = [
            '',
            sprintf('Total     : %d %s', $count, $noun($count)),
            '',
            sprintf('Optimized : %d %s', $totals['optimized'], $noun($totals['optimized'])),
            sprintf('Skipped   : %d %s', $totals['skipped'], $noun($totals['skipped'])),
            sprintf('Indexed   : %d %s', $totals['indexed'], $noun($totals['indexed'])),
            '',
            sprintf('In        : %d b', $totals['in']),
            sprintf('Out       : %d b', $totals['out']),
            sprintf('Diff      : %d b (%01.4f %%)', $totals['diff'], $totals['diff_pct']),
        ];
        $output->writeLn($messages);

        return $this;
    }
}
