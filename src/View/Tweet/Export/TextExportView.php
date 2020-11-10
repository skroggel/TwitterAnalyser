<?php
namespace Madj2k\TwitterAnalyser\View\Tweet\Export;

/**
 * TextExportView
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TextExportView extends ExportViewAbstract
{



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

        $file = $tab . '====================================' . "\n";
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