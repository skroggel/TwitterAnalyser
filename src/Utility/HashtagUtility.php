<?php
namespace Madj2k\TwitterAnalyser\Utility;

/**
 *  HashtagUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2020
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class HashtagUtility
{

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\TweetRepository
     */
    protected $tweetRepository;

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
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     * @throws \ReflectionException
     */
    public function __construct()
    {

        global $SETTINGS;
        $this->settings = &$SETTINGS;

        // set defaults
        $this->tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();
        $this->logUtility = new  \Madj2k\TwitterAnalyser\Utility\LogUtility();

    }



    /**
     * Analyse hashtags
     *
     * @param string $hashtag
     * @param string $fromDate
     * @param string $toDate
     * @return array
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    public function analyse (string $hashtag = '', string $fromDate = '', string $toDate = '')
    {

        // get all tweets that match the given criteria
        $resultDb = $this->tweetRepository->findTimelineTweetsByHashtagAndTimeInterval(
            $hashtag,
            strtotime($fromDate),
            strtotime($toDate)
        );

        $result = [];
        foreach ($resultDb as $item) {

            $hashtagsDb = explode(',', strtolower($item['hashtags_words_only']));
            foreach ($hashtagsDb as $hashtagDb) {
                if (isset($result[$hashtagDb])) {
                    $result[$hashtagDb] += $item['counter'];
                } else {
                    $result[$hashtagDb] = $item['counter'];
                }
            }
        }

        arsort($result);
        return $result;
    }



}