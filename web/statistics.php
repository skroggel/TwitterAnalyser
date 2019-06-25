<?php
// <!doctype html>
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
    $tweetRepository = new \Madj2k\TwitterAnalyser\Repository\TweetRepository();

?>
<html>
    <head>
        <title>TwitterAnalyser</title>

        <style type="text/css">
            .tweet__header { font-weight:bold; }
        </style>
    </head>
    <body>
        <p>Accounts: <?php echo number_format($accountRepository->countAll(), 0, ',', '.'); ?></p>
        <p>Tweets: <?php echo number_format($tweetRepository->countAll(), 0, ',', '.'); ?></p>

    </body>

</html>