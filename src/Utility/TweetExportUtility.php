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
     * @param int $replyCount
     * @param int $limit
     * @param bool $dryRun
     * @return array
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     */
    public function export (
        string $hashtags = '',
        string $party = '',
        string $fromDate = '',
        string $toDate = '',
        int $averageInteractionTime = 14400,
        int $replyCount = 1,
        int $limit = 0,
        bool $dryRun = false
    ) {

        // get all tweets that match the given criteria
        $tweetList = $this->tweetRepository->findTimelineTweetsByHashtagsAndPartyAndTimeIntervalAndAverageInteractionTimeAndReplyCount(
            explode("\n", trim($hashtags)),
            $party,
            strtotime($fromDate),
            strtotime($toDate),
            $averageInteractionTime,
            $replyCount,
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
                    $filePath . '/_hashtags.txt',
                    $hashTagList
                );

                // set timezone to UTC
                $timeZone = date_default_timezone_get();
                date_default_timezone_set('UTC');

                /** @var \Madj2k\TwitterAnalyser\Model\Tweet $tweet **/
                $statistics = '';
                foreach ($tweetList as $key => $tweet) {

                    $avgInteractionTime = round($tweet->getInteractionTime() / $tweet->getReplyCount());
                    $statistics .= '========================================' . "\n" .
                        'Statistics for export number ' . ($key + 1) . "\n" .
                        '========================================' . "\n" .
                        'replyCount: ' . $tweet->getReplyCount() . "\n" .
                        'avgInteractionTime: ' . date('H:i:s', $avgInteractionTime) . ' (' . $avgInteractionTime . ' sec)' . "\n\n";

                    file_put_contents(
                        $filePath . '/' . ($key + 1) . '.txt',
                        $this->tweetExportView->render($tweet)
                    );
                }
                // reset timezone
                date_default_timezone_set($timeZone);

                // statistics
                file_put_contents(
                    $filePath . '/_statistics.txt',
                    $statistics
                );

                exec($zipCommand);

                return [
                    'count' => count($tweetList),
                    'zip'   => 'export/' . $folderName . '.zip',
                ];

            }
        } else {
            return [
                'count' => count($tweetList),
            ];
        }

        return [];
    }



}