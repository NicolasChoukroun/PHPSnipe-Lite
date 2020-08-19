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

    if ($user->is_admin==true) { ?>
    <h1><center>Admin menu</center></h1>

    <div class="row  bg--white boxed" style="background-color:#eeeeee;">
        <div class="col-md-12 ">
<div class="container">
            <ul class=" nav nav-pills " style="margin-top:0;z-index:1;">
                <li role="Blog" <?php if (strtolower($_SERVER["PHP_SELF"]) == "/admin_blog.php") {
                    echo 'class="active btn btn-success"';
                }else   echo 'class="btn btn-primary"';?>>
                    <a href="/admin_blog.php">Blog</a></li>


                <li role="traduction" <?php if (strtolower($_SERVER["PHP_SELF"]) == "/admin_traduction.php") {
                    echo 'class="active btn btn-success"';
                }else   echo 'class="btn btn-primary "';?>>
                    <a href="/admin_traduction.php">Traduction</a></li>


                <li role="userspresentation" <?php if (strtolower($_SERVER["PHP_SELF"]) == "/admin_users.php") {
                    echo 'class="active btn btn-success"';
                }else   echo 'class="btn btn-primary "';?>>
                    <a href="/admin_users.php">Users</a></li>



                <li role="challenge" <?php if (strtolower($_SERVER["PHP_SELF"]) == "/admin_challenges.php") {
                    echo 'class="active btn btn-success"';
                }else   echo 'class="btn btn-primary"';?>>
                    <a href="/admin_challenges.php">Challenges</a></li>

                <li role="ong" <?php if (strtolower($_SERVER["PHP_SELF"]) == "/admin_ong.php") {
                    echo 'class="active btn btn-success"';
                }else   echo 'class="btn btn-primary"';?>>
                    <a href="/admin_ong.php">ONG</a></li>


                <li role="votes" <?php if (strtolower($_SERVER["PHP_SELF"]) == "/admin_votes.php") {
                    echo 'class="active btn btn-success"';
                }else   echo 'class="btn btn-primary"';?>>
                    <a href="/admin_votes.php">Votes</a></li>

                <li role="paid" <?php if (strtolower($_SERVER["PHP_SELF"]) == "/admin_paid.php") {
                    echo 'class="active btn btn-success"';
                }else   echo 'class="btn btn-primary"';?>>
                    <a href="/admin_paid.php">Paid</a></li>


                <li role="settings" <?php if (strtolower($_SERVER["PHP_SELF"]) == "/admin_settings.php") {
                    echo 'class="active btn btn-success "';
                }else   echo 'class="btn btn-primary"';?>>
                    <a href="/admin_settings.php">settings</a></li>
            </ul>
</div>
        </div>
    </div>
<?php } ?>

