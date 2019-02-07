<?php
declare(strict_types=1);

namespace Application\History;

abstract class AbstractHistory
{
    /**
     * Algorithm to create hashes
     *
     * @var string
     */
    protected const HASH_ALGORITHM = 'sha256';

    /**
     * Directory in which to store hashes
     *
     * @var string
     */
    protected const HASH_DIRECTORY = '.image_optimizer';

    /**
     * Depth of hash directory
     *
     * @var integer
     */
    protected const HASH_DIRECTORY_DEPTH = 3;

    /**
     * Length of each sub directory name in hash directory
     *
     * @var integer
     */
    protected const HASH_DIRECTORY_LENGTH = 2;
}
