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
     * @var \Madj2k\TwitterAnalyser\Repository\RateLimitRepository
     */
    protected $rateLimitRepository;

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\AccountRepository
     */
    protected $accountRepository;

    /**
     * @var \Madj2k\TwitterAnalyser\Utility\LogUtility
     */
    protected $logUtility;

    /**
     * @var \Madj2k\TwitterAnalyser\Utility\PaginationUtility
     */
    protected $paginationUtility;


    /**
     * @var \Madj2k\TwitterAnalyser\Utility\TweetImportUtility
     */
    protected $tweetImportUtility;

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
        $this->rateLimitRepository = new \Madj2k\TwitterAnalyser\Repository\RateLimitRepository();
        $this->accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();

        $this->logUtility = new  \Madj2k\TwitterAnalyser\Utility\LogUtility();
        $this->paginationUtility = new  \Madj2k\TwitterAnalyser\Utility\PaginationUtility();
        $this->tweetImportUtility = new \Madj2k\TwitterAnalyser\Utility\TweetImportUtility();


        // init Twitter API
        $this->twitter = new \TwitterAPIExchange($this->settings['twitter']);
    }



    /**
     * Get timeline tweets of user
     *
     * return void
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function fetchTweets()
    {
        
        // 1.) Get rate limit for timeline API and fetch an according amount of accounts for that
        /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $rateLimit */
        if ($rateLimit = $this->getRateLimitForMethod('statuses', 'user_timeline')) {

            $limit = ($rateLimit->getRemaining() > intval($this->settings['max_fetch'])) ? intval($this->settings['max_fetch']) : $rateLimit->getRemaining();
            if (
                ($limit > 0)
                && ($accounts = $this->accountRepository->findAllSortedByLastFetchTimeline($limit))
            ){

                /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                foreach ($accounts as $account) {
                    $this->fetchTweetsOfAccount($account);

                    $account->setFetchTimelineTimestamp(time());
                    $this->accountRepository->update($account);
                    sleep(2);
                }

                $rateLimit->setRemaining($rateLimit->getRemaining() - count($accounts));
                $this->rateLimitRepository->update($rateLimit);

                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Fetched timeline tweets for %s account(s). Remaining rate limit is %s.', count($accounts), $rateLimit->getRemaining()));
            } else {
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('No timeline tweets fetch. Rate limit reached.'));
            }
        }

        // 2.) Get rate limit for search  APIand fetch an according amount of accounts for that
        /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $rateLimit */
        if ($rateLimit = $this->getRateLimitForMethod('search', 'tweets')) {

            $limit = ($rateLimit->getRemaining() > intval($this->settings['max_fetch'])) ? intval($this->settings['max_fetch']) : $rateLimit->getRemaining();
            if (
                ($limit > 0)
                && ($accounts = $this->accountRepository->findAllSortedByLastFetchAddressed($limit))
            ){

                /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                foreach ($accounts as $account) {
                    $this->fetchTweetsAddressedToAccount($account);

                    $account->setFetchAddressedTimestamp(time());
                    $this->accountRepository->update($account);
                    sleep(2);
                }

                $rateLimit->setRemaining($rateLimit->getRemaining() - count($accounts));
                $this->rateLimitRepository->update($rateLimit);

                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Fetched addressed-to tweets for %s account(s). Remaining rate limit is %s.', count($accounts), $rateLimit->getRemaining()));
            } else {
                $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('No addressed-to tweets fetch. Rate limit reached.'));
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
            'exclude_replies=true',
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
            $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Fetched tweets for user %s', $account->getUserName()), $url . $this->twitter->getGetfield());


            if (
                ($tweets)
                && (is_array($tweets))
            ) {

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
            'q=' . $account->getUserName(),
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

            $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Fetched tweets for user %s', $account->getUserName()), $url . $this->twitter->getGetfield());

            if (
                ($jsonResult)
                && ($tweets = $jsonResult->statuses)
                && (is_array($tweets))
            ){

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




    /**
     * Get rate limit for API-call
     *
     * @param string $category
     * @param string $method
     * @return \Madj2k\TwitterAnalyser\Model\RateLimit|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function getRateLimitForMethod($category, $method)
    {

        /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $rateLimit */
        if ($rateLimit = $this->rateLimitRepository->findOneByCategoryAndMethod($category, $method)) {
            $this->logUtility->log($this->logUtility::LOG_DEBUG, 'Fetched rate limit from database');
            if ($rateLimit->getReset() > time()) {
                return $rateLimit;
                //===
            }
        };


        // 2.) if there is nothing in the database we fetch it directly from twitter and save it in database
        $url = 'https://api.twitter.com/1.1/application/rate_limit_status.json';
        $getFields = '?resources=application,' . $category;
        try {

            $jsonResult = json_decode(
                $this->twitter->setGetfield($getFields)
                ->buildOauth($url, 'GET')
                ->performRequest()
            );
            $this->logUtility->log($this->logUtility::LOG_INFO, 'Fetched rate limit from API');

            if (
                ($jsonResult)
                && ($jsonResult->resources)
                && ($jsonResult->resources->{$category})
                && ($object = $jsonResult->resources->{$category}->{'/' . $category . '/' . $method})
            ) {

                /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $rateLimit */
                if (! $rateLimit) {
                    $rateLimit = new RateLimit($object);
                    $rateLimit->setCategory($category);
                    $rateLimit->setMethod($method);

                    $this->rateLimitRepository->insert($rateLimit);
                } else {
                    $rateLimit->setLimits($object->limit);
                    $rateLimit->setRemaining($object->remaining);
                    $rateLimit->setReset($object->reset);

                    $this->rateLimitRepository->update($rateLimit);
                }

                $this->logUtility->log($this->logUtility::LOG_DEBUG, 'Saved rate limit in database');
                return $rateLimit;
                //===
            }

        } catch (\Exception $e) {
            $this->logUtility->log($this->logUtility::LOG_ERROR, $e->getMessage());
        }

        return null;
        //===

    }

}