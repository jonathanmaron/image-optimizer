<?php

namespace Application;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;
use Symfony\Component\Console\Exception\RuntimeException;

class Worker
{
    public function optimizeImage($filename)
    {
        $mode = fileperms($filename);

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        switch ($extension) {
            case 'png':
                $ret = $this->optimizePng($filename);
                break;
            case 'jpg':
            case 'jpeg':
                $ret = $this->optimizeJpg($filename);
                break;
            default:
                throw new RuntimeException(
                    "Unknown image file type - {$filename}"
                );
                break;
        }

        chmod($filename, $mode);

        return $ret;
    }

    public function optimizePng($filename)
    {
        $tempFilename = $this->getTempFilename();

        $format = '/usr/bin/env pngout %s -s0 > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($filename));
        exec($exec);

        $format = '/usr/bin/env pngcrush -rem alla -brute -reduce %s %s > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($filename), escapeshellarg($tempFilename));
        exec($exec);

        rename($tempFilename, $filename);

        return true;
    }

    public function optimizeJpg($filename)
    {
        $tempFilename = $this->getTempFilename();

        $format = '/usr/bin/env jpegtran -optimize -progressive -outfile %s %s > /dev/null 2>&1';
        $exec   = sprintf($format, escapeshellarg($tempFilename), escapeshellarg($filename));
        exec($exec);

        rename($tempFilename, $filename);

        return true;
    }

    public function getTempFilename()
    {
        $path = sys_get_temp_dir();

        return tempnam($path, 'image_optimizer_');
    }

    public function checkDependencies()
    {
        $commands = [
            'tput'     => '/usr/bin/env tput               > /dev/null 2>&1',   // get terminal width
            'pngout'   => '/usr/bin/env pngout             > /dev/null 2>&1',
            'pngcrush' => '/usr/bin/env pngcrush           > /dev/null 2>&1',
            'jpegtran' => '/usr/bin/env jpegtran --version > /dev/null 2>&1',
        ];

        foreach ($commands as $command => $exec) {
            exec($exec, $output, $returnVar);
            if (1 != $returnVar) {
                throw new RuntimeException(
                    "The required command '{$command}' is not installed."
                );
            }
        }

        return true;
    }

    public function searchForImageFiles($path)
    {
        return $this->searchForFiles($path, '/^.+(.jpe?g|.png)$/i');
    }

    protected function searchForFiles($path, $pattern)
    {
        $fileInfos = [];

        $recursiveDirectoryIterator = new RecursiveDirectoryIterator($path);
        $recursiveIteratorIterator  = new RecursiveIteratorIterator($recursiveDirectoryIterator);
        $regexIterator              = new RegexIterator($recursiveIteratorIterator, $pattern, RecursiveRegexIterator::GET_MATCH);

        foreach ($regexIterator as $filename => $array) {
            $fileInfo = new SplFileInfo($filename);
            array_push($fileInfos, $fileInfo);
        }

        return $fileInfos;
    }

}