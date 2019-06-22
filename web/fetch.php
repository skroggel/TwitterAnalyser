
<?php
if (php_sapi_name() != "cli") {
    echo 'This script has to be executed via CLI.';
    exit;
}

// error_reporting(E_ALL);
require_once(__DIR__ . '/../config/settings.php');
require_once(__DIR__ . '/../vendor/autoload.php');

try {
    if (! file_exists(__DIR__ . '/../cli.lock')) {
        touch(__DIR__ . '/../cli.lock');
        $twitter = new \Madj2k\TwitterAnalyser\TwitterAnalyser();
        $twitter->fetchTweets();
        unlink(__DIR__ . '/../cli.lock');
        echo 'Done';
    }else {
        echo 'Script is already running.';
    }

} catch (\Exception $e) {
    echo $e->getMessage();
}