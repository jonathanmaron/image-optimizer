<?php
declare(strict_types=1);

namespace Application\Component\Finder;

use Symfony\Component\Finder\Finder as ParentFinder;

class Finder extends ParentFinder
{
    public function images(): self
    {
        $finder = $this->files();
        $finder->name('*.jpg');
        $finder->name('*.jpeg');
        $finder->name('*.png');
        $finder->name('*.gif');

        return $finder;
    }
}
