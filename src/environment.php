<?php

use Dotenv\Dotenv;

// load environment variables from .env file in api root directory
$dotenv = Dotenv::create(dirname(__DIR__));
$dotenv->load();

// these variables are required
$dotenv->required([
    'DISPLAY_ERROR_DETAILS',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
]);