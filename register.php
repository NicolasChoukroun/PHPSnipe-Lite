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


//echo "ok";
    if ($settings->defcon > 0) {
        header("Location: index.php");
        exit;
    }
    if ($settings->no_registration > 0) {
        header("Location: /404.html");
        exit;
    }
// load all the countries in a table
    $c = new Database();
    $sql = "select * from countries";
    $c->query($sql);
    while ($row1 = $c->next()) {
        $countries[] = $c->rs;
    }

    $cookietime = time() + 3600 * 24 * 30;
    $invitetime = 3600 * 24 * 2;

// check invite code
    if ($settings->invite && isset($_REQUEST['invite'])) {
        $x = $invite = $_REQUEST['invite'];
        $t1 = round(time() / ($invitetime));
        $code = hex2bin($x);

        $ar = explode("|", $code);
        $uid = $ar[0];
        $t2 = $ar[1];
        //echo $code . "-" . $t1 . " - " . $t2;
        //exit;
        if ($uid != "") {
            if ($t1 != $t2) {
                $_REQUEST['submit-register'] = "";
                $settings->error = "Wrong Invite Code.";
            }
        }
    }
    $test = false;
    $settings->iframe = true;
    $title = "Register";
    $menu = false;
//echo "ok countries";exit;

//$settings->warning = "Names like gggg, asdfgh, qwerty etc... are not authorized and will not get you approved here. <b>Do not open multiple accounts, this will get all your accounts terminated.</b>";

    $db=new Database();
    if (isset($_REQUEST['submit-register']) && $settings->error == "") {

        $settings->error = "";
        // get the variables from the form

        //$login = isset($_REQUEST['login']) ? clean($_REQUEST['login'], "varchar(128)") : "";
        $public_nickname = isset($_REQUEST['public_nickname']) ? clean($_REQUEST['public_nickname'], "varchar(128)") : "";
        $email = isset($_REQUEST['email']) ? clean($_REQUEST['email'], "varchar(128)") : "";
        $password = isset($_REQUEST['pwd1']) ? clean($_REQUEST['pwd1'], "varchar(128)") : "";
        $confirmpass = isset($_REQUEST['pwd2']) ? clean($_REQUEST['pwd2'], "varchar(128)") : "";
        $firstname = isset($_REQUEST['firstname']) ? clean($_REQUEST['firstname'], "varchar(64)") : "";
        $lastname = isset($_REQUEST['lastname']) ? clean($_REQUEST['lastname'], "varchar(64)") : "";
        $address = isset($_REQUEST['address']) ? clean($_REQUEST['address'], "text") : "";
        $city = isset($_REQUEST['city']) ? clean($_REQUEST['city'], "varchar(64)") : "";
        $company = isset($_REQUEST['company']) ? clean($_REQUEST['company'], "varchar(64)") : "";
        $kyf = isset($_REQUEST['kyf']) ? clean($_REQUEST['kyf'], "varchar(64)") : "";
        $web = isset($_REQUEST['web']) ? clean($_REQUEST['web'], "varchar(128)") : "";
        $zip = isset($_REQUEST['zip']) ? clean($_REQUEST['zip'], "int(11)") : "";
        $ok1 = isset($_REQUEST['ok1']) ? clean($_REQUEST['ok1'], "int(11)") : 0; // newsletter
        $ok2 = isset($_REQUEST['ok2']) ? clean($_REQUEST['ok2'], "int(11)") : 0; // eula
        $investor = isset($_REQUEST['investor']) ? clean($_REQUEST['investor'], "int(1)") : 0;
        $miner = isset($_REQUEST['miner']) ? clean($_REQUEST['miner'], "int(1)") : 0;
        //$newsletter = isset($_REQUEST['newsletter']) ? clean($_REQUEST['newsletter'], "int(11)") : "";
        $phone = isset($_REQUEST['phone']) ? clean($_REQUEST['phone'], "varchar(32)") : "";
        $countryid = isset($_REQUEST['countryid']) ? clean($_REQUEST['countryid'], "int(11)") : "";

        $captcha_answer = strtolower($_REQUEST[('captcha_answer')]);
        $captcha = strtolower(decrypt($_SESSION['captcha']));
       // echo "captcha=".$_SESSION['captcha'];
       // echo "<br>captcha2=".$captcha;
        if ($captcha_answer == $captcha) {

            if ($email <> "") {
                setCookies("email", $email);
            } else {
                $email = getCookies("email");
            }
            if ($firstname <> "") {
                setCookies("firstname", $firstname);
            } else {
                $firstname = getCookies("firstname");
            }
            if ($lastname <> "") {
                setCookies("lastname", $lastname);
            } else {
                $lastname = getCookies("lastname");
            }
            if ($password <> "") {
                setCookies("password", $password);
            } else {
                $password = getCookies("password");
            }

            //    echo "pass=".$password."    cpass=".$confirmpass." --->".$_REQUEST['password'];
            //    echo "pass=".$password."    cpass=".$confirmpass." --->".$_REQUEST['password'];

            //if (strpos($email, "yahoo.com") || strpos($email, "mac.com") > 0 || strpos($email, "gmx") > 0 || strpos($email, "live.com") > 0) {
            //    $settings->error = _t("<strong>Error:</strong> &nbsp yahoo,mail.com,gmx, live.com are not supported here. They are blocking most of the non big corporate emails on purpose and you will never receive your lost password for
            // example. Please use anything email provider.");
            //}

            if ($ok2 <> 1) {
                //password is too short
                $settings->error = _t("<strong>Error:</strong> &nbsp You absolutly must approve our EULA in order to create an account.");
            }

            if (strlen($kyf) <= 10) {
                //password is too short
                $settings->error = _t("<strong>Error:</strong> &nbsp Wrong KYF address.");
            }
            /*if (strlen($login) <= 4) {
                //password is too short
                $settings->error = _t("<strong>Error:</strong> &nbsp Login too short, 6 characters minimum.");
            }*/
            if (strlen($public_nickname) <= 4) {
                //password is too short
                $settings->error = _t("<strong>Error:</strong> &nbsp Public Nickname too short, 6 characters minimum.");
            }
            if (strlen($password) < 6) {
                //password is too short
                $settings->error = _t("<strong>Error:</strong> &nbsp Password too short, 6 characters minimum.");
            }

            if (strlen($password) > 20) {
                //password is too long
                $settings->error = _t("<strong>Error:</strong> &nbsp Password too long, 20 characters maximum.");
            }
            if (!preg_match("#[0-9]+#", $password)) {
                //password does not contain numbers
                $settings->error = _t("<strong>Error:</strong> &nbsp The password needs at least one number.");
            }
            if (!preg_match("#[a-z]+#", $password)) {
                //password does not contain lower-case letters
                $settings->error = _t("<strong>Error:</strong> &nbsp The password needs at least 1 lower case character.");
            }
            if (!preg_match("#[A-Z]+#", $password)) {
                //password does not contain capital letters
                $settings->error = _t("<strong>Error:</strong> &nbsp The password needs at least 1 upper case character.");
            }
            //_d("checking the form");
            if ($settings->error == "" && $firstname != ''  && $public_nickname != '' && $lastname != '' && $email != '' && $password != '' && $confirmpass != '') {
                if ($password == $confirmpass) {
                    $t_hasher = new PasswordHash(12, true);
                    $hash = $t_hasher->HashPassword($password);
                    //echo $hash."<br>";
                    $today = date("Y-m-d H:i:s", time());
                    // check unique email
                    $sql = 'SELECT id FROM users WHERE LOWER(email)="' . strtolower($email) . '"';
                    //echo $sql;exit;
                    $result = $db->query($sql);
                    $nbr = $db->nbr();
                    if ($nbr == 0) {

                        $sql = 'SELECT id FROM users WHERE LOWER(public_nickname)="' . strtolower($public_nickname) . '"';
                        $result = $db->query($sql);
                        $nbr = $db->nbr();
                        if ($nbr == 0) {
                            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                            } else {
                                $ip = $_SERVER['REMOTE_ADDR'];
                            }
                            $sql = 'INSERT INTO users (
					   email,
					   password, 
					   login,
					   kyf,
					   public_nickname,
					   firstname,
					   lastname,
					   remote_addr,
					   investor,
					   miner,	
					   newsletter,
					   statusid,    
					   is_locked,
					   is_stopped,
					   is_approved,
					   is_silenced,
					   added,
					   date
					) VALUES (
					   ' . sql_val($email) . ',
					   ' . sql_val($hash) . ',
					   ' . sql_val($public_nickname) . ',
					   ' . sql_val($kyf) . ',
					   ' . sql_val($public_nickname) . ',
					   ' . sql_val($firstname) . ',
					   ' . sql_val($lastname) . ', 
					   ' . sql_val($ip) . ',
					   ' . sql_val($investor) . ',
					   ' . sql_val($miner) . ',
					   ' . sql_val(1) . ',
					   ' . sql_val(1) . ',	   
					   ' . sql_val(0) . ',  
					   ' . sql_val(0) . ',  
					    ' . sql_val(1) . ',
					   ' . sql_val(0) . ', 
					   ' . sql_val($today) . ',
					   ' . sql_val($today) . '
					)';
                            //echo $sql;exit;
                            $db->query($sql);
                            $settings->info = xdb_decrypt(_t("&nbsp A confirmation has been sent to your email."));
                            //smtpmail($email, "FRANC Opening of your account", "We have received your request for a new account  <br><br> Your request will be valided as soon as possible.  " . $user->email_signature);
                            $settings->success = xdb_encrypt(_t("Success! Thanks you to have opened an account to our store.You can " . "<a href='/login.php'>" . "login" . " </a> " . "and enjoy our community now.</a>."));

                            $sessionid = md5(xdb_decrypt($db->lastId()));
                            setCookies("login", decrypt($email)); // this mean that the customer is logged.
                            setCookies("sessionid", $sessionid);
                            setCookies("password", decrypt($hash));
                            header("Location: /login.php?success=" . $settings->success);
                            exit;
                        } else {
                            $settings->error = _t("<strong>Used: </strong> &nbsp This public nickname is already used, try again with another one.").$sql;
                        }
                    } else {
                        $settings->error = _t("<strong>Used: </strong> &nbsp This email is already used, try ") . "<a href='forget.php'> " . _t("the forget password page") . "</a> " . _t("to change get a new password.");
                    }
                } else {
                    $settings->error = _t("&nbsp Your password and its confirmation are not matching.");
                }
            } else {
                if ($settings->error == "") {
                    $settings->error = _t("&nbsp You must fill all fields. At least one is empty.");
                }
            }
            if ($settings->error == "") {
                $settings->success = xdb_encrypt("Success! Thanks you to have opened an account to our store.You can " . "<a href='/login.php'>" . "login" . " </a> " . "and enjoy our community now.</a>.");

                $sessionid = md5(decrypt($db->lastId()));
                setCookies("login",decrypt($email)); // this mean that the customer is logged.
                setCookies("sessionid", $sessionid);
                setCookies("password", decrypt($hash));
                header("Location: /login.php?success=" . $settings->success);

                exit;
            }
        } else {
            $settings->error = _t("&nbsp Captcha is not correct. Please try again. " . $captcha_answer . " - " . $captcha);
        }

    } ?>
    <script>
        function checkForm(form) {
            if (!form.ok2.checked) {
                alert("Please indicate that you accept the Terms and Conditions");
                form.terms.focus();
                return false;
            }
            return true;
        }

        function checkPass() {
            //Store the password field objects into variables ...
            var pass1 = document.getElementById('pass1');
            var pass2 = document.getElementById('pass2');

            var message = document.getElementById('confirmMessage');
            var goodColor = "#ddffdd";
            var badColor = "#ffdddd";
            if (pass1.value === pass2.value) {
                pass2.style.backgroundColor = goodColor;
                message.style.color = goodColor;
                message.innerHTML = "<?php echo _t('Passwords Match!'); ?>"
            } else {
                pass2.style.backgroundColor = badColor;
                message.style.color = badColor;
                message.innerHTML = "<?php echo _t('Passwords Does Not Match!'); ?>"
            }
        }
    </script>
<?php
    $settings->iframe = true;

    if (strlen($user->login) < 4) {
        $login = "";
    }

    if ($settings->closed) {
        echo "<center><h1>Closed</h1></center>";
        exit;
    }

    include_once("_header.php");

    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/macros/mc_alerts.php");

?>

    <div class="alert alert-info ">
        <button type="button" class="close" data-dismiss="alert" ><i class="glyphicon glyphicon-remove-sign"></i></button>
            <div class="row boxed" style="width:80%;min-width:600px;max-width:1400px;margin:auto;">
                <div class="col-md-10 col-centered" style="margin-top:10px;text-align:justify;">
                    <img src="/img/KYF-airdrops-free2.jpg">
                    <h3><?php echo _t("Register now, win 5 000 KYF 01 March 2020."); ?></h3>
                    <?php echo _t("Greetings and welcome to the Kryptofranc airdrop!"); ?><Br>
                    <?php echo _t("We would like you to join the KYF community and become an agent for good around the world."); ?><br>
                    <?php echo _t("How does that work ?");?>
                    <b><?php echo _t("You just need to register and you will be awarded 5 000 KYF on 01 March 2020.");?></b><Br>
                    <?php echo _t("To understand how the KYF project intends to support good deeds around the world, you just need to download our");?> <a href='https://kryptofranc.com/whitepaper.php'>white paper</a> <?php echo _t("and review it."); ?><br>
                    <?php echo _t("Let's get together and take a stand for freedom !"); ?><br>


                    <div id="first_countdown" style="position: relative; width: 80%; height: 40px; margin:auto;"></div>
                </div>

            </div>

    </div>



    <link href="/css/login.css?r=900" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>

    <script>
        $("#first_countdown").ResponsiveCountdown({
            target_date:"2020/3/1 00:00:00",
            time_zone:0,target_future:true,
            set_id:1,pan_id:2,day_digits:2,
            fillStyleSymbol1:"rgba(251, 71, 41, 1)",
            fillStyleSymbol2:"rgba(251, 71, 41, 1)",
            fillStylesPanel_g1_1:"rgba(13, 13, 138, 1)",
            fillStylesPanel_g1_2:"rgba(255, 255, 255, 1)",
            fillStylesPanel_g2_1:"rgba(12, 12, 102, 1)",
            fillStylesPanel_g2_2:"rgba(16, 16, 161, 1)",
            text_color:"rgba(0,0,0,1)",
            text_glow:"rgba(100, 100, 100, 1)",
            show_ss:true,show_mm:true,
            show_hh:true,show_dd:true,
            f_family:"Verdana",show_labels:true,
            type3d:"single",max_height:100,
            days_long:"days",days_short:"TT",
            hours_long:"hours",hours_short:"SS",
            mins_long:"minutes",mins_short:"MM",
            secs_long:"seconds",secs_short:"SS",
            min_f_size:9,max_f_size:30,
            spacer:"none",groups_spacing:2,text_blur:2,
            font_to_digit_ratio:0.15,labels_space:1.2
        });
    </script>

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Get a New Adress</h4>
                </div>
                <div class="modal-body">
                    <p>Check the menus in the wallet, there is a "WINDOW" menu. In this menu there is the "CONSOLE", click on this option, then type "getnewaddress" in the console. It will generate an unique address that you can use to send,
                        receive KYF money.</p>
                    <img align="center" src="/img/getnewaddress.jpg">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <div class="row" style="width:80%;min-width:600px;max-width:1200px;margin:auto;">
        <div class="col-md-8 col-centered" style="margin-top:50px;">
            <div class="shadowed huge-padding huge-margin bg-white form-box">
                <div class="panel-body p">
                    <h2 class="form-login-heading"><?php echo _t('Create a new account') ?></h2><br>
                    <form class="form-horizontal" action="/register.php" role="form" id="register-form" method="post" onsubmit="onsubmit=" submit-register.disabled=true; return checkForm(this);
                    ">
                    <div class="form-group">
                        <div class=" col-md-12">
                            <?php // pattern="^$|^[k][a-km-zA-HJ-NP-Z0-9]{26,33}$" ?>
                            <input class="form-input" type="text" id="kyf"  name="kyf"
                                   placeholder="<?php if ($user->kyf=="") echo _t('your kyf address (optional)'); else echo $user->kyf; ?>"
                                   value="<?php if ($user->kyf=="") echo _t('your kyf address').' (optional)'; else echo $user->kyf; ?>" ><br>
                            <center><small>You have to install our wallet <a href="/downloads.php">here</a>, then get your address by opening the console and <a type="button" data-toggle="modal" data-target="#myModal">doing
                                    this</a></small></center><br>
                        </div>
                        <div class=" col-md-6">
                            <div class="inner-addon left-addon  large-margin">
                                <i class="glyphicon glyphicon-user"></i>
                                <input type="text" minlength=4 maxlength=20 placeholder="<?php echo _t('Public Nickname'); ?>" class="form-control"
                                       required name="public_nickname" id="public_nickname"
                                       value="<?php echo $user->public_nickname; ?>">

                            </div>
                        </div>

                        <div class=" col-md-6">
                            <div class="inner-addon left-addon  large-margin">
                                <i class="glyphicon glyphicon-user"></i>
                                <input type="text" minlength=4 maxlength=20 placeholder="<?php echo _t('First name'); ?>" class="form-control" required
                                       name="firstname" id="firstname" value="<?php echo $firstname; ?>">

                            </div>
                        </div>


                        <div class=" col-md-6">
                            <div class="inner-addon left-addon  large-margin">
                                <i class="glyphicon glyphicon-user"></i>
                                <input type="text" minlength=4 maxlength=20 placeholder="<?php echo _t('Last name'); ?>" class="form-control" required
                                       name="lastname" id="lastname" value="<?php echo $lastname; ?>">

                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class=" col-md-6">
                                <div class="inner-addon left-addon  large-margin">
                                    <i class="glyphicon glyphicon-user"></i>
                                    <input type="checkbox"  class="form-control" name="investor" id="investor" value="<?php echo $investor; ?>"> Investor?

                                </div>
                            </div>
                            <div class=" col-md-6">
                                <div class="inner-addon left-addon  large-margin">
                                    <i class="glyphicon glyphicon-user"></i>
                                    <input type="checkbox"  class="form-control" name="miner" id="miner" value="<?php echo $miner; ?>"> Miner?

                                </div>
                            </div>
                        </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="inner-addon left-addon  large-margin">
                                <i class="glyphicon glyphicon-envelope"></i>
                                <input minlength=4 maxlength=60 type="email" placeholder="e-mail" class="form-control" required name="email" id="email"
                                       value="<?php echo $email; ?>">

                            </div>
                        </div>

                        <hr>
                        <div class="col-md-6">
                            <div class="inner-addon left-addon  large-margin">
                                <i class="glyphicon glyphicon-lock"></i>
                                <input type="password" placeholder="<?php echo _t('Password'); ?>" class="form-control"
                                       title="<?php echo _t('Password must contain at least 6 characters, including UPPER/lowercase and numbers'); ?>"
                                       required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" name="pwd1"
                                       onchange="this.setCustomValidity(this.validity.patternMismatch ? this.title : '')"
                                       id="pass1">

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="inner-addon left-addon  large-margin">
                                <i class="glyphicon glyphicon-lock"></i>
                                <input type="password" placeholder="<?php echo _t('Confirm password'); ?>" class="form-control"
                                       title="<?php echo _t('Please enter the same Password.'); ?>"
                                       required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" name="pwd2"
                                       onchange="this.setCustomValidity(this.validity.patternMismatch ? this.title : '')"
                                       id="pass2" onkeyup="checkPass(); return false;">


                            </div>
                        </div>

                        <div class="col-md-12">
                            <meter max="4" id="password-strength-meter"></meter>

                        </div>

                        <?php if ($settings->invite) { ?>
                            <div class="col-md-12">
                                <div class="inner-addon left-addon  large-margin">
                                    <i class="glyphicon glyphicon-flash"></i>
                                    <input type="text" placeholder="Enter the invite code" class="form-control"
                                           title="<?php echo _t('Please enter the invite code.'); ?>"
                                           name="invite" value="<?php echo $invite; ?>"
                                           onchange="this.setCustomValidity(this.validity.patternMismatch ? this.title : ''"
                                           id="pass2" onkeyup="checkPass(); return false;">

                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-12" style="text-align:center;">
                                <?php

                                    echo '<img id="capt" src="/core/macros/mc_captcha.php?rand_number=' . time() . '"  onClick="reload();">';
                                ?>
                                <script type="text/javascript">
                                    function reload() {
                                        img = document.getElementById("capt");
                                        img.src = "/core/macros/mc_captcha.php?rand_number=" + Math.random();
                                    }
                                </script>

                                <input class="form-input " type="captcha" id="captcha_answere" name="captcha_answer"
                                       placeholder="<?php echo _t('enter the captcha text'); ?>" required>

                            </div>
                        </div>
                        <div class="row container">
                            <div class="col-md-12">
                                <div class="checkbox checkbox-primary checkbox-circle ">
                                    <input type="checkbox" name="ok1" id="checkbox-1" value="1" checked>
                                    <label
                                            for="checkbox-1"><?php echo _t(" Send me a newsletter about this website."); ?></label>
                                </div>
                                <div class="checkbox checkbox-primary checkbox-circle">
                                    <input type="checkbox" id="checkbox-2" value="1" required name="ok2" checked>
                                    <label
                                            for="checkbox-2"> <?php echo _t("I accept the the terms of the <a href=/eula.php>EULA"); ?></a></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 text-center " ">
                        <button class="btn submit-button btn-block" type="submit" id="submit-register" name="submit-register" value="ok">
                            <?php echo _t("Create User Account"); ?></button>
                        <br>
                    </div>

                <div class="col-xs-10 col-md-12 text-center">
                    <div class="login-options"> <?php echo _t("Already have an account?"); ?><a href="/login.php">
                            <?php echo _t("Login."); ?></a></div>

                </div>
                </div>
            </div>
            </form>
        </div>
    </div>


    <!-- Panel Widget -->
    <script>
        var password = document.getElementById('pass1');
        var meter = document.getElementById('password-strength-meter');
        var text = document.getElementById('password-strength-text');

        password.addEventListener('input', function () {
            var val = password.value;
            var result = zxcvbn(val);

            // Update the password strength meter
            meter.value = result.score;

        });
    </script>
    <br><br><Br>

<?php
    require_once("_footer.php");
?>