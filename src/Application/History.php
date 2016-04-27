<?php

namespace Application;

class History
{
    const HASH_DIRECTORY_DEPTH = 5;

    /**
     * Get the directory in which to store history
     *
     * @return string
     */
    public function getBasePath()
    {
        $basePath = getenv('HOME');

        $basePath .= DIRECTORY_SEPARATOR . '.image_optimizer';

        if (!is_dir($basePath)) {
            mkdir($basePath, 0700, true);
        }
        
        return $basePath;
    }

    /**
     * Return a hash of the filename
     *
     * @param $filename
     * @return mixed
     */
    public function getHash($filename)
    {
        return hash('sha256', $filename);
    }

    /**
     * Return a hash of the contents of the file
     *
     * @param $filename
     * @return mixed
     */
    public function getHashFile($filename)
    {
        return hash_file('sha256', $filename);
    }

    /**
     * Return the filename in the history directory of the specified hash
     *
     * @param $hash
     * @return string
     */
    public function getHashFilename($hash)
    {
        $path = $this->getBasePath();

        for ($i = 0; $i < self::HASH_DIRECTORY_DEPTH; $i++) {
            $path .= DIRECTORY_SEPARATOR . $hash{$i};
        }

        return $path . DIRECTORY_SEPARATOR . $hash;
    }

    /**
     * Mark the image filename as 'optimized', by saving a hash of the contents of the file in the history directory
     *
     * @param $filename
     * @return mixed
     */
    public function setImageAsOptimized($filename)
    {
        $hash         = $this->getHash($filename);
        $hashFile     = $this->getHashFile($filename);

        $hashFilename = $this->getHashFilename($hash);
        $hashPath     = dirname($hashFilename);

        if (!is_dir($hashPath)) {
            mkdir($hashPath, 0700, true);
        }

        return file_put_contents($hashFilename, $hashFile);
    }

    /**
     * Return true, if an image file needs to be optimized. i.e. it is currenlty in an 'unoptimized' state.
     * @param $filename
     * @return bool
     */
    public function isUnoptimizedImage($filename)
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