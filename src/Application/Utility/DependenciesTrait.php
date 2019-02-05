<?php
declare(strict_types=1);

namespace Application\Utility;

use Application\Component\Finder\Finder;
use Application\History\History;
use Application\Optimizer\Optimizer;

trait DependenciesTrait
{
    private $optimizer;

    private $history;

    private $finder;

    public function getOptimizer(): Optimizer
    {
        return $this->optimizer;
    }

    public function setOptimizer(Optimizer $optimizer): self
    {
        $this->optimizer = $optimizer;

        return $this;
    }

    public function getHistory(): History
    {
        return $this->history;
    }

    public function setHistory(History $history): self
    {
        $this->history = $history;

        return $this;
    }

    public function getFinder(): Finder
    {
        return $this->finder;
    }

    public function setFinder(Finder $finder): self
    {
        $this->finder = $finder;

        return $this;
    }
}
