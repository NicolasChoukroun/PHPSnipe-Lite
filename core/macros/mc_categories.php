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

// manage the categories
if (isset($_REQUEST['name'])) $name = $_REQUEST['name']; else  $name = "";
if (!$settings->topbar) {

	if ($_REQUEST['qsection'] == "reset") {
		setCookies('qsection', 0);
		setCookies('qcategory', 0);
		setCookies('qsubcategory', 0);
		setCookies('qsubcategory2', 0);
		setCookies('qsubcategory3', 0);
		$settings->qsection = 0;
		$settings->qcategory = 0;
		$settings->qsubcategory = 0;
		$settings->qsubcategory2 = 0;
		$settings->qsubcategory3 = 0;
	}
	if ($_REQUEST['qcategory'] == "reset") {
		setCookies('qcategory', 0);
		setCookies('qsubcategory', 0);
		setCookies('qsubcategory2', 0);
		setCookies('qsubcategory3', 0);
		$settings->qcategory = 0;
		$settings->qsubcategory = 0;
		$settings->qsubcategory2 = 0;
		$settings->qsubcategory3 = 0;
	}
	if ($_REQUEST['qsubcategory'] == "reset") {
		setCookies('qsubcategory', 0);
		setCookies('qsubcategory2', 0);
		setCookies('qsubcategory3', 0);
		$settings->qsubcategory = 0;
		$settings->qsubcategory2 = 0;
		$settings->qsubcategory3 = 0;
	}
	if ($_REQUEST['qsubcategory2'] == "reset") {
		setCookies('qsubcategory2', 0);
		setCookies('qsubcategory3', 0);
		$settings->qsubcategory2 = 0;
		$settings->qsubcategory3 = 0;
	}
	if ($_REQUEST['qsubcategory3'] == "reset") {
		setCookies('qsubcategory3', 0);
		$settings->qsubcategory3 = 0;
	}

	if ($_REQUEST['qsection'] == 0) {
		$settings->qsection = getCookies('qsection');
	} else {

		$settings->qsection = 0;
		$settings->qcategory = 0;
		$settings->qsubcategory1 = 0;
		$settings->qsubcategory2 = 0;
		$settings->qsubcategory3 = 0;
		if ($_REQUEST['qsection'] <> "reset") {
			$settings->qsection = $_REQUEST['qsection'];
			setCookies('qsection', $settings->qsection);
			setCookies('qcategory', 0);
			setCookies('qsubcategory', 0);
			setCookies('qsubcategory2', 0);
			setCookies('qsubcategory3', 0);
		} else {
			$settings->qsection = 0;
			$settings->qcategory = 0;
			$settings->qsubcategory = 0;
			$settings->qsubcategory2 = 0;
			$settings->qsubcategory3 = 0;
		}
	}

	if ($_REQUEST['qcategory'] == 0) {
		$settings->qcategory = getCookies('qcategory');
	} else {
		$settings->qcategory = 0;
		$settings->qsubcategory = 0;
		$settings->qsubcategory2 = 0;
		$settings->qsubcategory3 = 0;
		if ($_REQUEST['qcategory'] <> "reset") {
		
			$settings->qcategory = $_REQUEST['qcategory'];
			setCookies('qcategory', $settings->qcategory);
			setCookies('qsubcategory', 0);
			setCookies('qsubcategory2', 0);
			setCookies('qsubcategory3', 0);
		} else {
			$settings->qcategory = 0;
			$settings->qsubcategory = 0;
			$settings->qsubcategory2 = 0;
			$settings->qsubcategory3 = 0;
		}
	}
	if ($_REQUEST['qsubcategory'] == 0) {
		$settings->qsubcategory = getCookies('qsubcategory');
	} else {
		$settings->qsubcategory = 0;
		$settings->qsubcategory2 = 0;
		$settings->qsubcategory3 = 0;
		if ($_REQUEST['qsubcategory'] <> "reset") {
		
			$settings->qsubcategory = $_REQUEST['qsubcategory'];
			setCookies('qsubcategory', $settings->qsubcategory);
			setCookies('qsubcategory2', 0);
			setCookies('qsubcategory3', 0);
		} else {
			$settings->qsubcategory = 0;
			$settings->qsubcategory2 = 0;
			$settings->qsubcategory3 = 0;
		}
	}
	if ($_REQUEST['qsubcategory2'] == 0) {
		$settings->qsubcategory2 = getCookies('qsubcategory2');
	} else {
		$settings->qsubcategory2 = 0;
		$settings->qsubcategory3 = 0;
		if ($_REQUEST['qsubcategory2'] <> "reset") {
			
			$settings->qsubcategory2 = $_REQUEST['qsubcategory2'];
			setCookies('qsubcategory3', 0);
			setCookies('qsubcategory2', $settings->qsubcategory2);
		} else {
			$settings->qsubcategory2 = 0;
			$settings->qsubcategory3 = 0;
		}
	}
	if ($_REQUEST['qsubcategory3'] == 0) {
		$settings->qsubcategory3 = getCookies('qsubcategory3');
	} else {
		if ($_REQUEST['qsubcategory3'] <> "reset") {
			$settings->qsubcategory3 = $_REQUEST['qsubcategory3'];
			setCookies('qsubcategory3', $settings->qsubcategory3);
		} else {
			$settings->qsubcategory3 = 0;
		}
	}
}


$settings->qsection=$_COOKIE["qsection"];
$settings->qcategory=$_COOKIE["qcategory"];
$settings->qsubcategory=$_COOKIE["qsubcategory"];
$settings->qsubcategory2=$_COOKIE["qsubcategory2"];
$settings->qsubcategory3=$_COOKIE["qsubcategory3"];