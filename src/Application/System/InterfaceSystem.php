<?php
declare(strict_types=1);

namespace Application\System;

interface InterfaceSystem
{
    public function isInstalled(string $exec): bool;

    public function optimize(string $filename): bool;
}
