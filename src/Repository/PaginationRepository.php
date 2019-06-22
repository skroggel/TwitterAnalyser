<?php
namespace Madj2k\TwitterAnalyser\Repository;


/**
 * PaginationRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class PaginationRepository extends RepositoryAbstract
{

    /**
     * Find one by account and type
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @param string $type
     * @return \Madj2k\TwitterAnalyser\Model\Pagination|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findOneByAccountAndType (\Madj2k\TwitterAnalyser\Model\Account $account, string $type)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE account = ? AND type = ?';

        /** @var \Madj2k\TwitterAnalyser\Model\Pagination $result */
        $result = $this->_findOne($sql, [$account->getUid(), $type]);
        return $result;

    }

}