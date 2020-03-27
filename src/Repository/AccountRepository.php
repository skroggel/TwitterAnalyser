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
     * Count all by time interval but no suggestions and no secondary accounts
     *
     * @param int $fromTimestamp
     * @param int $toTimestamp
     * @param bool $onlyPrimary
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function countAllByTimeInterval (int $fromTimestamp = 0, int $toTimestamp = 0, $onlyPrimary = false)
    {
        // add time filter
        $timeFilter = '';
        if ($fromTimestamp) {
            $timeFilter = ' AND create_timestamp >= ' . intval($fromTimestamp);
            $timeFilter .= ' AND (
                resigned_timestamp <= 0 
                OR resigned_timestamp >= '. intval($fromTimestamp) . '
            )';
        }
        if ($toTimestamp) {
            $timeFilter .= ' AND create_timestamp <= ' . intval($toTimestamp);
        }

        // add primary filter
        $primaryFilter = '';
        if ($onlyPrimary) {
            $primaryFilter = ' AND is_secondary = 0';
        }

        $sql = 'SELECT COUNT(uid) FROM ' . $this->table . ' WHERE is_suggestion = 0 '. $primaryFilter . $timeFilter;

        $result = $this->_countAll($sql);
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
     * Find all sorted by last fetchTimelineTimestamp
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
     * Find all sorted by last fetchAddressedTimestamp
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

        // $sql = 'SELECT * FROM ' . $this->table . ' WHERE is_suggestion = ' . intval($suggestion) . ' AND is_secondary = 0 ORDER BY suggestion_for_name, name ASC';
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE is_suggestion = ' . intval($suggestion) . ' ORDER BY suggestion_for_name, name ASC';

        $result = $this->_findAll($sql, []);
        return $result;
    }

}