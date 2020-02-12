<?php

use Dotenv\Dotenv;

// load environment variables from .env file in api root directory
$dotenv = Dotenv::create(dirname(__DIR__));
$dotenv->load();

// these variables are required
$dotenv->required([
    'API_MODE',
    'API_DISPLAY_ERROR_DETAILS',
    'API_LOG_ERRORS',
    'API_DB_CONNECTION',
    'API_DB_HOST',
    'API_DB_PORT',
    'API_DB_DATABASE',
    'API_DB_USERNAME',
    'API_DB_PASSWORD',
]);

// set constants based on the environment we want
// should be 'dev' or 'prod'
$mode = getenv('API_MODE');
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