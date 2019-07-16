<?php
namespace Madj2k\TwitterAnalyser\Utility;
use \Madj2k\TwitterAnalyser\Model\RateLimit;

/**
 *  RateLimitUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RateLimitUtility
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
     * @throws \ReflectionException
     */
    public function __construct()
    {

        global $SETTINGS;
        $this->settings = &$SETTINGS;

        // set defaults
        $this->rateLimitRepository = new \Madj2k\TwitterAnalyser\Repository\RateLimitRepository();
        $this->logUtility = new  \Madj2k\TwitterAnalyser\Utility\LogUtility();

        // init Twitter API
        $this->twitter = new \TwitterAPIExchange($this->settings['twitter']);
    }



    /**
     * Get rate limit for API-call
     *
     * @param string $category
     * @param string $method
     * @return int|false
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function getRateLimitForMethod($category, $method)
    {

        /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $rateLimit */
        if ($rateLimit = $this->rateLimitRepository->findOneByCategoryAndMethod($category, $method)) {
            $this->logUtility->log($this->logUtility::LOG_DEBUG, 'Fetched rate limit from database.');
            if ($rateLimit->getReset() > time()) {
                return $rateLimit->getRemaining();
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
            $this->logUtility->log($this->logUtility::LOG_DEBUG, 'Fetched rate limit from API.');

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

                $this->logUtility->log($this->logUtility::LOG_DEBUG, 'Saved rate limit in database.');
                return $rateLimit->getRemaining();
                //===
            }

        } catch (\Exception $e) {
            $this->logUtility->log($this->logUtility::LOG_ERROR, $e->getMessage());
        }

        return false;
        //===

    }

    /**
     * Set rate limit
     *
     * @param string $category
     * @param string $method
     * @param int $resultCount
     * @return int|false
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function setRateLimitForMethod($category, $method, $resultCount)
    {

        /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $rateLimit */
        if (
            ($resultCount > 0)
            && ($rateLimit = $this->rateLimitRepository->findOneByCategoryAndMethod($category, $method))
        ){
            $rateLimit->setRemaining($rateLimit->getRemaining() - $resultCount);
            $this->rateLimitRepository->update($rateLimit);

            $this->logUtility->log($this->logUtility::LOG_DEBUG, 'Updated rate limit in database.');
            return $rateLimit->getRemaining();
        };

        return false;

    }

}