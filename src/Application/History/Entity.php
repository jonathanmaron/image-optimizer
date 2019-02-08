<?php
declare(strict_types=1);

namespace Application\History;

use SplFileInfo;

class Entity
{
    /**
     * Algorithm to create hash of file contents
     *
     * @var string
     */
    protected const HASH_ALGORITHM = 'sha512';

    public $id;

    public $filename;

    public $perms;

    public $owner;

    public $group;

    public $size;

    public $inode;

    public $extension;

    public $aTime;

    public $mTime;

    public $cTime;

    public function __construct(string $id, string $filename)
    {
        $filename = realpath($filename);

        $this->setId($id);
        $this->setFilename($filename);

        $splFileInfo = new SplFileInfo($filename);

        $this->setPerms($splFileInfo->getPerms());
        $this->setOwner($splFileInfo->getOwner());
        $this->setGroup($splFileInfo->getGroup());

        $this->setSize($splFileInfo->getSize());

        $this->setInode($splFileInfo->getInode());

        $this->setExtension($splFileInfo->getExtension());

        $this->setATime($splFileInfo->getATime());
        $this->setMTime($splFileInfo->getMTime());
        $this->setCTime($splFileInfo->getCTime());

        unset($splFileInfo);
    }

    public static function createId(string $filename): string
    {
        return hash_file(self::HASH_ALGORITHM, $filename);
    }

    /**
     * @return mixed
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Entity
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return bool|string
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param bool|string $filename
     *
     * @return Entity
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return int
     */
    public function getPerms(): int
    {
        return $this->perms;
    }

    /**
     * @param int $perms
     *
     * @return Entity
     */
    public function setPerms(int $perms): self
    {
        $this->perms = $perms;

        return $this;
    }

    /**
     * @return int
     */
    public function getOwner(): int
    {
        return $this->owner;
    }

    /**
     * @param int $owner
     *
     * @return Entity
     */
    public function setOwner(int $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroup(): int
    {
        return $this->group;
    }

    /**
     * @param int $group
     *
     * @return Entity
     */
    public function setGroup(int $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return Entity
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return int
     */
    public function getInode(): int
    {
        return $this->inode;
    }

    /**
     * @param int $inode
     *
     * @return Entity
     */
    public function setInode(int $inode): self
    {
        $this->inode = $inode;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return Entity
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return int
     */
    public function getATime(): int
    {
        return $this->aTime;
    }

    /**
     * @param int $aTime
     *
     * @return Entity
     */
    public function setATime(int $aTime): self
    {
        $this->aTime = $aTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getMTime(): int
    {
        return $this->mTime;
    }

    /**
     * @param int $mTime
     *
     * @return Entity
     */
    public function setMTime(int $mTime): self
    {
        $this->mTime = $mTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getCTime(): int
    {
        return $this->cTime;
    }

    /**
     * @param int $cTime
     *
     * @return Entity
     */
    public function setCTime(int $cTime): self
    {
        $this->cTime = $cTime;

        return $this;
    }
}
