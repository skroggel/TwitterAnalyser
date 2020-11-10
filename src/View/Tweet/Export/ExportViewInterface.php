<?php
namespace Madj2k\TwitterAnalyser\View\Tweet\Export;

/**
 * ViewInterface
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
interface ExportViewInterface
{

    /**
     * Constructor
     *
     * @param string $filePath
     * @throws \ReflectionException
     */
    public function __construct(string $filePath);


    /**
     * Renders tweets
     *
     * @param array $tweetList
     * @return void
     */
    public function render (array $tweetList);


    /**
     * Renders sub-tweets
     *
     * @param \Madj2k\TwitterAnalyser\Model\Tweet $tweet
     * @param string $tab
     * @param int $maxWidth
     * @return string
     * @throws \Madj2k\SpencerBrown\Repository\RepositoryException
     */
    public function renderSub (\Madj2k\TwitterAnalyser\Model\Tweet $tweet, string $tab = '', int $maxWidth = 80);

}