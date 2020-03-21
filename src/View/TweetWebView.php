<?php
namespace Madj2k\TwitterAnalyser\View;

/**
 * TweetWebView
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TweetWebView
{


    /**
     * @var array
     */
    protected $settings;


    /**
     * @var \Madj2k\TwitterAnalyser\Repository\TweetRepository
     */
    protected $tweetRepository;



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
        $this->tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();

    }


    /**
     * Renders tweets
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @param int $fromTime
     * @param int $toTime
     * @return string
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function render (\Madj2k\TwitterAnalyser\Model\Account $account, int $fromTime = 0, int $toTime = 0)
    {
        $html = '';
        if ($tweets = $this->tweetRepository->findTimelineTweetsByAccountAndTimeOrderedByCreateAt($account, $fromTime, $toTime)) {

            $html = '<ol class="tweet-list" >';

            /** @var \Madj2k\TwitterAnalyser\Model\Tweet $tweet */
            foreach ($tweets as $tweet) {
                $html .= $this->renderSub($tweet);
            }

            $html .= '</ol>';
        }

        return $html;
    }


    /**
     * Renders sub-tweets
     *
     * @param \Madj2k\TwitterAnalyser\Model\Tweet $tweet
     * @return string
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    protected function renderSub (\Madj2k\TwitterAnalyser\Model\Tweet $tweet)
    {

        $html = '<li class="tweet" data-tweet-id="' . $tweet->getTweetId() . '">';
        $html .= '<span class="tweet__header">' . $tweet->getUserName() . ' (' . date('d.m.y H:i', $tweet->getCreatedAt()) . ') RTs: ' . $tweet->getRetweetCount() . ', FAVs: ' . $tweet->getFavoriteCount() . '</span>';
        $html .= '<span class="tweet__body">' . $tweet->getFullText() . '<span>';

        if ($tweet->getMedia()) {
            foreach(explode('|', $tweet->getMedia()) as $medium) {
                $html .= '<span class="tweet__media"><a href="' . preg_replace('#\[.+\]#', '', $medium) . '" target="_blank">' . $medium . '</a><span>';
            }
        }

        if ($subTweets = $this->tweetRepository->findByInReplyToTweetIdOrderedByCreateAt($tweet->getTweetId())) {

            $html .= '<ol class="tweet-sublist" >';

            /** @var \Madj2k\TwitterAnalyser\Model\Tweet $tweet */
            foreach ($subTweets as $subTweet) {
                $html .= $this->renderSub($subTweet);
            }

            $html .= '</ol>';
        }
        $html .= '</li>';

        return $html;
    }

}