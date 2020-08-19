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



require_once($_SERVER['DOCUMENT_ROOT'] . "/core/macros/mc_globals.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/_ini.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/common.php');

// cookies -----------------------------------------------------------------------------------------------------------------------------
//session_start();

// protection 1
//$headers = apache_request_headers();
if (isset($headers['CsrfToken'])) {
	if ($headers['CsrfToken'] !== $_SESSION['csrf_token']) {
		//echo "Wrong CSRF token: 1-".$headers['CsrfToken'] ." 2-".$_SESSION['csrf_token'];exit;
		exit;
	}
}
// protection 2
if (isset($_SERVER['HTTP_ORIGIN'])) {
	$address = $theme->url;
	if (strpos($address, $_SERVER['HTTP_ORIGIN']) !== 0) {
		//echo "Wrong origin: 1-".$address." 2-".$_SERVER['HTTP_ORIGIN'];exit;
		//exit;
	}
}


$image = imagecreatetruecolor(200, 50) or die("Cannot Initialize new GD image stream");
$c=255;
$background_color = imagecolorallocate($image, $c, $c, $c);
$text_color = imagecolorallocate($image, 0, $c, $c);
$line_color = imagecolorallocate($image, 128, 128, 128);
$pixel_color = imagecolorallocate($image, rand(64,200), rand(64,200), rand(64,200));

imagefilledrectangle($image, 0, 0, 200, 50, $background_color);

for ($i = 0; $i < 4; $i++) {
	//echo $i;
	imageline($image, 0, rand() % 50, 200, rand() % 50, $line_color);
}

for ($i = 0; $i < 3500; $i++) {
	//echo ".";
	imagesetpixel($image, rand() % 200, rand() % 50, $pixel_color);
}

$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
$len = strlen($letters);
$letter = $letters[rand(0, $len - 1)];

$word = "";
// more fonts here: http://www.danceswithferrets.org/lab/gdfs/
//echo $_SERVER['DOCUMENT_ROOT'].'/fonts/captcha.gdf';exit;
$font = imageloadfont(__DIR__.'/HomBoldB_16x24_LE.gdf');
if ($font==false) {echo "error cannot load font";exit;}
for ($i = 0; $i < 5; $i++) {
	$letter = $letters[rand(0, $len - 1)];
	$text_color = imagecolorallocate($image, rand(50,150), rand(50,150), rand(50,150));
	imagestring($image, $font, 2 + ($i * 40), 30+rand(0,-30), $letter, $text_color);
	$word .= $letter;
}

session_start();
$_SESSION['captcha'] = encrypt(strtolower($word)); // save as a session

//echo $word;exit;
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Content-type: image/png");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Expires: ' . gmdate ('D, d M Y H:i:s', time()));

imagejpeg($image);

//$save =ROOT  . "/test.png";
//chmod($save,0755);
//imagepng($image, $save, 0, NULL);
imagedestroy($image);



//echo "captcha=".$_SESSION['captcha'];

session_write_close();
//echo "<script>alert('".$_SESSION['captcha']."');</script>";