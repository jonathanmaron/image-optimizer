<?php
declare(strict_types=1);

namespace Application\System;

use Application\Exception\RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

abstract class AbstractSystem
{
    private const TIMEOUT = 3600;

    public function isInstalled(string $exec): bool
    {
        if (!is_executable($exec)) {
            $format  = 'The required command "%s" is not installed.';
            $message = sprintf($format, $exec);
            throw new RuntimeException($message);
        }

        return true;
    }

    public function getTempFilename(): string
    {
        $filesystem = new Filesystem();

        $path   = sys_get_temp_dir();
        $prefix = 'image_optimizer_';

        $ret = $filesystem->tempnam($path, $prefix);

        return $ret;
    }

    protected function execute(array $command): bool
    {
        $ret = false;

        $process = new Process($command);
        $process->setTimeout(self::TIMEOUT);
        $process->run();

        if ($process->isSuccessful()) {
            $ret = true;
        }

        /*
        dump('---');
        dump($process->getCommandLine());
        dump($process->getOutput());
        dump($process->getErrorOutput());
        dump($process->getExitCode());
        dump('---');
        */

        return $ret;
    }

    protected function rename(string $sourceFilename, string $destinationFilename): bool
    {
        $filesystem = new Filesystem();

        $filesystem->rename($sourceFilename, $destinationFilename, true);

        return true;
    }
}
