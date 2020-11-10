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
     * @var \Madj2k\TwitterAnalyser\View\Tweet\Export\ExportViewInterface
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
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     * @throws \ReflectionException
     */
    public function __construct()
    {

        global $SETTINGS;
        $this->settings = &$SETTINGS;

        // set defaults
        $this->tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();
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
     * @param string $type
     * @return array
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     * @throws \ReflectionException
     */
    public function export (
        string $hashtags = '',
        string $party = '',
        string $fromDate = '',
        string $toDate = '',
        int $averageInteractionTime = 14400,
        int $replyCount = 1,
        int $limit = 0,
        bool $dryRun = false,
        string $type = 'txt'
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

                $view = new \Madj2k\TwitterAnalyser\View\Tweet\Export\TextExportView($filePath);
                $exportClass = '\\Madj2k\\TwitterAnalyser\\View\\Tweet\\Export\\' . ucfirst(strtolower($type)) . 'ExportView';
                if (class_exists($exportClass)) {
                    $view = new $exportClass($filePath);
                }

                // hashtag list
                file_put_contents(
                    $filePath . '/_hashtags.txt',
                    $hashTagList
                );

                // set timezone to UTC
                $timeZone = date_default_timezone_get();
                date_default_timezone_set('UTC');

                // do the export
                $view->render($tweetList);

                // reset timezone
                date_default_timezone_set($timeZone);

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