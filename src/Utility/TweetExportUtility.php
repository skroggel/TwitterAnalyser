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
     * @param string $fromDate
     * @param string $toDate
     * @param int $averageInteractionTime
     * @param int $limit
     * @param bool $dryRun
     * @return array
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function export (string $hashtags = '', string $party = '', string $fromDate = '', string $toDate = '', int $averageInteractionTime = 14400, int $limit = 0, bool $dryRun = false)
    {

        // get all tweets that match the given criteria
        $tweetList = $this->tweetRepository->findTimelineTweetsByHashtagsAndPartyAndTimeIntervalAndAverageInteractionTime(
            explode("\n", trim($hashtags)),
            $party,
            strtotime($fromDate),
            strtotime($toDate),
            $averageInteractionTime,
            ($dryRun ? 0 : $limit)
        );

        if (! $dryRun) {

            $timestamp = time();
            $hashTagList = trim(strtolower($hashtags));
            $folderName = $timestamp . '-' . $party;
            $filePath = $this->settings['exportPath'] . '/' . $folderName;
            $zipFile = $this->settings['exportPath'] . '/' . $folderName . '.zip';
            $zipCommand = 'zip -j ' . $zipFile . ' ' . $filePath . '/*';

            if (!file_exists($filePath)) {
                mkdir($filePath);
            }

            if ($tweetList) {

                // hashtag list
                file_put_contents(
                    $filePath . '/hashtags.txt',
                    $hashTagList
                );

                foreach ($tweetList as $key => $tweet) {

                    file_put_contents(
                        $filePath . '/' . ($key + 1) . '.txt',
                        $this->tweetExportView->render($tweet)
                    );
                }
                exec($zipCommand);

                return [
                    'count' => count($tweetList),
                    'zip'   => 'export/' . $folderName . '.zip'
                ];

            }
        } else {
            return [
                'count' => count($tweetList)
            ];
        }

        return [];
    }



}