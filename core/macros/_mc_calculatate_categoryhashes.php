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

// uploads
$t = time();
$dbx = new Database();
$dbx1 = new Database();
$db1 = new Database();
$db = new Database();

echo "\nStarting calculation of Category Hashes task";

$sql = "TRUNCATE TABLE qcategories";
$db->query($sql);
$sql = "TRUNCATE TABLE categories";
$db->query($sql);
$id = 0;
$i = 0;
$xdico = [];

$sql = "SELECT qsection,section FROM products GROUP BY qsection";
$dbx->query($sql);
while ($dbx->next()) {
	$sql = "INSERT INTO qcategories (section,qsection,category,qcategory,subcategory,qsubcategory,subcategory2,qsubcategory2,subcategory3,qsubcategory3,nbr)
                VALUES ('" . $dbx->rs['section'] . "'," . qcat($dbx->rs['section']) . ",'',0,'',0,'',0,'','0','0')";
	//echo $sql."<br>";
	$dbx1->query($sql);
	$xdico[qcat($dbx->rs['section'])] = strtolower($dbx->rs['section']);
	$xdico[qcat($dbx->rs['category'])] = strtolower($dbx->rs['category']);
	$xdico[qcat($dbx->rs['subcategory'])] = strtolower($dbx->rs['subcategory']);
	$xdico[qcat($dbx->rs['subcategory2'])] = strtolower($dbx->rs['subcategory2']);
	$xdico[qcat($dbx->rs['subcategory3'])] = strtolower($dbx->rs['subcategory3']);

}

$sql = "SELECT qsection,section, qcategory,category FROM products GROUP BY qsection,qcategory";
$dbx->query($sql);
while ($dbx->next()) {
	$sql = "INSERT INTO qcategories (section,qsection,category,qcategory,subcategory,qsubcategory,subcategory2,qsubcategory2,subcategory3,qsubcategory3,nbr)
                VALUES ('" . $dbx->rs['section'] . "'," . qcat($dbx->rs['section']) . ",'" . $dbx->rs['category'] . "'," . qcat($dbx->rs['category']) . " ,'',0,'',0,'','0','0')";
	//echo $sql."<br>";
	$dbx1->query($sql);
	$xdico[qcat($dbx->rs['section'])] = strtolower($dbx->rs['section']);
	$xdico[qcat($dbx->rs['category'])] = strtolower($dbx->rs['category']);
	$xdico[qcat($dbx->rs['subcategory'])] = strtolower($dbx->rs['subcategory']);
	$xdico[qcat($dbx->rs['subcategory2'])] = strtolower($dbx->rs['subcategory2']);
	$xdico[qcat($dbx->rs['subcategory3'])] = strtolower($dbx->rs['subcategory3']);
}
$sql = "SELECT qsection,section,qcategory,category,qsubcategory,subcategory FROM products GROUP BY qsection,qcategory,qsubcategory";
$dbx->query($sql);
while ($dbx->next()) {
	$sql = "INSERT INTO qcategories (section,qsection,category,qcategory,subcategory,qsubcategory,subcategory2,qsubcategory2,subcategory3,qsubcategory3,nbr)
                VALUES ('" . $dbx->rs['section'] . "'," . qcat($dbx->rs['section']) . ",'" . $dbx->rs['category'] . "'," . qcat($dbx->rs['category']) . ",
                '" . $dbx->rs['subcategory'] . "'," . qcat($dbx->rs['subcategory']) . ",'',0,'','0','0')";
	//echo $sql."<br>";
	$dbx1->query($sql);
	$xdico[qcat($dbx->rs['section'])] = strtolower($dbx->rs['section']);
	$xdico[qcat($dbx->rs['category'])] = strtolower($dbx->rs['category']);
	$xdico[qcat($dbx->rs['subcategory'])] = strtolower($dbx->rs['subcategory']);
	$xdico[qcat($dbx->rs['subcategory2'])] = strtolower($dbx->rs['subcategory2']);
	$xdico[qcat($dbx->rs['subcategory3'])] = strtolower($dbx->rs['subcategory3']);
}
$sql = "SELECT qsection,section,qcategory,category,qsubcategory,subcategory,qsubcategory2,subcategory2 FROM products GROUP BY qsection,qcategory,qsubcategory,qsubcategory2";
$dbx->query($sql);
while ($dbx->next()) {
	$sql = "INSERT INTO qcategories (section,qsection,category,qcategory,subcategory,qsubcategory,subcategory2,qsubcategory2,subcategory3,qsubcategory3,nbr)
                VALUES ('" . $dbx->rs['section'] . "'," . qcat($dbx->rs['section']) . ",'" . $dbx->rs['category'] . "'," . qcat($dbx->rs['category']) . " ,
                '" . $dbx->rs['subcategory'] . "'," . qcat($dbx->rs['subcategory']) . ",'" . $dbx->rs['subcategory2'] . "'," . qcat($dbx->rs['subcategory2']) . ",'','0','0')";
	//echo $sql."<br>";
	$dbx1->query($sql);
	$xdico[qcat($dbx->rs['section'])] = strtolower($dbx->rs['section']);
	$xdico[qcat($dbx->rs['category'])] = strtolower($dbx->rs['category']);
	$xdico[qcat($dbx->rs['subcategory'])] = strtolower($dbx->rs['subcategory']);
	$xdico[qcat($dbx->rs['subcategory2'])] = strtolower($dbx->rs['subcategory2']);
	$xdico[qcat($dbx->rs['subcategory3'])] = strtolower($dbx->rs['subcategory3']);
}
$sql = "SELECT qsection,section,qcategory,category,qsubcategory,subcategory,qsubcategory2,subcategory2,qsubcategory3,subcategory3 FROM products GROUP BY qsection,qcategory,qsubcategory,subcategory2,qsubcategory3";
$dbx->query($sql);
while ($dbx->next()) {
	$sql = "INSERT INTO qcategories (section,qsection,category,qcategory,subcategory,qsubcategory,subcategory2,qsubcategory2,subcategory3,qsubcategory3,nbr)
                VALUES ('" . $dbx->rs['section'] . "'," . qcat($dbx->rs['section']) . ",'" . $dbx->rs['category'] . "'," . qcat($dbx->rs['category']) . " ,'" . $dbx->rs['subcategory'] . "'," . qcat($dbx->rs['subcategory']) . ",'" . $dbx->rs['subcategory2'] . "'," . qcat($dbx->rs['subcategory2']) . ",'" . $dbx->rs['subcategory3'] . "'," . qcat($dbx->rs['subcategory3']) . ",'0')";
	//echo $sql."<br>";
	$dbx1->query($sql);
	$xdico[qcat($dbx->rs['section'])] = strtolower($dbx->rs['section']);
	$xdico[qcat($dbx->rs['category'])] = strtolower($dbx->rs['category']);
	$xdico[qcat($dbx->rs['subcategory'])] = strtolower($dbx->rs['subcategory']);
	$xdico[qcat($dbx->rs['subcategory2'])] = strtolower($dbx->rs['subcategory2']);
	$xdico[qcat($dbx->rs['subcategory3'])] = strtolower($dbx->rs['subcategory3']);
}

foreach ($xdico as $key => $value) {
	//echo $key." - ".$value."<Br>";
	$sql = "INSERT INTO categories (id,name) VALUES ($key,'" . $value . "')";
	$dbx1->query($sql);

}

echo "\n\nUpdate qcategory & created the category lookup table was done succesfully.\n\r";

$db->close();
$db1->close();
$dbx->close();
$dbx1->close();
unset($db);
unset($db1);
unset($dbx);
unset($dbx1);

echo "Creating the precalculation numbers.\n\r";
$id = 0;
$i = 0;
$sql = "SELECT * FROM qcategories";

$dbx = new Database();
$dbx1 = new Database();
$db = new Database();
$db->query($sql);
while ($db->next()) {
	// update the number of items per category
	cleannone1($db);
	$cid = $db->rs['id'];
	$qsection = $db->rs['qsection'];
	$qcategory = $db->rs['qcategory'];
	$qsubcategory = $db->rs['qsubcategory'];
	$qsubcategory2 = $db->rs['qsubcategory2'];
	$qsubcategory3 = $db->rs['qsubcategory3'];

	$section = $db->rs['section'];
	$category = $db->rs['category'];
	$subcategory = $db->rs['subcategory'];
	$subcategory2 = $db->rs['subcategory2'];
	$subcategory3 = $db->rs['subcategory3'];
	$sql_section = $sql_category = $sql_subcategory = $sql_subcategory2 = $sql_subcategory3 = "";
	$sql_section = " qsection=" . $qsection;

	if ($qcategory > 0) {
		$sql_category = " and qcategory=" . $qcategory;
	}
	if ($qsubcategory > 0) {
		$sql_subcategory = " and qsubcategory=" . $qsubcategory;
	}
	if ($qsubcategory2 > 0) {
		$sql_subcategory2 = " and qsubcategory2=" . $qsubcategory2;
	}
	if ($qsubcategory3 > 0) {
		$sql_subcategory3 = " and qsubcategory3=" . $qsubcategory3;
	}
	if ($qsubcategory3 == 0) {

		$sql = "SELECT count(*) AS total FROM products WHERE  " . $sql_section . $sql_category . $sql_subcategory . $sql_subcategory2 . $sql_subcategory3;
		$dbx1->query($sql);
		$dbx1->single();
		$nbr = $dbx1->rs['total'];
		$sql = "UPDATE qcategories SET nbr=" . $nbr . " WHERE id=" . $cid;
		$dbx->query($sql);
		$i++;
		if ($i % 1000) echo "*";
	}

}
$db->close();
$dbx->close();
$dbx1->close();
unset($db);
unset($dbx);
unset($dbx1);

echo "\r\nUpdate number of product per category done succesfully.\r\n";

function cleannone1 (&$dbx)
{
	if (strtolower($dbx->rs['section']) == "none") $dbx->rs['section'] = "";
	if (strtolower($dbx->rs['category']) == "none") $dbx->rs['category'] = "";
	if (strtolower($dbx->rs['subcategory']) == "none") $dbx->rs['subcategory'] = "";
	if (strtolower($dbx->rs['subcategory2']) == "none") $dbx->rs['subcategory2'] = "";
	if (strtolower($dbx->rs['subcategory3']) == "none") $dbx->rs['subcategory3'] = "";
}





