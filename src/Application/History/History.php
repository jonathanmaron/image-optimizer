<?php
declare(strict_types=1);

namespace Application\History;

use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class History extends AbstractHistory
{
    /**
     * Return the entity filename, based on the image filename
     *
     * @param $filename
     *
     * @return string
     */
    public function getEntityFilename(string $filename): string
    {
        $hash = hash(self::HASH_ALGORITHM, $filename);

        $path = $this->getBasePath();

        $chunks = str_split($hash, self::ENTITY_DIRECTORY_LENGTH);

        for ($i = 0; $i < self::ENTITY_DIRECTORY_DEPTH; $i++) {
            $path .= DIRECTORY_SEPARATOR . $chunks[$i];
        }

        return $path . DIRECTORY_SEPARATOR . $hash . '.serialized';
    }

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

        $fileInfo         = new SplFileInfo($filename);
        $entitySerialized = file_get_contents($entityFilename);
        $entity           = unserialize($entitySerialized);

        if ($fileInfo->getMTime() !== $entity->getMTime()) {
            return false;
        }

        if ($fileInfo->getSize() !== $entity->getSize()) {
            return false;
        }

        // relatively slow (keep last)
        $entityId = Entity::createId($filename);
        if ($entityId !== $entity->getId()) {
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

        $entityFilename = $this->getEntityFilename($filename);
        $entityId       = Entity::createId($filename);

        $entity           = new Entity($entityId, $filename);
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
