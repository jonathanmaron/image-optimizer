<?php
declare(strict_types=1);

namespace Application\History;

class History
{
    private const HASH_DIRECTORY_DEPTH = 5;

    /**
     * Mark the image filename as 'optimized', by saving a hash of the contents of the file in the history directory
     *
     * @param $filename
     *
     * @return bool
     */
    public function setImageAsOptimized(string $filename): bool
    {
        $hash     = $this->getHash($filename);
        $hashFile = $this->getHashFile($filename);

        $hashFilename = $this->getHashFilename($hash);
        $hashPath     = dirname($hashFilename);

        if (!is_dir($hashPath)) {
            mkdir($hashPath, 0700, true);
        }

        file_put_contents($hashFilename, $hashFile);

        return true;
    }

    /**
     * Return a hash of the filename
     *
     * @param $filename
     *
     * @return mixed
     */
    public function getHash(string $filename): string
    {
        return hash('sha256', $filename);
    }

    /**
     * Return a hash of the contents of the file
     *
     * @param $filename
     *
     * @return mixed
     */
    public function getHashFile(string $filename): string
    {
        return hash_file('sha256', $filename);
    }

    /**
     * Return the filename in the history directory of the specified hash
     *
     * @param $hash
     *
     * @return string
     */
    public function getHashFilename(string $hash): string
    {
        $path = $this->getBasePath();

        for ($i = 0; $i < self::HASH_DIRECTORY_DEPTH; $i++) {
            $path .= DIRECTORY_SEPARATOR . $hash{$i};
        }

        $ret = $path . DIRECTORY_SEPARATOR . $hash;

        return $ret;
    }

    /**
     * Get the directory in which to store history
     *
     * @return string
     */
    public function getBasePath(): string
    {
        $ret = getenv('HOME') . DIRECTORY_SEPARATOR . '.image_optimizer';

        if (!is_dir($ret)) {
            mkdir($ret, 0700, true);
        }

        return $ret;
    }

    /**
     * Return true, if an image file needs to be optimized. i.e. it is currently in an 'unoptimized' state.
     *
     * @param $filename
     *
     * @return bool
     */
    public function isUnoptimizedImage(string $filename): bool
    {
        $ret = true;

        $hash         = $this->getHash($filename);
        $hashFilename = $this->getHashFilename($hash);

        if (is_readable($hashFilename)) {
            clearStatCache();
            $hashFile = file_get_contents($hashFilename);
            if ($hashFile === $this->getHashFile($filename)) {
                $ret = false;
            }
        }

        return $ret;
    }
}
