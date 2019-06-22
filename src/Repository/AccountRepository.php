<?php
namespace Madj2k\TwitterAnalyser\Repository;


/**
 * AccountRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class AccountRepository extends RepositoryAbstract
{

    /**
     * Find one by type and method
     *
     * @param string $type
     * @param string $method
     * @return \Madj2k\TwitterAnalyser\Model\Account|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findOneByTypeAndMethod (string $type, string $method)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE type = ? AND method = ? ORDER BY reset DESC';

        /** @var \Madj2k\TwitterAnalyser\Model\Account $result */
        $result = $this->_findOne($sql, [$type, $method]);
        return $result;
    }


    /**
     * Find all sorted by last fetch timestamp
     *
     * @param int $limit
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findAllSortedByLastFetchTimeline (int $limit)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE 1 = 1 ORDER BY fetch_timeline_timestamp ASC LIMIT ' . intval($limit);

        $result = $this->_findAll($sql, []);
        return $result;
    }

    /**
     * Find all sorted by last fetch timestamp
     *
     * @param int $limit
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findAllSortedByLastFetchAddressed (int $limit)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE 1 = 1 ORDER BY fetch_addressed_timestamp ASC LIMIT ' . intval($limit);

        $result = $this->_findAll($sql, []);
        return $result;
    }
}