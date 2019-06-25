
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

        if (!file_exists(__DIR__ . '/../reporter.lock')) {
            touch(__DIR__ . '/../reporter.lock');

            if (! isset($SETTINGS['report']['email'])) {
                $logUtility->log($logUtility::LOG_ERROR, 'Please specify an e-mail address to send the report to.');
                unlink(__DIR__ . '/../reporter.lock');
                exit(1);
            }

            $logLevel = (isset($SETTINGS['report']['log_level']) ? intval($SETTINGS['report']['log_level']) : 0);
            $maxTime = (isset($SETTINGS['report']['max_time']) ? intval($SETTINGS['report']['max_time']) : 0);

            $logRepository = new \Madj2k\TwitterAnalyser\Repository\LogRepository();
            if ($logs = $logRepository->findAllByLevelAndTime($logLevel, (time() - $maxTime))) {

                $mailText = '';

                /** @var \Madj2k\TwitterAnalyser\Model\Log $log */
                foreach ($logs as $log) {
                    $mailText .= date ('Y-m-d H:i:s', $log->getCreateTimestamp()) .
                        ' [LEVEL ' . $log->getLevel() . '] ' . $log->getClass() .
                        ($log->getMethod() ? '->' . $log->getMethod() : '') . ': ' . $log->getComment() . "\n";
                }

                mail($SETTINGS['report']['email'] , 'TwitterAnalyser Status Report', $mailText);
                $logUtility->log($logUtility::LOG_INFO, 'Sent status report.');
            }

            unlink(__DIR__ . '/../reporter.lock');
            $logUtility->log($logUtility::LOG_DEBUG, 'Done.');

        } else {
            $logUtility->log($logUtility::LOG_WARNING, 'Script is already running.');
        }


    } catch (\Exception $e) {
        $logUtility->log($logUtility::LOG_ERROR, $e->getMessage());
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}