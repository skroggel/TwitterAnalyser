<?php
namespace Madj2k\TwitterAnalyser;
use \Madj2k\TwitterAnalyser\Model\Account;
use \Madj2k\TwitterAnalyser\Model\Url;


/**
 * TwitterAnalyser
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TwitterAccountFinder
{

    /**
     * @var \TwitterAPIExchange
     */
    protected $twitter;

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\AccountRepository
     */
    protected $accountRepository;

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\UrlRepository
     */
    protected $urlRepository;

    /**
     * @var \Madj2k\TwitterAnalyser\Utility\RateLimitUtility
     */
    protected $rateLimitUtility;

    /**
     * @var \Madj2k\TwitterAnalyser\Utility\LogUtility
     */
    protected $logUtility;

    /**
     * @var array
     */
    protected $settings;


    /**
     * Constructor
     *
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     * @throws \ReflectionException
     */
    public function __construct()
    {

        global $SETTINGS;
        $this->settings = &$SETTINGS;

        // set defaults
        $this->accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
        $this->urlRepository = new \Madj2k\TwitterAnalyser\Repository\UrlRepository();

        $this->rateLimitUtility = new \Madj2k\TwitterAnalyser\Utility\RateLimitUtility();
        $this->logUtility = new  \Madj2k\TwitterAnalyser\Utility\LogUtility();

        // init Twitter API
        $this->twitter = new \TwitterAPIExchange($this->settings['twitter']);
    }



    /**
     * Fetch detail links to process from a link list as webpage
     *
     * @param string $url
     * @param string $baseUrl
     * @param string $regExpDetailLinks
     * @return int|false
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function fetchDetailLinksFromWebList(
        $url = 'https://www.bundestag.de/ajax/filterlist/de/abgeordnete/525246-525246/h_e3c112579919ef960d06dbb9d0d44b67?limit=9999&view=BTBiographyList',
        $baseUrl = 'https://www.bundestag.de',
        $regExpDetailLinks = '#<a[^>]+href="(/abgeordnete/biografien/[^"]+)"[^>]+>#'
    ) {

        if ($listPage = $this->fetchData($url)) {
            $matches = [];

            if (
                (preg_match_all($regExpDetailLinks , $listPage, $matches))
                && ($detailLinks = $matches[1])
            ){

                $importCount = 0;
                foreach ($detailLinks as $link) {
                    if (! $this->urlRepository->findOneByUrlAndProcessed($link, false)) {

                        /** @var \Madj2k\TwitterAnalyser\Model\Url $urlObject */
                        $urlObject = new Url ();
                        $urlObject->setBaseUrl($baseUrl)
                            ->setUrl($link);

                        $this->urlRepository->insert($urlObject);
                        $importCount++;
                        $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Fetched link %s from %s.', $urlObject->getBaseUrl() . $urlObject->getUrl(), $url));
                    } else {
                        $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Link %s from %s already imported and unprocessed.', $link, $url));
                    }
                }
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Fetched %s links from %s.', $importCount, $url));
                return $importCount;
            }
        } else {
            $this->logUtility->log($this->logUtility::LOG_WARNING, sprintf('Can not load url %s.', $url));
        }

        return false;
    }


    /**
     * Get twitter accounts using a list of peoples with detail links
     *
     * @param string $regExpTwitterLinks
     * @param string $regExpNames
     * @param int $limit
     * @return int|false
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function fetchAccountNamesFromDetailLinks ($regExpTwitterLinks = '#<a[^>]+href="http[s]?://[^\.]*\.?twitter\.com/(@?(\w){1,15})[^"]*"[^>]+>#', $regExpNames = '#<div class="[^"]+ bt-biografie-name">[^<]*<h3>([^<]+)</h3>#', $limit = 10)
    {

        // Get rate limit for user search API and fetch an according amount of urls
        if ($rateLimit = $this->rateLimitUtility->getRateLimitForMethod('users', 'search')) {

            $importCount = 0;
            $limit = ($rateLimit > intval($this->settings['max_fetch'])) ? intval($this->settings['max_fetch']) : $rateLimit;
            if (
                ($limit > 0)
                && ($urlList = $this->urlRepository->findByProcessedSortedByCreateTimestamp(false, $limit))
            ){

                /** @var \Madj2k\TwitterAnalyser\Model\Url $url */
                foreach ($urlList as $url) {

                    $matches = [];
                    try {
                        if ($detailPage = $this->fetchData($url->getBaseUrl() . $url->getUrl())) {

                            // 1. Check for Twitter-Links
                            if (
                                (preg_match($regExpTwitterLinks, $detailPage, $matches))
                                && ($userName = $matches[1])
                            ) {

                                /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                                $account = new Account();
                                $account->setUserName($userName);

                                /** @var \Madj2k\TwitterAnalyser\Model\Account $databaseAccount */
                                $databaseAccount = $this->accountRepository->findOneByUserName($account->getUserName(), false);
                                if (! $databaseAccount) {
                                    $this->accountRepository->insert($account);
                                    $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Inserted new account %s found in url with id = %s.', $account->getUserName(), $url->getUid()));
                                    $importCount++;

                                } else {

                                    // if it is a secondary account or a suggestion it now becomes a primary one!
                                    if (
                                        ($databaseAccount->getIsSecondary())
                                        || ($databaseAccount->getIsSuggestion())
                                    ){
                                        $databaseAccount->setIsSecondary(false);
                                        $databaseAccount->setIsSuggestion(false);
                                        $databaseAccount->setSuggestionForName('');

                                        $this->accountRepository->update($databaseAccount);
                                        $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Account %s found in url with id = %s already exists as secondary or suggestion account. Set to primary account now.', $account->getUserName(), $url->getUid()));

                                    } else {
                                        $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Account %s found in url with id = %s already exists.', $account->getUserName(), $url->getUid()));
                                    }
                                }

                            // 2. Search via API by name
                            } else if (
                                (preg_match($regExpNames, $detailPage, $matches))
                                && ($name = trim($matches[1]))
                            ) {

                                // remove comma appendix
                                if (false !== $pos = strpos($name, ',')) {
                                    $name = trim(substr($name, 0, $pos));
                                }

                                // remove title prefix
                                if (false !== $pos = strpos($name, 'Dr.')) {
                                    $name = trim(substr($name, strlen('Dr.')));
                                }

                                // prepare API call
                                $apiUrl = 'https://api.twitter.com/1.1/users/search.json';
                                $constraints = [
                                    'q=' . $name,
                                    'include_entities=false',
                                    'count=20', // maximum value
                                    'page=1',
                                ];


                                $foundAccounts = json_decode(
                                    $this->twitter->setGetfield('?' . implode('&', $constraints))
                                        ->buildOauth($apiUrl, 'GET')
                                        ->performRequest()
                                );

                                $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Searched possible accounts for name "%s".', $name), $apiUrl . $this->twitter->getGetfield());

                                if (
                                    (is_array($foundAccounts))
                                    && (count($foundAccounts) > 0)
                                ){

                                    foreach ($foundAccounts as $foundAccount) {

                                        /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                                        $account = new Account($foundAccount);
                                        $account->setIsSuggestion(true)
                                            ->setSuggestionForName($name);

                                        // only take verified accounts as suggestions
                                        if (! $account->getVerified()) {
                                            continue;
                                        }

                                        /** @var \Madj2k\TwitterAnalyser\Model\Account $databaseAccount */
                                        $databaseAccount = $this->accountRepository->findOneByUserName($account->getUserName(), false);
                                        if (! $databaseAccount) {
                                            $this->accountRepository->insert($account);
                                            $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Inserted new account %s as suggestion found in url with id = %s.', $account->getUserName(), $url->getUid()));
                                            $importCount++;

                                        } else {

                                            // if it is a secondary account we mark it as suggestion!
                                            if ($databaseAccount->getIsSecondary()) {
                                                $databaseAccount->setIsSuggestion(true)
                                                    ->setSuggestionForName($name);
                                                $this->accountRepository->update($databaseAccount);
                                                $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Account %s found as suggestion in url with id = %s already exists as secondary account. Marked as suggestion.', $account->getUserName(), $url->getUid()));

                                            } else {
                                                $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Account %s found as suggestion in url with id = %s already exists.', $account->getUserName(), $url->getUid()));
                                            }
                                        }
                                    }

                                    $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Found %s possible account(s) for name "%s".', count($foundAccounts), $name));

                                } else {
                                    $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('No possible accounts for name "%s" found.', $name));
                                }
                            }
                        }

                    } catch (\Throwable $e) {
                        $this->logUtility->log($this->logUtility::LOG_ERROR, $e->getMessage());
                    }

                    $url->setProcessed(true);
                    $this->urlRepository->update($url);
                    $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Processed url with id = %s.', $url->getUid()));

                    sleep(1);
                }

                $remainingRateLimit = $this->rateLimitUtility->setRateLimitForMethod('users', 'search', count($urlList));
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Imported %s account(s) via %s processed urls. Remaining rate limit is %s.', $importCount, count($urlList), $remainingRateLimit));
            }

            return $importCount;
        }

        return false;
    }


    /**
     * Fetch data from url
     *
     * @param string $url
     * @return string|false
     */
    protected function fetchData($url)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        return preg_replace('#[[:cntrl:]]#', '', $data);
    }


}