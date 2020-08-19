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
 ?>
<?php require("common.php"); ?>
<?php //if (startc() == true) { // use if you want to cache the page ?>
<?php 
    $settings->showadminmenu = true;    
    // include the master header
    require_once("_header.php");
?>
<div class="main-container">
    <div class="row">
        <div class="col-md-12">
        </div>
</div>
    
<?php 
    require_once("_footer.php"); 
    ?>

<?php //endc(); } // use if you want to cache the page  ?>

