<?php
declare(strict_types=1);

namespace Application\Utility;

trait ConfigTrait
{
    protected $config = [];

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }
}
