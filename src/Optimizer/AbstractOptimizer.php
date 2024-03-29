<?php
declare(strict_types=1);

namespace Application\Optimizer;

use Application\Utility\ConfigTrait;

abstract class AbstractOptimizer
{
    use ConfigTrait;

    /**
     * PNG image filename extension
     *
     * @var string
     */
    public const EXTENSION_PNG = 'png';

    /**
     * JPG image filename extension
     *
     * @var string
     */
    public const EXTENSION_JPG = 'jpg';

    /**
     * JPEG image filename extension
     *
     * @var string
     */
    public const EXTENSION_JPEG = 'jpeg';

    /**
     * GIF image filename extension
     *
     * @var string
     */
    public const EXTENSION_GIF = 'gif';

    /**
     * Return true is className is active
     *
     * @param string $className
     *
     * @return bool
     */
    protected function isActive(string $className): bool
    {
        $config = $this->getConfig();

        return $config['system'][$className]['active'] ?? false;
    }
}
