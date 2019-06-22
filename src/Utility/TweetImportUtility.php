<?php
namespace Madj2k\TwitterAnalyser\Utility;

use \Madj2k\TwitterAnalyser\Model\Tweet;

/**
 * TweetImportUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TweetImportUtility
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
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
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
     * Logs actions
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @param string $type
     * @param \stdClass $object
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function import (\Madj2k\TwitterAnalyser\Model\Account $account, string $type, \stdClass $object)
    {

        $tweet = new Tweet($object);
        $tweet->setAccount($account->getUid());
        $tweet->setType($type);

        // import some sub-objects with relevant data
        if ($object->user) {
            $tweet->setUserId($object->user->id);
            $tweet->setUserName($object->user->screen_name);
        }

        if ($object->entities) {

            if (
                (isset($object->entities->hashtags))
                && ($hastags = $object->entities->hashtags)
            ){
                foreach ($hastags as $hashtag) {
                    $tweet->addHashtag($hashtag->text . ' [' . $hashtag->indices[0] . ',' . $hashtag->indices[1] . ']');
                }
            }

            if (
                (isset($object->entities->user_mentions))
                && ($mentions = $object->entities->user_mentions)
            ){
                foreach ($mentions as $mention) {
                    $tweet->addMention($mention->screen_name . ' [' . $mention->indices[0] . ',' . $mention->indices[1] . ']');
                }
            }

            if (
                (isset($object->entities->symbols))
                && ($symbols = $object->entities->symbols)
            ){
                foreach ($symbols as $symbol) {
                    $tweet->addSymbol($symbol->text . ' [' . $symbol->indices[0] . ',' . $symbol->indices[1] . ']');
                }
            }

            if (
                (isset($object->entities->urls))
                && ($urls = $object->entities->urls)
            ){
                foreach ($urls as $url) {
                    $tweet->addUrl($url->expanded_url . ' [' . $url->indices[0] . ',' . $url->indices[1] . ']');
                }
            }

            if (
                (isset($object->entities->media))
                && ($media = $object->entities->media)
            ){
                foreach ($media as $medium) {
                    $tweet->addMedium($medium->expanded_url . ' [' . $medium->indices[0] . ',' . $medium->indices[1] . ']');
                }
            }
        }

        // check for some properties that should be available at least
        if (
            ($tweet->getTweetId())
            && ($tweet->getCreatedAt())
            && ($tweet->getFullText())
        ){
            $this->tweetRepository->insert($tweet);
            $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Imported Tweet with id = %s for user = %s into database.', $tweet->getTweetId(), $account->getUserName()));
            return true;
        }

        $this->logUtility->log($this->logUtility::LOG_ERROR, sprintf('Could not import given Tweet for user = %s into database.', $account->getUserName()));
        return false;
    }
}