<?php
namespace Madj2k\TwitterAnalyser\Model;

/**
 * Pagination
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Pagination extends ModelAbstract
{

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
    protected $highestId = 0;

    /**
     * @var int
     */
    protected $lowestId = 0;

    /**
     * @var int
     */
    protected $sinceId = 0;

    /**
     * @var int
     */
    protected $maxId = 0;
    
    
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
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Gets highestId
     *
     * @return int
     */
    public function getHighestId()
    {
        return $this->highestId;
    }


    /**
     * Sets highestId
     *
     * @param int $highestId
     * @return $this
     */
    public function setHighestId($highestId)
    {
        $this->highestId = intval($highestId);
        return $this;
    }


    /**
     * Gets lowestId
     *
     * @return int
     */
    public function getLowestId()
    {
        return $this->lowestId;
    }


    /**
     * Sets lowestId
     *
     * @param int $lowestId
     * @return $this
     */
    public function setLowestId($lowestId)
    {
        $this->lowestId = intval($lowestId);
        return $this;
    }


    /**
     * Gets sinceId
     *
     * @return int
     */
    public function getSinceId()
    {
        return $this->sinceId;
    }


    /**
     * Sets sinceId
     *
     * @param int $sinceId
     * @return $this
     */
    public function setSinceId($sinceId)
    {
        $this->sinceId = intval($sinceId);
        return $this;
    }


    /**
     * Gets maxId
     *
     * @return int
     */
    public function getMaxId()
    {
        return $this->maxId;
    }


    /**
     * Sets maxId
     *
     * @param int $maxId
     * @return $this
     */
    public function setMaxId($maxId)
    {
        $this->maxId = intval($maxId);
        return $this;
    }



}