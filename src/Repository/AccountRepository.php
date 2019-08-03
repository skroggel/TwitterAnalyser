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
     * Find one by uid and time
     *
     * @param int $uid
     * @param int $fromTime
     * @param int $toTime
     * @return \Madj2k\TwitterAnalyser\Model\Account|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException

    public function findOneByUidAndTime (int $uid, int $fromTime = 0, int $toTime = 0)
    {
        $timeWhere = '';
        if ($fromTime) {
            $timeWhere = 'AND created_at >= ' . intval($fromTime);
        }
        if ($toTime ) {
            $timeWhere = 'AND created_at <= ' . intval($toTime);
        }

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE is_suggestion = 0 AND is_secondary = 0 AND uid = ? ' . $timeWhere;

        /** @var \Madj2k\TwitterAnalyser\Model\Account $result
        $result = $this->_findOne($sql, [$uid]);
        return $result;
    }
    */

    /**
     * Count all but no suggestions and no secondary accounts
     *
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function countAll ()
    {

        $sql = 'SELECT COUNT(uid) FROM ' . $this->table . ' WHERE is_suggestion = 0 AND is_secondary = 0';

        $result = $this->_countAll($sql, []);
        return $result;
    }

    /**
     * Find all but no suggestions and no secondary accounts
     *
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findAll ()
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE is_suggestion = 0 AND is_secondary = 0 ORDER BY name ASC';

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
    public function findAllSortedByLastFetchTimeline (int $limit)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE is_suggestion = 0 AND is_secondary = 0 ORDER BY fetch_timeline_timestamp ASC LIMIT ' . intval($limit);

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

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE is_suggestion = 0 AND is_secondary = 0 ORDER BY fetch_addressed_timestamp ASC LIMIT ' . intval($limit);

        $result = $this->_findAll($sql, []);
        return $result;
    }

    /**
     * Find all suggestions
     *
     * @param bool $suggestion
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findBySuggestionOrderedBySuggestionForNameAndName (bool $suggestion = true)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE is_suggestion = ' . intval($suggestion) . ' AND is_secondary = 0 ORDER BY suggestion_for_name, name ASC';

        $result = $this->_findAll($sql, []);
        return $result;
    }

}