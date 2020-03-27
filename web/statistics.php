<!doctype html>
<?php
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
    $tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();

?>
<html>
    <head>
        <title>TwitterAnalyser – Statistics</title>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
    </head>
    <body>
        <?php
            $result = [];
            if (isset($_POST['search'])) {

                $processStart = microtime(true);

                // count accounts
                $result['accounts'] = $accountRepository->countAllByTimeInterval(
                    strtotime($_POST['dateFrom']),
                    strtotime($_POST['dateTo'])
                );

                // count primary accounts

                $result['accountsPrimary'] = $accountRepository->countAllByTimeInterval(
                    strtotime($_POST['dateFrom']),
                    strtotime($_POST['dateTo']),
                    true
                );

                // count tweets
                $result['tweets'] = $tweetRepository->countAllByTimeInterval(
                    strtotime($_POST['dateFrom']),
                    strtotime($_POST['dateTo'])
                );

                $processTime = microtime(true) - $processStart;
                echo sprintf('<p class="message">Processed in %s seconds.</p>', $processTime);
            }
        ?>

        <h1>Statistics</h1>
        <form method="post" >
            <fieldset>
                <legend>Filter</legend>
                <label for="date-from">From date (format: YYYY-mm-dd):</label>
                <input id="date-from" type="text" name="dateFrom" value="<?php echo (isset($_POST['dateFrom']) ? $_POST['dateFrom'] : '2019-07-01'); ?>"/>

                <label for="date-to">To date (format: YYYY-mm-dd):</label>
                <input id="date-to" type="text" name="dateTo" value="<?php echo (isset($_POST['dateTo']) ? $_POST['dateTo'] : '2020-02-29'); ?>"/>

                <button class="save" type="submit" name="search">Search</button>
            </fieldset>
        </form>

        <?php if ($result) { ?>
            <h2>Result</h2>
            <table class="list">
                <tr>
                    <th>Accounts (total):</th>
                    <td><?php echo number_format($result['accounts'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <th>Accounts (primary):</th>
                    <td><?php echo number_format($result['accountsPrimary'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <th>Tweets:</th>
                    <td><?php echo number_format($result['tweets'], 0, ',', '.'); ?></td>
                </tr>
            </table>
        <?php } ?>
    </body>

</html>