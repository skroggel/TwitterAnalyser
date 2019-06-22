
<?php

// error_reporting(E_ALL);
require_once(__DIR__ . '/../config/settings.php');
require_once(__DIR__ . '/../vendor/autoload.php');


$accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
$account = $accountRepository->findOneByUid(1);
$tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();
$timelineTweets = $tweetRepository->findAllByAccountAndTypeOrderedByCreateAt($account);
?>


<ul>
    <?php
        /** @var Madj2k\TwitterAnalyser\Model\Tweet $tweet */
        foreach ($timelineTweets as $tweet) {
    ?>
    <li data-tweet-id="<?php echo $tweet->getTweetId(); ?>">
        <?php
            echo $tweet->getUserName() .' (' . date('m.d.y H:i', $tweet->getCreatedAt()) . '-' . $tweet->getCreatedAt().  '): ' . $tweet->getFullText();
            // now get conversation
            if ($conversation = $tweetRepository->findAllByInReplyToTweetIdAndTypeOrderedByCreateAt($tweet->getTweetId(), 'searchTo')) {
        ?>
        <ul>
            <?php
            /** @var Madj2k\TwitterAnalyser\Model\Tweet $conversationTweet */
                foreach ($conversation as $conversationTweet)  {
            ?>
                <li data-tweet-id="<?php echo $conversationTweet->getTweetId(); ?>">
                    <?php
                        echo $conversationTweet->getUserName() .' (' . date('m.d.y H:i', $conversationTweet->getCreatedAt()) . '): ' . $conversationTweet->getFullText();

                        // get sub-conversation if available
                        if ($subConversation = $tweetRepository->findAllByInReplyToTweetIdAndTypeOrderedByCreateAt($conversationTweet->getTweetId(), 'searchTo')) {
                    ?>
                    <ul>
                        <?php
                            /** @var Madj2k\TwitterAnalyser\Model\Tweet $subConversationTweet */
                            foreach ($subConversation as $subConversationTweet)  {
                            ?>
                            <li data-tweet-id="<?php echo $subConversationTweet->getTweetId(); ?>">
                                <?php
                                echo $subConversationTweet->getUserName() .' (' . date('m.d.y H:i',$subConversationTweet->getCreatedAt()) . '): ' . $subConversationTweet->getFullText()
                                ?>
                            </li>
                        <?php
                            }
                        ?>

                    </ul>
                    <?php
                        }
                    ?>
                </li>
            <?php
                }
            ?>

        </ul>
        <?php
            }
        ?>

    <?php
        }
    ?>
    </li>
</ul>

tests