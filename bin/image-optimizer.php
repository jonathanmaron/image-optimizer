#!/usr/bin/env php
<?php

set_time_limit(0);

require_once __DIR__ . '/../vendor/autoload.php';

use Application\Component\Console\Application;

$application = new Application();
$application->run();