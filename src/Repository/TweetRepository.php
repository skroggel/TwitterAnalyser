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
     * Get latest tweet of user
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @param string $type
     * @return \Madj2k\TwitterAnalyser\Model\Tweet|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException

    public function findLastByAccountAndType(\Madj2k\TwitterAnalyser\Model\Account $account, string $type)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE account = ? AND type = ? ORDER BY tweet_id DESC';

        /** @var \Madj2k\TwitterAnalyser\Model\Tweet $result
        $result = $this->_findOne($sql, array($account->getUid(), $type));
        return $result;
    }*/


    /**
     * Get tweets by account and type
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @param string $type
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findAllByAccountAndTypeOrderedByCreateAt(\Madj2k\TwitterAnalyser\Model\Account $account, $type = 'timeline')
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE account = ? AND type = ? ORDER BY created_at DESC';

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