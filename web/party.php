<!doctype html>
<?php
    // error_reporting(E_ALL);
    require_once(__DIR__ . '/../config/settings.php');
    require_once(__DIR__ . '/../vendor/autoload.php');

    $accountRepository = new \Madj2k\TwitterAnalyser\Repository\AccountRepository();
?>
<html>
    <head>
        <title>TwitterAnalyser - Party Affiliation</title>
        <link rel="stylesheet" type="text/css" href="css/main.css" />
    </head>
    <body>

        <?php
            /** @var \Madj2k\TwitterAnalyser\Model\Account $saveAccount */
            if (
                (isset($_POST['save']))
                && ($saveAccount = $accountRepository->findOneByUid(intval($_POST['save'])))
            ){
                $saveAccount->setParty($_POST['party']);
                $saveAccount->setResignedTimestamp(strtotime($_POST['resignedDate']));

                $accountRepository->update($saveAccount);
                echo sprintf('<p class="message">Updated account %s.</p>', $saveAccount->getUid());
            }


            // get all relevant accounts
            $accountList = $accountRepository->findAll();
        ?>
        <p>Sum: <?php echo count($accountList) ?></p>
        <hr />
        <table class="list">
            <tr>
                <th>Uid</th>
                <th>UserName</th>
                <th>Name</th>
                <th>Description</th>
                <th>Verified</th>
                <th>Party & Resigned Date (Format: Y-m-d)</th>
            </tr>
            <?php

                /** @var \Madj2k\TwitterAnalyser\Model\Account $account */
                foreach ($accountList as $account) {
            ?>
            <tr >
                <td id="account-<?php echo $account->getUid()?>">
                    <?php echo $account->getUid()?>
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
                <td class="<?php if ($account->getParty()) { echo 'party-done party-' . $account->getParty(); } ?>" >
                    <form action="party.php?#account-<?php echo $account->getUid()?>" method="post" name="save">
                        <input type="hidden" name="save" value="<?php echo $account->getUid()?>"/>
                        <label for="party-<?php echo $account->getUid()?>">Party:</label>
                        <select id="party-<?php echo $account->getUid()?>" name="party" onchange="this.form.submit()">
                            <option value="">-</option>
                            <option value="cdu" <?php if ($account->getParty() == 'cdu') { echo 'selected="selected"'; }?>>CDU/CSU</option>
                            <option value="spd" <?php if ($account->getParty() == 'spd') { echo 'selected="selected"'; }?>>SPD</option>
                            <option value="gruene" <?php if ($account->getParty() == 'gruene') { echo 'selected="selected"'; }?>>Bündnis 90/Die Grünen</option>
                            <option value="linke" <?php if ($account->getParty() == 'linke') { echo 'selected="selected"'; }?>>DIE LINKE</option>
                            <option value="fdp" <?php if ($account->getParty() == 'fdp') { echo 'selected="selected"'; }?>>FDP</option>
                            <option value="afd" <?php if ($account->getParty() == 'afd') { echo 'selected="selected"'; }?>>AfD</option>
                            <option value="fraktionslos" <?php if ($account->getParty() == 'fraktionslos') { echo 'selected="selected"'; }?>>fraktionslos</option>
                        </select>
                        <br />
                        <label for="resigned-date-<?php echo $account->getUid()?>">Resigned Date (Format: Y-m-d):</label>
                        <input id="resigned-date-<?php echo $account->getUid()?>" type="text" name="resignedDate" value="<?php echo date('Y-m-d', $account->getResignedTimestamp()) ?>"/>
                        <button class="save" type="submit">Save</button>
                    </form>
                </td>
            </tr>
            <?php
                }
            ?>
        </table>
    </body>

</html>