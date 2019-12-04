<?php
declare(strict_types=1);

namespace Application\Statistics;

class Statistics extends AbstractStatistics
{
    /**
     * Get skipped files counter
     *
     * @return int
     */
    public function getSkipped(): int
    {
        return $this->skipped;
    }

    /**
     * Return skipped files counter
     *
     * @param int $skipped
     *
     * @return Statistics
     */
    public function setSkipped(int $skipped): self
    {
        $this->skipped = $skipped;

        return $this;
    }

    /**
     * Increment skipped files counter
     *
     * @return Statistics
     */
    public function incrementSkipped(): self
    {
        $skipped = $this->getSkipped();
        $skipped = $skipped + 1;

        $this->setSkipped($skipped);

        return $this;
    }

    /**
     * Get optimized files counter
     *
     * @return int
     */
    public function getOptimized(): int
    {
        return $this->optimized;
    }

    /**
     * Return optimized files counter
     *
     * @param int $optimized
     *
     * @return Statistics
     */
    public function setOptimized(int $optimized): self
    {
        $this->optimized = $optimized;

        return $this;
    }

    /**
     * Increment optimized files counter
     *
     * @return Statistics
     */
    public function incrementOptimized(): self
    {
        $optimized = $this->getOptimized();
        $optimized = $optimized + 1;

        $this->setOptimized($optimized);

        return $this;
    }

    /**
     * Get indexed files counter
     *
     * @return int
     */
    public function getIndexed(): int
    {
        return $this->indexed;
    }

    /**
     * Return indexed files counter
     *
     * @param int $indexed
     *
     * @return Statistics
     */
    public function setIndexed(int $indexed): self
    {
        $this->indexed = $indexed;

        return $this;
    }

    /**
     * Increment indexed files counter
     *
     * @return Statistics
     */
    public function incrementIndexed(): self
    {
        $indexed = $this->getIndexed();
        $indexed = $indexed + 1;

        $this->setIndexed($indexed);

        return $this;
    }

    /**
     * Get total files counter
     *
     * @return int
     */
    public function getTotal(): int
    {
        $skipped   = $this->getSkipped();
        $optimized = $this->getOptimized();
        $indexed   = $this->getIndexed();

        return $skipped + $optimized + $indexed;
    }

    /**
     * Get number of bytes before optimization
     *
     * @return int
     */
    public function getBytesIn(): int
    {
        return $this->bytesIn;
    }

    /**
     * Return number of bytes before optimization
     *
     * @param int $bytesIn
     *
     * @return Statistics
     */
    public function setBytesIn(int $bytesIn): self
    {
        $totalBytesIn = $this->getTotalBytesIn();
        $totalBytesIn = $totalBytesIn + $bytesIn;
        $this->setTotalBytesIn($totalBytesIn);

        $this->bytesIn = $bytesIn;

        return $this;
    }

    /**
     * Reset number of bytes before optimization
     *
     * @return Statistics
     */
    public function resetBytesIn(): self
    {
        $this->setBytesIn(0);

        return $this;
    }

    /**
     * Get number of bytes after optimization
     *
     * @return int
     */
    public function getBytesOut(): int
    {
        return $this->bytesOut;
    }

    /**
     * Return number of bytes after optimization
     *
     * @param int $bytesOut
     *
     * @return Statistics
     */
    public function setBytesOut(int $bytesOut): self
    {
        $totalBytesOut = $this->getTotalBytesOut();
        $totalBytesOut = $totalBytesOut + $bytesOut;
        $this->setTotalBytesOut($totalBytesOut);

        $this->bytesOut = $bytesOut;

        return $this;
    }

    /**
     * Reset number of bytes after optimization
     *
     * @return Statistics
     */
    public function resetBytesOut(): self
    {
        $this->setBytesIn(0);

        return $this;
    }

    /**
     * Get difference between bytes in and out
     *
     * @return int
     */
    public function getBytesDifference(): int
    {
        $bytesOut = $this->getBytesOut();
        $bytesIn  = $this->getBytesIn();

        return $bytesIn - $bytesOut;
    }

    /**
     * Get difference between bytes in and out as a percentage
     *
     * @return float
     */
    public function getBytesDifferenceAsPercentage(): float
    {
        $bytesOut = $this->getBytesOut();
        $bytesIn  = $this->getBytesIn();

        return $this->calculatePercentage($bytesOut, $bytesIn);
    }

    /**
     * Get total number of bytes before optimization
     *
     * @return int
     */
    public function getTotalBytesIn(): int
    {
        return $this->totalBytesIn;
    }

    /**
     * Return total number of bytes before optimization
     *
     * @param int $totalBytesIn
     *
     * @return Statistics
     */
    public function setTotalBytesIn(int $totalBytesIn): self
    {
        $this->totalBytesIn = $totalBytesIn;

        return $this;
    }

    /**
     * Get total number of bytes after optimization
     *
     * @return int
     */
    public function getTotalBytesOut(): int
    {
        return $this->totalBytesOut;
    }

    /**
     * Return total number of bytes after optimization
     *
     * @param int $totalBytesOut
     *
     * @return Statistics
     */
    public function setTotalBytesOut(int $totalBytesOut): self
    {
        $this->totalBytesOut = $totalBytesOut;

        return $this;
    }

    /**
     * Get difference between total bytes in and out
     *
     * @return int
     */
    public function getTotalBytesDifference(): int
    {
        $bytesOut = $this->getTotalBytesOut();
        $bytesIn  = $this->getTotalBytesIn();

        return $bytesIn - $bytesOut;
    }

    /**
     * Get difference between total bytes in and out as a percentage
     *
     * @return float
     */
    public function getTotalBytesDifferenceAsPercentage(): float
    {
        $bytesOut = $this->getTotalBytesOut();
        $bytesIn  = $this->getTotalBytesIn();

        return $this->calculatePercentage($bytesOut, $bytesIn);
    }
}
