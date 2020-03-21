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
     * Get timeline tweets by account and time
     *
     * @param \Madj2k\TwitterAnalyser\Model\Account $account
     * @param int $fromTime
     * @param int $toTime
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findTimelineTweetsByAccountAndTimeOrderedByCreateAt(\Madj2k\TwitterAnalyser\Model\Account $account, int $fromTime = 0, int $toTime = 0)
    {

        $timeWhere = '';
        if ($fromTime) {
            $timeWhere = ' AND created_at >= ' . intval($fromTime) . ' ';
        }
        if ($toTime) {
            $timeWhere .= ' AND created_at <= ' . intval($toTime) . ' ';
        }

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE account = ? AND type = \'timeline\' AND is_reply = 0' . $timeWhere . ' ORDER BY created_at DESC';

        $result = $this->_findAll($sql, [$account->getUid()]);
        return $result;
    }



    /**
     * Get all timeline tweets
     *
     * @param int $limit
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findTimelineTweetsByCalculationTimestamp (int $limit = 100)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE type = \'timeline\' AND is_reply = 0 AND calculation_timestamp = 0 LIMIT ' . intval($limit);

        $result = $this->_findAll($sql, []);
        return $result;
    }



    /**
     * Get tweets by inReplyToTweetId
     *
     * @param string $tweetId
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findByInReplyToTweetIdOrderedByCreateAt($tweetId)
    {

        $sql = 'SELECT * FROM ' . $this->table . ' WHERE is_reply = 1 AND in_reply_to_tweet_id = ? ORDER BY created_at ASC';

        $result = $this->_findAll($sql, [$tweetId]);
        return $result;
    }



    /**
     * Find by party and hashtags
     *
     * @param array $hashtags
     * @param string $party
     * @param int $averageInteractionTime
     * @param int $limit
     * @return array|null
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function findByHashtagsAndPartyAndAverageInteractionTime (array $hashtags, string $party, int $averageInteractionTime = 14400, int $limit = 100)
    {
        $findInSet = [];
        $params = [];

        // add hashtag filter
        $hashtagFilter = '';
        if ($hashtags) {

            foreach ($hashtags as $hashtag) {
                if (strlen($hashtag) >= 3 ) {
                    $findInSet[] = 'FIND_IN_SET (?, ' . $this->table . '.hashtags_words_only)';
                    $params[] = trim(strtolower($hashtag));
                }
            }
            if (count($findInSet)) {
                $hashtagFilter = 'AND (' . implode(' OR ', $findInSet) . ')';
            }
        }

        // add party filter
        $partyFilter = '';
        if ($party) {
            $partyFilter = 'AND account.party = ?';
            $params[] = $party;
        }

        $sql = 'SELECT * FROM ' . $this->table . '
            INNER JOIN account
                ON ' . $this->table  . '.account = account.uid
                AND account.exported = 0 
                ' . $partyFilter . '
            WHERE ' . $this->table  . '.type = \'timeline\'
                AND ' . $this->table  . '.is_reply = 0
                AND ' . $this->table  . '.exported = 0
                AND ' . $this->table  . '.calculation_timestamp > 0
                AND ' . $this->table  . '.interaction_time > 0
                AND ' . $this->table  . '.reply_count > 0
                AND (' . $this->table  . '.interaction_time / ' . $this->table  . '.reply_count) <= ' . intval($averageInteractionTime) . '
               ' . $hashtagFilter . '
            ORDER BY reply_count DESC LIMIT ' . intval($limit);

        $result = $this->_findAll($sql, $params);
        return $result;
    }


}