<!-- - - - - - - - - - - - - - Alerts - - - - - - - - - - - - - - - - -->
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


//$error=generate($error);
if ($user->is_logged) {
?>
<div class="row"><div class="col-sm-12">
<?php /* ***************    warnings and alerts ***************/ ?>
<?php if ($settings->warning<>"") { ?>
 <div class="alert alert-warning ">
 <button type="button" class="close" data-dismiss="alert" ><i class="glyphicon glyphicon-remove-sign"></i></button>
 <span class="vd_alert-icon"><i class="fa fa-exclamation-circle vd_red"></i></span><strong><?php echo $settings->warning; ?></strong></div>
 <?php } ?>

<?php if ($settings->warning_global<>"") { ?>
 <div class="alert alert-warning ">
 <button type="button" class="close" data-dismiss="alert" ><i class="glyphicon glyphicon-remove-sign"></i></button>
 <span class="vd_alert-icon"><i class="fa fa-exclamation-circle vd_red"></i></span><strong><?php echo $settings->warning_global; ?></strong></div>
 <?php } ?>

 <?php if ($settings->error<>"") { ?>
 <div class="alert alert-danger ">
 <button type="button" class="close" data-dismiss="alert" ><i class="glyphicon glyphicon-remove-sign"></i></button>
 <span class="vd_alert-icon"><i class="fa fa-exclamation-circle vd_red"></i></span><strong><?php echo $settings->error; ?></strong></div>
 <?php } ?>

 <?php if ($settings->success<>"") { ?>
 <div class="alert alert-success">
 <button type="button" class="close" data-dismiss="alert" ><i class="glyphicon glyphicon-remove-sign"></i></button>
 <span class="vd_alert-icon"><i class="fa fa-exclamation-circle vd_green"></i></span><strong><?php echo $settings->success; ?></strong></div>
 <?php } ?>

 <?php if ($settings->info<>"") { ?>
 <div class="alert alert-info ">
 <button type="button" class="close" data-dismiss="alert"><i class="glyphicon glyphicon-remove-sign"></i></button>
 <span class="vd_alert-icon"><i class="fa fa-exclamation-circle vd_blue"></i></span><strong><?php echo $settings->info; ?></strong></div>
 <?php } ?>

 <?php if ($settings->alert<>"") { ?>
 <div class="alert alert-danger ">
 <button type="button" class="close" data-dismiss="alert"><i class="glyphicon glyphicon-remove-sign"></i></button>
 <span class="vd_alert-icon"><i class="fa fa-exclamation-circle vd_blue"></i></span><strong><?php echo $settings->alert; ?></strong></div>
 <?php } ?>

</div></div>
<!-- - - - - - - - - - - - - end Alerts - - - - - - - - - - - - - - - -->
<?php }else{

if ($settings->error<>"") { 
		echo '<div class="alert alert-danger ">
			<button type="button" class="close" data-dismiss="alert" ><i class="glyphicon glyphicon-remove-sign"></i></button>
			<span class="alert-icon"><i class="fa fa-exclamation-circle vd_red"></i></span><strong> '.$settings->error.'</strong></div>';
}else{

    if ($settings->warning<>"") { 
         echo '<div class="alert alert-warning ">
         <button type="button" class="close" data-dismiss="alert" ><i class="glyphicon glyphicon-remove-sign"></i></button>
         <span class="vd_alert-icon"><i class="fa fa-exclamation-circle vd_red"></i></span><strong>'.$settings->warning.'</strong></div>';
      } else {
    
         if ($settings->success<>"") {
        		echo '<div class="alert alert-success">
        			<button type="button" class="close" data-dismiss="alert" ><i class="glyphicon glyphicon-remove-sign"></i></button>
        			<span class="alert-icon" ><i class="fa fa-exclamation-circle vd_green"></i></span> <strong>'.$settings->success.'</strong></div>';
         } 
    }
}


} ?>
<?php /*

	<div class="alert alert-danger ">
		<button type="button" class="close" data-dismiss="alert"><i class="glyphicon glyphicon-remove-sign"></i></button>
		<span class="vd_alert-icon"><i class="fa fa-exclamation-circle vd_blue"></i></span><strong>We have lost +100 important asset, <a href="/pathfixing.php">please help re-collect the missing zip here.</a></strong></div>

*/ ?>