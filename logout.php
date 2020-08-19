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
include ("common.php");

logout();

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]
	);
}




// unset cookies
if (isset($_SERVER['HTTP_COOKIE'])) {
	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
	foreach($cookies as $cookie) {
		$parts = explode('=', $cookie);
		$name = trim($parts[0]);
		setcookie($name, '', time()-1000);
		setcookie($name, '', time()-1000, '/');
	}
}
// Finally, destroy the session.
session_destroy();

$settings->iframe = true; 
require_once ("_header.php");
require_once ($_SERVER['DOCUMENT_ROOT']."/core/macros/mc_alerts.php" );

?>
<div class="row col-md-12">
<center><span class="glyphicon glyphicon-globe" style="font-size:100px;font-color:white;"></span></center>
</div>
<section class="row col-md-12  bg--white">
<div class="container shadowed" style="margin:auto;" >
    <div class="panel-body">
      <h1 class="font-semibold text-center" style="font-size:52px"><?php echo _t('You Have Been Logged Out');?></h1>
    
        <div class="form-group">
          <div class="col-md-12">
            <h4 class="text-center mgbt-xs-20"><br><br><br><br><?php echo _t('Thank you for using our website');?></h4>
              <p class="text-center" style="z-index:1000;"> <?php echo _t('Please');?> <a href="/login.php"><?php echo _t("Click here");?> <?php echo _t('to login back to our website');?></a></p>
    
    
          </div>
        </div>
      
    </div>
</div>

</section>


<!-- Specific Page Scripts Put Here -->


<?php require_once ("_footer.php"); ?>