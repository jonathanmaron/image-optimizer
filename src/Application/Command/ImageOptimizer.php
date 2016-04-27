<?php

namespace Application\Command;

use League\CLImate\CLImate;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Application\History;
use Application\Worker;

class ImageOptimizer extends Command
{
    const CHAR_WIDTH = 150;

    protected $console;
    protected $path;
    protected $indexOnly;

    protected function configure()
    {
        $this->setName('image-optimizer');

        $this->setDescription("Image optimization / compression CLI tool. This tool optimizes PNG and JPEG files from the CLI, using 'pngout', 'pngcrush' and 'jpegtran'.");

        $this->addArgument(
            'path',
            InputArgument::REQUIRED,
            'Path in which to search for images to optimize.'
        );

        $this->addOption(
            'index-only',
            null,
            InputOption::VALUE_NONE,
            'Do not optimize images, just index them.'
        );

    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $worker = new Worker();
        $worker->checkDependencies();
    }

    protected function interact  (InputInterface $input, OutputInterface $output)
    {
        $path     = $input->getArgument('path');
        $realPath = realpath($path);

        if (false === $realPath) {
            throw new RuntimeException(
                "The path '{$path}' does not exist."
            );
        }

        $this->setPath($realPath);

        if ($input->getOption('index-only')) {
            $indexOnly = true;
        } else {
            $indexOnly = false;
        }

        $this->setIndexOnly($indexOnly);
    }

    protected function execute   (InputInterface $input, OutputInterface $output)
    {
        $history = new History();
        $worker  = new Worker();

        $console = $this->getConsole();
        $padding = $console->padding(self::CHAR_WIDTH);

        $grandTotals = [
            'skipped'   => 0,   // Number of image files, which have been skipped
            'optimized' => 0,   // Number of image files, which have been optimized
            'indexed'   => 0,   // Number of image files, which have been indexed
            'in'        => 0,   // Total number of bytes before optimization
            'out'       => 0,   // Total number of bytes after optimization
            'diff'      => 0,   // Difference in bytes between 'in' and 'out'
            'diff_pct'  => 0,   // Difference as percent between 'in' and 'out'
        ];

        $fileInfos        = $worker->searchForImageFiles($this->getPath());
        $fileInfosCount   = count($fileInfos);
        $fileInfosCounter = 0;

        $this->prefixBanner($fileInfosCount);

        foreach ($fileInfos as $fileInfo)
        {
            $fileInfosCounter++;

            $filename = $fileInfo->getPathname();

            if ($this->getIndexOnly()) {

                $label   = sprintf('%d/%d: %s', $fileInfosCounter, $fileInfosCount, $filename);
                $current = $padding->label($label);
                $current->result('Indexed.');

                $grandTotals['indexed']++;

                $history->setImageAsOptimized($filename);

                continue;
            }

            if ($history->isUnoptimizedImage($filename)) {

                $subTotals = [
                    'in'       => filesize($filename), // Number of bytes before optimization
                    'out'      => 0,                   // Number of bytes after optimization
                    'diff'     => 0,                   // Difference in bytes between 'in' and 'out'
                    'diff_pct' => 0,                   // Difference as percent between 'in' and 'out'
                ];

                $label   = sprintf('%d/%d: %s', $fileInfosCounter, $fileInfosCount, $filename);
                $current = $padding->label($label);

                if ($worker->optimizeImage($filename)) {

                    clearStatCache();

                    $subTotals['out']  = filesize($filename);
                    $subTotals['diff'] = $subTotals['in'] - $subTotals['out'];

                    if ($subTotals['out'] > 0 && $subTotals['in'] > 0) {
                        $subTotals['diff_pct'] = 100 - ( ($subTotals['out'] / $subTotals['in']) * 100 );
                    }

                    $result = sprintf('Saving: %01.4f %%.', $subTotals['diff_pct']);
                    $current->result($result);

                    $grandTotals['in']  += $subTotals['in'];
                    $grandTotals['out'] += $subTotals['out'];
                    $grandTotals['optimized']++;

                    $history->setImageAsOptimized($filename);
                }

            } else {

                $label   = sprintf('%d/%d: %s', $fileInfosCounter, $fileInfosCount, $filename);
                $current = $padding->label($label);
                $current->result('Skipped.');

                $grandTotals['skipped']++;
            }
        }

        $this->suffixGrandTotals($grandTotals, $fileInfosCount)
             ->suffixBanner();
    }

    protected function prefixBanner($fileInfosCount)
    {
        $console = $this->getConsole();

        $console->clear()
                ->br()
                ->out(sprintf('Started at %s.', date('r')))
                ->br()
                ->out(sprintf('Found %d image file(s).', $fileInfosCount))
                ->br();

        return $this;
    }

    protected function suffixBanner()
    {
        $console = $this->getConsole();

        $console->br()
            ->out(sprintf('Finished at %s.', date('r')))
            ->br();

        return $this;
    }

    protected function suffixGrandTotals($grandTotals, $fileInfosCount)
    {
        $console = $this->getConsole();

        $grandTotals['diff'] = $grandTotals['in'] - $grandTotals['out'];

        if ($grandTotals['out'] > 0 && $grandTotals['in'] > 0) {
            $grandTotals['diff_pct'] = 100 - ( ($grandTotals['out'] / $grandTotals['in']) * 100 );
        }

        $console->br()
                ->out('Grand totals:')
                ->br()
                ->out(sprintf('  Total     : %d file(s)'       , $fileInfosCount)           )
                ->br()
                ->out(sprintf('  Optimized : %d file(s)'       , $grandTotals['optimized']) )
                ->out(sprintf('  Skipped   : %d file(s)'       , $grandTotals['skipped'])   )
                ->out(sprintf('  Indexed   : %d file(s)'       , $grandTotals['indexed'])   )
                ->br()
                ->out(sprintf('  In        : %d b'             , $grandTotals['in'])        )
                ->out(sprintf('  Out       : %d b'             , $grandTotals['out'])       )
                ->out(sprintf('  Diff      : %d b (%01.4f %%)' , $grandTotals['diff']
                                                               , $grandTotals['diff_pct'])  );
        return $this;
    }

    protected function getConsole()
    {
        if (null === $this->console) {
            $this->console = new CLImate();
        }

        return $this->console;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getIndexOnly()
    {
        return $this->indexOnly;
    }

    public function setIndexOnly($indexOnly)
    {
        $this->indexOnly = $indexOnly;
    }

}