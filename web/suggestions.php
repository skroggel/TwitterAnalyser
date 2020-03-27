<!doctype html>
<?php
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
?>
<html>
    <head>
        <title>TwitterAnalyser - Suggestions</title>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
    </head>
    <body>

        <?php
            /** @var \Madj2k\TwitterAnalyser\Model\Account $verifyAccount */
            if (
                (isset($_POST['verify']))
                && ($verifyAccount = $accountRepository->findOneByUid(intval($_POST['verify'])))
            ){
                $verifyAccount->setIsSuggestion(false);
                $verifyAccount->setIsSecondary(false);
                $accountRepository->update($verifyAccount);
                echo sprintf('<p class="message">Set account %s as primary account.</p>', $verifyAccount->getUid());

                // delete all accounts which are related to this suggestion
                $otherSuggestions = $accountRepository->findBySuggestionForName($verifyAccount->getSuggestionForName());

                /** @var \Madj2k\TwitterAnalyser\Model\Account $otherSuggestion */
                foreach ($otherSuggestions as $otherSuggestion) {

                    if ($otherSuggestion->getIsSuggestion()) {
                        $otherSuggestion->setDeleted(true);
                        echo sprintf('<p class="message">Marked account %s as deleted.</p>', $otherSuggestion->getUid());
                        $accountRepository->update($otherSuggestion);
                    }
                }
            }

            /** @var \Madj2k\TwitterAnalyser\Model\Account $deleteAccount */
            if (
                (isset($_POST['delete']))
                && ($deleteAccount = $accountRepository->findOneByUid(intval($_POST['delete'])))
            ){
                $deleteAccount->setDeleted(true);
                $accountRepository->update($deleteAccount);
                echo sprintf('<p class="message">Marked account %s as deleted.</p>', $deleteAccount->getUid());
            }

            // get all suggestions
            $accountSuggestions = $accountRepository->findBySuggestionOrderedBySuggestionForNameAndName();
        ?>
        <h1>Confirm accounts based on search results of importer</h1>
        <p>Sum: <?php echo count($accountSuggestions) ?></p>
        <hr />
        <table class="list">
            <tr>
                <th>Uid</th>
                <th>SuggestionForName</th>
                <th>UserName</th>
                <th>Name</th>
                <th>Description</th>
                <th>Verified</th>
                <th>&nbsp;</th>
            </tr>
            <?php

                /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                foreach ($accountSuggestions as $account) {
            ?>
            <tr>
                <td>
                    <?php echo $account->getUid()?>
                </td>
                <td>
                    <?php echo $account->getSuggestionForName()?>
                </td>
                <td>
                    <a href="https://www.twitter.com/<?php echo $account->getUserName()?>" target="_blank"><?php echo $account->getUserName()?></a>
                </td>
                <td>
                    <?php echo $account->getName()?>
                </td>
                <td>
                    <?php echo $account->getDescription()?>
                </td>
                <td>
                    <?php echo intval($account->getVerified())?>
                </td>
                <td>
                    <form method="post" name="verifier">
                        <input type="hidden" name="verify" value="<?php echo $account->getUid()?>"/>
                        <button class="verify" type="submit">Verify</button>
                    </form>
                    <form method="post" name="deleter">
                        <input type="hidden" name="delete" value="<?php echo $account->getUid()?>"/>
                        <button class="delete" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php
                }
            ?>
        </table>
    </body>

</html>