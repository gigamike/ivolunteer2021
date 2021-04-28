<?php

// https://github.com/maxmind/GeoIP2-php
// https://dev.maxmind.com/geoip/geoip2/downloadable/#MaxMind_APIs
// https://dev.maxmind.com/geoip/geoip2/geolite2/#MaxMind_APIs

error_reporting(E_ALL);
ini_set('display_errors', 1);

$basePath = dirname(dirname(__FILE__));
require_once $basePath . '/vendor/autoload.php';
use GeoIp2\Database\Reader;

$reader = new Reader($basePath . "/geoip/GeoLite2-City.mmdb");

if(isset($_GET['ip']) && !empty($_GET['ip'])){
  $ip = $_GET['ip'];
}else{
  $ip = $_SERVER['REMOTE_ADDR'];
}

$record = $reader->city($ip);

echo "Your IP: " . $ip . "<br>";
echo "Your Country Code: " . $record->country->isoCode . "<br>";
echo "Your Country Name: " . $record->country->name . "<br>";
