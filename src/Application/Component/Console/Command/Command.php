<?php
declare(strict_types=1);

namespace Application\Component\Console\Command;

use Application\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setName('image-optimizer');

        $description = 'Image optimization / compression CLI tool. This tool optimizes PNG, JPEG and GIF files, ';
        $description .= 'using a number of external tools that must be installed.';

        $this->setDescription($description);

        $name        = 'path';
        $shortcut    = null;
        $mode        = InputOption::VALUE_REQUIRED;
        $description = 'Path in which to search for images to optimize.';
        $default     = '';

        $this->addOption($name, $shortcut, $mode, $description, $default);

        $name        = 'index-only';
        $shortcut    = null;
        $mode        = InputOption::VALUE_NONE;
        $description = 'Do not optimize images, just index them.';

        $this->addOption($name, $shortcut, $mode, $description);

        $name        = 'force';
        $shortcut    = null;
        $mode        = InputOption::VALUE_NONE;
        $description = 'Always optimize images, ignoring history.';

        $this->addOption($name, $shortcut, $mode, $description);

        return;
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $path = (string) $input->getOption('path');
        $path = trim($path);
        if (!is_readable($path)) {
            $message = "The '--path' option is missing or invalid.";
            throw new RuntimeException($message);
        }
        $this->setPath(realpath($path));

        $indexOnly = (bool) $input->getOption('index-only');
        $this->setIndexOnly($indexOnly);

        $force = (bool) $input->getOption('force');
        $this->setForce($force);

        return;
    }

    protected function main(InputInterface $input, OutputInterface $output): int
    {
        $finder     = $this->getFinder();
        $formatter  = $this->getNumberFormatter();
        $history    = $this->getHistory();
        $optimizer  = $this->getOptimizer();
        $statistics = $this->getStatistics();

        $finder    = $finder->in($this->getPath());
        $filenames = $finder->getFilenames();
        $count     = count($filenames);
        $counter   = 0;

        foreach ($filenames as $filename) {

            $counter++;

            $statistics->resetBytesIn();
            $statistics->resetBytesOut();

            $format  = '[%s/%s] Processing "%s"... ';
            $message = sprintf($format, $formatter->format($counter), $formatter->format($count), $filename);
            $output->write($message);

            if ($this->getIndexOnly()) {
                $history->setAsOptimized($filename);
                $statistics->incrementIndexed();
                $output->writeLn('Indexed.');
                continue;
            }

            if ($history->isOptimized($filename) && !$this->getForce()) {
                $output->writeln('Skipped.');
                $statistics->incrementSkipped();
                continue;
            }

            $statistics->setBytesIn(filesize($filename));

            if ($optimizer->optimize($filename)) {

                clearStatCache();

                $statistics->setBytesOut(filesize($filename));

                $format  = 'Saving: %01.4f %%.';
                $message = sprintf($format, $statistics->getBytesDifferenceAsPercentage());
                $output->writeln($message);

                $history->setAsOptimized($filename);
                $statistics->incrementOptimized();
            }
        }

        $this->bannerGrandTotals($input, $output, $statistics);

        return 1;
    }
}
