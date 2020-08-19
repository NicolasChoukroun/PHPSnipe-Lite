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


$settings->time = time();
$user->ip = $_SERVER['REMOTE_ADDR'];
$user->email = $email= decrypt(getCookies("email"));
$pass=decrypt(getCookies("password"));

$login = new Database();$login2 = new Database();
$sql = "SELECT * FROM users WHERE (email = '" . $user->email . "' OR login = '" . $user->email . "')  AND is_approved=1 AND is_locked<>1 AND statusid=1";
//echo $sql . " - " . $error."<br>";
$login->query($sql);
$login->single();
$nbr = $login->nbr();
if ($nbr == 1) {

	$hash = $login->rs['password'];
	$t_hasher = new PasswordHash(12, true);
	$check = $t_hasher->CheckPassword($pass, $hash);

	if ($check == 1) {

		$settings->sessionid = md5(encrypt($login->rs['id']));
		setCookies("email", encrypt($user->email)); // this mean that the customer is logged.
		setCookies("sessionid", $settings->sessionid);
		setCookies("password", encrypt($pass));
		
		$settings->pass = encrypt($pass);
		$user->is_editor=$user->is_visitor=$settings->free=$user->is_admin=false;
		$user->is_logged = true;
		$user->is_logged = $logged = true;
		$user->id = $login->Record['id'];
		$user->firstname = $login->Record['firstname'];
		$user->public_nickname = $login->Record['public_nickname'];
		$user->lastname = $login->Record['lastname'];
		$user->fullname = ucwords($firstname . " " . $lastname);
		$user->level = $login->Record['levelid'];
		$user->email = $login->Record['email'];
		$user->date = $login->Record['date'];
		$user->userdate = $login->Record['date'];
		$user->is_stopped = $login->Record['is_stopped'];
		$user->is_locked = $login->Record['is_locked'];
		$user->is_approved = $login->Record['is_approved'];
		$settings->is_silenced = $login->Record['is_silenced'];
		$user->userstatus = $login->Record['statusid'];

		$user->usercredits =  $login->Record['credits'];
		$user->score = $login->Record['score'];
		$user->rank = $login->Record['rank'];
		$settings->thanks = $login->Record['thanks'];
		$settings->nbrreviews = $login->Record['nbrreviews'];
		$settings->nbrcomments = $login->Record['nbrcomments'];
		$settings->nbrdownloads = $login->Record['nbrdownloads'];
		$settings->nbractions = $login->Record['nbractions'];
		$settings->last_ip = $login->Record['last_ip'];
		$settings->last_login = $login->Record['last_login'];
		$settings->sparkle = $login->Record['sparkle'];
		$settings->anonymous = $login->Record['anonymous'];
		$settings->random = $login->Record['random'];
		$settings->css = $login->Record['css'];
		$settings->type=$login->Record['type'];
		$settings->kma = $login->Record['kma'];
		$user->patron = intval($login->Record['patron']);
		$sql="update users set remote_addr='".$user->ip."', last_ip='".$user->ip."' WHERE id=".$user->id;
		$login->query($sql);
		if ($login->Record['type'] == 1) {
			$user->is_admin = $admin = true;
			$user->is_editor=$user->is_visitor=$settings->free=false;
			if ($settings->logas >1) {
				$userid = $user->id = $settings->logas;
				$user->is_admin = $admin = true;
				$sql = "SELECT * FROM users WHERE id=".$settings->logas;
				$login2->query($sql);
				$login2->single();
				$user->public_nickname = $login2->Record['public_nickname'];
				$user->lastname = $login2->Record['lastname'];
				$user->fullname = ucwords($firstname . " " . $lastname);
				$user->level = $login2->Record['levelid'];
				$user->email = $login2->Record['email'];
				$user->date = $login2->Record['date'];
				$user->userdate = $login2->Record['date'];
				$user->is_stopped = $login2->Record['is_stopped'];
				$user->is_locked = $login2->Record['is_locked'];
				$user->is_approved = $login2->Record['is_approved'];
				$settings->is_silenced = $login2->Record['is_silenced'];
				$user->userstatus = $login2->Record['statusid'];
				$user->usercredits =  $login2->Record['credits'];

				$user->score = $login2->Record['score'];
				$user->rank = $login2->Record['rank'];
				$user->score = $login2->Record['score'];
				$settings->thanks = $login2->Record['thanks'];
				$settings->nbrreviews = $login2->Record['nbrreviews'];
				$settings->nbrcomments = $login2->Record['nbrcomments'];
				$settings->nbrdownloads = $login2->Record['nbrdownloads'];
				$settings->nbractions = $login2->Record['nbractions'];
				$user->patron = intval($login2->Record['patron']);
				$settings->patreon_email = $login2->Record['patreon_email'];
				$settings->discord_nickname = $login2->Record['discord_nickname'];
			}
		}else {
			if ($login->Record['type'] == 2) {
				$user->is_admin = $admin = false;
				$user->is_editor = $editor = true;
				$user->is_visitor = $settings->free = false;
			}

		}


	} else {
		//echo "5";exit;
		setCookies("login", 0);
		setCookies("sessionid", 0);
		setCookies("password", 0);
		session_unset();
		session_destroy();
		$error = encrypt(_t("Wrong Password, please login again."));
		header("Location: /login.php?error=" . $error);
		//echo "redirect1";
		exit;
	}
} else {
	//echo "4";exit;
	setCookies("login", 0);
	setCookies("sessionid", 0);
	setCookies("password", 0);
	session_unset();
	session_destroy();
	$error = encrypt("Wrong Login, please login again.");
	header("Location: /login.php?error=" . $error);
	//echo "redirect2";
	exit;
}

$user->ip = $_SERVER['REMOTE_ADDR'];
$darray = explode('.', $_SERVER['HTTP_HOST']);
$narray = array_reverse($darray);
$domain = $narray[1];
unset($darray, $narray);
$settings->servername = strtolower($domain);


refreshSettings();

$root = dirname(__file__);
$settings->root = $root;
//$theme=refreshThemes();
refreshCategories();
refreshGeoIP();

if ($user->is_admin && $settings->logas == 0) $user->rank = 14;
// patreon
if ($user->patron>0 && $user->rank<$user->patron) $user->rank=$user->patron;


refreshCredits();
refreshSession();

$login->close();
