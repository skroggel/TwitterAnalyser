
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

        if (! file_exists(__DIR__ . '/../import.lock')) {
            touch(__DIR__ . '/../import.lock');

            $params = [
                'url' => (isset($argv[1]) ? $argv[1] : 'https://www.bundestag.de/ajax/filterlist/de/abgeordnete/525246-525246/h_e3c112579919ef960d06dbb9d0d44b67?limit=9999&view=BTBiographyList'),
                'baseUrl' => (isset($argv[2]) ? $argv[2] : 'https://www.bundestag.de'),
                'regExpDetailLinks' => (isset($argv[3]) ? $argv[3] : '#<a[^>]+href="(/abgeordnete/biografien/[^"]+)"[^>]+>#'),
                'regExpTwitterLinks' => (isset($argv[4]) ? $argv[4] : '#<a[^>]+href="(https://(www.)?twitter.com/[^"]+)"[^>]+>#'),
                'maxLinksLimit' => (isset($argv[5]) ? $argv[5] : 10),
                'checkInterval'=> (isset($argv[6]) ? $argv[6] : 604800),
            ];
    
            if (! $params['url']) {
                $logUtility->log($logUtility::LOG_ERROR, 'Please specify the url to fetch detail pages from.');
                unlink(__DIR__ . '/../import.lock');
                exit(1);
            }
    
            if (! $params['baseUrl']) {
                $logUtility->log($logUtility::LOG_ERROR, 'Please specify the base-url.');
                unlink(__DIR__ . '/../import.lock');
                exit(1);
            }
    
            if (! $params['regExpDetailLinks']) {
                $logUtility->log($logUtility::LOG_ERROR, 'Please specify the regular expression for extracting the links to the detail pages.');
                unlink(__DIR__ . '/../import.lock');
                exit(1);
            }
    
            if (! $params['regExpTwitterLinks']) {
                $logUtility->log($logUtility::LOG_ERROR, 'Please specify the regular expression for extracting the Twitter links on the detail pages.');
                unlink(__DIR__ . '/../import.lock');
                exit(1);
            }


            /** @var \Madj2k\TwitterAnalyser\TwitterAccountFinder $accountFinder */
            $accountFinder = new \Madj2k\TwitterAnalyser\TwitterAccountFinder();

            if (
                (! file_exists(__DIR__ . '/../import-details.lock'))
                || (
                    (file_exists(__DIR__ . '/../import-details.lock'))
                    && (filemtime(__DIR__ . '/../import-details.lock') < (time() - $params['checkInterval']))
                )
            ) {
                touch (__DIR__ . '/../import-details.lock');
                $logUtility->log($logUtility::LOG_DEBUG, 'Checking for detail links.');
                if ($importedLinks = $accountFinder->fetchDetailLinksFromWebList($params['url'], $params['baseUrl'], $params['regExpDetailLinks'])) {
                    $logUtility->log($logUtility::LOG_INFO, sprintf('Imported %s detail links for further processing on the given url.', $importedLinks));
                } else {
                    $logUtility->log($logUtility::LOG_INFO, 'No detail links found on the given url or all detail links found have already been imported for further processing.');
                }
            } else {
                $logUtility->log($logUtility::LOG_INFO, sprintf('Check for detail links skipped. Next check interval reached in %s seconds.', ((filemtime(__DIR__ . '/../import-details.lock') +  $params['checkInterval']) - time())));
            }
            

            $logUtility->log($logUtility::LOG_DEBUG, sprintf('Checking for account names on imported detail pages, using maxLinksLimit=%s', $params['maxLinksLimit']));
            if ($importedAccounts = $accountFinder->fetchAccountNamesFromDetailLinks($params['regExpTwitterLinks'], $params['maxLinksLimit'])) {
                $logUtility->log($logUtility::LOG_INFO, sprintf('Imported %s Twitter accounts based on details links on the given url.', $importedAccounts));
            } else {
                $logUtility->log($logUtility::LOG_INFO, 'No Twitter accounts imported based on the given url.');
            }

            unlink(__DIR__ . '/../import.lock');
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