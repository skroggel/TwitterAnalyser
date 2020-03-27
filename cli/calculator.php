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
            if (!file_exists(__DIR__ . '/../calculate-finished.lock')) {

                touch(__DIR__ . '/../calculate.lock');

                $limit = (isset($argv[1]) ? $argv[1] : 100);
                $logUtility->log($logUtility::LOG_INFO, sprintf('Calculating interaction-time and counting replies of timeline tweets with limit %s.', $limit));

                /** @var \Madj2k\TwitterAnalyser\TweetCalculator $tweetCalculator */
                $tweetCalculator = new \Madj2k\TwitterAnalyser\TweetCalculator();
                $count = $tweetCalculator->calculateInteractionTimeAndCountReplies($limit);

                unlink(__DIR__ . '/../calculate.lock');
                $logUtility->log($logUtility::LOG_INFO, sprintf('Finished calculating for %s tweets.', $count));
                if ($count < 1) {
                    touch(__DIR__ . '/../calculate-finished.lock');
                }

            } else {
                $logUtility->log($logUtility::LOG_INFO, 'Calculation finished.');
            }

        } else {
            $logUtility->log($logUtility::LOG_WARNING, 'Script is already running.');
        }

    } catch (\Exception $e) {
        $logUtility->log($logUtility::LOG_ERROR, $e->getMessage());
    }

} catch (\Exception $e) {
    echo $e->getMessage();
}