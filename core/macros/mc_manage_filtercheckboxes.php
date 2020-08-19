<?php
/**
 * Copyright (c) 2016. TheWolf
 */

/**
 * fileshareclub.com
 * File: mc_manage_filtercheckboxes.php
 * Created by Administrator
 * Date: 8/1/2016
 * Time: 3:11 PM
 */

if (isset($_REQUEST['otherfilter_exclu'])) {
	$otherfilter_exclu=$_REQUEST['otherfilter_exclu'];
	setCookies("otherfilter_exclu", $otherfilter_exclu);
}else{
	if ($_REQUEST['action']=="switch1")  { $otherfilter_exclu=1;setCookies("otherfilter_exclu", $otherfilter_exclu);} else {$otherfilter_exclu=getCookies("otherfilter_exclu");}

}
if (isset($_REQUEST['otherfilter_forged1'])) {
	$otherfilter_forged1=$_REQUEST['otherfilter_forged1'];
	setCookies("otherfilter_forged1", $otherfilter_forged1);
}else{
	if ($_REQUEST['action']=="switch2")  { $otherfilter_forged1=1;setCookies("otherfilter_forged1", $otherfilter_forged1);} else {$otherfilter_forged1=getCookies("otherfilter_forged1");}
}
if (isset($_REQUEST['otherfilter_forged2'])) {
	$otherfilter_forged2=$_REQUEST['otherfilter_forged2'];
	setCookies("otherfilter_forged2", $otherfilter_forged2);
}else{
	if ($_REQUEST['action']=="switch3")  { $otherfilter_forged2=1;setCookies("otherfilter_forged2", $otherfilter_forged2);} else {$otherfilter_forged2=getCookies("otherfilter_forged2");}
}
if (isset($_REQUEST['otherfilter_free'])) {
	$otherfilter_free=$_REQUEST['otherfilter_free'];
	setCookies("otherfilter_free", $otherfilter_free);
}else{
	if ($_REQUEST['action']=="switch4")  { $otherfilter_free=1;setCookies("otherfilter_free", $otherfilter_free);} else {$otherfilter_free=getCookies("otherfilter_free");}
}