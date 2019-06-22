
<?php

// error_reporting(E_ALL);
require_once('../config/settings.php');
require_once('../vendor/autoload.php');

//try {
    $twitter = new \Madj2k\TwitterAnalyser\TwitterAnalyser();
    $twitter->fetchTweets();

//} catch (\Exception $e) {
    //echo $e->getMessage();
//}