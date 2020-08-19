<?php
/*
 *  Copyright (c) 2013-2020. Nicolas Choukroun.
 *  Copyright (c) 2013-2020. The PHPSnipe Developers.
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the Attribution 4.0 International License as published by the
 *  Creative Commons Corporation; either version 2 of the License, or (at your option)
 *  any later version.  See COPYING for more details.
 *
 ******************************************************************************/ 
 
    include("common.php");

    if (!$user->is_logged) {
        header("Location: /login.php");
        exit;
    }
    $settings->showadminmenu = false;

    if (intval(($_REQUEST['cancel'])) > 0) {
        $id = intval(($_REQUEST['cancel']));
        $c = new Database();

        $sql = "select * from paid WHERE status='WAITING' AND id=" . $id . " and userid=" . $user->id;

        $c->query($sql);
        $c->single();
        $value = $c->rs['amount'];
        $type = $c->rs['type'];

        $sql = "DELETE from paid where id=" . $id;
        $c->query($sql);
        switch ($type) {
            case 'ONG':
                $sql = "UPDATE users  SET kyf_ong=kyf_ong+" . $value . " WHERE id=" . $user->id;
                break;
            case 'VOTER':
                $sql = "UPDATE users  SET kyf_voter=kyf_voter+" . $value . " WHERE id=" . $user->id;
                break;
            case 'REFERER':
                $sql = "UPDATE users  SET kyf_ref=kyf_ref+" . $value . " WHERE id=" . $user->id;
                break;
            case 'AIRDROP':
                $sql = "UPDATE users  SET kyf_airdrop=kyf_airdrop+" . $value . " WHERE id=" . $user->id;
                break;
            case 'BOUNTY':
                $sql = "UPDATE users  SET kyf_bounty=kyf_bounty+" . $value . " WHERE id=" . $user->id;
                break;

        }
        $c->query($sql);
        $settings->success = "Your redeem order has been cancelled.";
        $c->close();

    }

    $profile = new Database();
    $p = new Database();

    $sql = 'SELECT * FROM users WHERE id = ' . $user->id;
    $profile->query($sql);
    $profile->single();
    $row = $profile->rs;
    $email = $row['email'];
    $password = $row['password'];
    $firstname = ucwords($row['firstname']);
    $lastname = ucwords($row['lastname']);
    $addressx1 = ucwords($row['address1']);
    $addressx2 = ucwords($row['address2']);
    $url = $profile->rs['url'];
    $nickname = $profile->rs['public_nickname'];
    $rank = $profile->rs['rank'];
    $score = $profile->rs['score'];
    $bio = $profile->rs['bio'];
    $interest = $profile->rs['interest'];
    $help = $profile->rs['help'];
    $signature = $profile->rs['signature'];
    $title = "User Page " . $nickname;
    $interval = getSetting("score_interval", "int");
    $maxscore = getSetting("highest_score", "int");
    $thanks = $profile->rs['thanks'];
    $kyf = $profile->rs['kyf'];

    if ($_REQUEST['redeem'] == "airdrop") {
        $c = new Database();
        $d = new Database();
        $sql = "select * from paid WHERE status='WAITING' and userid=" . $user->id;
        $c->query($sql);
        $c->single();
        if ($c->nbr() == 0) {
            $value = $profile->rs['kyf_airdrop'];

            if ($value > 0) {
                if ($kyf == "") {
                    $settings->error = $value . " you need to enter a KYF address in your profile in order to be able to redeem KYF to your wallet." . $user->kyf;
                } else {
                    // finally process
                    $sql = "INSERT INTO paid SET amount=" . $value . ", status='WAITING', type='AIRDROP',userid=" . $user->id . ", kyf='" . $kyf . "'";
                    //echo $sql;exit;

                    $d->query($sql);
                    $sql = "UPDATE users SET kyf_airdrop=0 WHERE id=" . $user->id;
                    $d->query($sql);
                }
            } else {
                $settings->error = "redeem value is null";
            }
        } else {
            $settings->error = "something is waiting in the redeem queue, wait that it is processed before redeeming something else.";
        }
        $c->close();
        $d->close();

    }
    if ($_REQUEST['redeem'] == "voter") {
        $c = new Database();
        $d = new Database();
        $sql = "select * from paid WHERE status='WAITING' and userid=" . $user->id;
        $c->query($sql);
        $c->single();
        if ($c->nbr() == 0) {
            $value = $profile->rs['kyf_voter'];

            if ($value > 0) {
                if ($kyf == "") {
                    $settings->error = $value . " you need to enter a KYF address in your profile in order to be able to redeem KYF to your wallet." . $user->kyf;
                } else {
                    // finally process
                    $sql = "INSERT INTO paid SET amount=" . $value . ", status='WAITING', type='VOTER',userid=" . $user->id . ", kyf='" . $kyf . "'";
                    //echo $sql;exit;

                    $d->query($sql);
                    $sql = "UPDATE users SET kyf_voter=0 WHERE id=" . $user->id;
                    $d->query($sql);
                }
            } else {
                $settings->error = "redeem value is null";
            }
        } else {
            $settings->error = "something is waiting in the redeem queue, wait that it is processed before redeeming something else.";
        }
        $c->close();
        $d->close();

    }
    if ($_REQUEST['redeem'] == "referer") {
        $c = new Database();
        $d = new Database();
        $sql = "select * from paid WHERE status='WAITING' and userid=" . $user->id;
        $c->query($sql);
        $c->single();
        if ($c->nbr() == 0) {
            $value = $profile->rs['kyf_ref'];

            if ($value > 0) {
                if ($kyf == "") {
                    $settings->error = $value . " you need to enter a KYF address in your profile in order to be able to redeem KYF to your wallet." . $user->kyf;
                } else {
                    // finally process
                    $sql = "INSERT INTO paid SET amount=" . $value . ", status='WAITING', type='REFERER',userid=" . $user->id . ", kyf='" . $kyf . "'";
                    //echo $sql;exit;

                    $d->query($sql);
                    $sql = "UPDATE users SET kyf_ref=0 WHERE id=" . $user->id;
                    $d->query($sql);
                }
            } else {
                $settings->error = "redeem value is null";
            }
        } else {
            $settings->error = "something is waiting in the redeem queue, wait that it is processed before redeeming something else.";
        }
        $c->close();
        $d->close();

    }
    if ($_REQUEST['redeem'] == "bounty") {
        $c = new Database();
        $d = new Database();
        $sql = "select * from paid WHERE status='WAITING' and userid=" . $user->id;
        $c->query($sql);
        $c->single();
        if ($c->nbr() == 0) {
            $value = $profile->rs['kyf_bounty'];

            if ($value > 0) {
                if ($kyf == "") {
                    $settings->error = $value . " you need to enter a KYF address in your profile in order to be able to redeem KYF to your wallet." . $user->kyf;
                } else {
                    // finally process
                    $sql = "INSERT INTO paid SET amount=" . $value . ", status='WAITING', type='BOUNTY',userid=" . $user->id . ", kyf='" . $kyf . "'";
                    //echo $sql;exit;

                    $d->query($sql);
                    $sql = "UPDATE users SET kyf_bounty=0 WHERE id=" . $user->id;
                    $d->query($sql);
                }
            } else {
                $settings->error = "redeem value is null";
            }
        } else {
            $settings->error = "something is waiting in the redeem queue, wait that it is processed before redeeming something else.";
        }
        $c->close();
        $d->close();

    }
    if ($_REQUEST['redeem'] == "ong") {
        $c = new Database();
        $d = new Database();
        $sql = "select * from paid WHERE status='WAITING' and userid=" . $user->id;
        $c->query($sql);
        $c->single();
        if ($c->nbr() == 0) {
            $value = $profile->rs['kyf_ong'];

            if ($value > 0) {
                if ($kyf == "") {
                    $settings->error = $value . " you need to enter a KYF address in your profile in order to be able to redeem KYF to your wallet." . $user->kyf;
                } else {
                    // finally process
                    $sql = "INSERT INTO paid SET amount=" . $value . ", status='WAITING', type='ONG',userid=" . $user->id . ", kyf='" . $kyf . "'";
                    //echo $sql;exit;

                    $d->query($sql);
                    $sql = "UPDATE users SET kyf_ong=0 WHERE id=" . $user->id;
                    $d->query($sql);
                }
            } else {
                $settings->error = "redeem value is null";
            }
        } else {
            $settings->error = "something is waiting in the redeem queue, wait that it is processed before redeeming something else.";
        }
        $c->close();
        $d->close();

    }
    if ($_REQUEST['update'] == 1) {

        $password = $_REQUEST['pwd1'];
        $passwordverify = $_REQUEST['pwd2'];
        if (strlen($password) > 6 && $password = $passwordverify) {
            $t_hasher = new PasswordHash(12, true);
            $hash = $t_hasher->HashPassword($password);
            $sql = 'UPDATE users SET
         `email` = ' . sql_val($_REQUEST['email']) . ',
         `password` = ' . sql_val($hash) . ',
         `kyf` = ' . sql_val($_REQUEST['kyf']) . ',
         `public_nickname` = ' . sql_val($_REQUEST['public_nickname']) . ',
         `firstname` = ' . sql_val($_REQUEST['firstname']) . ',
         `lastname` = ' . sql_val($_REQUEST['lastname']) . ',
         `address1` = ' . sql_val($_REQUEST['addressx1']) . ',
         `address2` = ' . sql_val($_REQUEST['addressx2']) . ',         
         `city` = ' . sql_val($_REQUEST['city']) . ',
         `zip` = ' . sql_val($_REQUEST['zip']) . ',
         `phone` = ' . sql_val($_REQUEST['phone']) . ',
         `company` = ' . sql_val($_REQUEST['company']) . '
         WHERE `user_id` = ' . sql_val($user->id);
            $profile->query($sql);
            //  echo $sql;exit;
            $settings->success = _t("Your profile has been saved sucessfully. Your new password is saved. Please log-in with your new password.");
            header("Location: /logout.php?alert=" . $success);
            exit;
        }

        if (strlen($password) == 0 || $password <> $passwordverify) {
            $sql = 'UPDATE users SET
         `email` = ' . sql_val($_REQUEST['email']) . ',
          `kyf` = ' . sql_val($_REQUEST['kyf']) . ',         
         `public_nickname` = ' . sql_val($_REQUEST['public_nickname']) . ',
         `firstname` = ' . sql_val($_REQUEST['firstname']) . ',
         `lastname` = ' . sql_val($_REQUEST['lastname']) . ',
         `address1` = ' . sql_val($_REQUEST['addressx1']) . ',
         `address2` = ' . sql_val($_REQUEST['addressx2']) . ', 
         `city` = ' . sql_val($_REQUEST['city']) . ',
         `zip` = ' . sql_val($_REQUEST['zip']) . ',
         `phone` = ' . sql_val($_REQUEST['phone']) . ',
         `company` = ' . sql_val($_REQUEST['company']) . '
         WHERE `id` = ' . sql_val($user->id);

            $profile->query($sql);

            $settings->success = _t("Your profile has been updated successfully.");
        }
        if (strlen($password) <= 7 && strlen($password) != 0) {
            $settings->error = _t("Your password is too small or you forgot to enter it.");
        }

    }

    $newsletter = $row['newsletter'];
    $city = $row['city'];
    $zip = $row['zip'];
    $credits = $row['credits'];
    $public_nickname = $row['public_nickname'];

    $phoneext = $row['phoneext'];
    $phone = $row['phone'];
    $company = $row['company'];
    $web = $row['web'];
    $userdate = displayDate($row['added']);

    if ($_REQUEST['newsletterchange'] == 1) {
        if ($newsletter == 1) {
            $newsletter = 0;
        } else {
            $newsletter = 1;
        }
        $sql = 'UPDATE users SET
     `newsletter` = ' . sql_val($newsletter) . '
     WHERE `id` = ' . sql_val($user->id);
        $profile->query($sql);
    }
    $iframe = false;
    require_once("_header.php");

    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/macros/mc_alerts.php");

?>
<script>
    function checkForm(form) {
        if (!form.ok2.checked) {
            alert('<?php echo _t("Please indicate that you accept the Terms and Conditions");?>');
            form.terms.focus();
            return false;
        }
        return true;
    }

    function checkPass() {
        //Store the password field objects into variables ...
        var pass1 = document.getElementById('pwd1');
        var pass2 = document.getElementById('pwd2');
        //Store the Confimation Message Object ...
        var message = document.getElementById('confirmMessage');
        //Set the colors we will be using ...
        var goodColor = "#66cc66";
        var badColor = "#ff6666";
        //Compare the values in the password field
        //and the confirmation field
        if (pass1.value == pass2.value) {
            //The passwords match.
            //Set the color to the good color and inform
            //the user that they have entered the correct password
            pass2.style.backgroundColor = goodColor;
            //message.style.color = goodColor;
            message.innerHTML = "<?php echo _t('Passwords Match!');?>"
        } else {
            //The passwords do not match.
            //Set the color to the bad color and
            //notify the user.
            pass2.style.backgroundColor = badColor;
            //message.style.color = badColor;
            message.innerHTML = "<?php echo _t('Passwords Do Not Match!');?>"
        }
    }
</script>
<?php $avatar = getAvatarFromId($url, $user->id, 200); ?>

<div class=" bg--white" style="margin:auto;background-image: url('/img/background03.jpg'); background-repeat: no-repeat, no-repeat; background-size: cover;" data-overlay="7">
    <h1 class="boxed" style="height:85px;">
        <font color="black"><?php echo("Your Profile"); ?></font>

    </h1>
    <br><br>
    <div class=" shadowed  bg--white" style="margin:auto;padding:20px;width:90%;">


        <div class="row">
            <div class="col-md-4">
                <h2>Mr(s)<?php echo $firstname . " " . $lastname; ?></h2>
                <h4><?php echo $company; ?></h4>
                <table class="table bg--white">
                    <tbody>
                    <tr>
                        <td style="width:50%;"><?php echo _t("User Status"); ?></td>
                        <td style="width:50%;">
                            <?php
                                if ($row['is_blocked'] == true) {
                                    echo ' <span class="label label-danger">' . _t("Banned") . '</span>';
                                } elseif ($row['is_stopped'] == true) {
                                    echo ' <span class="label label-warning">' . _t("On Hold") . '</span>';
                                } else {
                                    echo ' <span class="label label-success">' . _t("Ok") . '</span>';
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><?php echo _t("Member Since"); ?>: <?php echo $userdate; ?>
                        </td>
                    </tr>


                    <?php
                        $newdate = strtotime('-3 day', strtotime($date));
                        $newdate = date('Y-m-j', $newdate);
                    ?>
                    <tr>
                        <td><?php echo _t("Newsletter"); ?></td>
                        <td><?php
                                if ($newsletter == 1) {
                                    echo ' <span class="label label-success">' . _t("Yes") . '</span>';
                                } else {
                                    echo ' <span class="label label-danger">' . _t("No") . '</span>';
                                }

                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="mgtp-20">
                                <a href="/profile.php?newsletterchange=1">
                                    <button class="btn btn-primary" type="button">
                                        <?php echo _t("Newsletter Status");
                                        ?>
                                    </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <h4><?php echo _t("Your KYF"); ?></h4>

                <table class="table bg--white">
                    <tbody>
                    <tr>
                        <td style="width:50%;"><?php echo _t("List of your KYF hold by us"); ?></td>
                    </tr>
                    <?php
                        $c = new Database();
                        $sql = "select * from paid WHERE status='WAITING' and userid=" . $user->id;
                        $c->query($sql);
                        $value = $c->nbr();

                    ?>
                    <tr>
                        <td><?php echo _t("Voter KYF"); ?></td>
                        <td><?php

                                if ($row['kyf_voter'] == 0) {
                                    echo ' <span class="label label-warning">0.0 KYF</span>';
                                } else {
                                    echo ' <span class="label label-warning">' . $row['kyf_voter'] . ' KYF</span>';
                                }
                            ?>

                        </td>
                        <td>
                            <?php
                                if ($value == 0 && $row['kyf_voter']>0 ) {
                                    ?>
                                    <a href="/profile.php?redeem=voter">
                                        <button onclick="$.blockUI();" class="btn-sm btn-primary" type="button">
                                            <?php echo _t("Redeem Voter KYF");
                                            ?>
                                        </button>
                                    </a>
                                <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo _t("Referer KYF"); ?></td>
                        <td><?php

                                if ($row['kyf_ref'] == 0) {
                                    echo ' <span class="label label-warning">0.0 KYF</span>';
                                } else {
                                    echo ' <span class="label label-warning">' . $row['kyf_ref'] . ' KYF</span>';
                                }

                            ?>
                        </td>
                        <td>
                            <?php
                                if ($value == 0 && $row['kyf_ref'] > 0) {
                                    ?>
                                    <a href="/profile.php?redeem=referer">
                                        <button onclick="$.blockUI();" class="btn-sm btn-primary" type="button">
                                            <?php echo _t("Redeem Referer KYF");
                                            ?>
                                        </button>
                                    </a>
                                <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo _t("ONG Kyf"); ?></td>
                        <td><?php

                                if ($row['kyf_ong'] == 0) {
                                    echo ' <span class="label label-warning">0.0 KYF</span>';
                                } else {
                                    echo ' <span class="label label-warning">' . $row['kyf_ong'] . ' KYF</span>';
                                }

                            ?>
                        </td>

                        <td>
                            <?php
                                if ($value == 0 && $row['kyf_ong'] > 0) {
                                    ?>
                                    <a href="/profile.php?redeem=ong">
                                        <button onclick="$.blockUI();" class="btn-sm btn-primary" type="button">
                                            <?php echo _t("Redeem ONG KYF");
                                            ?>
                                        </button>
                                    </a>
                                <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo _t("Airdrop Kyf"); ?></td>
                        <td><?php

                                if ($row['kyf_airdrop'] == 0) {
                                    echo ' <span class="label label-warning">0.0 KYF</span>';
                                } else {
                                    echo ' <span class="label label-warning">' . $row['kyf_airdrop'] . ' KYF</span>';
                                }

                            ?>
                        </td>

                        <td>
                            <?php
                                if ($value == 0 && $row['kyf_airdrop'] > 0) {
                                    ?>
                                    <a href="/profile.php?redeem=airdrop">
                                        <button onclick="$.blockUI();" class="btn-sm btn-primary" type="button">
                                            <?php echo _t("Redeem AIRDROP KYF");
                                            ?>
                                        </button>
                                    </a>
                                <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo _t("Bounty Kyf"); ?></td>
                        <td><?php

                                if ($row['kyf_bounty'] == 0) {
                                    echo ' <span class="label label-warning">0.0 KYF</span>';
                                } else {
                                    echo ' <span class="label label-warning">' . $row['kyf_bounty'] . ' KYF</span>';
                                }

                            ?>
                        </td>
                        <td>
                            <?php
                                if ($value == 0 && $row['kyf_bounty'] > 0) {
                                    ?>
                                    <a href="/profile.php?redeem=bounty">
                                        <button onclick="$.blockUI();" class="btn-sm btn-primary" type="button">
                                            <?php echo _t("Redeem BOUNTY KYF");
                                            ?>
                                        </button>
                                    </a>
                                <?php } ?>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <div class="col-md-8" style="margin:auto;">
                <div class="tabs" style="width:100%">

                    <!-- TAB 1 PROFILE --------------------------------->
                    <div class="tab-content small-padding">
                        <div id="profile-tab" class="tab-pane active">
                            <form action="/profile.php" method="POST" id="users" name="users">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Companie"); ?>:</label>
                                        <div class="col-xs-7 controls">
                                            <input type="text" value="<?php if (isset($company)) {
                                                echo clean($company);
                                            } ?>" id="company" name="company" maxlength="128" width="128"/>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Public Nickname"); ?>:</label>
                                        <div class="col-xs-7 controls">
                                            <input type="text" value="<?php if (isset($public_nickname)) {
                                                echo clean($public_nickname);
                                            } ?>" required id="public_nickname" name="public_nickname" width="128"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-xs-5 "><span class="control-label"><b><?php echo _t("Your KYF address"); ?>:</b></span><br>
                                        </div>
                                        <input class="form-input" style="width:100%;" type="text" maxlength="128" id="kyf" pattern="^[k][a-km-zA-HJ-NP-Z0-9]{26,33}$" name="kyf"
                                               placeholder="<?php if ($kyf == "") {
                                                   echo _t('your kyf address');
                                               } else {
                                                   echo $kyf;
                                               } ?>"
                                               value="<?php if ($kyf == "") {
                                                   echo "";
                                               } else {
                                                   echo $kyf;
                                               } ?>
"
                                               required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("First Name"); ?>:</label>
                                        <div class="col-xs-7 controls">
                                            <input type="text" value="<?php if (isset($firstname)) {
                                                echo clean($firstname);
                                            } ?>" id="firstname" name="firstname" maxlength="64"/></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Last Name"); ?>:</label>
                                        <div class="col-xs-7 controls">
                                            <input type="text" value="<?php if (isset($lastname)) {
                                                echo clean($lastname);
                                            } ?>" id="lastname" name="lastname" maxlength="64"/></div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Address 1"); ?>:</label>
                                        <div class="col-xs-7 controls">
                                            <input type="text" value="<?php if (isset($addressx1)) {
                                                echo clean($addressx1);
                                            } ?>" id="address" name="addressx1" maxlength="512" placeholder="<?php echo _t('Enter your address'); ?>"/></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Address 2"); ?>:</label>
                                        <div class="col-xs-7 controls">
                                            <input type="text" value="<?php if (isset($addressx2)) {
                                                echo clean($addressx2);
                                            } ?>" id="address2" name="addressx2" maxlength="512" placeholder="<?php echo _t('Enter your address'); ?>"/></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("City"); ?>:</label>
                                        <div class="col-xs-5 controls">
                                            <input type="text" value="<?php if (isset($city)) {
                                                echo clean($city);
                                            } ?>" id="city" name="city" maxlength="64"/></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Zip Code"); ?>:</label>
                                        <div class="col-xs-5 controls">
                                            <input type="text" value="<?php if (isset($zip)) {
                                                echo $zip;
                                            } ?>" id="zip" name="zip" maxlength="8"/></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Email"); ?>:</label>
                                        <div class="col-xs-7 controls">
                                            <input type="email" placeholder="E-mail" class="form-control" required name="email" id="email" value="<?php echo $email; ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="col-xs-5 "><span class="control-label"><b><?php echo _t("Password"); ?>:</b></span><br>

                                        </div>

                                        <div class="col-xs-5 controls">
                                            <input type="password" class="form-control"
                                                   title="<?php echo _t('Password must contain at least 6 characters, including UPPER/lowercase and numbers'); ?>"
                                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}"
                                                   name="pwd1"
                                                   onchange="this.setCustomValidity(this.validity.patternMismatch ? this.title : '')"
                                                   value=""
                                                   id="pwd1">

                                            <input type="password" class="form-control"
                                                   title="<?php echo _t('Please enter the same Password.'); ?>"
                                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" name="pwd2"
                                                   onchange="this.setCustomValidity(this.validity.patternMismatch ? this.title : '')"
                                                   value=""
                                                   id="pwd2"
                                                   onkeyup="checkPass(); return false;">
                                        </div>
                                        <script>
                                            document.getElementById("pwd1").value = "";
                                            document.getElementById("pwd2").value = "";
                                        </script>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Phones"); ?>:</label>
                                        <div class="col-xs-5 controls"><input type="text" value="<?php if (isset($phone)) {
                                                echo clean($phone);
                                            } ?>" id="phone" name="phone" maxlength="64" min="" max="" step=""/></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="col-xs-5 control-label"><?php echo _t("Credits"); ?>:</label>
                                        <div class="col-xs-5 controls"><input type="text" value="<?php if (isset($credits)) {
                                                echo clean($credits);
                                            } ?>" disabled/></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12"><br><br>
                                        <input type="hidden" name="update" value=1>
                                        <center><input type="Submit" value="<?php echo _t('Save the changes'); ?>" class="btn btn-primary"/></center>
                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
</div>


<?php require_once("_footer.php"); ?>
