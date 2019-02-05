<?php
declare(strict_types=1);

error_reporting(E_ALL);
set_time_limit(0);

if (!defined('REQUEST_MICROTIME')) {
    define('REQUEST_MICROTIME', microtime(true));
}

define('APPLICATION_ROOT', dirname(__FILE__, 2));

locale_set_default('en_US');
