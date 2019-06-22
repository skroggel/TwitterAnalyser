<?php
namespace Madj2k\TwitterAnalyser\Utility;

/**
 * PaginationUtility
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel 2019
 * @package Madj2k_TwitterAnalyser
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PaginationUtility
{


    /**
     * @var \Madj2k\TwitterAnalyser\Repository\PaginationRepository
     */
    protected $paginationRepository;


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
        $this->paginationRepository = new \Madj2k\TwitterAnalyser\Repository\PaginationRepository();

    }

    /**
     * Get constraints based on results
     *
     * @param \Madj2k\TwitterAnalyser\Model\Pagination $pagination
     * @param string $type
     * @param array $constraints
     * @return \Madj2k\TwitterAnalyser\Model\Pagination $pagination
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     * @see https://developer.twitter.com/en/docs/tweets/timelines/guides/working-with-timelines.html
     */
    public function getPagination (\Madj2k\TwitterAnalyser\Model\Account $account, string $type, array &$constraints)
    {

        // check for pagination - or create on
        /** @var \Madj2k\TwitterAnalyser\Model\Pagination $pagination */
        if (! $pagination = $this->paginationRepository->findOneByAccountAndType($account, $type)) {
            $pagination = new \Madj2k\TwitterAnalyser\Model\Pagination();
            $pagination->setAccount($account->getUid())
                ->setType($type);

            $this->paginationRepository->insert($pagination);
        }

        if ($pagination->getSinceId()) {
            $constraints[] = 'since_id=' . $pagination->getSinceId();
        }
        if ($pagination->getMaxId()) {
            $constraints[] = 'max_id=' . $pagination->getMaxId();
        }

        return $pagination;
    }


    /**
     * Set constraints based on results
     *
     * @param \Madj2k\TwitterAnalyser\Model\Pagination $pagination
     * @param array $jsonResult
     * @return void
     * @throws \Madj2k\TwitterAnalyser\Repository\RepositoryException
     * @see https://developer.twitter.com/en/docs/tweets/timelines/guides/working-with-timelines.html
     */
    public function setPagination (\Madj2k\TwitterAnalyser\Model\Pagination $pagination, array $jsonResult)
    {
        // if there are results returned...
        if (count($jsonResult) > 0) {

            // save highest and lowest ever processed tweet-id
            if ($pagination->getHighestId() < $jsonResult[0]->id) {
                $pagination->setHighestId($jsonResult[0]->id);
            }

            if (
                ($pagination->getLowestId() > $jsonResult[count($jsonResult)-1]->id)
                || ($pagination->getLowestId() < 1)
            ) {
                $pagination->setLowestId($jsonResult[count($jsonResult)-1]->id);
            }

            // check if lowest processed id is still above the limit defined by sinceId (from sinceId upwards)
            // if so we start from the lowest processed id next time in order to prevent duplicates
            if ($pagination->getLowestId() >= $pagination->getSinceId()) {
                $pagination->setMaxId($pagination->getLowestId() - 1);
            }
            $this->paginationRepository->update($pagination);

        // if there are no results left, we set the highest processed tweet id as new sinceId (from sinceId upwards)
        // and reset maxId (from maxId downwards) to zero, so that we get the newest tweets again
        } else {

            $pagination->setSinceId($pagination->getHighestId());
            $pagination->setMaxId(0);
            $this->paginationRepository->update($pagination);
        }
    }
}