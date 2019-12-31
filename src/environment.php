<?php

use Dotenv\Dotenv;

// load environment variables from .env file in api root directory
$dotenv = Dotenv::create(dirname(__DIR__));
$dotenv->load();

// these variables are required
$dotenv->required([
    'MODE',
    'DISPLAY_ERROR_DETAILS',
    'LOG_ERRORS',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
]);

// set constants based on the environment we want
// should be 'dev' or 'prod'
$mode = getenv('MODE');
$devMode = true;
$prodMode = false;
if ('dev' != $mode) {
    $mode = 'prod';
    $devMode = false;
    $prodMode = true;
}
define("MODE", $mode);
define("DEV_MODE", $devMode);
define("PROD_MODE", $prodMode);