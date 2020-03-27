<!doctype html>
<?php
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $exportUtility = new \Madj2k\TwitterAnalyser\Utility\TweetExportUtility();
?>
<html>
    <head>
        <title>TwitterAnalyser - Export</title>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
    </head>
    <body>

        <?php
            if (isset($_POST['search'])) {

                $processStart = microtime(true);

                // get all tweets that match the given criteria
                $result = $exportUtility->export(
                    $_POST['hashtags'],
                    $_POST['party'],
                    $_POST['dateFrom'],
                    $_POST['dateTo'],
                    intval($_POST['averageInteractionTime']),
                    intval($_POST['minReplyCount']),
                    intval($_POST['limit']),
                    (isset($_POST['dryRun']) ? 1 : 0)
                );

                $processTime = microtime(true) - $processStart;

                if ($result) {
                    if (isset($_POST['dryRun'])) {
                        echo sprintf('<p class="message">The given params will find a maximum of %s conversations to export. Processed in %s seconds.</p>', $result['count'], $processTime);
                    } else {
                        echo sprintf('<p class="message">Exported %s conversations. You can download them <a href="%s" target="_blank">here</a>. Processed in %s seconds.</p>', $result['count'], $result['zip'], $processTime);
                    }
                } else {
                    echo sprintf('<p class="message message--error">No conversations found. Processed in %s seconds.</p>', $processTime);
                }
            }
        ?>

        <h1>Export conversations</h1>
        <form method="post" >
            <fieldset>
                <legend>Filter</legend>
                <label for="hashtags">Hashtags (each hashtag in a new line)</label>
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

                <label for="min-reply-count">Min-Reply-Count :</label>
                <input id="min-reply-count" type="text" name="minReplyCount" value="<?php echo (isset($_POST['minReplyCount']) ? intval($_POST['minReplyCount']) : '1'); ?>"/>

                <label for="limit">Limit:</label>
                <input id="limit" type="text" name="limit" value="<?php echo (isset($_POST['limit']) ? intval($_POST['limit']) : '10'); ?>"/>

                <label for="date-from">From date (format: YYYY-mm-dd):</label>
                <input id="date-from" type="text" name="dateFrom" value="<?php echo (isset($_POST['dateFrom']) ? $_POST['dateFrom'] : '2019-07-01'); ?>"/>

                <label for="date-to">To date (format: YYYY-mm-dd):</label>
                <input id="date-to" type="text" name="dateTo" value="<?php echo (isset($_POST['dateTo']) ? $_POST['dateTo'] : '2020-02-29'); ?>"/>

                <label>
                    <input type="checkbox" name="dryRun" value="1" checked="checked"/>Dry Run
                </label>

                <button class="save" type="submit" name="search">Search</button>
            </fieldset>
        </form>

    </body>

</html>