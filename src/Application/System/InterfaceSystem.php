<?php

namespace Application\System;

interface InterfaceSystem
{
    public function optimize($filename);

    public function isInstalled($exec);
}

