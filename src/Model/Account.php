<?php
namespace Madj2k\TwitterAnalyser\Model;

/**
 * Account
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Account extends ModelAbstract
{

    /**
     * @var int
     */
    protected $userId = 0;

    /**
     * @var string
     */
    protected $userName = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
    * @var string
    */
    protected $location = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var bool
     */
    protected $verified = false;

    /**
     * @var bool
     */
    protected $isSuggestion = false;

    /**
     * @var string
     */
    protected $suggestionForName = '';

    /**
     * @var bool
     */
    protected $isSecondary = false;

    /**
     * @var int
     */
    protected $createdAt = 0;

    /**
     * @var int
     */
    protected $followersCount = 0;

    /**
     * @var int
     */
    protected $friendsCount = 0;

    /**
     * @var int
     */
    protected $listedCount = 0;

    /**
     * @var int
     */
    protected $favoritesCount = 0;

    /**
     * @var int
     */
    protected $statusesCount = 0;

    /**
     * @var int
     */
    protected $fetchTimelineTimestamp = 0;

    /**
     * @var int
     */
    protected $fetchAddressedTimestamp = 0;



    /**
     * @var array
     */
    protected $_mapping = [
        'id' => 'userId',
        'screen_name' => 'userName',
        'favouritesCount' => 'favoritesCount'
    ];

    
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
     * Gets name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Sets name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * Gets location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }


    /**
     * Sets location
     *
     * @param string $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }



    /**
     * Gets description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Sets description
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }


    /**
     * Gets url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Sets url
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }


    /**
     * Gets verified
     *
     * @return bool
     */
    public function getVerified()
    {
        return $this->verified;
    }


    /**
     * Sets verified
     *
     * @param bool $verified
     * @return $this
     */
    public function setVerified($verified)
    {
        $this->verified = boolval($verified);
        return $this;
    }

    /**
     * Gets isSuggestion
     *
     * @return bool
     */
    public function getIsSuggestion()
    {
        return $this->isSuggestion;
    }


    /**
     * Sets isSuggestion
     *
     * @param bool $isSuggestion
     * @return $this
     */
    public function setIsSuggestion($isSuggestion)
    {
        $this->isSuggestion = boolval($isSuggestion);
        return $this;
    }


    /**
     * Gets suggestionForName
     *
     * @return string
     */
    public function getSuggestionForName()
    {
        return $this->suggestionForName;
    }


    /**
     * Sets suggestionForName
     *
     * @param string $suggestionForName
     * @return $this
     */
    public function setSuggestionForName($suggestionForName)
    {
        $this->suggestionForName = $suggestionForName;
        return $this;
    }

    /**
     * Gets isSecondary
     *
     * @return bool
     */
    public function getIsSecondary()
    {
        return $this->isSecondary;
    }


    /**
     * Sets isSecondary
     *
     * @param bool $isSecondary
     * @return $this
     */
    public function setIsSecondary($isSecondary)
    {
        $this->isSecondary = boolval($isSecondary);
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
     * Gets followersCount
     *
     * @return int
     */
    public function getFollowersCount()
    {
        return $this->followersCount;
    }


    /**
     * Sets followersCount
     *
     * @param int $followersCount
     * @return $this
     */
    public function setFollowersCount($followersCount)
    {
        $this->followersCount = intval($followersCount);
        return $this;
    }


    /**
     * Gets friendsCount
     *
     * @return int
     */
    public function getFriendsCount()
    {
        return $this->friendsCount;
    }


    /**
     * Sets friendsCount
     *
     * @param int $friendsCount
     * @return $this
     */
    public function setFriendsCount($friendsCount)
    {
        $this->friendsCount = intval($friendsCount);
        return $this;
    }


    /**
     * Gets listedCount
     *
     * @return int
     */
    public function getListedCount()
    {
        return $this->listedCount;
    }


    /**
     * Sets listedCount
     *
     * @param int $listedCount
     * @return $this
     */
    public function setListedCount($listedCount)
    {
        $this->listedCount = intval($listedCount);
        return $this;
    }


    /**
     * Gets favoritesCount
     *
     * @return int
     */
    public function getFavoritesCount()
    {
        return $this->favoritesCount;
    }


    /**
     * Sets favoritesCount
     *
     * @param int $favoritesCount
     * @return $this
     */
    public function setFavoritesCount($favoritesCount)
    {
        $this->favoritesCount = intval($favoritesCount);
        return $this;
    }


    /**
     * Gets statusesCount
     *
     * @return int
     */
    public function getStatusesCount()
    {
        return $this->statusesCount;
    }


    /**
     * Sets statusesCount
     *
     * @param int $statusesCount
     * @return $this
     */
    public function setStatusesCount($statusesCount)
    {
        $this->statusesCount = intval($statusesCount);
        return $this;
    }


    /**
     * Gets FetchTimelineTimestamp
     *
     * @return int
     */
    public function getFetchTimelineTimestamp()
    {
        return $this->fetchTimelineTimestamp;
    }


    /**
     * Sets FetchTimelineTimestamp
     *
     * @param int $timestamp
     * @return $this
     */
    public function setFetchTimelineTimestamp(int $timestamp)
    {
        $this->fetchTimelineTimestamp = intval($timestamp);
        return $this;
    }


    /**
     * Gets FetchAddressedTimestamp
     *
     * @return int
     */
    public function getFetchAddressedTimestamp()
    {
        return $this->fetchAddressedTimestamp;
    }


    /**
     * Sets FetchAddressedTimestamp
     *
     * @param int $timestamp
     * @return $this
     */
    public function setFetchAddressedTimestamp(int $timestamp)
    {
        $this->fetchAddressedTimestamp = intval($timestamp);
        return $this;
    }

}