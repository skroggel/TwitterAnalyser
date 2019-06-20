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

    const LOG_DEBUG = 0;
    const LOG_INFO = 1;
    const LOG_WARNING = 2;
    const LOG_ERROR = 3;

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
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
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
     * @param string $type
     * @param string $method
     * @return \Madj2k\TwitterAnalyser\Model\RateLimit|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function getRateLimitForMethod($type, $method)
    {

        /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $rateLimit */
        if ($rateLimit = $this->rateLimitRepository->findOneByTypeAndMethod($type, $method)) {
            $this->logUtility->log(self::LOG_DEBUG, 'Fetched rate limit from database');
            if ($rateLimit->getReset() > time()) {
                return $rateLimit;
                //===
            }
        };


        // 2.) if there is nothing in the database we fetch it directly from twitter and save it in database
        $url = 'https://api.twitter.com/1.1/application/rate_limit_status.json';
        $getFields = '?resources=application,' . $type;
        try {

            $resultJson = json_decode(
                $this->twitter->setGetfield($getFields)
                ->buildOauth($url, 'GET')
                ->performRequest()
            );
            $this->logUtility->log(self::LOG_DEBUG, 'Fetched rate limit from API');

            if (
                ($resultJson)
                && ($resultJson->resources)
                && ($resultJson->resources->{$type})
                && ($object = $resultJson->resources->{$type}->{'/' . $type . '/' . $method})
            ) {

                /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $rateLimit */
                if (! $rateLimit) {
                    $rateLimit = new RateLimit($object);
                    $rateLimit->setType($type);
                    $rateLimit->setMethod($method);

                    $this->rateLimitRepository->insert($rateLimit);
                } else {
                    $rateLimit->setLimits($object->limit);
                    $rateLimit->setRemaining($object->remaining);
                    $rateLimit->setReset($object->reset);
                    
                    $this->rateLimitRepository->update($rateLimit);
                }

                $this->logUtility->log(self::LOG_DEBUG, 'Saved rate limit in database');
                return $rateLimit;
                //===
            }

        } catch (\Exception $e) {
            $this->logUtility->log(self::LOG_ERROR, $e->getMessage());
        }

        return null;
        //===

    }



    /**
     * Get accounts
     *
     * @return mixed|false
     */
    public function getAccounts()
    {

        $sql = 'SELECT * FROM accounts WHERE 1 = 1 ORDER BY last_update ASC';
        $sth = $this->pdo->prepare($sql);
        $sth->execute();

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
        //===


    }




}