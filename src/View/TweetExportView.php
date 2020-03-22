<?php
namespace Madj2k\TwitterAnalyser\View;

/**
 * TweetExportView
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TweetExportView
{

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\TweetRepository
     */
    protected $tweetRepository;

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\AccountRepository
     */
    protected $accountRepository;

     /**
     * @var \Madj2k\TwitterAnalyser\Repository\ExportRepository
     */
    protected $exportRepository;

    /**
     * @var array
     */
    protected $_cacheUsernames = [];


    /**
     * Constructor
     *
     * @throws \ReflectionException
     */
    public function __construct()
    {

        global $SETTINGS;
        $this->settings = &$SETTINGS;

        // set defaults
        $this->tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();
        $this->accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
        $this->exportRepository = new \Madj2k\TwitterAnalyser\Repository\ExportRepository();

    }


    /**
     * Renders tweets
     *
     * @param \Madj2k\TwitterAnalyser\Model\Tweet $tweet
     * @return string
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function render (\Madj2k\TwitterAnalyser\Model\Tweet $tweet)
    {
        return $this->renderSub($tweet);
    }

    /**
     * Renders sub-tweets
     *
     * @param \Madj2k\TwitterAnalyser\Model\Tweet $tweet
     * @param string $tab
     * @param int $maxWidth
     * @return string
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    protected function renderSub (\Madj2k\TwitterAnalyser\Model\Tweet $tweet, string $tab = '', int $maxWidth = 80)
    {

        $file = $tab . '====================================' . "\n";
        $file .= $tab . $this->anonymizeUsername($tweet->getUserName()). ' (' . date('d.m.y H:i', $tweet->getCreatedAt()) . ') RTs: ' . $tweet->getRetweetCount() . ', FAVs: ' . $tweet->getFavoriteCount() . "\n";
        $file .= $tab . '====================================' . "\n";
        $file .= $tab . wordwrap(str_replace("\n", ' ',  $this->anonymizeText($tweet->getFullText())), $maxWidth, "\n" . $tab)  . "\n";

        if ($tweet->getMedia()) {
            $file .= "\n";
            $file .= $tab . 'Media:';
            foreach(explode('|', $tweet->getMedia()) as $medium) {
                $file .= $tab . preg_replace('#\[.+\]#', '', $medium) . "\n";
            }
        }
        if ($tweet->getHashtagsWordsOnly()) {
            $file .= "\n";
            $file .= $tab . 'Hashtags:' . "\n";
            $file .= $tab . $tweet->getHashtagsWordsOnly() . "\n";
        }

        $file .= "\n";

        if ($subTweets = $this->tweetRepository->findByInReplyToTweetIdOrderedByCreateAt($tweet->getTweetId())) {

            $tab .= "\t";

            /** @var \Madj2k\TwitterAnalyser\Model\Tweet $tweet */
            foreach ($subTweets as $subTweet) {
                $file .= $this->renderSub($subTweet, $tab, $maxWidth);
            }
        }

        return $file;
    }



    /**
     * @param string $username
     * @return string
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    protected function anonymizeUsername ($username)
    {

        if (isset($this->_cacheUsernames[$username])) {
           return $this->_cacheUsernames[$username];
        }

        // do not anonymize verified accounts
        /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
        if ($account = $this->accountRepository->findOneByUserName($username)) {
            if ($account->getVerified()) {
                $this->_cacheUsernames[$username] = $username . ($account->getParty() ? '/' . $account->getParty() : '');
                return $this->_cacheUsernames[$username];
            }
        }

        $checkSum = md5(strtolower($username));

        // check for existing value in database
        /** @var \Madj2k\TwitterAnalyser\Model\Export $exportData */
        if ($exportData = $this->exportRepository->findOneByMd5UserName($checkSum)) {
            $this->_cacheUsernames[$username] =  'user-' . $exportData->getUid() . ($exportData->getParty() ? '/' . $exportData->getParty() : '');
            return $this->_cacheUsernames[$username];
        }

        // else create a new one!
        /** @var \Madj2k\TwitterAnalyser\Model\Export $exportData */
        $exportData = new \Madj2k\TwitterAnalyser\Model\Export();
        $exportData->setMd5UserName($checkSum);
        if (
            ($account)
            && ($party = $account->getParty())
        ){
            $exportData->setParty($party);
        }

        $this->exportRepository->insert($exportData);
        $this->_cacheUsernames[$username] =  'user-' . $exportData->getUid() . ($exportData->getParty() ? '/' . $exportData->getParty() : '');
        return $this->_cacheUsernames[$username];
    }

    /**
     * @param string $text
     * @return string
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    protected function anonymizeText($text)
    {

        return preg_replace_callback('#@([a-zA-Z0-9_-]+)#', [$this, 'anonymizeTextCallback'], $text);
    }

    /**
     * @param $match
     * @return string
     *  @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    protected function anonymizeTextCallback($match)
    {
        return '@' . $this->anonymizeUsername($match[1]);
    }



}