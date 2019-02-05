<?php
declare(strict_types=1);

error_reporting(E_ALL);
set_time_limit(0);

if (!defined('REQUEST_MICROTIME')) {
    define('REQUEST_MICROTIME', microtime(true));
}

$applicationRoot = dirname(__FILE__, 2);

require_once $applicationRoot . '/vendor/autoload.php';

define('APPLICATION_ROOT', $applicationRoot);

locale_set_default('en_US');
