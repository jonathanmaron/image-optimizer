<?php
declare(strict_types=1);

namespace Application\History;

use Symfony\Component\Filesystem\Filesystem;

class History extends AbstractHistory
{
    /**
     * Return the directory in which to store entities
     *
     * @return string
     */
    public function getBasePath(): string
    {
        $filesystem = new Filesystem();

        $ret = getenv('HOME') . DIRECTORY_SEPARATOR . self::ENTITY_DIRECTORY;

        if (!$filesystem->exists($ret)) {
            $filesystem->mkdir($ret, 0700);
        }

        return $ret;
    }

    /**
     * Return the entity filename, based on the image filename
     *
     * @param $filename
     *
     * @return string
     */
    public function getEntityFilename(string $filename): string
    {
        $path = $this->getBasePath();

        $hash   = hash(self::HASH_ALGORITHM, $filename);
        $chunks = str_split($hash, self::ENTITY_DIRECTORY_LENGTH);

        for ($i = 0; $i < self::ENTITY_DIRECTORY_DEPTH; $i++) {
            $path .= DIRECTORY_SEPARATOR . $chunks[$i];
        }

        return $path . DIRECTORY_SEPARATOR . $hash . '.serialized';
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

        $entityFilename = $this->getEntityFilename($filename);

        if (!$filesystem->exists($entityFilename)) {
            return false;
        }

        clearStatCache();

        $entitySerialized = file_get_contents($entityFilename);
        $entity           = unserialize($entitySerialized);
        $entityClass      = get_class($entity);

        $functions = [
            'filemtime', // 1 - fast test (inaccurate)
            'filesize',  // 2 - fast test (inaccurate)
            'id',        // 3 - slow test (accurate)
        ];

        foreach ($functions as $function) {
            $method = sprintf('get%s', ucfirst($function));
            if ($entityClass::$function($filename) === $entity->$method()) {
                return true;
            }
        }

        return false;
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
        $entity     = new Entity($filename);

        $entityFilename   = $this->getEntityFilename($filename);
        $entitySerialized = serialize($entity);

        $filesystem->dumpFile($entityFilename, $entitySerialized);

        return $filesystem->exists($entityFilename);
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

        $entityFilename = $this->getEntityFilename($filename);

        $filesystem->remove($entityFilename);

        return !$filesystem->exists($entityFilename);
    }
}
