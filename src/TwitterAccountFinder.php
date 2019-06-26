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
     * @var string
     */
    protected $url;

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\AccountRepository
     */
    protected $accountRepository;

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\UrlRepository
     */
    protected $urlRepository;

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

        $this->logUtility = new  \Madj2k\TwitterAnalyser\Utility\LogUtility();
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
                        $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Fetched links %s from %s.', $urlObject->getBaseUrl() . $urlObject->getUrl(), $url));
                    } else {
                        $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Link %s from %s already imported and unprocessed.', $link, $url));
                    }
                }
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Fetched %s links from %s.', $importCount, $url));
                return $importCount;
            }
        } else {
            $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Can not load url %s.', $url));
        }

        return false;
    }


    /**
     * Get twitter accounts using a list of peoples with detail links
     *
     * @param string $regExpTwitterLinks
     * @param int $limit
     * @return int|false
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function fetchAccountNamesFromDetailLinks ($regExpTwitterLinks = '#<a[^>]+href="(https://(www.)?twitter.com/[^"]+)"[^>]+>#', $limit = 10)
    {
        if ($urlList = $this->urlRepository->findByProcessedSortedByCreateTimestamp(false, $limit)) {

            /** @var \Madj2k\TwitterAnalyser\Model\Url $url */
            $importCount = 0;
            foreach ($urlList as $url) {

                $matches = [];
                if (
                    ($detailPage = $this->fetchData($url->getBaseUrl() . $url->getUrl()))
                    && (preg_match($regExpTwitterLinks, $detailPage, $matches))
                    && ($twitterLink = $matches[1])
                ) {

                    $userName = str_replace('/', '', parse_url($twitterLink, PHP_URL_PATH));

                    /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                    $account = new Account();
                    $account->setUserName($userName);

                    if (! $this->accountRepository->findOneByUserName($userName)) {
                        $this->accountRepository->insert($account);
                        $importCount++;
                        $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Inserted new account %s found in url with id = %s.', $userName, $url->getUid()));
                    } else {
                        $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Account %s found in url with id = %s. already exists.', $userName, $url->getUid()));
                    }
                }

                $url->setProcessed(true);
                $this->urlRepository->update($url);
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Processed url with id = %s.', $url->getUid()));

                sleep(1);
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

        return $data;
    }


}