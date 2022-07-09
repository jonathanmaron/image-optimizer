<?php
declare(strict_types=1);

namespace Application\History;

abstract class AbstractHistory
{
    /**
     * Algorithm to create hash of filenames
     *
     * @var string
     */
    protected const HASH_ALGORITHM = 'sha256';

    /**
     * Directory in which to store Entities
     *
     * @var string
     */
    protected const ENTITY_DIRECTORY = '.image_optimizer_3.0';

    /**
     * Depth of Entity directory
     *
     * @var integer
     */
    protected const ENTITY_DIRECTORY_DEPTH = 3;

    /**
     * Length of each subdirectory name in Entity directory
     *
     * @var integer
     */
    protected const ENTITY_DIRECTORY_LENGTH = 2;
}
