<!doctype html>
<?php
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $hashtagUtility = new \Madj2k\TwitterAnalyser\Utility\HashtagUtility();
?>
<html>
    <head>
        <title>TwitterAnalyser - Hashtags</title>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
    </head>
    <body>

        <?php
            $result = [];
            if (isset($_POST['search'])) {

                $processStart = microtime(true);

                // get all hashtags that are used in combination with the given one
                $result = $hashtagUtility->analyse(
                    $_POST['hashtag'],
                    $_POST['dateFrom'],
                    $_POST['dateTo']
                );

                $processTime = microtime(true) - $processStart;
                echo sprintf('<p class="message message--error">Processed in %s seconds.</p>', $processTime);
            }
        ?>

        <h1>Check for hashtag combinations</h1>
        <form method="post" >
            <fieldset>
                <legend>Filter</legend>
                <label for="hashtag">Hashtag to analyse</label>
                <input id="hashtag" type="text" name="hashtag" value="<?php echo (isset($_POST['hashtag']) ? $_POST['hashtag'] : ''); ?>"/>

                <label for="date-from">From date (format: YYYY-mm-dd):</label>
                <input id="date-from" type="text" name="dateFrom" value="<?php echo (isset($_POST['dateFrom']) ? $_POST['dateFrom'] : '2019-07-01'); ?>"/>

                <label for="date-to">To date (format: YYYY-mm-dd):</label>
                <input id="date-to" type="text" name="dateTo" value="<?php echo (isset($_POST['dateTo']) ? $_POST['dateTo'] : '2020-02-28'); ?>"/>

                <button class="save" type="submit" name="search">Search</button>
            </fieldset>
        </form>

        <?php
            if ($result) {
            ?>
                <h2>Result</h2>
                <table class="list">
                    <tr>
                        <th>Hashtag</th>
                        <th>Occurrence (absolute)</th>
                        <th>Occurrence (relative)</th>
                    </tr>
                    <?php
                    foreach ($result as $hashtag => $counter) {
                        ?>
                        <tr>
                            <td><?php echo $hashtag ?></td>
                            <td><?php echo $counter ?></td>
                            <td><?php echo round((100 / $result[$_POST['hashtag']] * $counter), 2) ?>%</td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            <?php
            }
        ?>


    </body>

</html>