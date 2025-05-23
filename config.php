<?php
function generateFingerprint() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    return hash('sha256', $userAgent . $ip . $acceptLanguage);
}

$ip = $_SERVER['REMOTE_ADDR'];
$blockedIPs = file_get_contents('BLOCKED_IPS.txt');
$blockedIPs = explode("\n", $blockedIPs);
if (in_array($ip, $blockedIPs)) {
    die("Tvoje IP adresa je zablokována.");
}


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


try{
    $dibi = new \Dibi\Connection(DB_CONFIG);
}catch(Exception $e){
    var_dump($e->getMessage());
    die();
}
