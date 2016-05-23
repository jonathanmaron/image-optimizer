<?php

namespace Application\Search;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;

class Images extends AbstractSearch
{
    public function getFileInfos($directoryOrFilename)
    {
        $fileInfos = [];

        if       ( is_dir($directoryOrFilename)) {
            $fileInfos = $this->getFileInfosFromDirectory($directoryOrFilename);
        } elseif (is_file($directoryOrFilename)) {
            $fileInfos = $this->getFileInfosFromFilename ($directoryOrFilename);
        }

        return $fileInfos;
    }

    protected function getFileInfosFromDirectory($directory)
    {
        $fileInfos = [];

        $pattern = '/^.+(.jpe?g|.png)$/i';

        $recursiveDirectoryIterator = new RecursiveDirectoryIterator($directory);
        $recursiveIteratorIterator  = new RecursiveIteratorIterator($recursiveDirectoryIterator);
        $regexIterator              = new RegexIterator($recursiveIteratorIterator, $pattern, RecursiveRegexIterator::GET_MATCH);

        foreach ($regexIterator as $filename => $array) {
            $fileInfo = new SplFileInfo($filename);
            array_push($fileInfos, $fileInfo);
        }

        return $fileInfos;
    }

    protected function getFileInfosFromFilename($filename)
    {
        $fileInfos = [];

        $fileInfo = new SplFileInfo($filename);
        array_push($fileInfos, $fileInfo);

        return $fileInfos;
    }
}