<?php

namespace Application\Command;

use League\CLImate\CLImate as Console;

abstract class ImageOptimizerAbstract extends Command
{
    /**
     * Default width of console, if console's width cannot be established
     */
    const DEFAULT_CONSOLE_WIDTH = 120;

    /**
     * Maximum length of 'result' part of line. Typical value of 'result' is 'Saving: 4.1887 %.'
     */
    const MAXIMUM_RESULT_LENGTH =  20;

    protected $console;
    protected $path;
    protected $indexOnly;

    protected function getConsole()
    {
        if (null === $this->console) {
            $this->console = new Console();
        }

        return $this->console;
    }

    protected function getLinePadding()
    {
        exec('/usr/bin/env tput cols', $output);

        if (isset($output[0]) && $output[0] > 0) {
            $consoleWidth = (integer) $output[0];
        } else {
            $consoleWidth = self::DEFAULT_CONSOLE_WIDTH;
        }

        return $consoleWidth - self::MAXIMUM_RESULT_LENGTH;
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

    protected function consoleBannerPrefix($fileInfosCount)
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

    protected function consoleBannerSuffix()
    {
        $console = $this->getConsole();

        $console->br()
            ->out(sprintf('Finished at %s.', date('r')))
            ->br();

        return $this;
    }

    protected function consoleGrandTotals($grandTotals, $fileInfosCount)
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

    protected function labelHelper($fileInfosCount, $fileInfosCounter, $filename)
    {
        $ellipsis    = '/[..]';
        $prefix      = sprintf('%d/%d: ', $fileInfosCounter, $fileInfosCount);
        $labelLength = strlen($prefix) + strlen($filename);

        if ($labelLength > $this->getLinePadding()) {
            $start  = $this->getLinePadding() - strlen($prefix) - strlen($ellipsis);
            $suffix = $ellipsis . substr($filename, -$start);
        } else {
            $suffix = $filename;
        }

        return $prefix . $suffix;
    }

}