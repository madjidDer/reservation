<?php
declare(strict_types=1);

// Minimal env switch: set APP_ENV=prod to disable display_errors.
$env = getenv('APP_ENV') ?: 'dev';

ini_set('log_errors', '1');

if ($env === 'prod') {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}
