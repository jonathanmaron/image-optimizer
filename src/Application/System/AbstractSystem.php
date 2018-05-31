<?php
declare(strict_types=1);

namespace Application\System;

use Application\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

abstract class AbstractSystem
{
    /**
     * Total number of seconds an installed executable may take to optimize image
     *
     * @var int
     */
    private const TIMEOUT = 3600;

    /**
     * Return true, if executable is installed. False otherwise.
     *
     * @param string $exec
     *
     * @return bool
     */
    public function isInstalled(string $exec): bool
    {
        if (!is_executable($exec)) {
            $format  = 'The required command "%s" is not installed.';
            $message = sprintf($format, $exec);
            throw new RuntimeException($message);
        }

        return true;
    }

    /**
     * Create an empty temporary file and return its filename
     *
     * @return string
     */
    public function getTempFilename(): string
    {
        $filesystem = new Filesystem();

        $path   = sys_get_temp_dir();
        $prefix = 'image_optimizer_';

        $ret = $filesystem->tempnam($path, $prefix);

        return $ret;
    }

    /**
     * Execute an installed executable to optimize image
     *
     * @param array $command
     *
     * @return bool
     */
    protected function execute(array $command): bool
    {
        $process = new Process($command);
        $process->setTimeout(self::TIMEOUT);
        $process->run();

        /*
        dump('---');
        dump($process->getCommandLine());
        dump($process->getOutput());
        dump($process->getErrorOutput());
        dump($process->getExitCode());
        dump('---');
        */

        return $process->isSuccessful();
    }

    /**
     * Rename a filename
     *
     * @param string $sourceFilename
     * @param string $destinationFilename
     *
     * @return bool
     */
    protected function rename(string $sourceFilename, string $destinationFilename): bool
    {
        $filesystem = new Filesystem();

        $filesystem->rename($sourceFilename, $destinationFilename, true);

        return true;
    }
}
