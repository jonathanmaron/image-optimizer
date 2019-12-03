<?php
declare(strict_types=1);

// Turn up error reporting to the maximum.

error_reporting(E_ALL);

// Set the microtime of the request.

if (!defined('REQUEST_MICROTIME')) {
    define('REQUEST_MICROTIME', microtime(true));
}

// Configure Xdebug.

ini_set('xdebug.var_display_max_depth', '999');

// Allow a Command to run forever.

set_time_limit(0);

// Application root path.
// All paths in the application must be relevant to this path.

define('APPLICATION_ROOT', __DIR__);

// In the case of a non-interactive shell (for example, when calling from a cronjob), Command::interact() is not
// executed by default. By forcing an interactive shell, Command::interact() is always executed, which is required
// to validate and set command line arguments.

putenv('SHELL_INTERACTIVE=true');

// For number formatting, set the default locale.

locale_set_default('en_US');
