<?php
namespace Madj2k\TwitterAnalyser\Repository;


/**
 * TweetRepository
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

class TweetRepository extends RepositoryAbstract
{


     /**
     * Get tweets by account and type
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @param string $type
     * @param int $fromTime
     * @param int $toTime
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findAllByAccountAndTypeAndTimeOrderedByCreateAt(\Madj2k\TwitterAnalyser\Model\Account $account, string $type = 'timeline', int $fromTime = 0, int $toTime = 0)
    {

        $timeWhere = '';
        if ($fromTime) {
            $timeWhere = ' AND created_at >= ' . intval($fromTime) . ' ';
        }
        if ($toTime) {
            $timeWhere .= ' AND created_at <= ' . intval($toTime) . ' ';
        }

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE account = ? AND type = ? ' . $timeWhere . ' ORDER BY created_at DESC';

        $result = $this->_findAll($sql, [$account->getUid(), $type]);
        return $result;
    }



    /**
     * Get tweets by inReplyToTweetId
     *
     * @param string $tweetId
     * @param string $type
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findAllByInReplyToTweetIdAndTypeOrderedByCreateAt($tweetId,  $type = 'timeline')
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE in_reply_to_tweet_id = ? AND type = ? ORDER BY created_at ASC';

        $result = $this->_findAll($sql, [$tweetId, $type]);
        return $result;
    }


}