<?php
declare(strict_types=1);

namespace Application\System;

interface InterfaceSystem
{
    public function optimize(string $filename): bool;

    public function isInstalled(string $exec): bool;
}

