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
    protected $md5UserName = '';

    /**
     * @var string
     */
    protected $party = '';


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


    /**
     * Gets party
     *
     * @return string
     */
    public function getParty()
    {
        return $this->party;
    }


    /**
     * Sets party
     *
     * @param string $party
     * @return $this
     */
    public function setParty($party)
    {
        $this->party = $party;
        return $this;
    }
}