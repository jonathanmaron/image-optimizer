<?php
declare(strict_types=1);

namespace Application\Utility;

trait ArgumentsTrait
{
    protected string $path = '';

    protected bool $indexOnly = false;

    protected bool $force = false;

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getIndexOnly(): bool
    {
        return $this->indexOnly;
    }

    public function setIndexOnly(bool $indexOnly): self
    {
        $this->indexOnly = $indexOnly;

        return $this;
    }

    public function getForce(): bool
    {
        return $this->force;
    }

    public function setForce(bool $force): self
    {
        $this->force = $force;

        return $this;
    }
}
