<?php
declare(strict_types=1);

namespace Application\Component\Console\Command\ImageOptimizerCommand;

use Application\Exception\RuntimeException;
use Application\Statistics\Statistics;
use Application\Utility\ArgumentsTrait;
use Application\Utility\ConfigTrait;
use Application\Utility\DependenciesTrait;
use NumberFormatter;
use Symfony\Component\Console\Command\Command as ParentCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends ParentCommand
{
    use ArgumentsTrait;
    use ConfigTrait;
    use DependenciesTrait;

    protected const BANNER_START = 'banner_start';

    protected const BANNER_END   = 'banner_end';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->banner($input, $output, self::BANNER_START);
        $this->main($input, $output);
        $this->banner($input, $output, self::BANNER_END);

        return 0;
    }

    protected function banner(InputInterface $input, OutputInterface $output, string $type): void
    {
        $timestamp = time();
        $execution = microtime(true) - REQUEST_MICROTIME;

        switch ($type) {
            case self::BANNER_START:
                $command  = $input->getArgument('command');
                $messages = [
                    null,
                    sprintf('## Command    : %s', $command),
                    sprintf('## Start time : %s', date('r', $timestamp)),
                    null,
                ];
                break;
            case self::BANNER_END:
                $messages = [
                    null,
                    sprintf('## End time       : %s', date('r', $timestamp)),
                    sprintf('## Execution time : %0.4f s', $execution),
                    null,
                ];
                break;
            default:
                $message = 'Invalid banner type';
                throw new RuntimeException($message);
                break;
        }

        $output->writeln($messages);

        return;
    }

    protected function bannerGrandTotals(InputInterface $input, OutputInterface $output, Statistics $statistics): void
    {
        $noun = function ($count) {
            return (1 === $count) ? 'file' : 'files';
        };

        $formatter = $this->getNumberFormatter();
        $optimized = $statistics->getOptimized();
        $skipped   = $statistics->getSkipped();
        $indexed   = $statistics->getIndexed();
        $total     = $statistics->getTotal();

        $totalBytesIn        = $statistics->getTotalBytesIn();
        $totalBytesOut       = $statistics->getTotalBytesOut();
        $totalBytesDiff      = $statistics->getTotalBytesDifference();
        $totalBytesDiffAsPct = $statistics->getTotalBytesDifferenceAsPercentage();

        $messages = [
            null,
            sprintf('Total     : %s %s', $formatter->format($total), $noun($total)),
            null,
            sprintf('Optimized : %s %s', $formatter->format($optimized), $noun($optimized)),
            sprintf('Skipped   : %s %s', $formatter->format($skipped), $noun($skipped)),
            sprintf('Indexed   : %s %s', $formatter->format($indexed), $noun($indexed)),
            null,
            sprintf('In        : %s b', $formatter->format($totalBytesIn)),
            sprintf('Out       : %s b', $formatter->format($totalBytesOut)),
            sprintf('Diff      : %s b (%01.4f %%)', $formatter->format($totalBytesDiff), $totalBytesDiffAsPct),
        ];
        $output->writeLn($messages);

        return;
    }
}
