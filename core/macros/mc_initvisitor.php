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
//$user->is_logged = false;
//$user->is_visitor = true;

$login=new Database();
// load the settings
$sql = "select * from settings";
$login->query($sql);
$i = 0;
while ($login->nextRecord()) {
	$field = $login->rs['name'];
	$settings->$field = $login->rs['value'];
	$settings->description[$field] = $login->rs['description'];
	$i++;
}

$user->ip = $_SERVER['REMOTE_ADDR'];
$darray = explode('.', $_SERVER['HTTP_HOST']);
$narray = array_reverse($darray);
$domain = $narray[1];
unset($darray, $narray);
$settings->servername = strtolower($domain);

//$theme=refreshThemes();

$settings->usernameclean = getNickNameFromIdClean($user->id);
$settings->servername = $_SERVER['SERVER_NAME'];
$root = dirname(__file__);
$settings->root = $root;

// grab the global settings in the DB write them in $settings->???

$user->ip = $_SERVER['REMOTE_ADDR'];
$darray = explode('.', $_SERVER['HTTP_HOST']);
$narray = array_reverse($darray);
$domain = $narray[1];
unset($darray, $narray);


$db1= new Database();
// fill in the category translation table.
$sql = "SELECT * FROM categories ";
$db1->query($sql);
while ($db1->nextRecord()) $settings->allcat[$db1->Record['id']] = $db1->Record['name'];

// storage path management
$upload_path = $settings->upload_path;
$download_path = $settings->download_path;
$tmp_path = $settings->tmp_path;
$download_url = $settings->download_url;
$ftp_server = $settings->ftp_server;
$ftp_user_name = $settings->ftp_user_name;
$ftp_user_pass = $settings->ftp_user_pass;
$settings->twitter = true;
$user->ip = $_SERVER['REMOTE_ADDR'];
$settings->servername = $_SERVER['SERVER_NAME'];

// Geolocalization stuff
include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/geoip.inc");
include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/geoipcity.inc");
include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/geoipregionvars.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/timezone.php");

// geo city
$gi = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIP.dat", GEOIP_STANDARD);
$settings->countrycode = geoip_country_code_by_addr($gi, $user->ip);
$settings->country = geoip_country_name_by_addr($gi, $user->ip);
$gi2 = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIPCity.dat", GEOIP_STANDARD);
$record = geoip_record_by_addr($gi2, $user->ip);
$settings->metro = $record->metro_code;
$settings->city = clean($record->city);
$settings->zipcode = $record->postal_code;
$settings->latitude = $record->latitude;
$settings->longitude = $record->longitude;
$settings->continent_code = $record->continent_code;
$settings->area_code = $record->area_code;
$giorg = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIPOrg.dat", GEOIP_STANDARD);
$settings->org = geoip_org_by_addr($giorg, $user->ip);
$giisp = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIPISP.dat", GEOIP_STANDARD);
$settings->domain = $settings->isp = geoip_org_by_addr($giisp, $user->ip);
geoip_close($giorg);
geoip_close($giisp);
geoip_close($gi2);
geoip_close($gi);
$settings->domain = gethostbyaddr($user->ip);
if (isset($_SERVER['HTTP_REFERER'])) $ref = $_SERVER['HTTP_REFERER']; else $ref = "";
$settings->referer = $ref;
$ref1 = str_replace("http://" . $theme->name . ".com", "", $ref);
$ref1 = str_replace("http://www." . $theme->name . ".com", "", $ref1);
if ($_SERVER["PHP_SELF"] == $_SERVER["REQUEST_URI"]) $_SERVER["REQUEST_URI"] = "";
$settings->url = strtolower($_SERVER['SERVER_NAME'] . $_SERVER["PHP_SELF"] . $_SERVER["REQUEST_URI"]);
$settings->url = "http://" . str_replace("https:", "http:", $settings->url);
$settings->head = $head = $_SERVER['HTTP_USER_AGENT'];

$settings->location = $settings->latitude . "," . $settings->longitude;
if (($settings->country == "" && $settings->countrycode == "") || $settings->countrycode == "A1"
) {
	$settings->country = "Proxy";
}

// bandwidth and everything else
// base of the rules
$user->dailybandwidth = 104857600;
$user->dailyfiles = 10;
$id_level = MEMBER;

if ($user->is_admin && $settings->logas == 0) $user->rank = 14;

$bot = new WebBotChecker();

//whitelist search engines
if ($settings->org == "Googlebot" || $bot->isThatBot() == true) {
	$user->rank = 14;
	$user->is_logged = true;
	$gold = true;
}

// save session
$settings->FSCUID = md5(FSCUID);
$s1 = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(SESSION_KEY), base64_encode(serialize($settings)), MCRYPT_MODE_CBC, md5(md5(SESSION_KEY)));
//echo "1- ".md5(FSCUID.$user->ip);echo "<br>";
setSession(md5(FSCUID), $s1);

$login->close();
$db1->close();

