<?php
namespace Madj2k\TwitterAnalyser\View\Tweet\Export;

/**
 * StructuredExportView
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class StructuredExportView extends ExportViewAbstract
{

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
            $texts = '';
            foreach ($tweetList as $key => $tweet) {

                // get all texts
                $texts .= '#TEXT ' . ($key + 1) . "\n";
                $texts .= $this->renderSub($tweet);
                $texts .= "\n";

                // log statistics
                $this->logStatistics($tweet, $key);
            }

            // write file
            file_put_contents(
                $this->filePath . '/export.txt',
                $texts
            );

        } catch (\Exception $e) {
            // do nothing
        }
    }



    /**
     * Renders sub-tweets
     *
     * @param \Madj2k\TwitterAnalyser\Model\Tweet $tweet
     * @param string $tab
     * @param int $maxWidth
     * @return string
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    public function renderSub (\Madj2k\TwitterAnalyser\Model\Tweet $tweet, string $tab = '', int $maxWidth = 80)
    {
        $file = '';
        if ($this->isVerfiedUsername($tweet->getUserName())) {
            $file .= '#SPEAKER ' . $this->anonymizeUsername($tweet->getUserName()) . "\n";
        } else {
            $file .= '#SPEAKER Anonymous ' . "\n";
        }
        $file .= $tab . '====================================' . "\n";
        $file .= $tab . $this->anonymizeUsername($tweet->getUserName()) . ' (' . date('d.m.y H:i', $tweet->getCreatedAt()) . ') RTs: ' . $tweet->getRetweetCount() . ', FAVs: ' . $tweet->getFavoriteCount() . "\n";
        $file .= $tab . '====================================' . "\n";
        $file .= $tab . wordwrap(str_replace("\n", ' ',  html_entity_decode($this->anonymizeText($tweet->getFullText()))), $maxWidth, "\n" . $tab)  . "\n";

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

        //if ($this->isVerfiedUsername($tweet->getUserName())) {
            $file .= '#ENDSPEAKER' . "\n";
       // }
        $file .= "\n";

        // set statistics
        $this->setUserInteractionStatistics($this->anonymizeUsername($tweet->getUserName()));

        // get sub-tweets
        if ($subTweets = $this->tweetRepository->findByInReplyToTweetIdOrderedByCreateAt($tweet->getTweetId())) {

            $tab .= "\t";

            /** @var \Madj2k\TwitterAnalyser\Model\Tweet $tweet */
            foreach ($subTweets as $subTweet) {
                $file .= $this->renderSub($subTweet, $tab, $maxWidth);
            }
        }

        return $file;
    }


}