<?php
namespace Madj2k\TwitterAnalyser\Model;
use Madj2k\TwitterAnalyser\Utility\GeneralUtility;

/**
 * ModelAbstract
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class ModelAbstract
{

    /**
     * @var int
     */
    protected $uid = 0;


    /**
     * @var int
     */
    protected $createTimestamp;


    /**
     * @var int
     */
    protected $changeTimestamp;


    /**
     * @var array
     */
    protected $_mapping;


    /**
     * Constructor
     *
     * @param array|object $data
     */
    public function __construct($data = [])
    {
        // set defaults
        $this->changeTimestamp = $this->createTimestamp = time();

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        // set given data to model properties
        if (
            (is_array($data))
            && (count($data) > 0)
        ) {
            foreach ($data as $key => $value) {
                $setter = 'set' . GeneralUtility::underscoreToCamelCase($key, true);
                if (method_exists($this, $setter)) {
                    $this->$setter($value);
                }
            }

            // mapping for field to property
            foreach ($this->_mapping as $source => $target) {
                if (isset($data[$source])) {
                    $setter = 'set' . GeneralUtility::underscoreToCamelCase($target, true);
                    if (method_exists($this, $setter)) {
                        $this->$setter($data[$source]);
                    }
                }
            }
        }
    }


    /**
     * Gets uid
     *
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }


    /**
     * Sets uid
     *
     * @param int $uid
     * @return $this
     */
    public function setUid($uid)
    {
        $this->uid = intval($uid);
        return $this;
    }

    
    
    /**
     * Gets createTimestamp
     *
     * @return int
     */
    public function getCreateTimestamp()
    {
        return $this->createTimestamp;
    }


    /**
     * Sets createTimestamp
     *
     * @param int $timestamp
     * @return $this
     */
    public function setCreateTimestamp($timestamp)
    {
        $this->createTimestamp = intval($timestamp);
        return $this;
    }


    /**
     * Gets ChangeTimestamp
     *
     * @return int
     */
    public function getChangeTimestamp()
    {
        return $this->changeTimestamp;
    }


    /**
     * Sets ChangeTimestamp
     *
     * @param int $timestamp
     * @return $this
     */
    public function setChangeTimestamp($timestamp)
    {
        $this->changeTimestamp = intval($timestamp);
        return $this;
    }
}