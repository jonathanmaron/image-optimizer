<?php
declare(strict_types=1);

namespace Application\Component\Finder;

use Symfony\Component\Finder\Finder as ParentFinder;

class Finder extends ParentFinder
{
    public function filenames(): array
    {
        $ret = [];

        $files = $this->files();

        $files->name('*.jpg');
        $files->name('*.jpeg');
        $files->name('*.png');
        $files->name('*.gif');

        foreach ($files as $fileInfo) {
            $ret[] = $fileInfo->getPathname();
        }

        sort($ret, SORT_NATURAL);

        return $ret;
    }
}
