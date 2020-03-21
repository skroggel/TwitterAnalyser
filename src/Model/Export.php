<?php
namespace Madj2k\TwitterAnalyser\Model;

/**
 * Export
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Export extends ModelAbstract
{

    /**
     * @var string
     */
    protected $md5UserId = '';

    /**
     * @var string
     */
    protected $md5UserName = '';


    /**
     * Gets md5UserId
     *
     * @return string
     */
    public function getMd5UserId()
    {
        return $this->md5UserId;
    }


    /**
     * Sets md5UserId
     *
     * @param string $md5UserId
     * @return $this
     */
    public function setMd5UserId($md5UserId)
    {
        $this->md5UserId = $md5UserId;
        return $this;
    }

    /**
     * Gets md5UserName
     *
     * @return string
     */
    public function getMd5UserName()
    {
        return $this->md5UserName;
    }


    /**
     * Sets md5UserName
     *
     * @param string $md5UserName
     * @return $this
     */
    public function setMd5UserName($md5UserName)
    {
        $this->md5UserName = $md5UserName;
        return $this;
    }


}