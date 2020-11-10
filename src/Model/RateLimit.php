<?php
namespace Madj2k\TwitterAnalyser\Model;

/**
 * RateLimit
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RateLimit extends \Madj2k\SpencerBrown\Model\ModelAbstract
{

    /**
     * @var string
     */
    protected $category = '';

    /**
     * @var string
     */
    protected $method = '';

    /**
     * @var int
     */
    protected $limits = 0;

    /**
     * @var int
     */
    protected $remaining = 0;

    /**
     * @var int
     */
    protected $reset = 0;


    /**
     * @var array
     */
    protected $_mapping = [
        'limit' => 'limits'
    ];


    /**
     * Gets category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }


    /**
     * Sets category
     *
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Gets method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }


    /**
     * Sets method
     *
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }


    /**
     * Gets limits
     *
     * @return int
     */
    public function getLimits()
    {
        return $this->limits;
    }


    /**
     * Sets limits
     *
     * @param int $limits
     * @return $this
     */
    public function setLimits($limits)
    {
        $this->limits = intval($limits);
        return $this;
    }



    /**
     * Gets remaining
     *
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }


    /**
     * Sets remaining
     *
     * @param int $remaining
     * @return $this
     */
    public function setRemaining($remaining)
    {
        $this->remaining = intval($remaining);
        return $this;
    }



    /**
     * Gets reset
     *
     * @return int
     */
    public function getReset()
    {
        return $this->reset;
    }


    /**
     * Sets reset
     *
     * @param int $reset
     * @return $this
     */
    public function setReset($reset)
    {
        $this->reset = intval($reset);
        return $this;
    }
    
}