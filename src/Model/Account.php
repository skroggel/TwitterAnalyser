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
     * @var int
     */
    protected $fetchTimelineTimestamp = 0;

    /**
     * @var int
     */
    protected $fetchAddressedTimestamp = 0;
    
    
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