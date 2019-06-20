
<?php

// error_reporting(E_ALL);
require_once('../config/settings.php');
require_once('../vendor/autoload.php');

$twitter = new \Madj2k\TwitterAnalyser\TwitterAnalyser();
var_dump($twitter->getRateLimitForMethod('search' , 'tweets'));
?>

tests