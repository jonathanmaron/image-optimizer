<?php

namespace Application\Console\Command;

use Application\Filesystem;
use Application\History;
use Application\Worker;
use League\CLImate\CLImate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImageOptimizer extends Command
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

        $fileInfos        = $worker->searchForImageFiles($this->getPath());
        $fileInfosCount   = count($fileInfos);
        $fileInfosCounter = 0;

        $padding = $console->padding(self::CHAR_WIDTH);

        $console->clear();

        $this->prefixBanner();

        $line = sprintf('Found %d image file(s).', $fileInfosCount);
        $console->out($line)->br();

        foreach ($fileInfos as $fileInfo)
        {
            $fileInfosCounter++;

            $filename = $fileInfo->getPathname();

            if ($this->getIndexOnly()) {

                $label  = sprintf('%d/%d: %s', $fileInfosCounter, $fileInfosCount, $filename);
                $result = 'Indexed.';
                $current = $padding->label($label);
                $current->result($result);

                $history->setImageAsOptimized($filename);

                continue;
            }

            if ($history->isUnoptimizedImage($filename)) {

                $label  = sprintf('%d/%d: %s', $fileInfosCounter, $fileInfosCount, $filename);
                $current = $padding->label($label);

                $s = ['in' => 0, 'out' => 0, 'diff' => 0, 'diff_p' => 0];

                $s['in'] = filesize($filename);

                if ($worker->optimizeImage($filename)) {

                    clearStatCache();

                    $s['out']  = filesize($filename);
                    $s['diff'] = $s['in'] - $s['out'];

                    if ($s['out'] > 0 && $s['in'] > 0) {
                        $s['diff_p'] = 100 - ( ($s['out'] / $s['in']) * 100 );
                    }

                    //$result = sprintf('In: %d Out: %d Diff: %d Diff_P : %01.2f',$s['in'], $s['out'], $s['diff'], $s['diff_p']);
                    $result = sprintf('Saving: %01.4f %%.', $s['diff_p']);
                    $current->result($result);

                    $history->setImageAsOptimized($filename);
                }

            } else {

                $label  = sprintf('%d/%d: %s', $fileInfosCounter, $fileInfosCount, $filename);
                $result = 'Skipped.';
                $current = $padding->label($label);
                $current->result($result);

            }

        }

        $this->suffixBanner();

    }

    protected function prefixBanner()
    {
        $console = $this->getConsole();

        $console->br()
                ->out('Started at ' . date('r'))
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