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
     * Directory in which to store entities
     *
     * @var string
     */
    protected const ENTITY_DIRECTORY = '.image_optimizer_3.0';

    /**
     * Depth of entity directory
     *
     * @var integer
     */
    protected const ENTITY_DIRECTORY_DEPTH = 3;

    /**
     * Length of each sub directory name in entity directory
     *
     * @var integer
     */
    protected const ENTITY_DIRECTORY_LENGTH = 2;
}
