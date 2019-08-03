<?php
// <!doctype html>
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
    $tweetListView = new \Madj2k\TwitterAnalyser\View\TweetListView();

?>
<html>
    <head>
        <title>TwitterAnalyser</title>

        <style type="text/css">
            .tweet { padding-top:1em;}
            .tweet__header { font-weight:bold; display:block;}
            .tweet__body,
            .tweet__media { display:block;}
        </style>
    </head>
    <body>
        <form method="post" name="filter">
            <?php
                $allAccounts = $accountRepository->findAll();
            ?>
            <p>Sum: <?php echo count($allAccounts) ?></p>
            <label><span>Account:</span>
                <select name="account">
                    <option value="">---</option>
                    <?php
                        /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                        foreach ($allAccounts as $account) {
                            echo '<option ' . (intval ($account->getUid()) == intval($_POST['account']) ? 'selected="selected"' : '' ) . 'value="' . intval($account->getUid()) . '">' . $account->getName()  . ' (@' . $account->getUserName() . ')</option>';
                        }
                    ?>
                </select>
            </label>
            <label>
                <span>Von:</span>
                <input name="fromTime" value="<?php echo (isset($_POST['fromTime']) ? preg_replace('#[^0-9/]+#', '', $_POST['fromTime']) : date("m/d/y", strtotime("first day of previous month"))) ?>">
            </label>
            <label>
                <span>Bis:</span>
                <input name="toTime" value="<?php echo (isset($_POST['toTime']) ? preg_replace('#[^0-9/]+#', '', $_POST['toTime']) : date("m/d/y")) ?>">
            </label>
            <button type="submit">OK</button>
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
                    echo $tweetListView->render($account, $fromTime, $toTime);
                }
            }
        ?>
    </body>

</html>