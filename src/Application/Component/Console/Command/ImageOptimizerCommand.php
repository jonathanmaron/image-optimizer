<?php
declare(strict_types=1);

namespace Application\Component\Console\Command;

use Application\Component\Finder\Finder;
use Application\Exception\RuntimeException;
use Application\History\History;
use Application\Optimizer\Optimizer;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImageOptimizerCommand extends AbstractCommand
{
    use LockableTrait;

    protected function configure(): self
    {
        $this->setName('image-optimizer');

        $description = 'Image optimization / compression CLI tool. This tool optimizes PNG, JPEG and GIF files, ';
        $description .= 'using a number of external toolsthat must be installed.';

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

        return $this;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): self
    {
        if (!$this->lock()) {
            $message = 'The script is already running in another process.';
            throw new RuntimeException($message);
        }

        return $this;
    }

    protected function interact(InputInterface $input, OutputInterface $output): self
    {
        $path = (string) $input->getOption('path');
        $path = trim($path);
        if (!is_readable($path)) {
            $message = sprintf('The "--path" option is missing or invalid.', $path);
            throw new RuntimeException($message);
        }
        $this->setPath(realpath($path));

        $indexOnly = (bool) $input->getOption('index-only');
        $this->setIndexOnly($indexOnly);

        $force = (bool) $input->getOption('force');
        $this->setForce($force);

        return $this;
    }

    protected function main(InputInterface $input, OutputInterface $output): self
    {
        $history   = new History();
        $finder    = new Finder();
        $optimizer = new Optimizer();

        $grandTotals = [
            'skipped'   => 0, // Number of image files that have been skipped
            'optimized' => 0, // Number of image files that have been optimized
            'indexed'   => 0, // Number of image files that have been indexed
            'in'        => 0, // Total number of bytes before optimization
            'out'       => 0, // Total number of bytes after optimization
            'diff'      => 0, // Difference in bytes between 'in' and 'out'
            'diff_pct'  => 0, // Difference as percent between 'in' and 'out'
        ];

        $finder  = $finder->in($this->getPath());
        $count   = $finder->count();
        $counter = 0;

        $format  = 'Found %d %s';
        $message = sprintf($format, $count, (1 == $count) ? 'file' : 'files');
        $output->writeln($message);
        $output->writeln('');

        foreach ($finder->filenames() as $fileInfo) {

            $filename = $fileInfo->getPathname();

            $counter++;

            $format  = '[%s/%d] Processing "%s"... ';
            $message = sprintf($format, $counter, $count, $filename);
            $output->write($message);

            if ($this->getIndexOnly()) {
                $history->setImageAsOptimized($filename);
                $grandTotals['indexed']++;
                $output->writeLn('Indexed only.');
                continue;
            }

            if ($this->getForce() || $history->isUnoptimizedImage($filename)) {

                $subTotals = [
                    'in'       => filesize($filename), // Number of bytes before optimization
                    'out'      => 0,                   // Number of bytes after optimization
                    'diff'     => 0,                   // Difference in bytes between 'in' and 'out'
                    'diff_pct' => 0,                   // Difference as percent between 'in' and 'out'
                ];

                if ($optimizer->optimizeImage($filename)) {

                    clearStatCache();

                    $subTotals['out']  = filesize($filename);
                    $subTotals['diff'] = $subTotals['in'] - $subTotals['out'];

                    if ($subTotals['out'] > 0 && $subTotals['in'] > 0) {
                        $subTotals['diff_pct'] = 100 - (($subTotals['out'] / $subTotals['in']) * 100);
                    }

                    $format  = 'Saving: %01.4f %%.';
                    $message = sprintf($format, $subTotals['diff_pct']);
                    $output->writeln($message);

                    $grandTotals['in']  += $subTotals['in'];
                    $grandTotals['out'] += $subTotals['out'];

                    $history->setImageAsOptimized($filename);

                    $grandTotals['optimized']++;
                }

                continue;
            }

            $output->writeln('Skipped.');

            $grandTotals['skipped']++;
        }

        $this->bannerGrandTotals($input, $output, $grandTotals, $count);

        return $this;
    }
}
