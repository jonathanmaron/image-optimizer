<?php

namespace Application\Search;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;

class Images extends AbstractSearch
{
    public function getFileInfos($path)
    {
        $fileInfos = [];

        $pattern = '/^.+(.jpe?g|.png)$/i';

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