<?php

namespace Application;

class History
{
    public function getBasePath()
    {
        $basePath = getenv('HOME');

        $basePath .= DIRECTORY_SEPARATOR . '.image_optimizer';

        if (!is_dir($basePath)) {
            mkdir($basePath, 0700, true);
        }
        
        return $basePath;
    }

    public function getHash($filename)
    {
        return hash('sha256', $filename);
    }
    
    public function getHashFilename($hash)
    {
        $path = $this->getBasePath();

        for ($i = 0; $i < 5; $i++) {
            $path .= DIRECTORY_SEPARATOR . $hash{$i};
        }

        $filename = $path . DIRECTORY_SEPARATOR . $hash;

        return $filename;
    }

    public function setImageAsOptimized($filename)
    {
        $hash         = $this->getHash($filename);
        $hashFilename = $this->getHashFilename($hash);
        $hashPath     = dirname($hashFilename);

        if (!is_dir($hashPath)) {
            mkdir($hashPath, 0700, true);
        }

        return file_put_contents($hashFilename, $filename);
    }

    public function isUnoptimizedImage($filename)
    {
        $ret = true;

        $hash         = $this->getHash($filename);
        $hashFilename = $this->getHashFilename($hash);

        if (is_file($hashFilename)) {
            $ret = false;
        }

        return $ret;
    }
    
}