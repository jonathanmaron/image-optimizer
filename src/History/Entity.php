<?php
declare(strict_types=1);

namespace Application\History;

class Entity
{
    /**
     * Algorithm to create Entity ID (hash of file contents)
     *
     * @var string
     */
    protected const HASH_ALGORITHM = 'sha256';

    /**
     * Entity ID (hash of file contents)
     *
     * @var string
     */
    protected $id;

    /**
     * Absolute filename of image file
     *
     * @var string
     */
    protected $filename;

    /**
     * Size of image file
     *
     * @var int
     */
    protected $filesize;

    /**
     * Last modified time of image file
     *
     * @var int
     */
    protected $filemtime;

    /**
     * Entity constructor
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->setFilename($filename);

        $this->setId(self::id($filename));
        $this->setFilesize(self::filesize($filename));
        $this->setFilemtime(self::filemtime($filename));
    }

    /**
     * Return ID for passed file
     *
     * @param string $filename
     *
     * @return string
     */
    public static function id(string $filename): string
    {
        return hash_file(self::HASH_ALGORITHM, $filename);
    }

    /**
     * Return filesize for passed file
     *
     * @param string $filename
     *
     * @return int
     */
    public static function filesize(string $filename): int
    {
        return \filesize($filename);
    }

    /**
     * Return last modified timestamp for passed file
     *
     * @param string $filename
     *
     * @return int
     */
    public static function filemtime(string $filename): int
    {
        return \filemtime($filename);
    }

    /**
     * Return Entity ID
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set Entity ID
     *
     * @param string $id
     *
     * @return Entity
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Return absolute filename of image file
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Set absolute filename of image file
     *
     * @param string $filename
     *
     * @return Entity
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Return size of image file
     *
     * @return int
     */
    public function getFilesize(): int
    {
        return $this->filesize;
    }

    /**
     * Set size of image file
     *
     * @param int $filesize
     *
     * @return Entity
     */
    public function setFilesize(int $filesize): self
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * Return last modified time of image file
     *
     * @return int
     */
    public function getFilemtime(): int
    {
        return $this->filemtime;
    }

    /**
     * Set last modified time of image file
     *
     * @param int $filemtime
     *
     * @return Entity
     */
    public function setFilemtime(int $filemtime): self
    {
        $this->filemtime = $filemtime;

        return $this;
    }
}
