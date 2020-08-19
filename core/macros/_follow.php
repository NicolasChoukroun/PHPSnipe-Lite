<?php
/*
 *  Copyright (c) 2018-2020. Nicolas Choukroun.
 *  Copyright (c) 2018-2020. The PHPSnipe Developers.
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the Attribution 4.0 International License as published by the
 *  Creative Commons Corporation; either version 2 of the License, or (at your option)
 *  any later version.  See COPYING for more details.
 *
 ******************************************************************************/ 

require_once $_SERVER['DOCUMENT_ROOT']."/common.php";
$nonindx=true;
// echo $_SERVER['HTTP_X_REQUESTED_WITH'];
if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
	exit;
}
$status=$_REQUEST['status'];
$productid=$_REQUEST['productid'];
$userid=$_REQUEST['userid'];
if ($userid>0)
{
	if ($status=="true") $status=1; else $status=0;

	$db1=new Database();
	if ($status>=1) 
	{
		$sql="INSERT INTO follow (productid,userid) VALUES ('$productid','$userid')";
		$db1->query($sql);
		echo _t("You will receive notifications\n when this product will be updated");
	}
	else
	{
		$sql="DELETE FROM follow where productid='$productid' and userid='$userid'";
		$db1->query($sql);
		echo _t("Notifications about this product\nhave been cancelled");

	}
}
// $debug="debug:status=".$status." productid=".$productid."  userid=".$userid;
// echo $debug;
?>

