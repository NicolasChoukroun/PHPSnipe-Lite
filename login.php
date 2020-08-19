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

    include_once($_SERVER['DOCUMENT_ROOT'] . "/common.php");

    $db = new Database();

    $loader->ckeditor = false;
    $loader->table = true;
    $loader->forms = true;
    $loader->togglebuttons = true;
    $loader->blockui = true;
    //$loader->blockuispinner;

// check if logged
    $email = decrypt(getSession("email"));
    $sessionid = getSession("sessionid");
//$pass = decrypt(getSession("password"));

    $captcha_answer = strtolower($_REQUEST['captcha_answer']);
    $captcha = decrypt($_SESSION['captcha']);

    $login = new Database();
    $email = $_REQUEST['email']; // Your Email Address
    $pass = $_REQUEST['password2'];
    $kyf = $_REQUEST['kyf'];

    $captcha_answer=$captcha;
    //smtpmail("nicolas.choukroun@yandex.com", _t("FRANC Opening of your account"), _t("We have received your request for a new account  <br><br> Your request will be valided."));

    if (isset($_REQUEST['login-submit'])) {
        if ($captcha_answer == $captcha) {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $user = new User($email, $pass);
            //dump($user);exit;
            if (is_object($user) && $user->id > 0) {

                $user->fullname = ucwords($user->firstname . " " . $user->lastname);
                // not approved
                $user->kyf=$kyf;
                if ($user->is_approved <> true) {
                    User::static_logout();
                    header("Location: /401waiting.html");
                    exit;
                }
                // locked
                if ($user->is_locked) {
                    User::static_logout();
                    header("Location: /401locked.html");
                    exit;
                }
                // locked
                if ($user->is_stopped) {
                    User::static_logout();
                    header("Location: /401onhold.html");
                    exit;
                }
                // need to update this
                $settings->start_time = microtime();

                $user->saveSession();

                if (!preg_match('/^[k][a-km-zA-HJ-NP-Z0-9]{26,33}$/', $kyf) && strlen($kyf)>0)
                {
                    $settings->error = _t("<strong>Error:</strong> &nbsp KYF address is wrong, install a wallet and do getnewaddress in the console to get an address.") ;
                }else {

                    if ($settings->timezone <> "") {
                        //$sql="SET SESSION time_zone = '".$settings->timezone."'";
                        //$db->query($sql);
                    }

                    $sql = "INSERT INTO successlogins (ip,time,userid) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "'," . time() . "," . $user->id . ")";
                    $login->query($sql);
                    header("Location: /profile.php?refresh=1&success=" . xdb_encrypt(_t("You are connected, welcome aboard! This is your private profile.")));
                    exit;
                }

            } else {
                $sql = "INSERT INTO failedlogins (ip,time,email,kyf) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "'," . time() . ",'" . $user->email . "','" . $user->kyf . "')";
                $login->query($sql);
                if ($user == ERROR_PASSWORD) {
                    $settings->error = _t("There is a problem with your credentials. Please try again.");
                    $user->logout();
                    setCookies("login", 0);
                    setCookies("sessionid", 0);
                    setCookies("password", 0);
                    session_unset();
                    session_destroy();
                    //exit;
                }
                if ($user == ERROR_LOGIN) {
                    $settings->error = _t("Login is incorrect.");

                } else {
                    $settings->error = _t("Password error.").$pass;
                }
            }

        } else {

            $settings->error = _t("<strong>Error:</strong> &nbsp Captcha is not correct, try again") . " - " . $captcha_answer . " - " . $captcha;
        }
    }
    if ($_REQUEST['logout'] == true) {
        User::static_logout();
        unset($user);
        $settings->error = _t("You are logged out now, as you requested.");
        exit;
    }

    $settings->iframe = true;
    $title = "Login";
    require("_header.php");

    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/macros/mc_alerts.php");
?>
    <link href="/css/login.css" rel="stylesheet" type="text/css">
<br><br>
        <div class="row col-md-8 col-centered" style=";width:50%;max-width:800px;min-width:400px;background:#fefefe;margin:auto;">
            <div class="shadowed huge-padding bg-white form-box">
                <div class="panel-body ">

                    <h2 class="form-login-heading"><?php echo _t("Log into your account"); ?></h2><br>

                    <form id="login-submit" action="/login.php" role="form" method="post" onsubmit="this.disabled=true; return true;">
                        <div class="col-md-12 text-center mgbt-xs-5"><br>
                            <input class="form-input" type="text" placeholder="<?php echo _t('e-mail or login'); ?>" required name="email" autofocus value="<?php echo $email; ?>">
                            <input class="form-input" type="password" id="inputPassword" name="password2" placeholder="<?php echo _t('password');?>" required>

                            <button onclick="$.blockUI();" class="btn submit-button " style="margin-left:1.5%;" type="submit" name="login-submit" value="ok"><?php echo _t("Login"); ?></button>
                        </div>
                    </form>
                    <br><br>

                    <!-- Panel Widget -->
                </div>
                <br><br> <br>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-xs-4 text-left"><br>
                            <div class="login-options"><a href="/forget.php"><?php echo _t(">forgot password?"); ?> </a></div>
                        </div>
                        <div class="col-xs-8 text-right"><br>
                            <div class="login-options"> <?php echo _t("Don't have an account yet?"); ?><a href="/register.php">
                                    <?php echo _t("Create one account now."); ?></a></div>
                        </div>
                    </div>
                </div>
                <!-- Middle Content End -->


            </div>
        </div>
    <br><br><br>
<?php require("_footer.php"); ?>