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
class RateLimit extends ModelAbstract
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $method;

    /**
     * @int
     */
    protected $limits;

    /**
     * @int
     */
    protected $remaining;

    /**
     * @int
     */
    protected $reset;


    /**
     * @var array
     */
    protected $_mapping = [
        'limit' => 'limits'
    ];


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
     * Sets type
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