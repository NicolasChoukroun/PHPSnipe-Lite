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


include_once($_SERVER['DOCUMENT_ROOT'] . "/common_min.php");

//recipient data
$email = $_REQUEST['email']; // Your Email Address

$alert = $_REQUEST['alert'];
$settings->iframe = true;
$title = "Login";
$user->is_logged = false;

if (isset($email)) {
    //echo "login:".$email;exit;

    $query = "SELECT * FROM users WHERE (LOWER(email) = '" . strtolower($email) . "' OR LOWER(login) = '" . strtolower($email) . "')";
    $db->query($query);
    $db->single();
    $row = $db->rs;
    $nbr = $db->nbr();
//echo $query."<br>".$nbr;exit;
    if ($nbr == 1) {
        $newpass = generateRandomString(10);
        $t_hasher = new PasswordHash(12, true);
        $hash = $t_hasher->HashPassword($newpass);

        $query = "UPDATE users SET password='" . $hash . "' WHERE email='" . $email . "'";
        $db->query($query);


        // envoie email
        $email_to = $email;
        $email_from = "thewolf@fileshareclub.com";
        $email_subject = "[FSC] Your lost password";
        $email_message = "Hello,
		<br/><br/>
		You asked for your lost password?
		<br/><br/>
		Here is a temporary one, just for help you to connect. You should change is as soon as you get inside.
		<br/>
		Temporary Password: " . $newpass . "<br/><br/>
		Thanks as always,<br/>
	    ".$user->email_signature;


        //@mail($email_to, $email_subject, $email_message, $headers);
        if (smtpmail($email_to, $email_subject, $email_message)) {

            $settings->success = _t("Your password has been sent sucessfully.");
            header("Location: /login.php?success=" . encrypt($settings->success));

            exit;
        } else {
            $settings->error = _t("Error: connection to your email server is impossible.");
        }

    } else {
        $settings->error = _t("Error: your email is not correct, try again or notify an admin.");
    }

}


require_once ("_header.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/core/macros/mc_alerts.php" );
?>
<link href="/css/login.css" rel="stylesheet" type="text/css">

<div class="row col-md-4 col-centered">
	<div class="shadowed huge-padding bg-white form-box" >
		<div class="panel-body ">

			<h2 class="form-login-heading"><?php echo _t("Forgotten password");?></h2><br>

			<form class="form-signin" id="login-form" action="/forget.php" role="form"  method="get">
				<div class="col-md-12">

					<input class="form-input" type="text" placeholder="<?php echo _t('Your email');?>" id="email" name="email" required value="<?php echo $email; ?>">


				</div>

				<div class="col-md-12 text-center mgbt-xs-5"><br>

					<button class="btn submit-button" type="submit" onclick="$.blockUI();"   id="login-submit" value="login-submit">
						<?php echo _t("Send a new password to your email");?></button>
				</div>


			</form>
			<br>
		</div> <br>
	</div>
	<div class="col-md-12">
		<div class="row">
			<div class="col-xs-6 text-left"><br>
				<div class="login-options"> <a href="/login.php"><?php echo _t("Login");?> </a> </div>
			</div>
			<div class="col-xs-6 text-right"><br>
				<div class="login-options"><a href="/register.php">
						<?php echo _t("Create an account");?></a> </div>
			</div>
		</div>
	</div>
</div>
<br>
<?php require_once ("_footer.php"); ?>


