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

// https redirect
    if (strpos($_SERVER['HTTP_HOST'], "www.") !== false) {
        $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $redirect_url = str_replace("www.", "", $redirect_url);
        header("Location: $redirect_url");
        exit();
    }
    if (!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') {
        $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $redirect_url = str_replace("www.", "", $redirect_url);
        header("Location: $redirect_url");
        exit();
    }

    /**
     * Copyright (c) 2016. TheWolf
     */
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('X-Frame-Options: GOFORIT');
    header_remove("X-Frame-Options");

    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/macros/mc_globals.php");
    // load the basic settings as created by the install/setup
    require_once($_SERVER['DOCUMENT_ROOT'] . '/_ini.php');
// load the core classes
    require_once($_SERVER['DOCUMENT_ROOT'] . '/core/db.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . "/core/functions.php");

    require_once($_SERVER['DOCUMENT_ROOT'] . '/3rdparty/x4-crypto-charts/x4-crypto-charts.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/3rdparty/x4-crypto-tables/x4-crypto-tables.php');

// cookies -----------------------------------------------------------------------------------------------------------------------------
// search filters

    //session_set_cookie_params(COOKIE_EXPIRATION);// sessions last 1 hour*24*30=1 month
    //ini_set('session.gc_maxlifetime', COOKIE_EXPIRATION);
    //ini_set('session.gc_probability', 1);
    //ini_set('session.gc_divisor', 1);
    date_default_timezone_set("Asia/Bangkok");
    session_start();

    $now = time();
    if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
        // this session has worn out its welcome; kill it and start a brand new one
        session_unset();
        session_destroy();
        session_start();
    }

// either new or old, it should live at most for another hour
    $_SESSION['discard_after'] = $now + SESSION_TIMEOUT;

//ob_start("ob_gzhandler"); // enable compression

    if (!is_writable(session_save_path())) {
        die('Session path "' . session_save_path() . '" is not writable for PHP!');
    }

    error_reporting(E_ERROR);
    setlocale(LC_ALL, 'en_US.UTF8');
    ini_set("log_errors", 1);
    ini_set("error_log", $_SERVER['DOCUMENT_ROOT'] . "/tmpdata/php-error.log");

    $time_start = microtime(true);

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(RandomToken());
    }

    require_once($_SERVER['DOCUMENT_ROOT'] . '/core/loader.php');
    $loader = new Loader();
    $loader->togglebuttons = true;

    require_once($_SERVER['DOCUMENT_ROOT'] . '/core/settings.php');

    $settings = new Settings();
    require_once($_SERVER['DOCUMENT_ROOT'] . '/3rdparty/passwordhash.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/core/user.php');
    $user = new User();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/3rdparty/sitemap.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/3rdparty/checkbot.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/3rdparty/htmlparser.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/3rdparty/phpmailer/PHPMailerAutoload.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/googletranslate.php");
// Geolocalization stuff
    include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/geoip.inc");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/geoipcity.inc");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/geoipregionvars.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/timezone.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/int_helper.php");

    $dir = $_SERVER['DOCUMENT_ROOT'];
    $f = $dir . '/themes/' . $theme->name . '/' . $url;
    $f1 = $dir . '/themes/default/' . $url;
    $settings->script = $url;

//if ($user->is_admin) {echo "search=-".$settings->search_compatible_pdf." - ".$_REQUEST['search_compatible_pdf'];exit;}

    $settings->language = getSession("language");
    $l = ["fr", "us", "po", "th", "th", "sp", "ru", "jp", "cn"];

    if ($settings->language == "") {
        $settings->language = $_REQUEST['language'];
    }
    if (isset($_REQUEST['language']) && in_array($_REQUEST['language'], $l)) {
        $settings->language = $_REQUEST['language'];
        setSession("language", $_REQUEST['language']);
    }
    if ($settings->language == "") {
        $lang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        switch ($lang) {
            case "fr":
                $settings->language = "fr";
                break;
            case "en":
                $settings->language = "us";
                break;
            case "po":
                $settings->language = "po";
                break;
            case "th":
                $settings->language = "th";
                break;
            case "sp":
                $settings->language = "sp";
                break;
            case "ru":
                $settings->language = "ru";
                break;
            case "ja":
                $settings->language = "jp";
                break;
            case "zh":
            case "zh-Hans":
            case "zh-Hant":
                $settings->language = "cn";
                break;
            default:
                $settings->language = "us";
                break;
        }

        setSession("language", $settings->language);
    }

    if (is_32bit()) {
        die("This is not a 64 bits version of PHP. Use x64 non thread safe version to run this script.");
    }

    set_error_handler("errorHandler");
    register_shutdown_function("shutdownHandler");

    if ($settings->renovation == 1 && ($settings->adminip <> $_SERVER['REMOTE_ADDR'])) {
        $user->logout();
        header("Location: /renovation/index.html");
        exit;
    }

// initialise some global settings
    $user->ip = $_SERVER['REMOTE_ADDR'];
    $db = new Database();

    if (isset($_REQUEST["success"])) $settings->success = xdb_decrypt($_REQUEST["success"]);
    if (isset($_REQUEST["warning"])) $settings->warning = xdb_decrypt($_REQUEST["warning"]);
    if (isset($_REQUEST["alert"])) $settings->alert = xdb_decrypt($_REQUEST["alert"]);
    if (isset($_REQUEST["info"])) $settings->info = xdb_decrypt($_REQUEST["info"]);
    if (isset($_REQUEST["error"])) $settings->error = xdb_decrypt($_REQUEST["error"]);

    $settings->sessionid = $_COOKIE["sessionid"];


    if (!function_exists('getallheaders')) {
        function getallheaders()
        {
            $headers = '';
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
    }

// protect against some attacker / scappers
    foreach (getallheaders() as $name => $value) {
        $h = $name . $value;
        if (strpos($h, "sqlmap")) {
            exit;
        }
        if (strpos($h, " . rima - tde . net")) {
            exit;
        }
    }

// clean headers
//foreach ($_REQUEST as $key => $value) $_REQUEST[$key] = sanitize($value);
//foreach ($_GET as $key => $value) $_GET[$key] = sanitize($value);
//foreach ($_POST as $key => $value) $_POST[$key] = sanitize($value);

    $search_button = true;
    $invite_code = true;

// storage path management
    $upload_path = $settings->upload_path;
    $download_path = $settings->download_path;
    $tmp_path = $settings->tmp_path;
    $download_url = $settings->download_url;
    $ftp_server = $settings->ftp_server;
    $ftp_user_name = $settings->ftp_user_name;
    $ftp_user_pass = $settings->ftp_user_pass;
    $settings->twitter = true;
    $user->ip = $_SERVER['REMOTE_ADDR'];
    $settings->servername = $_SERVER['SERVER_NAME'];

    $nonindex = false;

    if (isset($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
    } else {
        $id = 0;
    }
    if (isset($name) && strlen($name) > 1) {
        $_SESSION['name'] = $name;

    } else {
        if (isset($_SESSION['name'])) {
            $name = $_SESSION['name'];
        } else {
            $name = "";
        }
    }

    $sort = $_COOKIE["sort"];
    if ($sort == "") {
        $sort = " Date desc ";
    }
    if ($settings->qsection == COMPATIBLE_MOVIE_ID) {
        $sort = "name asc";
    }

    if ($settings->defcon >= 1 && $user->rank < $settings->defcon) {

        header("Location: /index.php");
        exit;

    }
    if (isset($_SERVER['HTTP_REFERER'])) {
        $ref = $_SERVER['HTTP_REFERER'];
    } else {
        $ref = "";
    }
    $settings->referer = $ref;

    if ($settings->org == "Googlebot") {
        $settings->googlebot = true;
    }

    if ($user->is_locked) {
        header('Location: 403login.php?message=' . $settings->comment);
        exit;

    }

    $url = "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $settings->host = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $settings->theme_css_topbar = $user->css;

    if (headers_sent()) {
        die("Headers allready sent");

    }

//if ($user->id==1) echo "memory user= ".getFileSize(memory_get_usage())."<Br>";
 //   session_write_close();
?>