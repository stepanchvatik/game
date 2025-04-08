<?php

session_start();
date_default_timezone_set('Europe/Prague');
require "./vendor/autoload.php";

$dbConfig = [
    'driver' => 'mysqli',
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'pass',
    'database' => 'game'
];
define('DB_CONFIG',$dbConfig);