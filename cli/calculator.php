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

        if (!file_exists(__DIR__ . '/../calculate.lock')) {
            touch(__DIR__ . '/../calculate.lock');

            $logUtility->log($logUtility::LOG_INFO, 'Calculating interaction-time and counting replies of timeline tweets.');
            $limit = (isset($argv[1]) ? $argv[1] : 100);

            /** @var \Madj2k\TwitterAnalyser\TweetCalculator $tweetCalculator */
            $tweetCalculator = new \Madj2k\TwitterAnalyser\TweetCalculator();
            $tweetCalculator->calculateInteractionTimeAndCountReplies($limit);

            unlink(__DIR__ . '/../calculate.lock');
            $logUtility->log($logUtility::LOG_INFO, 'Finished calculating.');

        } else {
            $logUtility->log($logUtility::LOG_WARNING, 'Script is already running.');
        }


    } catch (\Exception $e) {
        $logUtility->log($logUtility::LOG_ERROR, $e->getMessage());
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}