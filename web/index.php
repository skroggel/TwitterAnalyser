<!doctype html>
<?php
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
    $tweetWebView = new \Madj2k\TwitterAnalyser\View\TweetWebView();
?>
<html>
    <head>
        <title>TwitterAnalyser - Tweets</title>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
    </head>
    <body>
        <form method="post" name="filter">
            <?php
                $allAccounts = $accountRepository->findAll();
            ?>
            <p>Sum: <?php echo count($allAccounts) ?></p>
            <label for="account">Account:</label>
            <select id="account" name="account">
                <option value="">---</option>
                <?php
                    /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                    foreach ($allAccounts as $account) {
                        echo '<option ' . (intval ($account->getUid()) == intval($_POST['account']) ? 'selected="selected"' : '' ) . 'value="' . intval($account->getUid()) . '">' . $account->getName()  . ' (@' . $account->getUserName() . ')</option>';
                    }
                ?>
            </select>

            <label>
                <label for="date-from">From date (format: YYYY-mm-dd):</label>
                <input id="date-from" name="fromTime" value="<?php echo (isset($_POST['fromTime']) ? $_POST['fromTime'] : date("Y-m-d", strtotime("first day of previous month"))) ?>">
            </label>
            <label>
                <label for="date-to">To date (format: YYYY-mm-dd):</label>
                <input id=date-to" name="toTime" value="<?php echo (isset($_POST['toTime']) ? $_POST['toTime'] : date("Y-m-d")) ?>">
            </label>
            <button type="submit" class="save">OK</button>
        </form>
        <hr>
        <?php
            if (
                (isset($_POST['account']))
                 && ($_POST['account'] > 0)
            ){
                $fromTime = (isset($_POST['fromTime']) ? strtotime($_POST['fromTime']) : 0);
                $toTime = (isset($_POST['toTime']) ? strtotime($_POST['toTime']) : 0);

                if ($account = $accountRepository->findOneByUid(intval($_POST['account']))) {
                    echo $tweetWebView->render($account, $fromTime, $toTime);
                }
            }
        ?>
    </body>

</html>