<!doctype html>
<?php
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');
?>
<html>
    <head>
        <title>TwitterAnalyser</title>

        <style type="text/css">
            .tweet__header { font-weight:bold; }
        </style>
    </head>
    <body>
        <?php
            $accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
            $tweetListView = new \Madj2k\TwitterAnalyser\View\TweetListView();

            $account = $accountRepository->findOneByUid(2);
            echo $tweetListView->render($account);
        ?>
    </body>

</html>