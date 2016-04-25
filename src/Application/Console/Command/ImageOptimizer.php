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

        $console->out(sprintf('Found %d image file(s).',
            $fileInfosCount
        ));

        foreach ($fileInfos as $fileInfo)
        {
            $fileInfosCounter++;

            $filename = $fileInfo->getPathname();

            if ($this->getIndexOnly()) {

                $console->out(sprintf('%d/%d: %s - Indexed.',
                    $fileInfosCounter, $fileInfosCount, $filename
                ));

                $history->setImageAsOptimized($filename);

                continue;
            }

            if ($history->isUnoptimizedImage($filename)) {

                $s = ['in' => 0, 'out' => 0, 'diff' => 0, 'diff_p' => 0];

                $s['in'] = filesize($filename);

                if ($worker->optimizeImage($filename)) {

                    clearStatCache();

                    $s['out']  = filesize($filename);
                    $s['diff'] = $s['in'] - $s['out'];

                    if ($s['out'] > 0 && $s['in'] > 0) {
                        $s['diff_p'] = 100 - ( ($s['out'] / $s['in']) * 100 );
                    }

                    $console->out(sprintf('%d/%d: %s - In: %d Out: %d Diff: %d Diff_P : %01.2f',
                        $fileInfosCounter, $fileInfosCount, $filename,
                        $s['in'], $s['out'], $s['diff'], $s['diff_p']
                    ));

                    $history->setImageAsOptimized($filename);
                }

            } else {

                $console->out(sprintf('%d/%d: %s - Already optimized.',
                    $fileInfosCounter, $fileInfosCount, $filename
                ));

            }

        }

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