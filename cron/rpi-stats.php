<?php
require __DIR__ . "/../vendor/autoload.php";
$rpi = new App\RPI();
$rpi->store_stats();
