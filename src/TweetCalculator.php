<?php
namespace Madj2k\TwitterAnalyser;


/**
 * TweetCalculator
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TweetCalculator
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
     * Calculate interaction time and reply count tweets
     *
     * @param int $limit
     * @return int
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    public function calculateInteractionTimeAndCountReplies (int $limit = 100)
    {

        if ($tweets = $this->tweetRepository->findTimelineTweetsByCalculationTimestamp($limit)) {

            /** @var \Madj2k\TwitterAnalyser\Model\Tweet $tweet */
            foreach ($tweets as $tweet) {

                $interactionTime = 0;
                $replyCount = 0;
                $this->calculateInteractionTimeAndCountRepliesSub($tweet, $interactionTime, $replyCount);

                $tweet->setInteractionTime($interactionTime);
                $tweet->setReplyCount($replyCount);
                $tweet->setCalculationTimestamp(time());

                $this->tweetRepository->update($tweet);
                $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Updated interaction-time and reply-count for timeline tweet %s in database.', $tweet->getUid()));
            }

            return count($tweets);

        } else {
            $this->logUtility->log($this->logUtility::LOG_DEBUG, 'No tweets found for calculation of interaction-time and reply-count.');
        }

        return 0;
    }


    /**
     * Calculate interaction time and replies
     *
     * @param \Madj2k\TwitterAnalyser\Model\Tweet $tweet
     * @param int $interactionTime
     * @param int  $replyCount
     * @return void
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    protected function calculateInteractionTimeAndCountRepliesSub (\Madj2k\TwitterAnalyser\Model\Tweet $tweet, int &$interactionTime, int &$replyCount)
    {

        if ($subTweets = $this->tweetRepository->findByInReplyToTweetIdOrderedByCreateAt($tweet->getTweetId())) {

            /** @var \Madj2k\TwitterAnalyser\Model\Tweet $subTweet */
            $lastTweet = $tweet;
            foreach ($subTweets as $subTweet) {
                $replyCount++;
                $interactionTime += $subTweet->getCreatedAt() - $lastTweet->getCreatedAt();
                $this->calculateInteractionTimeAndCountRepliesSub($subTweet, $interactionTime, $replyCount);
                $lastTweet = $subTweet;
            }
        }
    }

}