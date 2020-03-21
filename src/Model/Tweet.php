<?php
namespace Madj2k\TwitterAnalyser\Model;

/**
 * Tweet
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tweet extends ModelAbstract
{

    /**
     * @var int
     */
    protected $tweetId = 0;

    /**
     * @var int
     */
    protected $userId = 0;

    /**
     * @var string
     */
    protected $userName = '';

    /**
     * @var int
     */
    protected $account = 0;

    /**
     * @var string
     */
    protected $type  = '';

    /**
     * @var int
     */
    protected $createdAt = 0;

    /**
     * @var string
     */
    protected $fullText = '';

    /**
     * @var string
     */
    protected $hashtags = '';

    /**
     * @var string
     */
    protected $hashtagsWordsOnly = '';

    /**
     * @var string
     */
    protected $mentions = '';

    /**
     * @var string
     */
    protected $urls = '';

    /**
     * @var string
     */
    protected $symbols = '';

    /**
     * @var string
     */
    protected $media = '';

    /**
     * @var string
     */
    protected $source = '';

    /**
     * @var int
     */
    protected $isReply  = 0;

    /**
     * @var int
     */
    protected $inReplyToTweetId = 0;

    /**
     * @var int
     */
    protected $inReplyToUserId = 0;

    /**
     * @var int
     */
    protected $retweetCount = 0;

    /**
     * @var int
     */
    protected $favoriteCount = 0;

    /**
     * @var bool
     */
    protected $exported = false;

    /**
     * @var int
     */
    protected $interactionTime = 0;


    /**
     * @var int
     */
    protected $replyCount = 0;


    /**
     * @var int
     */
    protected $calculationTimestamp = 0;


    /**
     * @var array
     */
    protected $_mapping = [
        'id' => 'tweetId',
        'created_at' => 'createdAt',
        'text' => 'fullText',
        'full_text' => 'fullText',
        'in_reply_to_status_id' => 'inReplyToTweetId',
        'in_reply_to_user_id' => 'inReplyToUserId',
        'retweet_count' => 'retweetCount',
        'favorite_count' => 'favoriteCount'
    ];


    
    /**
     * Gets tweetId
     *
     * @return int
     */
    public function getTweetId()
    {
        return $this->tweetId;
    }


    /**
     * Sets tweetId
     *
     * @param int $tweetId
     * @return $this
     */
    public function setTweetId($tweetId)
    {
        $this->tweetId = intval($tweetId);
        return $this;
    }


    /**
     * Gets userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }


    /**
     * Sets userId
     *
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = intval($userId);
        return $this;
    }



    /**
     * Gets userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }


    /**
     * Sets userName
     *
     * @param string $userName
     * @return $this
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }


    /**
     * Gets account
     *
     * @return int
     */
    public function getAccount()
    {
        return $this->account;
    }


    /**
     * Sets account
     *
     * @param int $account
     * @return $this
     */
    public function setAccount($account)
    {
        $this->account = intval($account);
        return $this;
    }



    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Sets tyoe
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }


    /**
     * Gets createdAt
     *
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }


    /**
     * Sets createdAt
     *
     * @param int|string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        if (! is_numeric($createdAt)) {
            $createdAt = strtotime($createdAt);
        }
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Gets fullText
     *
     * @return string
     */
    public function getFullText()
    {
        return $this->fullText;
    }


    /**
     * Sets fullText
     *
     * @param string $fullText
     * @return $this
     */
    public function setFullText(string $fullText)
    {
        $this->fullText = $fullText;
        return $this;
    }


    /**
     * Gets hashtags
     *
     * @return string
     */
    public function getHashtags()
    {
        return $this->hashtags;
    }


    /**
     * Sets hashtags
     *
     * @param string $hashtags
     * @return $this
     */
    public function setHashtags($hashtags)
    {
        $this->hashtags = $hashtags;
        return $this;
    }

    /**
     * Adds hashtag
     *
     * @param string $hashtag
     * @return $this
     */
    public function addHashtag($hashtag)
    {
        $exploded = explode('|', $this->hashtags);
        $exploded[] = $hashtag;
        $this->hashtags = implode('|', $exploded);
        return $this;
    }



    /**
     * Gets hashtagsWordsOnly
     *
     * @return string
     */
    public function getHashtagsWordsOnly()
    {
        return $this->hashtagsWordsOnly;
    }


    /**
     * Sets hashtagsWordsOnly
     *
     * @param string $hashtagsWordsOnly
     * @return $this
     */
    public function setHashtagsWordsOnly($hashtagsWordsOnly)
    {
        $this->hashtagsWordsOnly = $hashtagsWordsOnly;
        return $this;
    }

    /**
     * Adds hashtagWordsOnly
     *
     * @param string $hashtagWordsOnly
     * @return $this
     */
    public function addHashtagWordsOnly($hashtagWordsOnly)
    {
        $exploded = explode(',', $this->hashtagsWordsOnly);
        $exploded[] = $hashtagWordsOnly;
        $this->hashtagsWordsOnly = implode(',', $exploded);
        return $this;
    }


    /**
     * Gets mentions
     *
     * @return string
     */
    public function getMentions()
    {
        return $this->mentions;
    }


    /**
     * Sets mentions
     *
     * @param string $mentions
     * @return $this
     */
    public function setMentions($mentions)
    {
        $this->mentions = $mentions;
        return $this;
    }


    /**
     * Adds mention
     *
     * @param string $mention
     * @return $this
     */
    public function addMention($mention)
    {
        $exploded = explode('|', $this->mentions);
        $exploded[] = $mention;
        $this->mentions = implode('|', $exploded);
        return $this;
    }

    /**
     * Gets urls
     *
     * @return string
     */
    public function getUrls()
    {
        return $this->urls;
    }


    /**
     * Sets urls
     *
     * @param string $urls
     * @return $this
     */
    public function setUrls(string $urls)
    {
        $this->urls = $urls;
        return $this;
    }


    /**
     * Adds url
     *
     * @param string $url
     * @return $this
     */
    public function addUrl($url)
    {
        $exploded = explode('|', $this->urls);
        $exploded[] = $url;
        $this->urls = implode('|', $exploded);
        return $this;
    }


    /**
     * Gets symbols
     *
     * @return string
     */
    public function getSymbols()
    {
        return $this->symbols;
    }


    /**
     * Sets symbols
     *
     * @param string $symbols
     * @return $this
     */
    public function setSymbols(string $symbols)
    {
        $this->symbols = $symbols;
        return $this;
    }


    /**
     * Adds symbol
     *
     * @param string $symbol
     * @return $this
     */
    public function addSymbol($symbol)
    {
        $exploded = explode('|', $this->symbols);
        $exploded[] = $symbol;
        $this->symbols = implode('|', $exploded);
        return $this;
    }


    /**
     * Gets media
     *
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }


    /**
     * Sets media
     *
     * @param string $media
     * @return $this
     */
    public function setMedia(string $media)
    {
        $this->media = $media;
        return $this;
    }


    /**
     * Adds medium
     *
     * @param string $media
     * @return $this
     */
    public function addMedium($medium)
    {
        $exploded = explode('|', $this->media);
        $exploded[] = $medium;
        $this->media = implode('|', $exploded);
        return $this;
    }
    
    

    /**
     * Gets source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }


    /**
     * Sets source
     *
     * @param string $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Gets isReply
     *
     * @return int
     */
    public function getIsReply()
    {
        return $this->isReply;
    }


    /**
     * Sets isReply
     *
     * @param int $isReply
     * @return $this
     */
    public function setIsReply($isReply)
    {
        $this->isReply= intval($isReply);
        return $this;
    }


    /**
     * Gets inReplyToTweetId
     *
     * @return int
     */
    public function getInReplyToTweetId()
    {
        return $this->inReplyToTweetId;
    }


    /**
     * Sets inReplyToTweetId
     *
     * @param int $inReplyToTweetId
     * @return $this
     */
    public function setInReplyToTweetId($inReplyToTweetId)
    {
        $this->inReplyToTweetId = intval($inReplyToTweetId);
        return $this;
    }



    /**
     * Gets inReplyToUserId
     *
     * @return int
     */
    public function getInReplyToUserId()
    {
        return $this->inReplyToUserId;
    }


    /**
     * Sets inReplyToUserId
     *
     * @param int $inReplyToUserId
     * @return $this
     */
    public function setInReplyToUserId($inReplyToUserId)
    {
        $this->inReplyToUserId = intval($inReplyToUserId);
        return $this;
    }



    /**
     * Gets retweetCount
     *
     * @return int
     */
    public function getRetweetCount()
    {
        return $this->retweetCount;
    }


    /**
     * Sets retweetCount
     *
     * @param int $retweetCount
     * @return $this
     */
    public function setRetweetCount($retweetCount)
    {
        $this->retweetCount = intval($retweetCount);
        return $this;
    }



    /**
     * Gets favoriteCount
     *
     * @return int
     */
    public function getFavoriteCount()
    {
        return $this->favoriteCount;
    }


    /**
     * Sets favoriteCount
     *
     * @param int $favoriteCount
     * @return $this
     */
    public function setFavoriteCount($favoriteCount)
    {
        $this->favoriteCount = intval($favoriteCount);
        return $this;
    }

    /**
     * Gets exported
     *
     * @return bool
     */
    public function getExported()
    {
        return $this->exported;
    }

    /**
     * Sets exported
     *
     * @param bool $exported
     * @return $this
     */
    public function setExported($exported)
    {
        $this->exported = boolval($exported);
        return $this;
    }

    /**
     * Gets interactionTime
     *
     * @return int
     */
    public function getInteractionTime()
    {
        return $this->interactionTime;
    }


    /**
     * Sets interactionTIme
     *
     * @param int $interactionTime
     * @return $this
     */
    public function setInteractionTime($interactionTime)
    {
        $this->interactionTime = intval($interactionTime);
        return $this;
    }

    /**
     * Gets replyCount
     *
     * @return int
     */
    public function getReplyCount()
    {
        return $this->replyCount;
    }


    /**
     * Sets replyCount
     *
     * @param int $replyCount
     * @return $this
     */
    public function setReplyCount($replyCount)
    {
        $this->replyCount = intval($replyCount);
        return $this;
    }


    /**
     * Gets calculationTimestamp
     *
     * @return int
     */
    public function getCalculationTimestamp()
    {
        return $this->calculationTimestamp;
    }


    /**
     * Sets calculationTimestamp
     *
     * @param int $timestamp
     * @return $this
     */
    public function setCalculationTimestamp(int $timestamp)
    {
        $this->calculationTimestamp = intval($timestamp);
        return $this;
    }
}