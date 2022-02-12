<?php
declare(strict_types=1);

namespace Application\Statistics;

abstract class AbstractStatistics
{
    /**
     * Skipped files counter
     *
     * @var int
     */
    protected int $skipped = 0;

    /**
     * Optimized files counter
     *
     * @var int
     */
    protected int $optimized = 0;

    /**
     * Indexed files counter
     *
     * @var int
     */
    protected int $indexed = 0;

    /**
     * Number of bytes before optimization
     *
     * @var int
     */
    protected int $bytesIn = 0;

    /**
     * Number of bytes after optimization
     *
     * @var int
     */
    protected int $bytesOut = 0;

    /**
     * Total number of bytes before optimization
     *
     * @var int
     */
    protected int $totalBytesIn = 0;

    /**
     * Total number of bytes after optimization
     *
     * @var int
     */
    protected int $totalBytesOut = 0;

    /**
     * Calculate percentage
     *
     * @param int $x
     * @param int $y
     *
     * @return float
     */
    protected function calculatePercentage(int $x, int $y): float
    {
        if ($x === 0 || $y === 0) {
            return 0.0;
        }

        return 100 * (1 - $x / $y);
    }
}
