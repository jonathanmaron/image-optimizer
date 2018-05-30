<?php
declare(strict_types=1);

namespace Application\Component\Finder;

use Symfony\Component\Finder\Finder as ParentFinder;

class Finder extends ParentFinder
{
    /**
     * Patterns that match image filenames
     */
    private const PATTERNS
        = [
            '*.jpg',
            '*.JPG',
            '*.jpeg',
            '*.JPEG',
            '*.png',
            '*.PNG',
            '*.gif',
            '*.GIF',
        ];

    /**
     * Return an array of image filenames
     *
     * @return array
     */
    public function getFilenames(): array
    {
        $ret = [];

        $finder = $this->files();

        foreach (self::PATTERNS as $pattern) {
            $finder->name($pattern);
        }

        foreach ($finder as $fileInfo) {
            array_push($ret, (string) $fileInfo->getPathname());
        }

        sort($ret, SORT_NATURAL);

        return $ret;
    }
}
