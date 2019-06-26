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
    public function findByProcessedSortedByCreateTimestamp($processed = 0, $limit = 10)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE processed = ? ORDER BY create_timestamp ASC LIMIT ' . intval($limit);

        $result = $this->_findAll($sql, [$processed]);
        return $result;
    }


    /**
     * Find one by url and processed status
     *
     * @param string $url
     * @param int $processed
     * @return \Madj2k\TwitterAnalyser\Model\Url|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findOneByUrlAndProcessed(string $url, $processed = 0)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE url = ? AND processed = ?';

        /** @var \Madj2k\TwitterAnalyser\Model\Url $result */
        $result = $this->_findOne($sql, [$url, $processed]);
        return $result;
    }

}