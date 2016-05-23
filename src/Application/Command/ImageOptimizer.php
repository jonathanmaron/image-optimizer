<?php

namespace Application\Command;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Application\History;
use Application\Search\Images as ImagesSearch;

class ImageOptimizer extends AbstractImageOptimizer
{
    protected function configure()
    {
        $this->setName('image-optimizer');

        $this->setDescription("Image optimization / compression CLI tool. This tool optimizes PNG and JPEG files, using a number of external tools, which must be installed.");

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

        return $this;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $path     = $input->getArgument('path');
        $realPath = realpath($path);

        if (false === $realPath) {
            throw new RuntimeException(
                "The path or file '{$path}' does not exist."
            );
        }

        $this->setPath($realPath);

        if ($input->getOption('index-only')) {
            $indexOnly = true;
        } else {
            $indexOnly = false;
        }

        $this->setIndexOnly($indexOnly);

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $history = new History();
        $imagesSearch = new ImagesSearch();

        $consolePadding = $this->getConsole()->padding($this->getLinePadding());

        $grandTotals = [
            'skipped'   => 0,   // Number of image files, which have been skipped
            'optimized' => 0,   // Number of image files, which have been optimized
            'indexed'   => 0,   // Number of image files, which have been indexed
            'in'        => 0,   // Total number of bytes before optimization
            'out'       => 0,   // Total number of bytes after optimization
            'diff'      => 0,   // Difference in bytes between 'in' and 'out'
            'diff_pct'  => 0,   // Difference as percent between 'in' and 'out'
        ];

        $fileInfos        = $imagesSearch->getFileInfos($this->getPath());
        $fileInfosCount   = count($fileInfos);
        $fileInfosCounter = 0;

        $this->consoleBannerPrefix($fileInfosCount);

        foreach ($fileInfos as $fileInfo)
        {
            $fileInfosCounter++;

            $filename = $fileInfo->getPathname();

            if ($this->getIndexOnly()) {

                $consoleLabel = $this->labelHelper($fileInfosCount, $fileInfosCounter, $filename);

                $consolePadding->label($consoleLabel)->result('Indexed.');

                $history->setImageAsOptimized($filename);

                $grandTotals['indexed']++;

                continue;
            }

            if ($history->isUnoptimizedImage($filename)) {

                $subTotals = [
                    'in'       => filesize($filename), // Number of bytes before optimization
                    'out'      => 0,                   // Number of bytes after optimization
                    'diff'     => 0,                   // Difference in bytes between 'in' and 'out'
                    'diff_pct' => 0,                   // Difference as percent between 'in' and 'out'
                ];

                $consoleLabel = $this->labelHelper($fileInfosCount, $fileInfosCounter, $filename);

                $consoleCurrentPadding = $consolePadding->label($consoleLabel);

                if ($this->optimizeImage($filename)) {

                    clearStatCache();

                    $subTotals['out']  = filesize($filename);
                    $subTotals['diff'] = $subTotals['in'] - $subTotals['out'];

                    if ($subTotals['out'] > 0 && $subTotals['in'] > 0) {
                        $subTotals['diff_pct'] = 100 - ( ($subTotals['out'] / $subTotals['in']) * 100 );
                    }

                    $consoleCurrentResult = sprintf('Saving: %01.4f %%.', $subTotals['diff_pct']);
                    $consoleCurrentPadding->result($consoleCurrentResult);

                    $grandTotals['in']  += $subTotals['in'];
                    $grandTotals['out'] += $subTotals['out'];

                    $history->setImageAsOptimized($filename);

                    $grandTotals['optimized']++;
                }

            } else {

                $consoleLabel = $this->labelHelper($fileInfosCount, $fileInfosCounter, $filename);

                $consolePadding->label($consoleLabel)->result('Skipped.');

                $grandTotals['skipped']++;
            }
        }

        $this->consoleGrandTotals($grandTotals, $fileInfosCount);

        $this->consoleBannerSuffix();

        return $this;
    }

}