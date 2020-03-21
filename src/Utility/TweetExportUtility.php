<?php
namespace Madj2k\TwitterAnalyser\Utility;

/**
 * TweetExportUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2020
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TweetExportUtility
{

    /**
     * @var \Madj2k\TwitterAnalyser\Repository\TweetRepository
     */
    protected $tweetRepository;

    /**
     * @var \Madj2k\TwitterAnalyser\View\TweetExportView
     */
    protected $tweetExportView;

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
        $this->tweetExportView = new \Madj2k\TwitterAnalyser\View\TweetExportView();
        $this->logUtility = new  \Madj2k\TwitterAnalyser\Utility\LogUtility();

        if (! isset($this->settings['exportPath'])) {

            $pathParts = pathinfo($_SERVER['SCRIPT_FILENAME']);
            $this->settings['exportPath'] =  $pathParts['dirname'] . '/export';
        }

    }



    /**
     * Export tweets
     *
     * @param string $hashtags
     * @param string $party
     * @param int $averageInteractionTime
     * @param int $limit
     * @return int
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function export (string $hashtags, string $party, int $averageInteractionTime = 14400, int $limit = 100)
    {

        // get all tweets that match the given criteria
        $tweetList = $this->tweetRepository->findByHashtagsAndPartyAndAverageInteractionTime(
            explode(',', trim($hashtags)),
            $party,
            $averageInteractionTime,
            $limit
        );

        $timestamp = time();
        $hashTagList = trim(str_replace(',', '-', preg_replace('#[^a-z0-9,]+#', '', strtolower($hashtags))));
        $folderName = $timestamp . $party . '_'  . $hashTagList;
        $filePath = $this->settings['exportPath'] . '/' . $folderName;

        if (! file_exists($filePath)) {
            mkdir ($filePath);
        }

        if ($tweetList) {
            foreach ($tweetList as $key => $tweet) {

                file_put_contents(
                    $filePath . '/' . ($key + 1) . '.txt',
                    $this->tweetExportView->render($tweet)
                );
            }

            return count($tweetList);
        }

        return 0;
    }



}