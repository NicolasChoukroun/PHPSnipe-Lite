<?php
/*
 *  Copyright (c) 2013-2020. Nicolas Choukroun.
 *  Copyright (c) 2013-2020. The PHPSnipe Developers.
 *  This program is free software; you can redistribute it and/or modify it
 *  under the terms of the Attribution 4.0 International License as published by the
 *  Creative Commons Corporation; either version 2 of the License, or (at your option)
 *  any later version.  See COPYING for more details.
 *
 * **************************************************************************** */
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html class="ie8" lang="en-US" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"> <![endif]-->
<!--[if IE 9]>
<html class="ie9" lang="en-US" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"> <![endif]-->
<!--[if (gt IE 9)|!(IE)] lang="en-US" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"><![endif]-->
<html lang="en-US" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
    <head>
        <meta charset="utf-8"/>
        <meta name=viewport content="width=device-width, initial-scale=1.0"/>
        <!-- feeds & pingback -->
        <link rel="profile" href="https://gmpg.org/xfn/11"/>

        <?php
        if ($settings->default_title <> "")
            $settings->default_title = _DEFAULT_TITLE_;
        if ($settings->default_url <> "")
            $settings->default_title = _DEFAULT_URL_;
        if ($settings->default_sitename <> "")
            $settings->default_sitename = _DEFAULT_SITENAME_;
        if ($settings->default_author <> "")
            $settings->default_author = _DEFAULT_AUTHOR_;
        if ($settings->default_description <> "")
            $settings->default_description = _DEFAULT_DESCRIPTION_;
        if ($settings->default_keywords <> "")
            $settings->default_keywords = _DEFAULT_KEYWORDS_;
        ?>

        <title><?php echo $settings->default_title; ?></title>
        <?php // general META ?>
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <meta name="author" content="<?php echo $settings->default_author; ?>">
        <meta name="description" content="<?php echo $settings->description; ?>">
        <?php // Facebook META ?>
        <meta property="og:site_name" content="<?php echo $settings->default_sitename; ?>"/>
        <meta property="og:url" content="<?php echo $settings->default_url; ?>"/>
        <meta property="og:title" content="<?php echo $settings->default_url; ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:image" content="<?php echo $settings->default_url; ?>/img/main.jpg"/>
        <meta property="og:image:width" content="2048"/>
        <meta property="og:image:height" content="1152"/>

        <?php // Twitter META ?>
        <meta name="twitter:card" content="summary"/>
        <meta name="twitter:site" content="<?php echo $settings->default_url; ?>"/>
        <meta name="twitter:creator" content="<?php echo $settings->default_author; ?>"/>
        <meta name="twitter:url" content="<?php echo $settings->default_url; ?>"/>
        <meta name="twitter:title" content="<?php echo $settings->default_title; ?>"/>
        <meta name="twitter:image" content="<?php echo $settings->default_url; ?>/img/main.jpg"/>
        <meta name="twitter:description" content="<?php echo $settings->default_description; ?>"/>
        <meta name="twitter:keywords" content="<?php echo $settings->default_keywords; ?>"/>
        
        <?php // Snipe core css ?>
         <!-- Latest compiled and minified CSS -->
        <link href="/core/css/theme-blue.min.css" rel="stylesheet" type="text/css" media="all"/>
        <link href="/core/css/theme-extras.min.css" rel="stylesheet" type="text/css" media="all"/>
        <link href="/core/css/custom.min.css" rel="stylesheet" type="text/css" media="all"/>
        
        <?php // 3rd party css ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="/css/socicon.min.css" rel="stylesheet" type="text/css" media="all">
        <link href="/css/et-line.min.css" rel="stylesheet" type="text/css" media="all"/>
        <link href="/css/ionicons.min.css" rel="stylesheet" type="text/css" media="all"/>                             
        <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"  rel="stylesheet" type="text/css" media="all">
        <link href="/css/flexslider.min.css" rel="stylesheet" type="text/css" media="all"/>
        
	<link rel="apple-touch-icon" sizes="57x57" href="/core/img/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/core/img/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/core/img/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/core/img/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/core/img/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/core/img/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/core/img/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/core/img/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/core/img/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/core/img/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/core/img/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/core/img/favicon-16x16.png">
	<link rel="manifest" href="/core/img/manifest.json">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/core/img/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">

        <script src="/core/js/jquery-2.1.4.min.js"></script>
        <script src="/core/js/blockui.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
        <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
        <script src="/js/poppers.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="/js/flexslider.min.js"></script>
   
        <script src="/js/marquee.js"></script>

        <link href="https://fonts.googleapis.com/css?family=Titillium+Web:100,300,400,600,700" rel="stylesheet" type="text/css">
        <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,300|Source+Sans+Pro:400,300,600,400italic' rel='stylesheet' type='text/css'>
        <link href="https://fonts.googleapis.com/css?family=Fira+Sans&display=swap" rel="stylesheet">


    </head>

    <body class="gradient--active">
        <div class="nav-container" style="z-index:110;">
            <nav>
                <div class="nav-bar bg--white">
                    <div class="text-center-xs col-md-3">
                        <div class="nav-module logo-module">
                            <a href="/index.php"><img class="logo" alt="Logo" src="/core/img/logo.png" ></a>
                        </div>
                    </div>


                    <div class="nav-module menu-module col-md-6 col-sm-6 col-xs-12 text-right text-right-xs">
                        <ul class="manu nav navbar-nav navbar-right" style="margin-top:20px;">
                            <li>
                                <a href="/blog" target="_self"><?php echo _t("Blog"); ?></a>
                            </li>
                            <li>
                                <a href="/buy" target="_blanc"><?php echo _t("Buy"); ?></a>
                            </li>

                            <li >
                                <a href="/downloads"  ><?php echo _t("Downloads "); ?></a>
                            </li>
                            <li class="dropdown">
                                <a href="/vote" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo _t("Options "); ?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/option1.php" target="_self"><?php echo _t("Option 1"); ?> </a></li>
                                    <li><a href="/option2.php" target="_self"><?php echo _t("Option 2"); ?> </a></li>
                                    <li><a href="/option3.php" target="_self"><?php echo _t("Option 3"); ?> </a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo _t("Help "); ?><span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/option1.php" target="_self"><?php echo _t("Option 1"); ?> </a></li>
                                    <li class="divider"></li>
                                    <li><a href="/option2.php" target="_self"><?php echo _t("Option 2"); ?> </a></li>
                                    <li><a href="/option3.php" target="_self"><?php echo _t("Option 3"); ?> </a></li>                        

                                </ul>
                            </li>

                            <?php /* <li>
                              <a href="/index.php?#contact" target="_self"><?php echo _t("Contact"); ?></a>
                              </li> */ ?>
                            <?php if ($user->is_logged) { ?>
                                <li>
                                    <a href="/profile.php" target="_self"><?php echo _t("Profile"); ?></a>
                                </li>

                            <?php } ?>

                        </ul>
                    </div>
                    <?php
                    $s = basename($_SERVER["SCRIPT_FILENAME"], '.php') . ".php";
                    $t = _t("Please Wait...");
                    ?>

                    <div class="col-md-3 text-right text-center-xs " style="padding-right:150px;">
                        <div class="nav-module">
                            <div class="btn-group">

                                <button type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style=" z-index:100;border:none;">
                                    <img  style="border:none;margin-top:0;padding-right:5px;width:60px!important;height:50px!important;resize: none;" src="/img/<?php echo $settings->language; ?>.png">
                                </button>
                                <ul class="dropdown-menu" style="background-color:transparent;border-color:transparent;box-shadow: none;">

                                    <?php if ($settings->language <> 'fr') { ?>
                                        <a class="btn btn-default <?php
                                        if ($settings->language <> 'fr') {
                                            echo ' btn--filled ';
                                        }
                                        ?>" href="<?php echo $s; ?>?language=fr"
                                           onclick=" $.blockUI({ message: '<h1><?php echo $t; ?></h1>' }); document.body.st                                                    yle.cursor = 'wait'; return tru                                                                                e;">
                                            <img width="48" height="48" src="/img/fr.png">
                                        </a>
                                    <?php } ?><?php if ($settings->language <> 'us') { ?>
                                        <a class="btn btn-default <?php
                                        if ($settings->language <> 'us') {
                                            echo ' btn--filled ';
                                        }
                                        ?>" href="<?php echo $s; ?>?language=us"
                                           onclick=" $.blockUI({ message: '<h1><?php echo $t; ?></h1>' }); document.body.st                                                    yle.cursor = 'wait'; return tru                                                                                e;">
                                            <img width="48" height="48" src="/img/us.png">
                                        </a>
                                    <?php } ?><?php if ($settings->language <> 'cn') { ?>
                                        <a class="btn btn-default <?php
                                        if ($settings->language <> 'cn') {
                                            echo ' btn--filled ';
                                        }
                                        ?>" href="<?php echo $s; ?>?language=cn"
                                           onclick=" $.blockUI({ message: '<h1><?php echo $t; ?></h1>' }); document.body.st                                                    yle.cursor = 'wait'; return tru                                                                                e;">
                                            <img width="48" height="48" src="/img/cn.png">
                                        </a>
                                    <?php } ?><?php if ($settings->language <> 'ru') { ?>
                                        <a class="btn btn-default <?php
                                        if ($settings->language <> 'ru') {
                                            echo ' btn--filled ';
                                        }
                                        ?>" href="<?php echo $s; ?>?language=ru"
                                           onclick=" $.blockUI({ message: '<h1><?php echo $t; ?></h1>' }; document.body.st                                                    yle.cursor = 'wait'; return tru                                                                                e;">
                                            <img width="48" height="48" src="/img/ru.png">
                                        </a>
                                    <?php } ?><?php if ($settings->language <> 'jp') { ?>
                                        <a class="btn btn-default <?php
                                        if ($settings->language <> 'jp') {
                                            echo ' btn--filled ';
                                        }
                                        ?>" href="<?php echo $s; ?>?language=jp"
                                           onclick=" $.blockUI({ message: '<h1><?php echo $t; ?></h1>' }); document.body.st                                                    yle.cursor = 'wait'; return tru                                                                                e;">
                                            <img width="48" height="48" src="/img/jp.png">
                                        </a>
                                    <?php } ?><?php if ($settings->language <> 'sp') { ?>
                                        <a class="btn btn-default <?php
                                        if ($settings->language <> 'sp') {
                                            echo ' btn--filled ';
                                        }
                                        ?>" href="<?php echo $s; ?>?language=sp"
                                           onclick=" $.blockUI(); document.body.st                                                    yle.cursor = 'wait'; return tru                                                                                e;">
                                            <img width="48" height="48" src="/img/sp.png">
                                        </a>
                                    <?php } ?><?php if ($settings->language <> 'po') { ?>
                                        <a class="btn btn-default <?php
                                        if ($settings->language <> 'po') {
                                            echo ' btn--filled ';
                                        }
                                        ?>" href="<?php echo $s; ?>?language=po"
                                           onclick=" $.blockUI({ message: '<h1><?php echo $t; ?></h1>' }); document.body.st                                                    yle.cursor = 'wait'; return tru                                                                                e;">
                                            <img width="48" height="48" src="/img/po.png">
                                        </a>
                                    <?php } ?><?php if ($settings->language <> 'th') { ?>
                                        <a class="btn btn-default <?php
                                        if ($settings->language <> 'th') {
                                            echo ' btn--filled ';
                                        }
                                        ?>" href="<?php echo $s; ?>?language=th"
                                           onclick=" $.blockUI({ message: '<h1><?php echo $t; ?></h1>' }); document.body.st                                                    yle.cursor = 'wait'; return tru                                                                                e;">
                                            <img width="48" height="48" src="/img/th.png">
                                        </a>
                                    <?php } ?>
                                </ul>

                            </div>
                            <div class="hidden-xs hidden-sm hidden-md">
                                <?php if ($user->is_logged) { ?>
                                    <a href="/logout.php" class="btn btn--black btn--unfilled push-right" style="height:50px;position:absolute;top:25px;margin-right:5px;margin-left:5px;">
                                        <span class="btn__text"><?php echo _t("Logout"); ?></span>
                                        <i class="ion-arrow-right-c"></i>
                                    </a>

                                    <? } else { ?>
                                    <a href="/login.php" class="btn btn--black btn--unfilled push-right" style="height:50px;position:absolute;top:25px;margin-right:5px;margin-left:5px;">
                                        <span class="btn__text"><?php echo _t("Login"); ?></span>
                                        <i class="ion-arrow-right-c"></i>
                                    </a>
                                <?php } ?>
                                <!--<a class="btn btn--sm btn--white btn--unfilled" href="#">
                                <span class="btn__text">Login to your account</span>
                    
                                <i class="ion-bag"></i>
                                </a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>


        <?php
        require_once($_SERVER['DOCUMENT_ROOT'] . "/core/macros/mc_alerts.php");
        ?>

        <?php
        require_once("_menu_admin.php");
        ?>





