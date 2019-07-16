
<?php
if (php_sapi_name() != "cli") {
    echo 'This script has to be executed via CLI.';
    exit;
}

// error_reporting(E_ALL);
require_once(__DIR__ . '/../config/settings.php');
require_once(__DIR__ . '/../vendor/autoload.php');

try {

    /** @var \Madj2k\TwitterAnalyser\Utility\LogUtility $logUtility */
    $logUtility = new \Madj2k\TwitterAnalyser\Utility\LogUtility(true);

    try {

        if (!file_exists(__DIR__ . '/../fetch.lock')) {
            touch(__DIR__ . '/../fetch.lock');

            $logUtility->log($logUtility::LOG_INFO, 'Fetching tweets.');

            /** @var \Madj2k\TwitterAnalyser\TwitterAnalyser $twitter */
            $twitter = new \Madj2k\TwitterAnalyser\TwitterAnalyser();
            $twitter->fetchTweets();

            unlink(__DIR__ . '/../fetch.lock');
            $logUtility->log($logUtility::LOG_INFO, 'Finished fetching tweets.');

        } else {
            $logUtility->log($logUtility::LOG_WARNING, 'Script is already running.');
        }


    } catch (\Exception $e) {
        $logUtility->log($logUtility::LOG_ERROR, $e->getMessage());
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}