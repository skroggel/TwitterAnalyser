<?php
namespace Madj2k\TwitterAnalyser\Repository;


/**
 * UrlRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class UrlRepository extends RepositoryAbstract
{

    /**
     * Get Urls by processed status
     *
     * @param int $processed
     * @param int $limit
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findByProcessed($processed = 0, $limit = 10)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE processed = ? LIMIT ' . intval($limit);

        $result = $this->_findAll($sql, [$processed]);
        return $result;
    }

}