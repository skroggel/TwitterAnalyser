<?php
namespace Madj2k\TwitterAnalyser\Utility;

use \Madj2k\TwitterAnalyser\Model\Account;

/**
 * AccountImportUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AccountImportUtility
{

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\TweetRepository
     */
    protected $tweetRepository;


    /**
     * @var \Madj2k\TwitterAnalyser\Repository\AccountRepository
     */
    protected $accountRepository;


    /**
     * @var \Madj2k\TwitterAnalyser\Utility\LogUtility
     */
    protected $logUtility;
    
    /**
     * @var array
     */
    protected $settings;


    /**
     * Constructor
     *
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     * @throws \ReflectionException
     */
    public function __construct()
    {

        global $SETTINGS;
        $this->settings = &$SETTINGS;

        // set defaults
        $this->tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();
        $this->accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
        $this->logUtility = new  \Madj2k\TwitterAnalyser\Utility\LogUtility();
    }


    /**
     * Import primary
     *
     * @param \Madj2k\TwitterAnalyser\Model\Url $url
     * @param string $userName
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function importPrimary (\Madj2k\TwitterAnalyser\Model\Url $url, $userName)
    {

        /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
        $account = new Account();
        $account->setUserName($userName);

        /** @var \Madj2k\TwitterAnalyser\Model\Account $databaseAccount */
        $databaseAccount = $this->accountRepository->findOneByUserName($account->getUserName(), false);
        if (! $databaseAccount) {
            $this->accountRepository->insert($account);
            $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Inserted new account %s found in url with id = %s.', $account->getUserName(), $url->getUid()));
            return true;
        }

        // if it is a secondary account or a suggestion it now becomes a primary one!
        if (
            ($databaseAccount->getIsSecondary())
            || ($databaseAccount->getIsSuggestion())
        ){
            $databaseAccount->setIsSecondary(false);
            $databaseAccount->setIsSuggestion(false);
            $databaseAccount->setSuggestionForName('');

            $this->accountRepository->update($databaseAccount);

            $this->logUtility->log($this->logUtility::LOG_INFO, sprintf('Account %s found in url with id = %s already exists as secondary or suggestion account. Set to primary account now.', $account->getUserName(), $url->getUid()));
            return true;
        }

        $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Account %s found in url with id = %s already exists.', $account->getUserName(), $url->getUid()));
        return false;
    }



    /**
     * Import primary
     *
     * @param \Madj2k\TwitterAnalyser\Model\Url $url
     * @param \stdClass $foundAccount
     * @param string $name
     * @return bool
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function importSecondary (\Madj2k\TwitterAnalyser\Model\Url $url, \stdClass $foundAccount, $name)
    {

        /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
        $account = new Account($foundAccount);
        $account->setIsSuggestion(true)
            ->setSuggestionForName($name);

        // only take verified accounts as suggestions
        if (! $account->getVerified()) {
            return false;
        }

        // check for existing account by userName or userId - userNames may change...
        $checkMethod = 'findOneByUserName';
        $checkValue = $account->getUserName();
        if ($account->getUserId()) {
            $checkMethod = 'findOneByUserId';
            $checkValue = $account->getUserId();
        }

        /** @var \Madj2k\TwitterAnalyser\Model\Account $databaseAccount */
        $databaseAccount = $this->accountRepository->$checkMethod($checkValue, false);
        if (! $databaseAccount) {
            $this->accountRepository->insert($account);
            $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Inserted new account %s as suggestion found in url with id = %s.', $account->getUserName(), $url->getUid()));
            return true;
        }

        // if it is a secondary account we mark it as suggestion!
        if ($databaseAccount->getIsSecondary()) {
            $databaseAccount->setIsSuggestion(true)
                ->setSuggestionForName($name);
            $this->accountRepository->update($databaseAccount);
            $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Account %s found as suggestion in url with id = %s already exists as secondary account. Marked as suggestion.', $account->getUserName(), $url->getUid()));
            return true;
        }

        $this->logUtility->log($this->logUtility::LOG_DEBUG, sprintf('Account %s found as suggestion in url with id = %s already exists.', $account->getUserName(), $url->getUid()));
        return false;
    }
}