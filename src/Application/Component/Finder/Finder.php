<?php
declare(strict_types=1);

namespace Application\Component\Finder;

use Application\Optimizer\Optimizer;
use Symfony\Component\Finder\Finder as ParentFinder;

class Finder extends ParentFinder
{
    /**
     * Return an array of image filenames
     *
     * @return array
     */
    public function getFilenames(): array
    {
        $ret = [];

        $finder = $this->files();

        $extensions = [
            Optimizer::EXTENSION_GIF,
            Optimizer::EXTENSION_JPEG,
            Optimizer::EXTENSION_JPG,
            Optimizer::EXTENSION_PNG,
        ];

        foreach ($extensions as $extension) {
            $format    = '*.%s';
            $patternLc = sprintf($format, strtolower($extension));
            $patternUc = sprintf($format, strtoupper($extension));
            $finder->name($patternLc);
            $finder->name($patternUc);
        }

        foreach ($finder as $fileInfo) {
            $ret[] = (string) $fileInfo->getPathname();
        }

        sort($ret, SORT_NATURAL);

        return $ret;
    }
}
