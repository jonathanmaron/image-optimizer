<?php

namespace Application;

use League\CLImate\CLImate;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends ConsoleCommand
{
    const CHAR_WIDTH = 200;

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

        // -------------------------------------------------------------------------------------------------------------

        $grandTotals = [
            'skipped'   => 0,
            'optimized' => 0,
            'in'        => 0,
            'out'       => 0,
            'diff'      => 0,
            'diff_pct'  => 0,
        ];

        // -------------------------------------------------------------------------------------------------------------

        $fileInfos        = $worker->searchForImageFiles($this->getPath());
        $fileInfosCount   = count($fileInfos);
        $fileInfosCounter = 0;

        // -------------------------------------------------------------------------------------------------------------

        $this->prefixBanner($fileInfosCount);

        // -------------------------------------------------------------------------------------------------------------

        foreach ($fileInfos as $fileInfo)
        {
            $fileInfosCounter++;

            $filename = $fileInfo->getPathname();

            if ($this->getIndexOnly()) {

                $label   = sprintf('%d/%d: %s', $fileInfosCounter, $fileInfosCount, $filename);
                $current = $padding->label($label);
                $current->result('Indexed.');

                $history->setImageAsOptimized($filename);

                continue;
            }

            if ($history->isUnoptimizedImage($filename)) {

                $label   = sprintf('%d/%d: %s', $fileInfosCounter, $fileInfosCount, $filename);
                $current = $padding->label($label);

                $subTotals = [
                    'in'        => 0,
                    'out'       => 0,
                    'diff'      => 0,
                    'diff_pct'  => 0,
                ];

                $subTotals['in'] = filesize($filename);

                if ($worker->optimizeImage($filename)) {

                    clearStatCache();

                    $subTotals['out']  = filesize($filename);
                    $subTotals['diff'] = $subTotals['in'] - $subTotals['out'];

                    if ($subTotals['out'] > 0 && $subTotals['in'] > 0) {
                        $subTotals['diff_pct'] = 100 - ( ($subTotals['out'] / $subTotals['in']) * 100 );
                    }

                    $result = sprintf('Saving: %01.4f %%.', $subTotals['diff_pct']);
                    $current->result($result);

                    $grandTotals['in']  =+ $subTotals['in'];
                    $grandTotals['out'] =+ $subTotals['out'];

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

        // -------------------------------------------------------------------------------------------------------------

        $grandTotals['diff'] = $grandTotals['in'] - $grandTotals['out'];

        if ($grandTotals['out'] > 0 && $grandTotals['in'] > 0) {
            $grandTotals['diff_pct'] = 100 - ( ($grandTotals['out'] / $grandTotals['in']) * 100 );
        }

        $console->br()
                ->out('Grand totals:')
                ->br()
                ->out(sprintf('  Total     : %d file(s)'       , $fileInfosCount)           )
                ->out(sprintf('  Optimized : %d file(s)'       , $grandTotals['optimized']) )
                ->out(sprintf('  Skipped   : %d file(s)'       , $grandTotals['skipped'])   )
                ->br()
                ->out(sprintf('  In        : %d b'             , $grandTotals['in'])        )
                ->out(sprintf('  Out       : %d b'             , $grandTotals['out'])       )
                ->out(sprintf('  Diff      : %d b (%01.4f %%)' , $grandTotals['diff']
                                                               , $grandTotals['diff_pct'])  );

        // -------------------------------------------------------------------------------------------------------------

        $this->suffixBanner();

    }

    protected function prefixBanner($fileInfosCount)
    {
        $console = $this->getConsole();

        $console->clear();

        $console->br()
                ->out('Started at ' . date('r'))
                ->br()
                ->out(sprintf('Found %d image file(s).', $fileInfosCount))
                ->br();

        return $this;
    }

    protected function suffixBanner()
    {
        $console = $this->getConsole();

        $console->br()
                ->out('Finished at ' . date('r'))
                ->br();

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