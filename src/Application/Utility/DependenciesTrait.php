<?php
declare(strict_types=1);

namespace Application\Utility;

use Application\Component\Finder\Finder;
use Application\History\History;
use Application\Optimizer\Optimizer;
use Application\Statistics\Statistics;
use NumberFormatter;

trait DependenciesTrait
{
    protected $finder;

    protected $history;

    protected $optimizer;

    protected $statistics;

    protected $numberFormatter;

    public function getFinder(): Finder
    {
        return $this->finder;
    }

    public function setFinder(Finder $finder): self
    {
        $this->finder = $finder;

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

    public function getOptimizer(): Optimizer
    {
        return $this->optimizer;
    }

    public function setOptimizer(Optimizer $optimizer): self
    {
        $this->optimizer = $optimizer;

        return $this;
    }

    public function getStatistics(): Statistics
    {
        return $this->statistics;
    }

    public function setStatistics(Statistics $statistics): self
    {
        $this->statistics = $statistics;

        return $this;
    }

    public function getNumberFormatter(): NumberFormatter
    {
        return $this->numberFormatter;
    }

    public function setNumberFormatter(NumberFormatter $numberFormatter): self
    {
        $this->numberFormatter = $numberFormatter;

        return $this;
    }
}
