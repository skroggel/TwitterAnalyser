<?php
namespace Madj2k\TwitterAnalyser\Repository;


/**
 * RateLimitRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class RateLimitRepository extends \Madj2k\SpencerBrown\Repository\RepositoryAbstract
{


    /**
     * Find one by method and category
     *
     * @param string $category
     * @param string $method
     * @return \Madj2k\TwitterAnalyser\Model\RateLimit|null
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    public function findOneByCategoryAndMethod (string $category, string $method)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE category = ? AND method = ? ORDER BY reset DESC';

        /** @var \Madj2k\TwitterAnalyser\Model\RateLimit $result */
        $result = $this->_findOne($sql, [$category, $method]);
        return $result;

    }

}