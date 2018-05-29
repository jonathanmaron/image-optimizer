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
     * Return image filenames
     *
     * @return Finder
     */
    public function filenames(): self
    {
        $finder = $this->files();

        foreach (self::PATTERNS as $pattern) {
            $finder->name($pattern);
        }

        $finder->sortByName();

        return $finder;
    }
}
