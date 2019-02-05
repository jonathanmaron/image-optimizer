<?php
declare(strict_types=1);

namespace Application\History;

use Symfony\Component\Filesystem\Filesystem;

class History
{
    /**
     * Algorithm to create hashes
     *
     * @var string
     */
    private const HASH_ALGORITHM = 'sha256';

    /**
     * Directory in which to store hashes
     *
     * @var string
     */
    private const HASH_DIRECTORY = '.image_optimizer';

    /**
     * Depth of hash directory
     *
     * @var integer
     */
    private const HASH_DIRECTORY_DEPTH = 3;

    /**
     * Length of each sub directory name in hash directory
     *
     * @var integer
     */
    private const HASH_DIRECTORY_LENGTH = 2;

    /**
     * Return a hash of the filename
     *
     * @param $filename
     *
     * @return string
     */
    public function getHash(string $filename): string
    {
        return hash(self::HASH_ALGORITHM, $filename);
    }

    /**
     * Return a hash of the contents of the file
     *
     * @param $filename
     *
     * @return string
     */
    public function getHashFile(string $filename): string
    {
        return hash_file(self::HASH_ALGORITHM, $filename);
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

        $chunks = str_split($hash, self::HASH_DIRECTORY_LENGTH);

        for ($i = 0; $i < self::HASH_DIRECTORY_DEPTH; $i++) {
            $path .= DIRECTORY_SEPARATOR . $chunks[$i];
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
        $filesystem = new Filesystem();

        $ret = getenv('HOME') . DIRECTORY_SEPARATOR . self::HASH_DIRECTORY;

        if (!$filesystem->exists($ret)) {
            $filesystem->mkdir($ret, 0700);
        }

        return $ret;
    }

    /**
     * Return true, if an image filename has already been optimized
     *
     * @param $filename
     *
     * @return bool
     */
    public function isOptimized(string $filename): bool
    {
        $filesystem = new Filesystem();

        $hash         = $this->getHash($filename);
        $hashFilename = $this->getHashFilename($hash);

        if (!$filesystem->exists($hashFilename)) {
            return false;
        }

        clearStatCache();

        $hashFile = file_get_contents($hashFilename);
        if ($hashFile !== $this->getHashFile($filename)) {
            return false;
        }

        return true;
    }

    /**
     * Mark the image filename as 'optimized'
     *
     * @param $filename
     *
     * @return bool
     */
    public function setAsOptimized(string $filename): bool
    {
        $filesystem = new Filesystem();

        $hash         = $this->getHash($filename);
        $hashFile     = $this->getHashFile($filename);
        $hashFilename = $this->getHashFilename($hash);

        $filesystem->dumpFile($hashFilename, $hashFile);

        return $filesystem->exists($hashFilename);
    }

    /**
     * Mark the image filename as 'unoptimized'
     *
     * @param $filename
     *
     * @return bool
     */
    public function setAsUnoptimized(string $filename): bool
    {
        $filesystem = new Filesystem();

        $hash         = $this->getHash($filename);
        $hashFilename = $this->getHashFilename($hash);

        $filesystem->remove($hashFilename);

        return !$filesystem->exists($hashFilename);
    }
}
