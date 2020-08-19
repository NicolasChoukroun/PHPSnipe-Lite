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


$topbar=666;
require_once $_SERVER['DOCUMENT_ROOT']."/common.php";
$nonindx=true;
// echo $_SERVER['HTTP_X_REQUESTED_WITH'];
if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
	exit;
}
$status=$_REQUEST['status'];
$productid=$_REQUEST['productid'];
$userid=$_REQUEST['userid'];
$whouserid=$_REQUEST['whouserid'];
if ($userid>0)
{
	if ($status=="true") $statusid=1; else $status=0;
	$db1=new Database();
	if ($status>=1) 
	{
		$sql="SELECT *FROM thanks where productid='$productid' and userid='$userid' and whouserid='$whouserid'";
		$db1->query($sql);		
		if	($db1->numRows()==0)
		{	
			$sql="INSERT INTO thanks (productid,userid,whouserid) VALUES ('$productid','$userid','$whouserid')";
			$db1->query($sql);
		}	
		//echo "You will receive notifications\n when this product will be updated";
	}
	else
	{
		$sql="DELETE FROM thanks where productid='$productid' and userid='$userid' and whouserid='$whouserid'";
		$db1->query($sql);
		//echo "Notifications about this product\nhave been cancelled";

	}
}
 //$debug="debug:status=".$status." productid=".$productid."  userid=".$userid."<br>".$sql;
// echo $debug;
?>

