<?php
declare(strict_types=1);

namespace Application\Component\Finder;

use Application\Utility\ConfigTrait;
use Symfony\Component\Finder\Finder as ParentFinder;

class Finder extends ParentFinder
{
    use ConfigTrait;

    public function __construct($options = [])
    {
        if (!array_key_exists('config', $options)) {
            $format  = "Missing 'config' key in 'options' array at '%s'";
            $message = sprintf($format, __METHOD__);
            throw new InvalidArgumentException($message);
        }

        $this->setConfig($options['config']);

        parent::__construct();
    }

    /**
     * Return an array of image filenames
     *
     * @return array
     */
    public function getFilenames(): array
    {
        $ret = [];

        $config = $this->getConfig();

        if (!array_key_exists('extensions', $config)) {
            $format  = "Missing 'extensions' key in 'config' array at '%s'";
            $message = sprintf($format, __METHOD__);
            throw new InvalidArgumentException($message);
        }

        $finder = $this->files();

        foreach ($config['extensions'] as $extension) {
            $format  = '*.%s';
            $pattern = sprintf($format, $extension);
            $finder->name($pattern);
        }

        foreach ($finder as $fileInfo) {
            $ret[] = (string) $fileInfo->getPathname();
        }

        sort($ret, SORT_NATURAL);

        return $ret;
    }
}
