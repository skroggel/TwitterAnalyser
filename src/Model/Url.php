<?php
namespace Madj2k\TwitterAnalyser\Model;

/**
 * Url
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Url extends \Madj2k\SpencerBrown\Model\ModelAbstract
{


    /**
     * @var string
     */
    protected $url = '';


    /**
     * @var string
     */
    protected $baseUrl  = '';


    /**
     * @var string
     */
    protected $type  = '';


    /**
     * @var bool
     */
    protected $processed = false;


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
     * Gets baseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }


    /**
     * Sets tyoe
     *
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
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
     * Gets processed
     *
     * @return bool
     */
    public function getProcessed()
    {
        return $this->processed;
    }


    /**
     * Sets processed
     *
     * @param bool $processed
     * @return $this
     */
    public function setProcessed($processed)
    {
        $this->processed = boolval($processed);
        return $this;
    }
    

}