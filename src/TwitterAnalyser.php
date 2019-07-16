<?php
namespace Madj2k\TwitterAnalyser;
use \Madj2k\TwitterAnalyser\Model\RateLimit;


/**
 * TwitterAnalyser
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TwitterAnalyser
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
     * @var \Madj2k\TwitterAnalyser\Utility\PaginationUtility
     */
    protected $paginationUtility;

    /**
     * @var \Madj2k\TwitterAnalyser\Utility\TweetImportUtility
     */
    protected $tweetImportUtility;

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

        $this->paginationUtility = new  \Madj2k\TwitterAnalyser\Utility\PaginationUtility();
        $this->tweetImportUtility = new \Madj2k\TwitterAnalyser\Utility\TweetImportUtility();
        $this->rateLimitUtility = new \Madj2k\TwitterAnalyser\Utility\RateLimitUtility();
        $this->logUtility = new  \Madj2k\TwitterAnalyser\Utility\LogUtility();

        // init Twitter API
        $this->twitter = new \TwitterAPIExchange($this->settings['twitter']);
    }



    /**
     * Get timeline tweets of user
     *
     * @return void
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function fetchTweets()
    {
        
        // 1.) Get rate limit for timeline API and fetch an according amount of accounts for that
        if ($rateLimit = $this->rateLimitUtility->getRateLimitForMethod('statuses', 'user_timeline')) {

            $limit = ($rateLimit > intval($this->settings['max_fetch'])) ? intval($this->settings['max_fetch']) : $rateLimit;
            if (
                ($limit > 0)
                && ($accounts = $this->accountRepository->findAllSortedByLastFetchTimeline($limit))
            ){

                /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                foreach ($accounts as $account) {
                    $this->fetchTweetsOfAccount($account);

                    $account->setFetchTimelineTimestamp(time());
                    $this->accountRepository->update($account);
                    sleep(1);
                }

                // update rate limit
                $remainingRateLimit = $this->rateLimitUtility->setRateLimitForMethod('statuses', 'user_timeline', count($accounts));
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Fetched timeline tweets for %s account(s). Remaining rate limit is %s.', count($accounts), $remainingRateLimit));
            } else {
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('No timeline tweets fetched. Rate limit reached or no accounts available.'));
            }
        }

        // 2.) Get rate limit for search  APIand fetch an according amount of accounts for that
        if ($rateLimit = $this->rateLimitUtility->getRateLimitForMethod('search', 'tweets')) {

            $limit = ($rateLimit > intval($this->settings['max_fetch'])) ? intval($this->settings['max_fetch']) : $rateLimit;
            if (
                ($limit > 0)
                && ($accounts = $this->accountRepository->findAllSortedByLastFetchAddressed($limit))
            ){

                /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                foreach ($accounts as $account) {
                    $this->fetchTweetsAddressedToAccount($account);

                    $account->setFetchAddressedTimestamp(time());
                    $this->accountRepository->update($account);
                    sleep(1);
                }

                // update rate limit
                $remainingRateLimit = $this->rateLimitUtility->setRateLimitForMethod('search', 'tweets', count($accounts));
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Fetched addressed-to tweets for %s account(s). Remaining rate limit is %s.', count($accounts), $remainingRateLimit));
            } else {
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('No addressed-to tweets fetched. Rate limit reached or no accounts available.'));
            }
        }
    }


    /**
     * Get timeline tweets of user
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function fetchTweetsOfAccount(\Madj2k\TwitterAnalyser\Model\Account $account)
    {

        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $constraints = [
            'screen_name=' . $account->getUserName(),
            'count=100',
            'exclude_replies=false',
            'include_rts=true',
            'tweet_mode=extended',
        ];

        // get pagination
        /** @var \Madj2k\TwitterAnalyser\Model\Pagination $pagination */
        $pagination = $this->paginationUtility->getPagination($account, 'timeline', $constraints);

        try {

            $tweets = json_decode(
                $this->twitter->setGetfield('?' . implode('&', $constraints))
                    ->buildOauth($url, 'GET')
                    ->performRequest()
            );
            $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Fetched tweets for user %s', $account->getUserName()), $url . $this->twitter->getGetfield());

            if (is_array($tweets)) {

                // update pagination
                $this->paginationUtility->setPagination($pagination, $tweets);

                if (count($tweets) > 0) {

                    // import tweets
                    foreach ($tweets as $tweet) {
                        $this->tweetImportUtility->import($account, 'timeline', $tweet);
                    }

                    $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Found %s new tweets for user = %s', count($tweets), $account->getUserName()));
                    return true;
                }
            }
            $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('No tweets available for user %s', $account->getUserName()));

        } catch (\Exception $e) {
            $this->logUtility->log($this->logUtility::LOG_ERROR, $e->getMessage());
        }

        return false;
    }



    /**
     * Get tweets which are addressed to user
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function fetchTweetsAddressedToAccount(\Madj2k\TwitterAnalyser\Model\Account $account)
    {

        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $constraints = [
            'q=to:' . $account->getUserName(),
            'result_type=recent',
            'count=100',
            'tweet_mode=extended',
        ];

        // get pagination
        /** @var \Madj2k\TwitterAnalyser\Model\Pagination $pagination */
        $pagination = $this->paginationUtility->getPagination($account, 'searchTo', $constraints);

        try {

            $jsonResult = json_decode(
                $this->twitter->setGetfield('?' . implode('&', $constraints))
                    ->buildOauth($url, 'GET')
                    ->performRequest()
            );

            $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Fetched tweets for user %s', $account->getUserName()), $url . $this->twitter->getGetfield());

            if (
                ($jsonResult)
                && (is_array($jsonResult->statuses))
            ){

                $tweets = $jsonResult->statuses;

                // update pagination
                $this->paginationUtility->setPagination($pagination, $tweets);

                if (count($tweets) > 0) {

                    // import tweets
                    $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Found %s new tweets for user = %s', count($tweets), $account->getUserName()));
                    foreach ($tweets as $tweet) {
                        $this->tweetImportUtility->import($account, 'searchTo', $tweet);
                    }

                    return true;
                }
            }

            $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('No tweets available for user %s', $account->getUserName()));

        } catch (\Exception $e) {
            $this->logUtility->log($this->logUtility::LOG_ERROR, $e->getMessage());
        }

        return null;
        //===

    }


}