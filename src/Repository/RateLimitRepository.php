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

class RateLimitRepository extends RepositoryAbstract
{


    /**
     *
     * @param string $type
     * @param string $method
     * @return \Madj2k\TwitterAnalyser\Model\RateLimit
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findOneByTypeAndMethod ($type, $method)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE type = ? AND method = ? ORDER BY reset DESC LIMIT 1';

        $sth = $this->pdo->prepare($sql);
        if ($sth->execute(array($type, $method))) {
            if ($resultDb = $sth->fetch(\PDO::FETCH_ASSOC)) {
                return new $this->model($resultDb);
            };

        } else {
            throw new RepositoryException($sth->errorInfo()[2]);
        }


    }

}