<?php
namespace Madj2k\TwitterAnalyser\View\Tweet\Export;

/**
 * ViewAbstract
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class ExportViewAbstract implements ExportViewInterface
{


    /**
     * @var string
     */
    protected $filePath = '';

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
    protected $_cacheAnonymizedUsernames = [];

    /**
     * @var array
     */
    protected $_cacheVerifiedUsernames = [];

    /**
     * @var array
     */
    protected $_userInteractionStatistics = [];



    /**
     * Constructor
     *
     * @param string $filePath
     * @throws \ReflectionException
     */
    public function __construct(string $filePath)
    {

        global $SETTINGS;
        $this->settings = &$SETTINGS;

        // set defaults
        $this->filePath = $filePath;
        $this->tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();
        $this->accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
        $this->exportRepository = new \Madj2k\TwitterAnalyser\Repository\ExportRepository();

    }

    /**
     * Renders tweets
     *
     * @param array $tweetList
     * @return void
     */
    public function render (array $tweetList)
    {

        try {

            /** @var \Madj2k\TwitterAnalyser\Model\Tweet $tweet * */
            foreach ($tweetList as $key => $tweet) {

                // write files
                file_put_contents(
                    $this->filePath . '/' . ($key + 1) . '.txt',
                    $this->renderSub($tweet)
                );

                // log statistics
                $this->logStatistics($tweet, $key);
            }

        } catch (\Exception $e) {
            // do nothing
        }
    }

    /**
     * @param \Madj2k\TwitterAnalyser\Model\Tweet $tweet
     * @param int $cnt
     * @return void
     */
    protected function logStatistics (\Madj2k\TwitterAnalyser\Model\Tweet $tweet, $cnt)
    {

        $avgInteractionTime = round($tweet->getInteractionTime() / $tweet->getReplyCount());
        $statistics = '========================================' . "\n" .
            'Statistics for export number ' . ($cnt + 1) . "\n" .
            '========================================' . "\n" .
            'replyCount: ' . $tweet->getReplyCount() . "\n" .
            'userCount: ' . count($this->_userInteractionStatistics) . "\n" .
            'avgInteractionTime: ' . date('H:i:s', $avgInteractionTime) . ' (' . $avgInteractionTime . ' sec)' . "\n\n".
            'userInteractionStatistics: ' . "\n";

        ksort($this->_userInteractionStatistics);
        foreach ($this->_userInteractionStatistics as $user => $count) {
            $statistics .= "\t" . $user .' = ' . $count . "\n";
        }
        $statistics .=  "\n";

        $this->resetUserInteractionStatistics();

        // statistics
        file_put_contents(
            $this->filePath . '/_statistics.txt',
            $statistics,
            FILE_APPEND
        );
    }



    /**
     * @param string $username
     * @return string
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    protected function anonymizeUsername ($username)
    {

        if (isset($this->_cacheAnonymizedUsernames[$username])) {
           return $this->_cacheAnonymizedUsernames[$username];
        }

        // do not anonymize verified accounts
        /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
        if ($account = $this->accountRepository->findOneByUserName($username)) {
            if ($account->getVerified()) {
                $this->_cacheVerifiedUsernames[$username] = $this->_cacheAnonymizedUsernames[$username] = $username . ($account->getParty() ? '/' . $account->getParty() : '');
                return $this->_cacheAnonymizedUsernames[$username];
            }
        }

        $checkSum = md5(strtolower($username));

        // check for existing value in database
        /** @var \Madj2k\TwitterAnalyser\Model\Export $exportData */
        if ($exportData = $this->exportRepository->findOneByMd5UserName($checkSum)) {
            $this->_cacheAnonymizedUsernames[$username] =  'user-' . $exportData->getUid() . ($exportData->getParty() ? '/' . $exportData->getParty() : '');
            return $this->_cacheAnonymizedUsernames[$username];
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
        $this->_cacheAnonymizedUsernames[$username] =  'user-' . $exportData->getUid() . ($exportData->getParty() ? '/' . $exportData->getParty() : '');
        return $this->_cacheAnonymizedUsernames[$username];
    }

    /**
     * @param string $text
     * @return string
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    protected function anonymizeText($text)
    {
        return preg_replace_callback('#@([a-zA-Z0-9_-]+)#', [$this, 'anonymizeTextCallback'], $text);
    }


    /**
     * @param $match
     * @return string
     *  @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    protected function anonymizeTextCallback($match)
    {
        return '@' . $this->anonymizeUsername($match[1]);
    }

    /**
     * Check if it is a verified username
     *
     * @param string $username
     * @return bool
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    protected function isVerfiedUsername ($username)
    {

        if (isset($this->_cacheVerifiedUsernames[$username])) {
            return isset($this->_cacheVerifiedUsernames[$username]);
        }

        $this->anonymizeUsername($username);
        return isset($this->_cacheVerifiedUsernames[$username]);
    }

    /**
     * Resets user interaction statistics
     *
     * @return void
     */
    protected function resetUserInteractionStatistics ()
    {
        $this->_userInteractionStatistics = [];
    }


    /**
     * Sets user interaction statistics for a given user
     *
     * @param string $user
     * @return void
     */
    protected function setUserInteractionStatistics ($user)
    {
        if (isset($this->_userInteractionStatistics[$user])) {
            $this->_userInteractionStatistics[$user]++;
        } else {
            $this->_userInteractionStatistics[$user] = 1;
        }
    }


}