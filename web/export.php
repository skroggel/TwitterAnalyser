<!doctype html>
<?php
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $exportUtility = new \Madj2k\TwitterAnalyser\Utility\TweetExportUtility();
$tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();
echo $tweetRepository->findOneByTweetId('1232953003105275904')->getFulltext();
exit();
?>
<html>
    <head>
        <title>TwitterAnalyser - Export</title>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
    </head>
    <body>

        <?php
            if (isset($_POST['search'])) {

                // get all tweets that match the given criteria
                $tweetCount = $exportUtility->export(
                    $_POST['hashtags'],
                    $_POST['party'],
                    intval($_POST['averageInteractionTime']),
                    intval($_POST['limit'])
                );

                if ($tweetCount) {
                    echo sprintf('<p class="message">Exported %s conversations.</p>', $tweetCount);
                } else {
                    echo sprintf('<p class="message message--error">No conversations found.</p>');
                }
            }
        ?>

        <form method="post" >
            <fieldset>
                <legend>Filter</legend>
                <label for="hashtags">Hashtags (comma-separated list)</label>
                <textarea id="hashtags" name="hashtags" rows="4" cols="50"><?php echo (isset($_POST['hashtags']) ? $_POST['hashtags'] : ''); ?></textarea>

                <label for="party">Party:</label>
                <select id="party" name="party">
                    <option value="">-</option>
                    <option value="cdu" <?php if (isset($_POST['party']) && $_POST['party'] == 'cdu') { echo 'selected="selected"'; }?>>CDU/CSU</option>
                    <option value="spd" <?php if (isset($_POST['party']) && $_POST['party'] == 'spd') { echo 'selected="selected"'; }?>>SPD</option>
                    <option value="gruene" <?php if (isset($_POST['party']) && $_POST['party'] == 'gruene') { echo 'selected="selected"'; }?>>Bündnis 90/Die Grünen</option>
                    <option value="linke" <?php if (isset($_POST['party']) && $_POST['party'] == 'linke') { echo 'selected="selected"'; }?>>DIE LINKE</option>
                    <option value="fdp" <?php if (isset($_POST['party']) && $_POST['party'] == 'fdp') { echo 'selected="selected"'; }?>>FDP</option>
                    <option value="afd" <?php if (isset($_POST['party']) && $_POST['party'] == 'afd') { echo 'selected="selected"'; }?>>AfD</option>
                    <option value="fraktionslos" <?php if (isset($_POST['party']) && $_POST['party'] == 'fraktionslos') { echo 'selected="selected"'; }?>>fraktionslos</option>
                </select>

                <label for="avg-interaction-time">Average Interaction Time (in Sec):</label>
                <input id="avg-interaction-time" type="text" name="averageInteractionTime" value="<?php echo (isset($_POST['averageInteractionTime']) ? intval($_POST['averageInteractionTime']) : '14400'); ?>"/>

                <label for="limit">Limit:</label>
                <input id="limit" type="text" name="limit" value="<?php echo (isset($_POST['limit']) ? intval($_POST['limit']) : '100'); ?>"/>


                <button class="save" type="submit" name="search">Search</button>
            </fieldset>
        </form>

    </body>

</html>