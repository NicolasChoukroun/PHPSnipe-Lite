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


/**
 * Display the file size in format Gb/Mb/Byte
 *     * @param $filesize : is the size to display in bytes
 */
function displayFileSize($filesize) {

    if (is_numeric($filesize)) {
        $decr = 1024;
        $step = 0;
        $prefix = ['Byte', 'KB', 'MB', 'GB', 'TB', 'PB'];

        while (($filesize / $decr) > 0.9) {
            $filesize = $filesize / $decr;
            $step++;
        }
        echo round($filesize, 2) . ' ' . $prefix[$step];
    } else {

        echo '0 Byte';
    }
}

/**
 * Get the file size in format Gb/Mb/Byte\
 *
 * @param $filesize : is the size to display in bytes
 *
 * @return: the text to display
 */
function getFileSize($filesize) {

    if (is_numeric($filesize)) {
        $decr = 1024;
        $step = 0;
        $prefix = ['Byte', 'KB', 'MB', 'GB', 'TB', 'PB'];

        while (($filesize / $decr) > 0.9) {
            $filesize = $filesize / $decr;
            $step++;
        }
        return round($filesize, 2) . ' ' . $prefix[$step];
    } else {

        return '0';
    }
}

/**
 * Add a rating to a product
 *
 * @param $product : product to rate
 * @param $user : userid of who is rating
 * @param $rateval : the rating (1-5)
 */
function addRating($product, $user, $rateval) {

    // rating
    $ser = $_SERVER['HTTP_HOST'];
    $ref = $_SERVER['HTTP_REFERER'];
    $host = parse_url($ref);

    $dat = date('y-m-d');

    $dbr1 = new Database();

    $sql = "select product from ratings where  userid='$user' AND product=$product";
    $dbr1->query($sql);
    $num_rows1 = $dbr1->numRows();

    $sql = "select * from ratings where product=$product";
    $dbr1->query($sql);
    $num_rows2 = $dbr1->numRows();

    if ($num_rows1 <= 1 && $ser == $host[host]) {
        $sql = "insert into ratings values(NULL,'$user','$product','$dat','$rateval')";
        $dbr1->query($sql);
    } else {
        $sql = "UPDATE ratings SET rateval='" . $rateval . "', dat='" . $dat . "' WHERE product=" . $product . " AND userid='" . $user . "'";
        $dbr1->query($sql);
    }

    echo "#final" . $product . "#";
    echo ($num_rows2 + 1) . "#";
    echo "<div id=final$product>";
    echo '<div id="rate2" class="rating hint--top" data-hint="Give a rating to this product">';
    for ($i = 1; $i <= 5; $i++) {
        if ($rateval >= 1) {
            echo "<div id='" . $i . "' class='star1'>	<a id='" . $i . "' '>" . $i .
            "</a></div>";
            $rateval = $rateval - 1;
        } else {
            if ($rateval <= 0) {
                echo "<div id='" . $i . "' class='star'>   <a id='" . $i . "' '>" . $i . "</a></div>";
            }
        }
    }
    echo "</div></div><br>";

    $sql = "SELECT avg(rateval) AS average FROM ratings WHERE product=" . $product . " GROUP BY product";
    $dbr1->query($sql);
    $dbr1->singleRecord();
    $average = round($dbr1->Record['average']);

    $sql = "update products set rating=" . $average . " where id=$product";
    $dbr1->query($sql);
}

/**
 * Clear the download file cache
 *
 * @param path : path where the cache is (must be write enabled)
 * @param cachetime : time in ms until a file in the cache is deleted
 * @param v : verbose resulto
 */
function cache($path, $cachetime, $v = false) {
    Global $settings, $user;

    if (time() - $settings->tmpcachecleaned > $cachetime) {
        $handle = opendir($path);
        $entry = readdir($handle);
        $nbrtotal = 0;
        while ($entry) {
            if (!strpos($entry, "..") && strlen($entry) > 3) {
                $rep[$nbrtotal] = $entry;
            }
            $entry = readdir($handle);
            $nbrtotal++;
        }
        $df = 0;
        $de = 0;
        for ($i = 0; $i < $nbrtotal; $i++) {
            $v = $path . "/" . $rep[$i];
            if (time() - filemtime($v) > $cachetime) {
                //if ($user->is_admin) {
                //	echo "<br>".realpath($v);
                //}
                chmod($v, 0777);
                $df += filesize($v);
                $de++;
                $r = unlink($v);
                if ($r) {
                    $r = "true";
                } else {
                    $r = "false";
                }

                /* if ($user->is_admin) {
                  echo "---> r=" . $r;

                  if (is_writable($v)) $w = "true"; else $w = "false";
                  if (is_writable($v)) echo "--------> writable=" . $w;
                  } */
            }
        }
        $settings->tmpcachecleaned = time();
        if ($v == true) {
            if (intval($df / 1024 / 1024) > 1024) {
                $df = intval($df / 1024 / 1024 / 1024) . " Gb";
            } else {
                $df = intval($df / 1024 / 1024) . " Mb";
            }
            //echo "<br>You have deleted $de files using this size $df<br><br>";
        }
    } else {
        if ($v == true) {
            echo "<br>Cannot clean not, waiting when ready: " . time() - $settings->tmpcachecleaned . " seconds<br>";
        }
    }
    return $de;
}

/*
 * download a file from an URL to a local space
 * @param url: url from where to grab the file
 * @param path: local path where to save the file
 */

function downloadFile($url, $path) {
    $newfname = $path;
    $newf = "";
    $file = fopen($url, "rb");
    if ($file) {
        $newf = fopen($newfname, "wb");

        if ($newf) {
            while (!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }
    if ($file) {
        fclose($file);
    }

    if ($newf) {
        fclose($newf);
    }
}

/*
 * check if an IP matches its CIDR representation
 * @CIDR: filter to match
 * @ip: ip to compare
 */

function netMatch($CIDR, $ip) {
    list($net, $mask) = explode('/', $CIDR);
    return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) == ip2long($net);
}

function cidr2broadcast($network, $cidr) {
    $broadcast = long2ip(ip2long($network) + pow(2, (32 - $cidr)) - 1);
    return $broadcast;
}

/*
 * check if an IP is a proxy server
 */

function checkProxy() {
    $proxy = "";
    $check = "";
    //return false;
    // Check for Proper Encoding
    $proxy = ($_SERVER['HTTP_ACCEPT_ENCODING'] != 'gzip, deflate') ? true : false;

    // Check for Connection and Cache
    if (empty($_SERVER['HTTP_CONNECTION'])) {
        // A Proxy or VPN Has Been Detected
        // $check = ($proxy === true) ? 'proxy' : 'vpn';
        return true;
    }
    return false;
    // return $check;
}

/*
 * shuffle a list of words for SEO optimization
 */

function seoShuffle(&$items, $string) {
    mt_srand(strlen($string));
    for ($i = count($items) - 1; $i > 0; $i--) {
        $j = @mt_rand(0, $i);
        $tmp = $items[$i];
        $items[$i] = $items[$j];
        $items[$j] = $tmp;
    }
}

/*
 * remove the links from a string
 * @str: string to clean
 */

function removeLinks($str) {
    $regex = '/<a (.*)<\/a>/isU';
    preg_match_all($regex, $str, $result);
    foreach ($result[0] as $rs) {
        $regex = '/<a (.*)>(.*)<\/a>/isU';
        $text = preg_replace($regex, '$2', $rs);
        $str = str_replace($rs, $text, $str);
    }
    return $str;
}

/*
 * check if the connection is corrently secured with SSL
 */

function is_ssl() {
    if (isset($_SERVER['HTTPS'])) {
        if ('on' == strtolower($_SERVER['HTTPS'])) {
            return true;
        }
        if ('1' == $_SERVER['HTTPS']) {
            return true;
        }
    } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
        return true;
    }
    return false;
}

function url_origin($s, $use_forwarded_host = false) {
    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $port = $s['SERVER_PORT'];
    $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
    $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset
                    ($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
    $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host;
}

function full_url($s, $use_forwarded_host = false) {
    return url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
}

/*
 * helper to include all the php at location
 * @folder: folder where all the php files are located like c:/php/folder/
 */

function include_all_php($folder) {

    $folder = preg_replace('/(\*|\?|\[)/', '[$1]', $folder) . "/*.php";
    foreach (glob($folder) as $filename) {
        $filename = str_replace("//", "/", $filename);
        include_once($filename);
    }
}

/*
 * Helper to include a themed file
 * First the file will be looked into its theme folder
 * then if not found will be loaded from the default theme
 * @s: path to the file to be loaded in a local path format (c:/)
 */

function require_theme($s) {
    global $settings;
    $f = $_SERVER['DOCUMENT_ROOT'] . '/themes/' . $settings->theme . '/' . $s;
    $f1 = $_SERVER['DOCUMENT_ROOT'] . '/themes/default/' . $s;

    if (file_exists($f)) {
        return ($f);
    } else {
        if (file_exists($f1)) {

            return ($f1);
        } else {
            die("include file not found: " . $f1);
        }
    }
}

/*
 * Helper to include a themed file from its URL
 * First the file will be looked into its theme folder
 * then if not found will be loaded from the default theme
 * @s: path to the file to be loaded in a URL path format (https://)
 */

function require_theme_url($s) {
    global $settings;
    $f = $_SERVER['DOCUMENT_ROOT'] . '/themes/' . $settings->theme . '/' . $s;
    $f1 = $_SERVER['DOCUMENT_ROOT'] . '/themes/default/' . $s;

    $fu = '/themes/' . $settings->theme . '/' . $s;
    $f1u = '/themes/default/' . $s;

    if (file_exists($f)) {
        return ($fu);
    } else {
        if (file_exists($f1)) {
            return ($f1u);
        } else {
            die("include file not found: " . $f1u);
        }
    }
}

function include_theme($s) {
    global $settings;
    $f = $_SERVER['DOCUMENT_ROOT'] . '/themes/' . $settings->theme . '/' . $s;
    $f1 = $_SERVER['DOCUMENT_ROOT'] . '/themes/default/' . $s;

    if (file_exists($f)) {
        return ($f);
    } else {
        if (file_exists($f1)) {
            return ($f1);
        } else {
            die("include file not found: " . $f1);
        }
    }
}

function theme_file($s) {
    global $settings;
    $f = '/themes/' . $settings->theme . '/' . $s;
    if (file_exists($f)) {
        return $_SERVER['DOCUMENT_ROOT'] . '\\themes\\' . $settings->theme . '\\' . $s;
    } else {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '\\themes\\default\\' . $s)) {
            return $_SERVER['DOCUMENT_ROOT'] . '\\themes\\default\\' . $s;
        } else {
            return $s;
        }
    }
}

function theme_url($s) {
    global $settings;
    $f = '/themes' . $settings->theme . '/' . $s;
    if (file_exists($f)) {
        return '/themes/' . $settings->theme . '/' . $s;
    } else {
        if (file_exists('/themes/default' . $s)) {
            return '/themes/default' . $s;
        } else {
            return $s;
        }
    }
}

function quote_path($path) {
    $split_path = preg_split('/[\\\\\/]/', $path);
    for ($i = 0; $i < count($split_path); $i++) {
        if (preg_match('/ /', $split_path[$i])) {
            $split_path[$i] = '"' . $split_path[$i] . '"';
        }
    }
    return implode('/', $split_path);
}

function qcat($t) {
    return abs(crc32(strtolower($t)));
}

function ucfstring($string) {
    $s = explode("<br />", $string);
    $new_string = "";
    foreach ($s as $key => $s1) {
        $new_string .= ' <br /> ' . ucfirst(trim($s1));
    }

    // $sentences = preg_split('/([.?!]+)/', $new_string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    // $new_string = '';
    // foreach ($sentences as $key => $sentence) {
    //     $new_string2 .= ($key & 1) == 0? ucfirst(strtolower(trim($sentence))) : $sentence.' ';
    // }
    //$new_string4=ucfirst($new_string3);
    //$new_string5=str_replace(".",".<br>",$new_string4);
    return trim($new_string);
}

function ftp_mksubdirs($ftpcon, $ftpath) {
    $parts = explode('/', $ftpath); // 2013/06/11/username
    foreach ($parts as $part) {
        if (!@ftp_chdir($ftpcon, $part)) {
            ftp_mkdir($ftpcon, $part); //echo "mkdir $part <br>";
            ftp_chdir($ftpcon, $part); //echo "chdir $part <br>";
            ftp_chmod($ftpcon, 0777, $part); //echo "chmod 777 $part <br>";
        }
    }
}

function printipinfo($ip) {

    $giorg = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIPOrg.dat", GEOIP_STANDARD);
    $giisp = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIPISP.dat", GEOIP_STANDARD);

    print "<b>org =" . geoip_org_by_addr($giorg, $ip) . "</b><br>";
    print "<b>isp =" . geoip_org_by_addr($giisp, $ip) . "</b><br>";

    geoip_close($giorg);
    geoip_close($giisp);
}

function png2jpg($originalFile, $outputFile, $quality) {
    $image = imagecreatefrompng($originalFile);
    imagejpeg($image, $outputFile, $quality);
    imagedestroy($image);
}

function glob_recursive($pattern, $flags = 0) {
    $files = glob($pattern, $flags);

    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }

    return $files;
}

function RemoveEmptySubFolders($path) {
    $empty = true;
    foreach (glob($path . DIRECTORY_SEPARATOR . "*") as $file) {
        $empty &= is_dir($file) && RemoveEmptySubFolders($file);
    }
    return $empty && rmdir($path);
}

function encrypt($s) {
    //return xdb_decrypt($s);
    $secretHash = ENCRYPTION_KEY;
    return openssl_encrypt($s, 'AES-256-CBC', $secretHash, 0, '1234567890123456');
}

function decrypt($s) {
    //return xdb_encrypt($s);
    //echo "decrypt=".$s;
    $secretHash = ENCRYPTION_KEY;
    return openssl_decrypt($s, 'AES-256-CBC', $secretHash, 0, '1234567890123456');
}

//------------------------------------------------------
// Encryption and Decryption routines
//
// For security, all the login and password and encrypted in the database
// I'm using a custom algorithm so there is no way to find out how
// it works having just the encrypted result
//
// author Nikko 23/06/2007

function xdb_encrypt($txt) {
    $CRYPT_KEY = "[#asdkjasdlplp#$%MASDFDFI#]";
    if (!$txt && $txt != "0") {
        return false;
        exit;
    }

    if (!$CRYPT_KEY) {
        return false;
        exit;
    }

    $kv = keyvalue($CRYPT_KEY);
    $estr = "";
    $enc = "";
    for ($i = 0; $i < strlen($txt); $i++) {
        $e = ord(substr($txt, $i, 1));
        $e = $e + $kv[1];
        $e = $e * $kv[2];
        (double) microtime() * 1000000;
        $rstr = chr(72);
        $estr .= "$rstr$e";
    }
    return base64_encode($estr);
}

function xdb_decrypt($txt) {
    $CRYPT_KEY = "[#asdkjasdlplp#$%MASDFDFI#]";
    if (!$txt && $txt != "0") {
        return false;
        exit;
    }

    if (!$CRYPT_KEY) {
        return false;
        exit;
    }

    $kv = keyvalue($CRYPT_KEY);
    $estr = "";
    $tmp = "";
    $txt = base64_decode($txt);
    for ($i = 0; $i < strlen($txt); $i++) {
        if (ord(substr($txt, $i, 1)) > 64 && ord(substr($txt, $i, 1)) < 91) {
            if ($tmp != "") {
                $tmp = $tmp / $kv[2];
                $tmp = $tmp - $kv[1];
                $estr .= chr($tmp);
                $tmp = "";
            }
        } else {
            $tmp .= substr($txt, $i, 1);
        }
    }
    $tmp = $tmp / $kv[2];
    $tmp = $tmp - $kv[1];
    $estr .= chr($tmp);
    $estr = str_replace("///", "/", $estr);
    $estr = str_replace("//", "/", $estr);
    $estr = str_replace("//", "/", $estr);
    $estr = str_replace(".//", "./", $estr);
    return $estr;
}

function keyvalue($CRYPT_KEY) {
    $keyvalue = "";
    $keyvalue[1] = "0";
    $keyvalue[2] = "0";
    for ($i = 1; $i < strlen($CRYPT_KEY); $i++) {
        $curchr = ord(substr($CRYPT_KEY, $i, 1));
        $keyvalue[1] = $keyvalue[1] + $curchr;
        $keyvalue[2] = strlen($CRYPT_KEY);
    }
    return $keyvalue;
}

function curlPost($url, $post) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // in real life you should use something like:
    // curl_setopt($ch, CURLOPT_POSTFIELDS,
    //          http_build_query(array('postvar1' => 'value1')));
    // receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close($ch);
    return $server_output;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * outputImage()
 *
 * @return
 */
function outputImage($source, $w = 200) {
    global $settings;
    if (strpos($source, "noimage") > 0) {
        return $source;
    }
    $watermark = dirname(__FILE__) . "/img/watermark1.png";

    //$path = $_SERVER['DOCUMENT_ROOT'] . "/img/products/" . basename($source);
    //$r = str_replace(dirname(__FILE__), "", $dest);
    //$source=str_replace("//","/",$source);
    //if ($settings->cdn<>"") $thumb = $settings->cdn."/thumb.php?src=" . $source . "&w=$w&zc=0";
    //else $thumb = "/thumb.php?src=" . $source . "&w=$w&zc=0";
    //echo "cdn=".$settings->cdn;exit;
    return $source;
    //}
}

function logs($action, $productid, $id2 = 0, $exclu = 0, $version = "1.0") {
    global $settings, $user;
    if ($user->id == 1) {
        return;
    } // do not log admin
    // Get IP address
    if (($ip = $_SERVER['REMOTE_ADDR']) == '') {
        $ip = "REMOTE_ADDR_UNKNOWN";
    }

    $l = new Database();
    $thetime = time();
    if ($productid > 0 && $productid != "") {
        $sql = "SELECT * FROM products WHERE id=" . $productid;
        $l->query($sql);
        //$nbr = $l->numRows();
        $l->singleRecord();
        $size = $l->Record['size'];
        $userid = $l->Record['userid'];
    } else {
        $size = 0;
    }
    if ($id2 == 0) {
        $id2 = $user->id;
    }

    if ($productid == 0 || $productid == "") {
        $sql = "INSERT INTO action_logs (actionid,ip,size,userid,date,userid2,version) VALUES('$action','$ip',$size,$id2,$thetime,$user->id,'$version')";
    } else {
        if ($exclu == 0) {
            $exclu = "0";
        }
        $sql = "INSERT INTO action_logs (actionid, ip,productid,size,userid,date,exclu,userid2,version) VALUES('$action','$ip',$productid,$size,$id2,$thetime,'$exclu',$userid,'$version')";
    }

    $l->query($sql);
    $l->close();
}

function notify($action, $from, $to, $someid = 0, $text = "no comment") {
    //global $settings;
    if ($text == "") {
        $text = "no comment";
    }
    if ($someid == 0) {
        $someid = "0";
    }
    if ($to == 0) {
        $to = "0";
    }
    $thetime = time();
    $db = new Database();
    $sql = "INSERT INTO notifications (actionid,fromid,toid,someid,datecreated,text,seen) VALUES(" . sql_val($action) . "," . sql_val($from) . "," . sql_val($to) . "," . sql_val($someid) . "," . sql_val($thetime) . ",'" . htmlspecialchars($text) . "',0)";
    $db->query($sql);
}

function transaction($from, $to, $creditfile = 0, $creditbandwidth = 0, $creditexclu = 0, $reason = "no reason given") {
    //global $settings;

    $reason = strip_tags($reason);
    $creditfile = ($creditfile ?: "0");
    $creditbandwidth = ($creditbandwidth ?: "0");
    $creditexclu = ($creditexclu ?: "0");
    if ($reason == "") {
        $reason = "no reason given";
    }
    $thetime = time();
    $db = new Database();
    $sql = "INSERT INTO transactions(fromid,toid,creditfile,creditbandwidth,creditexclu,reason,datecreated) VALUES('$from','$to','$creditfile','$creditbandwidth','$creditexclu','$reason','$thetime')";
    $db->query($sql);
    $db->close();
}

function loginvite($uid, $invitecode) {
    // Get IP address
    if (($ip = $_SERVER['REMOTE_ADDR']) == '') {
        $ip = "REMOTE_ADDR_UNKNOWN";
    }
    $code = hex2bin($invitecode);

    $ar = explode("|", $code);
    $nick = $ar[0];

    $t2 = $ar[1];
    $d = time();
    $l = new Database();
    if ($code != "") {
        $sql = "INSERT INTO loginvite (userid, date,ip,invitecode) VALUES($uid,$d,'$ip','$code')";
        $l->query($sql);
    }
    $l->close();
}

function checkExclu($productid) {
    // memoize
    static $cache;
    $key = md5(serialize(func_get_args()));
    if (!$cache[$key]) {

        $dbx = new Database();
        if ($productid == 0 || $productid == "") {
            $result = false;
        } else {
            $sql = "SELECT exclu FROM products WHERE  id=" . $productid;
            $dbx->query($sql);
            $dbx->singleRecord();
            $ok = $dbx->Record['exclu'];
            if ($ok > 0) {
                $result = true;
            } else {
                $result = false;
            }
        }
        $dbx->close();
        $cache[$key] = $result;
    }

    return $cache[$key];
}

function checkReward($productid) {
    global $settings;
    // memoize
    static $cache;
    $key = md5(serialize(func_get_args()));
    if (!$cache[$key]) {

        $dbx = new Database();
        if ($productid == 0 || $productid == "") {
            $result = false;
        } else {
            $sql = "SELECT rewardwinid FROM products WHERE  id=" . $productid;
            $dbx->query($sql);
            $dbx->singleRecord();
            $ok = $dbx->Record['rewardwinid'];
            if ($ok > 0) {
                $result = true;
            } else {
                $result = false;
            }
        }
        $dbx->close();
        $cache[$key] = $result;
    }
    return $cache[$key];
}

function checkHidden($productid) {
    // memoize
    static $cache;
    $key = md5(serialize(func_get_args()));
    if (!$cache[$key]) {
        $dbx = new Database();
        if ($productid == 0 || $productid == "") {
            $result = false;
        } else {
            $sql = "SELECT hide FROM products WHERE  id=" . $productid;
            $dbx->query($sql);
            $dbx->singleRecord();
            $ok = $dbx->Record['hide'];
            if ($ok > 0) {
                $result = true;
            } else {
                $result = false;
            }
        }
        $dbx->close();
        $cache[$key] = $result;
    }

    return $cache[$key];
}

function checkProductOwner($userid, $productid) {
    // memoize
    static $cache;
    $key = md5(serialize(func_get_args()));
    if (!$cache[$key]) {
        $dbx = new Database();
        if ($productid == 0 || $productid == "") {
            $result = false;
        } else {
            $sql = "SELECT userid FROM products WHERE userid=" . $userid . " AND id=" . $productid;

            $dbx->query($sql);
            $ok = $dbx->numRows();
            if ($ok == 0) {
                $result = false;
            } else {
                $result = true;
            }
        }
        $dbx->close();
        $cache[$key] = $result;
    }

    return $cache[$key];
}

function getProductOwner($productid) {
    // memoize
    static $cache_getProductOwner;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getProductOwner[$key]) {

        $dbx = new Database();
        if ($productid == 0 || $productid == "") {
            $result = false;
        } else {
            $sql = "SELECT userid from products where  id=$productid";

            $dbx->query($sql);
            $dbx->singleRecord();

            $result = $dbx->Record['userid'];
        }
        $dbx->close();
        $cache_getProductOwner[$key] = $result;
    }

    return $cache_getProductOwner[$key];
}

function updateCategories() {
    $db = new Database();
    $s_db = new Database();
    $c_db = new Database();
    $sc_db = new Database();
    $sc_db2 = new Database();
    $sc_db3 = new Database();

    // get all sections
    $sql = "SELECT DISTINCT section FROM products WHERE deleted IS NULL";
    $s_db->query($sql . " order by section asc ");

    $sql = "SELECT DISTINCT category FROM products WHERE deleted IS NULL";
    $c_db->query($sql . " order by category asc ");

    $sql = "SELECT DISTINCT subcategory FROM products WHERE deleted IS NULL AND LOWER(subcategory) NOT LIKE LOWER('%.zip%')";
    $sc_db->query($sql . " order by subcategory asc ");

    $sql = "SELECT DISTINCT subcategory2 FROM products WHERE deleted IS NULL AND LOWER(subcategory2) NOT LIKE LOWER('%.zip%') ";
    $sc_db2->query($sql . " order by subcategory2 asc ");

    $sql = "SELECT DISTINCT subcategory3 FROM products WHERE deleted IS NULL AND LOWER(subcategory3) NOT LIKE LOWER('%.zip%') ";
    $sc_db3->query($sql . " order by subcategory3 asc ");

    $sql = "TRUNCATE TABLE section";
    $db->query($sql);
    $sql = "ALTER TABLE section AUTO_INCREMENT = 1";
    $db->query($sql);
    $sql = "TRUNCATE TABLE category";
    $db->query($sql);
    $sql = "ALTER TABLE category AUTO_INCREMENT = 1";
    $db->query($sql);
    $sql = "TRUNCATE TABLE subcategory";
    $db->query($sql);
    $sql = "ALTER TABLE subcategory AUTO_INCREMENT = 1";
    $db->query($sql);
    $sql = "TRUNCATE TABLE subcategory2";
    $db->query($sql);
    $sql = "ALTER TABLE subcategory2 AUTO_INCREMENT = 1";
    $db->query($sql);
    $sql = "TRUNCATE TABLE subcategory3";
    $db->query($sql);
    $sql = "ALTER TABLE subcategory3 AUTO_INCREMENT = 1";
    $db->query($sql);

    // subcategory3
    while ($sc_db3->nextRecord()) {

        $name = $sc_db3->Record['subcategory3'];
        if (strlen($name) > 2) {
            $sql = "INSERT INTO subcategory3 SET name='" . $name . "'";
            $db->query($sql);
            $id = $db->lastId();
            $sql = "UPDATE products SET qsubcategory3=" . $id .
                    " WHERE LOWER(subcategory3)=LOWER('" . $name . "')";
            $db->query($sql);
        }
    }

    // subcategory2
    while ($sc_db2->nextRecord()) {

        $name = $sc_db2->Record['subcategory2'];
        if (strlen($name) > 2) {
            $sql = "INSERT INTO subcategory2 SET name='" . $name . "'";
            $db->query($sql);
            $id = $db->lastId();
            $sql = "UPDATE products SET qsubcategory2=" . $id .
                    " WHERE LOWER(subcategory2)=LOWER('" . $name . "')";
            $db->query($sql);
        }
    }

    // subcategory
    while ($sc_db->nextRecord()) {

        $name = $sc_db->Record['subcategory'];
        if (strlen($name) > 2) {
            $sql = "INSERT INTO subcategory SET name='" . $name . "'";
            $db->query($sql);
            $id = $db->lastId();
            $sql = "UPDATE products SET qsubcategory=" . $id .
                    " WHERE LOWER(subcategory)=LOWER('" . $name . "')";
            $db->query($sql);
        }
    }

    // category
    while ($c_db->nextRecord()) {

        $name = $c_db->Record['category'];
        if (strlen($name) > 2) {
            $sql = "INSERT INTO category SET name='" . $name . "'";
            $db->query($sql);
            $id = $db->lastId();
            $sql = "UPDATE products SET qcategory=" . $id . " WHERE LOWER(category)=LOWER('" .
                    $name . "')";
            $db->query($sql);
        }
    }
    // sections
    while ($s_db->nextRecord()) {

        $name = $s_db->Record['section'];
        if (strlen($name) > 2) {
            $sql = "INSERT INTO section SET name='" . $name . "'";
            $db->query($sql);
            $id = $db->lastId();
            $sql = "UPDATE products SET qsection=" . $id . " WHERE LOWER(section)=LOWER('" .
                    $name . "')";
            $db->query($sql);
        }
    }
}

function seoUrl($string) {
    //Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
    $string = strtolower($string);
    $string = str_replace("-", "_", $string);
    $string = preg_replace('/[^A-Za-z0-9_-]+/', '-', $string);
    $string = str_replace(" ", "-", $string);
    $string = str_replace("--", "-", $string);
    $string = str_replace("--", "-", $string);
    $string = str_replace("--", "-", $string);

    $string = preg_replace('#[0-9 ]*#', '', $string);
    return substr($string, 0, 64);
}

function makeLinks($text) {
    //$text = html_entity_decode($text);
    $text = str_replace(" ", " ", $text);
    $text = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $text);

    //$text=str_replace("http:","https:",$text);
    $text = str_replace(" ;", " ", $text);
    $text = str_replace("<</a>", "</a>", $text);
    $text = str_replace('/"br />', "<br/>", $text);
    //$text=str_replace("https:","http:",$text);
    //$text=str_replace("www.",":",$text);
    return $text . " ";
}

function makeLinks2($content) {
    // The link list
    $links = [];

    // Links out of text links
    preg_match_all('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()#\!0-9@:%_+.~?&;//=]+)!i', $content, $matches);
    foreach ($matches[0] as $key => $link) {
        $links[$link] = $link;
    }

    // Get existing
    preg_match_all('/<a\s[^>]*href=([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU', $content, $matches);
    foreach ($matches[2] as $key => $value) {
        if (isset($links[$value])) {
            unset($links[$value]);
        }
    }

    // Replace in content
    foreach ($links as $key => $link) {
        $content = str_replace($link, '<a href="' . $link . '" target="_blank">' . $link . '</a>', $content);
    }

    return $content;
}

function getUrlFromId($id, $ext = "view", $db = null) {

    if ($id == 0 || $id == "") {
        return "";
    }

    $result = "/product_view.php?id=" . $id;
    return $result;

    // memoize
    static $cache_getUrlFromID;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getUrlFromID[$key]) {

        if ($db == null) {
            $db = new Database;
        }
        $sql = "SELECT * FROM products WHERE id=" . $id;
        $db->query($sql);
        $db->singleRecord();
        $name = trim($db->Record['name']);
        $section = trim($db->Record['section']);
        $dmca = trim($db->Record['dmca']);
        $category = trim($db->Record['category']);
        $subcategory = trim($db->Record['subcategory']);
        $subcategory2 = trim($db->Record['subcategory2']);
        $subcategory3 = trim($db->Record['subcategory3']);
        $idx = $db->Record['id'];
        if (strlen($section) > 1 && $section <> "none") {
            $section .= "/";
        } else {
            $section = "";
        }
        if (strlen($category) > 1 && $category <> "none") {
            $category .= "/";
        } else {
            $category = "";
        }
        if (strlen($subcategory) > 1 && $subcategory <> "none") {
            $subcategory .= "/";
        } else {
            $subcategory = "";
        }
        if (strlen($subcategory2) > 1 && $subcategory2 <> "none") {
            $subcategory2 .= "/";
        } else {
            $subcategory2 = "";
        }
        if (strlen($subcategory3) > 1 && $subcategory3 <> "none") {
            $subcategory3 .= "/";
        } else {
            $subcategory3 = "";
            //if ($dmca == "" && $idx > 0) {
            //	$dmca = generateRandomString(4);
            //	$sql = "UPDATE products SET dmca='" . $dmca . "' WHERE id=" . $idx;
            //	$db->query($sql);
        }
        $r = generateRandomString(4);
        $url = $section . $category . $subcategory . $subcategory2 . $subcategory3 . "/" . seoUrl($name) . "/" . $dmca .
                "/" . $idx;
        $url = str_replace("//", "/", $url);
        $url = str_replace(" ", "", $url);
        $url = str_replace("-", "", $url);
        $url = str_replace(".", "", $url);
        $url = str_replace("--", "-", $url);
        $url = str_replace("--", "-", $url);
        $url = str_replace("--", "-", $url);
        $url = str_replace("#", "_", $url);

        $url = str_replace("_", "", $url);
        $result = "/" . $ext . "/" . $url . ".html";
        $result = str_replace("//", "/_/", $result);

        $cache_getUrlFromID[$key] = $result;
    }

    return $cache_getUrlFromID[$key];
}

// get seo url
function getSU($id, $ext = "view") {
    $result = "/product_view.php?id=" . $id;
    return $result;

    if ($id == 0 || $id == "") {
        return "";
    }
    $db = new Database;
    $sql = "SELECT * FROM products WHERE id=" . $id;
    $db->query($sql);
    $db->singleRecord();
    $name = trim($db->Record['name']);
    $section = trim($db->Record['section']);
    $dmca = trim($db->Record['dmca']);
    $category = trim($db->Record['category']);
    $subcategory = trim($db->Record['subcategory']);
    $subcategory2 = trim($db->Record['subcategory2']);
    $subcategory3 = trim($db->Record['subcategory3']);

    $idx = $db->Record['id'];
    if (strlen($section) > 1 && $section <> "none") {
        $section .= "/";
    } else {
        $section = "";
    }
    if (strlen($category) > 1 && $category <> "none") {
        $category .= "/";
    } else {
        $category = "";
    }
    if (strlen($subcategory) > 1 && $subcategory <> "none") {
        $subcategory .= "/";
    } else {
        $subcategory = "";
    }
    if (strlen($subcategory2) > 1 && $subcategory2 <> "none") {
        $subcategory2 .= "/";
    } else {
        $subcategory2 = "";
    }
    if (strlen($subcategory3) > 1 && $subcategory3 <> "none") {
        $subcategory3 .= "/";
    } else {
        $subcategory3 = "";
    }
    //if ($dmca=="" && $idx>0)
    //{
    //$dmca = generateRandomString(8);
    //}
    $r = generateRandomString(8);
    $url = $section . $category . $subcategory . $subcategory2 . $subcategory3 . "/" . seoUrl($name) . "/" . $dmca .
            "/" . $idx;
    $url = str_replace("//", "/", $url);
    $url = str_replace(" ", "", $url);
    $url = str_replace("-", "", $url);
    $url = str_replace(".", "", $url);
    $url = str_replace("--", "-", $url);
    $url = str_replace("--", "-", $url);
    $url = str_replace("--", "-", $url);
    $url = str_replace("#", "_", $url);

    $url = str_replace("_", "", $url);
    //return "/" . $ext . "/" . $url . ".html?s=" . encrypt(time());
    $result = "/" . $ext . "/" . $url . ".html?s=1";
    $result = str_replace("//", "/_/", $result);
    return $result;
}

function sanitize($str) {

    $search = [
        '@<script[^>]*?>.*?</script>@si', // Strip out javascript
        '@<![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments
    ];
    $str = preg_replace($search, '', $str);

    // return $str;
    // --- array syntax based Full Path Disclosures --- //
    // if( is_array($str) ) $str = $str[0];
    // --- NULL byte injections --- //
    $str = str_replace(chr(0), '', $str);

    $str = str_replace('"', '', $str);
    $str = str_replace("'", '', $str);

    // --- XSS injections --- //
    // $str = strip_tags($str);
    //$str = htmlentities($str, ENT_QUOTES);
    // --- reduce risk of HTTP Reponse Splitting --- //
    //$str = nl2br($str);
    // Strip out all bbcode
    // $str = preg_replace('/\[(.*?)\](.*?)\[\/?(.*?)\]/iu', '\\2&#8242;', $str);
    // Matche one or more spaces and replaces it with a single space
    //$str = preg_replace('/( )+/u', ' ', trim($str));
    // SQL injection
    //$str=mysqli_escape_string($con_glob);
    if (strpos(strtolower($str), ".php")) {
        $str = "";
    }

    if (strpos(strtolower($str), "shema")) {
        $str = "";
    }
    if (strpos(strtolower($str), "concat")) {
        $str = "";
    }
    if (strpos(strtolower($str), "cmd=")) {
        $str = "";
    }
    if (strlen($str) > 12000) {
        $str = "";
    }
    return $str;
}

function in_ip_range($ip_one, $ip_two = false) {
    if ($ip_two === false) {
        if ($ip_one == $_SERVER['REMOTE_ADDR']) {
            $ip = true;
        } else {
            $ip = false;
        }
    } else {
        if (ip2long($ip_one) <= ip2long($_SERVER['REMOTE_ADDR']) && ip2long($ip_two) >= ip2long($_SERVER['REMOTE_ADDR'])
        ) {
            $ip = true;
        } else {
            $ip = false;
        }
    }
    return $ip;
}

function sanitizeFileName($string, $force_lowercase = true, $anal = false) {
    $strip = [
        "~",
        "`",
        "!",
        "@",
        "#",
        "$",
        "%",
        "^",
        "&",
        "&reg;",
        "*",
        "_",
        "=",
        "+",
        "{",
        "®",
        "}",
        "\\",
        "|",
        ";",
        ":",
        "\"",
        "'",
        "&#8216;",
        "&#8217;",
        "&#8220;",
        "&#8221;",
        "&#8211;",
        "&#8212;",
        "â€�?",
        "â€“",
        ",",
        "<",
        ".",
        ">",
        "/",
        "?"
    ];
    $ext = strtolower(pathinfo($string, PATHINFO_EXTENSION));
    $string = basename($string);
    $string = str_replace($ext, "", $string);
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    //$clean = preg_replace('/\s+/', "", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
    if ($force_lowercase) {
        strtolower($clean);
    }
    $clean = str_replace("[AKD]", "", $clean);
    return $clean . "." . $ext;
}

function sanitizeFileNameLight($string, $force_lowercase = true, $anal = false) {
    $strip = [
        "~",
        "`",
        "!",
        "@",
        "#",
        "$",
        "%",
        "^",
        "&reg;",
        "&",
        "*",
        "_",
        "=",
        "®",
        "+",
        "{",
        "}",
        "\\",
        "|",
        ";",
        ":",
        "\"",
        "'",
        "&#8216;",
        "&#8217;",
        "&#8220;",
        "&#8221;",
        "&#8211;",
        "&#8212;",
        "â€�?",
        "â€“",
        ",",
        "<",
        ">",
        "/",
        "?"
    ];

    $clean = trim(str_replace($strip, "", strip_tags($string)));
    return $clean;
}

function clean($input, $type = "", $no_tags = "") {
    $input = str_replace('< b>', "", $input);
    $input = str_replace('< a>', "", $input);
    $input = str_replace('< strong>', "", $input);
    $input = str_replace('< br>', "", $input);
    $input = str_replace('"', "`", $input);
    $input = str_replace("'", "`", $input);
    $input = str_replace("\\r", "<br>", $input);
    $input = str_replace("r&#10;", "<br>", $input);

    $input = str_replace("\\", "", $input);
    $input = str_replace("®", "", $input);
    $input = str_replace("&nbsp", " ", $input);

    $input = str_replace("\n", "&#10;", $input);

    $search = ["\\", "\x00", "\n", "\r", "'", '"', "\x1a"];
    $replace = ["\\\\", "\\0", "\\n", "<br>", "\\'", '\\"', "\\Z"];
    $input = str_replace($search, $replace, $input);

    if ($no_tags != "") {
        $input = trim(strip_tags($input, "<br>"), "<br>");
    }
    if ($type != "") {
        if (strlen(strstr($type, "(")) > 0) {
            $split = explode("(", $type);
            $type = $split['0'];
            $limit = str_replace(")", "", $split['1']);

            if (($type == "int") && (!is_int($input))) {
                $input = (int) substr($input, 0, $limit);
            } else {
                $input = substr($input, 0, $limit);
            }
        }
    }
    if (get_magic_quotes_gpc()) {
        $input = stripslashes($input);
    }
    $input = addslashes($input);
    return $input;
}

function getLibraryUrl($p = "section") {
    global $settings;
    $s = "<a href='/library.php?qsection=";
    $r = "";
    switch ($p) {
        case "section":
            $r = $s . $settings->qsection;
            break;
        case "category":
            $r = $s . $settings->qsection . "&qcategory=" . $settings->qcategory;
            break;
        case "subcategory":
            $r = $s . $settings->qsection . "&qcategory=" . $settings->qcategory . "&qsubcategory=" . $settings->qsubcategory;
            break;
        case "subcategory2":
            $r = $s . $settings->qsection . "&qcategory=" . $settings->qcategory . "&qsubcategory=" . $settings->qsubcategory . "&qsubcategory2=" . $settings->qsubcategory2;
            break;
        case "subcategory3":
            $r = $s . $settings->qsection . "&qcategory=" . $settings->qcategory . "&qsubcategory=" . $settings->qsubcategory .
                    "&qsubcategory2=" . $settings->qsubcategory2 . "&qsubcategory3=" . $settings->qsubcategory3;
            break;
    }
    $r .= "'>";
    return $r;
}

function getSearchKeywords($url = '') {
    // Get the referrer
    $referrer = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
    $referrer = (!empty($url)) ? $url : $referrer;
    if (empty($referrer)) {
        return false;
    }

    // Parse the referrer URL
    $parsed_url = parse_url($referrer);
    if (empty($parsed_url['host'])) {
        return false;
    }
    $host = $parsed_url['host'];
    $query_str = (!empty($parsed_url['query'])) ? $parsed_url['query'] : '';
    $query_str = (empty($query_str) && !empty($parsed_url['fragment'])) ? $parsed_url['fragment'] :
            $query_str;
    if (empty($query_str)) {
        return false;
    }

    // Parse the query string into a query array
    parse_str($query_str, $query);

    // Check some major search engines to get the correct query var
    $search_engines = [
        'q' => 'alltheweb|aol|ask|ask|bing|google',
        'p' => 'yahoo',
        'wd' => 'baidu'
    ];
    foreach ($search_engines as $query_var => $se) {
        $se = trim($se);
        preg_match('/(' . $se . ')\./', $host, $matches);
        if (!empty($matches[1]) && !empty($query[$query_var])) {
            return $query[$query_var];
        }
    }
    return false;
}

function isSpider() {
    // Add as many spiders you want in this array
    $spiders = [
        'Googlebot',
        'Yammybot',
        'Openbot',
        'Yahoo',
        'Slurp',
        'msnbot',
        'ia_archiver',
        'Lycos',
        'Scooter',
        'AltaVista',
        'Teoma',
        'Gigabot',
        'Googlebot-Mobile'
    ];

    // Loop through each spider and check if it appears in
    // the User Agent
    foreach ($spiders as $spider) {
        //preg_match('/pattern/i', $string, $matches)
        if (eregi($spider, $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
    }
    return false;
}

function blockUsers($ipAddresses) {
    $userOctets = explode('.', $_SERVER['REMOTE_ADDR']); // get the client's IP address and split it by the period character
    $userOctetsCount = count($userOctets); // Number of octets we found, should always be four

    $block = false; // boolean that says whether or not we should block this user

    foreach ($ipAddresses as $ipAddress) { // iterate through the list of IP addresses
        $octets = explode('.', $ipAddress);
        if (count($octets) != $userOctetsCount) {
            continue;
        }

        for ($i = 0; $i < $userOctetsCount; $i++) {
            if ($userOctets[$i] == $octets[$i] || $octets[$i] == '*') {
                continue;
            } else {
                break;
            }
        }

        if ($i == $userOctetsCount) { // if we looked at every single octet and there is a match, we should block the user
            $block = true;
            break;
        }
    }

    return $block;
}

/**
 * getIsCrawler()
 *
 * @return
 */
function getIsCrawler($userAgent) {
    $crawlers = 'Google|msnbot|Rambler|Yahoo|AbachoBOT|accoona|' .
            'AcioRobot|ASPSeek|CocoCrawler|Dumbot|FAST-WebCrawler|' .
            'GeonaBot|Gigabot|Lycos|MSRBOT|Scooter|AltaVista|IDBot|eStyle|Scrubby';
    $isCrawler = (preg_match("/$crawlers/", $userAgent) > 0);
    return $isCrawler;
}

/**
 * getEmail()
 *
 * @return
 */
function getEmail($id) {
    $r = ' <a href="/messages.php?action=new&destid=' . encrypt($id) .
            '"><i class="splashy-mail_light_new_2" ></i></a>';
    return $r;
}

/**
 * getAvatarFromId()
 *
 * @return
 */
function getAvatarFromId($user_id, $size = 200) {
    global $settings;

    if ($user_id <= 0) {
        return;
    }
    $db = new Database();
    $sql = "SELECT email,avatar FROM users WHERE id=" . $user_id;
    $db->query($sql);
    $db->singleRecord();
    $email = $db->Record['email'];
    $avatar = $db->Record['avatar'];
    if ($avatar == "") {

        $default = urlencode($settings->cdn . "/img/img-no-avatar.gif");
        $db->close();
        if ($avatar == "" || strlen($avatar) < 10 || !urlExists($avatar)) {

            $grav_url = get_gravatar($email, 200, $default);
            return $grav_url;
        } else {
            if (!urlExists($avatar)) {
                return $default;
            }
            return $avatar;
        }
    } else {
        return $avatar;
    }
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole  $img True to return a complete IMG tag False for just the URL
 * @param array  $atts Optional, additional key/value attributes to include in the IMG tag
 *
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */

/**
 * get_gravatar()
 *
 * @return
 */
function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = []) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
    if ($img) {
        $url = '<img src="' . $url . '"';
        foreach ($atts as $key => $val) {
            $url .= ' ' . $key . '="' . $val . '"';
        }
        $url .= ' />';
    }
    return $url;
}

/**
 * urlExists()
 *
 * @return
 */
function urlExists($url) {
    // Version 4.x supported
    $handle = curl_init($url);
    if (false === $handle) {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true); // this works
    curl_setopt($handle, CURLOPT_HTTPHEADER, ["User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15"]); // request as if Firefox
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);
    return $connectable;
}

/**
 * displayRank()
 *
 * @return
 */
function displayRank($i, $w = 60, $table = 0, $css = "", $hint = "") {
    global $settings;

    if ($i == 0) {
        $i = "0";
    }

    if ($table == 1) {
        echo "<table width=0 align=left><tr><td><img style='margin-right:10px;' width='" .
        $w . "' height='" . $w . "' align='center'   src='" . $settings->cdn . "/thumb.php?zc=1&w=" . $w . "&h=" . $w . "&src=/img/ranks/rank" . $i .
        ".jpg'></td></tr><tr><td align=center>Rank $i</td></tr></table>";
    } else {
        if ($hint == false) {
            echo "<img alt='Rank $i ' width='" . $w . "' height='" . $w . "' " . $css .
            "   style='position: relative;margin-top:-4px;margin-right:5px;'  src='" . $settings->cdn . "/thumb.php?zc=1&w=" . $w . "&h=" . $w . "&src=/img/ranks/rank" . $i .
            ".jpg'>";
        } else {
            echo "<a href='/how.php'><i class='hint--left' date=hint='Rank $i'><img style='position: relative;margin-top:-4px;margin-right:5px;' width='" .
            $w . "' height='" . $w . "' " . $css . "  src='" . $settings->cdn . "/thumb.php?zc=1&w=" . $w . "&h=" . $w . "&src=/img/ranks/rank" . $i .
            ".jpg'>/i></a>";
        }
    }
}

/**
 * returnDisplayRank()
 *
 * @return
 */
function returnDisplayRank($i, $w = 60, $table = 0, $css = "", $hint = false) {
    global $settings;
    if ($i == 0) {
        $i = "0";
    }

    if ($table == 1) {
        return "<table width=0 align=left><tr><td><img width='" . $w . "' height='" . $w . "' align=center   src='" . $settings->cdn . "/thumb.php?zc=1&w=" . $w . "&h=" . $w . "&src=/img/ranks/rank" . $i .
                ".jpg'></td></tr><tr><td align=center>Rank $i</td></tr></table>";
    } else {
        if ($hint == false) {
            return "<img width='" . $w . "' height='" . $w . "' " . $css .
                    " style='position: relative;margin-top:-4px;margin-right:2px;' src='" . $settings->cdn . "/thumb.php?zc=1&w=" . $w . "&h=" . $w . "&src=/img/ranks/rank" .
                    $i . ".jpg'>";
        } else {
            return "<a href='/how.php'><i class='hint--left' data-hint='Rank $i'><img style='position: relative;margin-bottom:5px;margin-right:2px;' width='" . $w . "' height='" . $w . "' " . $css . "  src='" . $settings->cdn . "/thumb.php?zc=1&w=" . $w . "&h=" . $w . "&src=/img/ranks/rank" . $i .
                    ".jpg'></i></a>";
        }
    }
}

function getNbrReviewComments($productid) {
    // memoize
    static $cache_getNbrReviewComments;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getNbrReviewComments[$key]) {

        $db = new Database;
        $sql = "SELECT reviewID from sr_reviews where ProductID='.$productid.'";
        $db->query($sql);
        $result = $db->nbr();
        $cache_getLevelFromId[$key] = $result;
        $db->close();
    }
    return $cache_getNbrReviewComments[$key];
}

/**
 * getLevelFromId()
 *
 * @return
 */
function getLevelFromId($id) {
    global $settings, $user;
    // memoize
    static $cache_getLevelFromId;
    $key = md5(serialize(func_get_args() . $user->is_blocked . $user->is_admin . $user->rank . $settings->rank_see_names));
    if (!$cache_getLevelFromId[$key]) {
        // error_reporting(255);
        $db = new Database;
        $sql = "SELECT id,public_nickname,is_locked,levelstatus,rank  from users WHERE id='$id'";
        $db->query($sql);
        $db->singleRecord();
        $name1 = $db->Record['public_nickname'];
        $banned = $db->Record['is_locked'];
        $rank = $db->Record['rank'];
        $levelstatus = $db->Record['levelstatus'];
        ;
        if (($user->is_admin || (!$user->is_blocked && $user->rank >= $settings->rank_see_names))) {
            $result = $levelstatus;
        } else {
            $result = "Anon";
        }
        $db->close();
        $cache_getLevelFromId[$key] = $result;
    }

    return $cache_getLevelFromId[$key];
}

/**
 * getRankFromId()
 *
 * @return
 */
function getRankFromId($id) {
    global $userid, $visitor;
    // memoize
    static $cache_getRankFromId;
    $key = md5(serialize(func_get_args() . $visitor));
    if (!$cache_getRankFromId[$key]) {
        // error_reporting(255);
        $db = new Database;
        $sql = "SELECT id,public_nickname,is_locked,levelstatus,rank from users WHERE id='$id'";
        $db->query($sql);
        //echo $sql."<br>";
        $db->singleRecord();
        $name1 = $db->Record['public_nickname'];
        $banned = $db->Record['is_locked'];
        $rank = $db->Record['rank'];
        if ($userid == 1 || (!$visitor) || $id == $userid) {
            $result = $rank;
        } else {
            $result = "Anon";
        }
        $db->close();
        $cache_getRankFromId[$key] = $result;
    }

    return $cache_getRankFromId[$key];

    //return "";
}

/**
 * getCompatibleFromId()
 *
 * @return
 */
function getCompatibleFromId($id) {
    // memoize
    static $cache_getCompatibleFromId;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getCompatibleFromId[$key]) {
        // error_reporting(255);
        $db = new Database;
        $sql = "SELECT compatible from product where id='$id'";
        $db->query($sql);

        $db->singleRecord();
        $result = $db->Record['compatible'];
        $db->close();
        $cache_getCompatibleFromId[$key] = $result;
    }

    return $cache_getCompatibleFromId[$key];
}

/**
 * getBadge()
 *
 * @return
 */
function getBadge($levelstatus) {
    global $paid, $userid, $thanks, $visitor, $free;
    $name2 = "";
    if ($levelstatus == SUPPORTER || $levelstatus == UNITY3D) {
        $name2 = "<i class='splashy-contact_grey'></i>";
    }
    if ($levelstatus == NOBRAINER || $levelstatus == NOBRAINER2 || $levelstatus ==
            NOBRAINER3
    ) {
        $name2 = "<i class='splashy-star_boxed_full'></i>";
    }
    if ($levelstatus == GOLD) {
        $name2 = "<i class='splashy-star_boxed_full'></i>";
    }
    if ($levelstatus == EDITOR) {
        $name2 = "<i class='splashy-check'></i>";
    }
    if ($levelstatus == ADMIN) {
        $name2 = "<i class='splashy-shield_chevrons'></i>";
    }
    if ($userid == 1 && !$visitor && !$free || (!$visitor && !$free)) {
        return $name2;
    } else {
        return "";
    }
    //return "";
}

/**
 * getLevelName()
 *
 * @return
 */
function getLevelName($levelstatus) {
    global $user;
    $name2 = "Unknown";
    if ($levelstatus == SUPPORTER || $levelstatus == UNITY3D) {
        $name2 = " Supporter Donator";
    }
    if ($levelstatus == NOBRAINER || $levelstatus == NOBRAINER2 || $levelstatus ==
            NOBRAINER3
    ) {
        $name2 = " No Brainer Donator";
    }
    if ($levelstatus == GOLD) {
        $name2 = " Gold Donator ";
    }
    if ($levelstatus == EDITOR) {
        $name2 = " Editor ";
    }
    if ($levelstatus == ADMIN) {
        $name2 = " Admin ";
    }
    if ($user->id == 1 || ($user->is_admin)) {
        return $name2;
    } else {
        return "";
    }
}

/**
 * getNickNameFromId()
 *
 * @return
 */
function getNickNameFromId($id) {
    global $user, $settings;

    if (($user->rank < $settings->rank_see_names) || !$user->is_logged) {
        $name1 = "";

        if (rand(1, 2) == 0) {
            $name1 = "Unity_" . rand(0, $id);
        } else {
            if (rand(1, 2) == 1) {
                $name1 = "Unity3d_" . rand(0, $id);
            } else {
                $name1 = "UnityAsset_" . rand(0, $id);
            }
        }

        //$name1.=" * 1 -".$user->rank."-".$user->is_logged."-";
        return $name1;
    }

    // memoize
    static $cache_getNickNameFromId;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getNickNameFromId[$key]) {

        $db = new Database;
        $sql = "SELECT id,public_nickname,is_locked,levelstatus,thanks,random,anonymous  from users WHERE id='$id'";
        $db->query($sql);

        $db->singleRecord();
        if ($db->rs['random'] && !$user->is_admin && !$user->is_editor) {
            $name1 = getRandomName(true);
        } else {
            $name1 = $db->rs['public_nickname'];
        }
        if ($db->rs['anonymous'] && !$user->is_admin && !$user->is_editor) {
            $name1 = "Anon";
        } else {
            $name1 = $db->rs['public_nickname'];
        }

        //$name1 = $db->Record['public_nickname'];
        $banned = $db->Record['is_locked'];
        $thanks = $db->Record['thanks'];
        $levelstatus = $db->Record['levelstatus'];

        if ($name1 == "" || ($user->rank < $settings->rank_see_names && $user->id <> $id)) {
            if (rand(1, 2) == 0) {
                $result = "Unity_" . $id;
            } else {
                if (rand(1, 2) == 1) {
                    $result = "Unity3d_" . $id;
                } else {
                    $result = "UnityAsset_" . $id;
                }
            }

            $result = $name1;
        } else {

            $result = $name1;
        }

        if ($name1 == "") {
            $name1 = "Anon_" . $id;
        }
        $name1 = ucwords($name1);
        if ($banned == 1) {
            $name1 = $name1 . "<img src='/img/banned.png'>";
            $result = $name1;
            $cache[$key] = $result;
            return $cache[$key];
        }
        // if ($levelstatus==FREE)	$name= "<i class='splashy-smiley_amused' ></i>".$name1.$thanksx;
        if ($levelstatus == FREE) {
            $name = $name1;
        }
        if ($levelstatus == SUPPORTER || $levelstatus == UNITY3D) {
            $name1 = "<i class='splashy-contact_grey'></i>" . $name1;
        }
        if ($levelstatus == NOBRAINER || $levelstatus == NOBRAINER2 || $levelstatus ==
                NOBRAINER3
        ) {
            $name1 = "<i class='splashy-star_boxed_full'></i>" . $name1;
        }
        if ($levelstatus == GOLD) {
            $name = "<i class='splashy-star_boxed_full'></i>" . $name1;
        }
        if ($levelstatus == EDITOR) {
            $name = "<i class='splashy-check'></i>" . $name1;
        }
        if ($levelstatus == ADMIN) {
            $name = "<i class='splashy-shield_chevrons'></i>" . $name1 . $thanks;
        }

        if ($user->id == 1 || $id == $user->id || $user->rank > 0) {
            $result = $name1;
        } else {
            $result = "";
        }
        //return "John Do";
        $db->close();
        //$result.= " * 2 -".$user->rank."-".$user->is_logged."-";
        $cache_getNickNameFromId[$key] = $result;
    }

    return $cache_getNickNameFromId[$key];
}

/**
 * getThanksFromId()
 *
 * @return
 */
function getThanksFromId($id) {

    static $cache_getThanksFromId;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getThanksFromId[$key]) {

        $db1 = new Database();
        $sql = "SELECT SQL_NO_CACHE count(thanks) as total from users WHERE id='$id'";
        $db1->query($sql);
        $db1->singleRecord();
        $nbrthanks = $db1->Record['total'];

        $result = $nbrthanks;
        $cache_getThanksFromId[$key] = $result;
        $db1->close();
    }

    return $cache_getThanksFromId[$key];
}

/**
 * getLoginFromId()
 *
 * @return
 */
function getLoginFromId($id) {
    global $settings, $user;
    static $cache_getLoginFromId;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getLoginFromId[$key]) {

        $db = new Database;
        $sql = "SELECT  SQL_CACHE  login from users WHERE id='$id'";
        $db->query($sql);

        $db->singleRecord();
        $name = $db->Record['login'];
        if ($user->id <> 1) {
            return "";
        }
        $result = ucwords($name);
        $cache_getLoginFromId[$key] = $result;
        $db->close();
    }

    return $cache_getLoginFromId[$key];
}

/**
 * getUserIdFromNickname()
 *
 * @return
 */
function getUserIdFromNickname($nick) {
    static $cache_getUserIdFromNickname;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getUserIdFromNickname[$key]) {
        $db = new Database;
        $sql = "SELECT  SQL_CACHE  id from users where public_nickname='$nick'";
        $db->query($sql);
        $db->singleRecord();
        $id = $db->Record['id'];
        $result = $id;
        $cache_getUserIdFromNickname[$key] = $result;
        $db->close();
    }

    return $cache_getUserIdFromNickname[$key];
}

/**
 * getUserNameFromId()
 *
 * @return
 */
function getUserNameFromId($id) {
    global $user, $settings;
    static $cache_getUserNameFromId;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getUserNameFromId[$key]) {
        $db = new Database;
        $sql = "SELECT  SQL_CACHE  firstname,lastname,random,anonymous from users WHERE id='$id'";
        $db->query($sql);

        $db->singleRecord();
        $name = $db->Record['name_f'] . " " . $db->Record['name_l'];
        if ($db->random) {
            $name = getRandomName(true);
        } else {
            $name = ucwords($db->rs['firstname'] . " " . $db->rs['lastname']);
        }
        if ($db->rs['random'] && !$user->is_admin && !$user->is_editor) {
            $name = getRandomName(true);
        } else {
            $name = $db->rs['public_nickname'];
        }
        if ($db->rs['anonymous'] && !$user->is_admin && !$user->is_editor) {
            $name = "Anon";
        } else {
            $name = $db->rs['public_nickname'];
        }

        if ($user->id == 1 || (!$user->is_logged) || $id == $user->id || $user->rank > 0) {
            $result = ucwords($name);
        } else {
            $result = "Anon";
        }
        $cache_getUserNameFromId[$key] = $result;
        $db->close();
    }

    return $cache_getUserNameFromId[$key];
}

function replace_between($str, $needle_start, $needle_end, $replacement) {
    $pos = strpos($str, $needle_start);
    $start = $pos === false ? 0 : $pos + strlen($needle_start);

    $pos = strpos($str, $needle_end, $start);
    $end = $pos === false ? strlen($str) : $pos;

    return substr_replace($str, $replacement, $start, $end - $start);
}

/**
 * getSetting()
 *
 * @return
 */
function getSetting($name) {
    static $cache_getSetting;
    $key = md5(serialize(func_get_args()));
    if (!$cache_getSetting[$key]) {
        $db = new Database();
        $sql = "SELECT value from settings where name='$name'";
        $db->query($sql) or die("error getting Setting.");
        $db->singleRecord();
        $result = $db->Record['value'];
        $cache_getSetting[$key] = $result;
        $db->close();
    }

    return $cache_getSetting[$key];
}

/**
 * setSetting()
 *
 * @return
 */
function setSetting($name, $value) {
    $db = new Database();

    $sql = "UPDATE settings SET value='$value' WHERE name='$name'";
    $db->query($sql);
    $db->close();
}

/**
 * getUserSetting()
 *
 * @return
 */
function getUserSetting($userid, $name) {
    if ($userid == 0) {
        return 0;
    }
    $db = new Database();
    $sql = "SELECT " . $name . " from users WHERE id=" . $userid;
    $db->query($sql) or die("error getting Setting.");
    $db->singleRecord();
    $r = $db->Record[$name];
    $db->close();
    return $r;
}

/**
 * setUserSetting()
 *
 * @return
 */
function setUserSetting($userid, $name, $value) {
    if ($userid == 0) {
        return;
    }
    $db = new Database();
    $sql = "UPDATE  users SET " . $name . "=" . $value . " WHERE id=" . $userid;
    $db->query($sql) or die("DB error getting Setting.");
    $db->close();
}

/**
 * displaySection()
 *
 * @return
 */
function displaySection() {
    global $movies, $poser, $unity, $emu, $vst, $showvideo, $showposer, $showunity,
    $showemu, $showvst;
    return "";
    $add = "";
    if (!$movies || $showvideo == false) {
        $add .= " and (products.ismovie=0 or products.ismovie is null)  ";
    }
    if (!$poser || $showposer == false) {
        $add .= " and (products.isposer=0 or products.isposer is null)  ";
    }
    if (!$unity || $showunity == false) {
        $add .= " and (products.isunity=0 or products.isunity is null)  ";
    }
    if (!$emu || $showemu == false) {
        $add .= " and (products.isemu=0 or products.isemu is null)  ";
    }
    if (!$vst || $showvst == false) {
        $add .= " and (products.isvst=0 or products.isvst is null)  ";
    }
    return $add;
}

/**
 * fExists()
 *
 * @return
 */
function fExists($path) {
    return (@fopen($path, "r") == true);
}

/**
 * strip_tags_content()
 *
 * @return
 */
function strip_tags_content($text, $tags = '', $invert = false) {

    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);

    if (is_array($tags) and count($tags) > 0) {
        if ($invert == false) {
            return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si',
                    '', $text);
        } else {
            return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
        }
    } elseif ($invert == false) {
        return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    }
    return $text;
}

/**
 * strip_word_html()
 *
 * @return
 */
function strip_word_html($text, $allowed_tags = "<b><i><sup><sub><em><u><br>") {
    mb_regex_encoding('UTF-8');
    //replace MS special characters first
    $search = [
        '/&lsquo;/u',
        '/&rsquo;/u',
        '/&ldquo;/u',
        '/&rdquo;/u',
        '/&mdash;/u'
    ];
    $replace = [
        '\'',
        '\'',
        '"',
        '"',
        '-'
    ];
    $text = preg_replace($search, $replace, $text);
    //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
    //in some MS headers, some html entities are encoded and some aren't
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    //try to strip out any C style comments first, since these, embedded in html comments, seem to
    //prevent strip_tags from removing html comments (MS Word introduced combination)
    if (mb_stripos($text, '/*') !== false) {
        $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
    }
    //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
    //'<1' becomes '< 1'(note: somewhat application specific)
    $text = preg_replace(['/<([0-9]+)/'], ['< $1'], $text);
    $text = strip_tags($text, $allowed_tags);
    //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
    $text = preg_replace([
        '/^\s\s+/',
        '/\s\s+$/',
        '/\s\s+/u'
            ], [
        '',
        '',
        ' '
            ], $text);
    //strip out inline css and simplify style tags
    $search = [
        '#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu',
        '#<(em|i)[^>]*>(.*?)</(em|i)>#isu',
        '#<u[^>]*>(.*?)</u>#isu'
    ];
    $replace = [
        '<b>$2</b>',
        '<i>$2</i>',
        '<u>$1</u>'
    ];
    $text = preg_replace($search, $replace, $text);
    //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
    //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
    //some MS Style Definitions - this last bit gets rid of any leftover comments */
    $num_matches = preg_match_all("/\<!--/u", $text, $matches);
    if ($num_matches) {
        $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
    }
    return $text;
}

/**
 * sendTwitter()
 *
 * @return
 */
function sendTwitter($message, $media) {
    global $error, $warning, $success, $settings;
    $er = false;
    $consumerKey = $settings->TwitterConsumerKey;
    $consumerSecret = $settings->TwitterConsumerSecret;
    $accessToken = $settings->TwitterAccessTocken;
    $accessTokenSecret = $settings->TwitterAccessTockenSecret;

    $twitter = null;
    try {
        $twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
    } catch (TwitterException $e) {
        $settings->error = 'Error Twitter API 1: ' . $e->getMessage();
        $er = true;
        //exit;
    }

    if (!$twitter->authenticate()) {
        $settings->error = 'Twitter API: Invalid name or password';
    }
    //$message="hello";
    try {
        $tweet = $twitter->send($message . " #gamedev #unity3d #ue4", $media);
    } catch (TwitterException $e) {
        $settings->error = 'Error Twitter API 2: ' . $e->getMessage();
        $er = true;
        //exit;
    }
    if (!$er) {
        $settings->success = "Your message has been sent to Twitter: " . $message;
    }
}

/**
 * sendFacebook()
 *
 * @return
 */
function sendFacebook($message, $title, $link, $image) {
    Global $settings;
    //error_reporting(255);
    ######### edit details ##########
    $appId = $settings->FacebookAppId; //Facebook App ID
    $secret = $settings->FacebookAppSecret; // Facebook App Secret
    $page_id = $settings->FacebookPageId;
    $return_url = $settings->FacebookReturnUrl; //return url (url to script)
    $homeurl = '/index.php'; //return to home
    $fbPermissions = 'publish_stream,manage_pages'; //Required facebook permissions
    ##################################

    $fb = new Facebook(['appId' => $appId, 'secret' => $secret]);
    $fbuser = $fb->getUser();

    if ($fbuser) {

        $page_access_token = "";
        $result = $fb->api("/me/accounts");

        // loop trough all your pages and find the right one
        if (!empty($result['data'])) {
            foreach ($result["data"] as $page) {
                if ($page["id"] == $page_id) {
                    $page_access_token = $page["access_token"];
                    break;
                }
            }
        } else {
            echo "AN ERROR OCCURED: could not get the access_token. Please verify the page ID " .
            $page_id . " exists.";
            exit;
        }

        // set the facebook active facebook access token as the one we just fetch
        $fb->setAccessToken($page_access_token);

        // Now try to post on page's wall
        try {
            $message = [
                'message' => $message,
                'picture' => $image,
                'description' => $title,
                'link' => $link
            ];
            $result = $fb->api('/' . $page_id . '/feed', 'POST', $message);
            if ($result) {
                echo 'Successfully posted to Facebook Wall...';
                exit;
            }
        } catch (FacebookApiException $e) {
            echo $e->getMessage();
        }
    } else {

        $fbloginurl = $fb->getLoginUrl(['redirect-uri' => $return_url, 'scope' => $fbPermissions]);
        echo '<a href="' . $fbloginurl . '">Login with Facebook</a>';
        exit;
    }
}

function cleanSessions() {

    session_unset();
    session_destroy();
}

/**
 * sql_val()
 *
 * @return
 */
function sql_val($input) {
    if (get_magic_quotes_gpc()) {
        $input = stripslashes($input);
    }
    return ("'" . $input . "'");
}

/**
 * sql_key()
 *
 * @return
 */
function sql_key($input) {
    if (get_magic_quotes_gpc()) {
        $input = stripslashes($input);
    }
    return ("`" . $input . "`");
}

/**
 * reverb()
 *
 * @return
 */
function reverb($value) {
    return htmlspecialchars(stripslashes($value));
}

/**
 * print_x()
 *
 * @return
 */
function print_x($value) {
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}

/**
 * vdump()
 *
 * @return
 */
function vdump(&$var, $var_name = null, $indent = null, $reference = null) {
    $do_dump_indent = "<span style='color:#666666;'>|</span>  ; ; ";
    $reference = $reference . $var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme';
    $keyname = 'referenced_object_name';

    // So this is always visible and always left justified and readable
    echo "<div style='text-align:left; background-color:white; font: 100% monospace; color:black;'>";

    if (is_array($var) && isset($var[$keyvar])) {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#666666'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    } else {
        $var = [$keyvar => $var, $keyname => $reference];
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        $type_color = "";
        if ($type == "String") {
            $type_color = "<span style='color:green'>";
        } elseif ($type == "Integer") {
            $type_color = "<span style='color:red'>";
        } elseif ($type == "Double") {
            $type_color = "<span style='color:#0099c5'>";
            $type = "Float";
        } elseif ($type == "Boolean") {
            $type_color = "<span style='color:#92008d'>";
        } elseif ($type == "NULL") {
            $type_color = "<span style='color:black'>";
        }

        if (is_array($avar)) {
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => " : "") .
            "<span style='color:#666666'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach ($keys as $name) {
                $value = &$avar[$name];
                vdump($value, "['$name']", $indent . $do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        } elseif (is_object($avar)) {
            echo "$indent$var_name <span style='color:#666666'>$type</span><br>$indent(<br>";
            foreach ($avar as $name => $value) {
                dump($value, "$name", $indent . $do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        } elseif (is_int($avar)) {
            echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) .
            ")</span> $type_color" . htmlentities($avar) . "</span><br>";
        } elseif (is_string($avar)) {
            echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) .
            ")</span> $type_color\"" . htmlentities($avar) . "\"</span><br>";
        } elseif (is_float($avar)) {
            echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) .
            ")</span> $type_color" . htmlentities($avar) . "</span><br>";
        } elseif (is_bool($avar)) {
            echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) .
            ")</span> $type_color" . ($avar == 1 ? "TRUE" : "FALSE") . "</span><br>";
        } elseif (is_null($avar)) {
            echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) .
            ")</span> {$type_color}NULL</span><br>";
        } else {
            echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) .
            ")</span> " . htmlentities($avar) . "<br>";
        }

        $var = $var[$keyvar];
    }

    echo "</div>";
}

/**
 * dump()
 *
 * @return
 */
function dump($value, $level = 0) {
    if ($level == -1) {
        $trans[' '] = '&there4;';
        $trans["\t"] = '&rArr;';
        $trans["\n"] = '&para;;';
        $trans["\r"] = '&lArr;';
        $trans["\0"] = '&oplus;';
        return strtr(htmlspecialchars($value), $trans);
    }
    if ($level == 0) {
        echo '<div class="row boxed"><div class="col-md-16 container" style="display:block;background-color:white;padding:20px;">';
    }
    $type = gettype($value);
    echo $type;
    if ($type == 'string') {
        echo '(' . strlen($value) . ')';
        $value = dump($value, -1);
    } elseif ($type == 'boolean') {
        $value = ($value ? 'true' : 'false');
    } elseif ($type == 'object') {
        $props = get_class_vars(get_class($value));
        echo '(' . count($props) . ') <u>' . get_class($value) . '</u>';
        foreach ($props as $key => $val) {

            echo "<br>" . str_repeat("\t", $level + 1) . $key . ' => ';
            dump($value->$key, $level + 1);
        }
        $value = '';
    } elseif ($type == 'array') {
        echo '(' . count($value) . ')';
        foreach ($value as $key => $val) {

            echo "<br>" . str_repeat(" - ", $level + 1) . dump($key, -1) . ' => ';
            dump($val, $level + 1);
        }
        $value = '<br>';
    }
    echo " <b>$value</b>";
    if ($level == 0) {
        echo '</div></div>';
    }
}

/**
 * cleanstring()
 *
 * @return
 */
function cleanString($description) {
    $description = preg_replace('/(?:<|&lt;)\/?([a-zA-Z]+) *[^<\/]*?(?:>|&gt;)/', '',
            html_entity_decode($description));
    // $description="";
    $description = str_replace("� ", "", $description);
    $description = str_replace("<br />", "", $description);
    $description = str_replace("<br/>", "", $description);
    $description = str_replace(" ", "", $description);
    $description = strip_tags($description);
    return $description;
}

/**
 * getSession()
 *
 * @return
 */
function getSession($v) {
    if (isset($_SESSION[$v])) {
        return $_SESSION[$v];
    } else {
        if (isset($_COOKIE[$v])) {
            return $_COOKIE[$v];
        }
    }
    return false;
}

/**
 * setCookies()
 *
 * @return
 */
function setCookies($s, $v) {
    $_COOKIE[$s] = $v;
    setcookie($s, $v, time() + COOKIE_EXPIRATION, "/");
}

/**
 * getSession()
 *
 * @return
 */
function getCookies($v) {
    if (isset($_SESSION[$v])) {
        return $_SESSION[$v];
    } else {
        if (isset($_COOKIE[$v])) {
            return $_COOKIE[$v];
        }
    }
    return false;
}

/**
 * setSession()
 *
 * @return
 */
function setSession($s, $v) {
    //global $user;
    //$_COOKIE[$s] = $v;
    // if ($user->is_admin) echo "<br>setsession: ".$s."-".$v;
    $_SESSION[$s] = $v;
    setcookie($s, $v, time() + (3600 * 24 * 30), "/");
}

/**
 * cleanCategory()
 *
 * @return
 */
function cleanCategory($c) {
    $c = str_replace("®", "", $c);
    $c = str_replace("&reg;", "", $c);

    $c = str_replace("%20", " ", $c);
    $c = str_replace(" & ", " and ", $c);
    if (strpos(strtolower($c), ".zip") > 1) {
        $c = "";
    }
    // $c=sanitizeFileName($c);
    $c = str_replace("/", "", $c);
    // $c = preg_replace('\//', '', $c);
    return $c;
}

/**
 * getDateFromNow()
 *
 * @return
 */
function getDateFromNow($d) {
    if ($d == 0) {
        return "Unknown";
    }
    $now = time(); // or your date as well
    $datediff = abs($now - $d);
    if ($datediff < 60) {
        "Now";
    }
    $days = floor($datediff / (60 * 60 * 24));
    $hours = floor($datediff / (60 * 60));
    if ($days == 0) {
        $r = "Today";
    } else {
        if ($days == 1) {
            $r = "1 day  ago";
        } else {
            if ($datediff > 365 * 86400) {
                $r = date("Y, D, d", $d);
            } else {
                $r = $days . " days ago";
            }
        }
    }
//
    return $r;
}

function getTime($d) {

    $days = floor($d / (60 * 60 * 24));
    $hours = floor($d / (60 * 60));
    $min = floor($d / (60));
    $sec = floor($d);
    $r = $days . " days " . $hours . " hours " . $min . " min " . $sec . " sec";
//
    return $r;
}

/**
 * zipIcon()
 *
 * @return
 */
function zipIcon() {
    ?>
    <a data-toggle="modal" data-backdrop="static" data-target="#openzipmodal" href="#openzipmodal">
        <button class="btn btn-primary"><i class="glyphicon glyphicon-zoom-in"></i> View inside the ZIP file</button>
    </a>


    <?php
}

/**
 * zipIcon()
 *
 * @return
 */
function zipIframe($path) {
    global $theme, $settings;
    $url = "_popup_zip.php";
    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $url)) {
        //$url="/_popup_zip.php";
    } else {
        die("_popup_zip.php not found.");
    }
    ?>

    <a data-fancybox data-type="iframe" data-src="<?php echo $url . '?path=' . encrypt($path); ?>">
        <button class="btn btn-primary"><i class="glyphicon glyphicon-zoom-in"></i> View inside the ZIP file</button>
    </a>

    <?php
}

/**
 * unityIcon()
 *
 * @return
 */
function unityIcon() {
    ?>
    <a data-toggle="modal" data-backdrop="static" data-target="#openunitymodal" href="#openunitymodal">
        <button class="btn btn-primary"><i class="glyphicon glyphicon-zoom-in"></i> Decode the Assetpackage file
        </button>
    </a>


    <?php
}

/**
 * getZipVersion()
 *
 * @return
 */
function getZipVersion($filename) {
    $zip = new ZipArchive;
    $idcode = abs(crc32($filename));
    if ($zip->open($filename) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('#\.(unitypackage)$#i', $entry) && strpos($entry, "_MACOSX") === false) {
                echo "<b>Unitypackage found=</b>" . $entry . "<br>";
                $fp = $zip->getStream($entry);
                if (!$fp) {
                    echo "<font color=red>failed</font><br>";
                } else {
                    $content = stream_get_contents($fp, 1024, -1);
                    $start = strpos($content, "{");
                    $content = substr($content, $start);
                    //echo $content;
                    if (preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content,
                                    $version)) {
                        
                    }
                    if (strpos($content, "version") > 0) {
                        $or = true;
                    } else {
                        $or = false;
                    }

                    if ($or) {
                        echo "<br><font color=green>This is an original from the Asset Store</font><br>";
                        ?>

                        <button class="btn btn" type="button" data-toggle="collapse"
                                data-target="#collapseExample<?php echo
                        $idcode;
                        ?>" aria-expanded="false" aria-controls="collapseExample">
                            Open Unityasset package header
                        </button>
                        <div class="collapse" id="collapseExample<?php echo $idcode; ?>">

                        <?php
                        dump($version);
                        ?>
                        </div>
                        <?php
                    } else {
                        echo "<font color=orange><b>Cannot verify the version number</b> This has been saved by the customer (not original). Can be a web rip or an old package (<2013).</font><br>";
                    }
                }
                fclose($fp);
                // break;
            }
        }
        $zip->close();
    } else {
        echo " <font color=red>ZIP archive failed: $filename</font><br>";
    }
}

/**
 * getUnityVersion()
 *
 * @return
 */
function getUnityVersion($filename) {
    $zip = new ZipArchive;
    if ($zip->open($filename) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('#\.(unitypackage)$#i', $entry)) {
                //echo "<b>Unitypackage found=</b>".$entry."<br>";
                $fp = $zip->getStream($entry);
                if (!$fp) {
                    echo "<font color=red>failed</font><br>";
                } else {
                    $content = stream_get_contents($fp, 1024, -1);
                    $start = strpos($content, "{");
                    $content = substr($content, $start);
                    // echo $content;
                    preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content, $version);
                    if (strpos($content, "version") > 0) {
                        $or = true;
                    } else {
                        $or = false;
                    }
                    if ($or) {
                        $zip->close();
                        return $version[0][6][1];
                    } else {
                        $zip->close();
                        return -1;
                    }
                }
                fclose($fp);
            }
        }
    }
    $zip->close();
    return -1;
}

/**
 * getUnityInfo()
 *
 * @return
 */
function getUnityInfo($filename) {
    $zip = new ZipArchive;
    if ($zip->open($filename) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('#\.(unitypackage)$#i', $entry)) {
                echo "<b>Unitypackage found=</b>" . $entry . "<br>";
                $fp = $zip->getStream($entry);
                if (!$fp) {
                    echo "<font color=red>failed</font><br>";
                } else {
                    $content = stream_get_contents($fp, 1024, -1);
                    $start = strpos($content, "{");
                    $content = substr($content, $start);

                    preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content, $version);
                    if (strpos($content, "version") > 0) {
                        $or = true;
                    } else {
                        $or = false;
                    }
                    if ($or) {
                        dump($version);
                        exit;
                        $v = getinfofromunitypackage("publisher", $version[0]);
                        // echo "newv=".$v."<br>";
                        $unityinfo['required_version'] = str_replace("\"", "", $version[0][getinfofromunitypackage
                                        ("unity_version", $version[0])]);
                        $unityinfo['published_date'] = str_replace("\"", "", $version[0][getinfofromunitypackage
                                        ("pubdate", $version[0])]);
                        $unityinfo['publisher'] = str_replace("\"", "", $version[0][getinfofromunitypackage
                                        ("publisher", $version[0]) + 1]);
                        $unityinfo['pid'] = str_replace("\"", "", $version[0][getinfofromunitypackage("id",
                                        $version[0])]);
                        $unityinfo['version'] = str_replace("\"", "", $version[0][getinfofromunitypackage
                                        ("version", $version[0])]);
                        $unityinfo['categories'] = str_replace("\"", "", $version[0][getinfofromunitypackage
                                        ("label", $version[0])]);
                        $unityinfo['description'] = str_replace("\"", "", $version[0][getinfofromunitypackage
                                        ("description", $version[0])]);
                        $unityinfo['id'] = str_replace("\"", "", $version[0][getinfofromunitypackage("id",
                                        $version[0])]);
                        $unityinfo['url'] = "https://www.assetstore.unity3d.com/en/#!/content/" .
                                str_replace("\"", "", $version[0][getinfofromunitypackage("id", $version[0])]);
                        $unityinfo['title'] = str_replace("\"", "", $version[0][getinfofromunitypackage
                                        ("title", $version[0])]);
                        if (strlen($unityinfo['version']) > 10) {
                            $unityinfo['version'] = str_replace("\"", "", $version[0][8]);
                        }
                        if ($unityinfo['required_version'] == "link") {
                            $unityinfo['required_version'] = "< 4.0.0";
                        }

                        return $unityinfo;
                    } else {
                        return 0;
                    }
                }
                fclose($fp);
            }
        }
    }

    $zip->close();
    return -1;
}

/**
 * remove_special()
 *
 * @return
 */
function remove_special($z) {
    $z = strtolower($z);
    $z = preg_replace('/[^a-z0-9 - ]+/', '-', $z);
    $z = str_replace(' ', '-', $z);
    return $z; //trim($z, '-');
}

/**
 * getinfofromunitypackage()
 *
 * @return
 */
function getinfofromunitypackage($info, $v) {
    foreach ($v as $key => $value) {
        $value = str_replace("\"", "", $value);
        if ($value == $info) {
            return $key + 1;
        }
    }
    return 0;
}

/**
 * getUnityAllInfo()
 *
 * @return
 */
function getUnityAllInfo($filename) {
    $zip = new ZipArchive;
    if ($zip->open($filename) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            $fp = $zip->getStream($entry);
            if (!$fp) {
                echo "<font color=red>failed</font><br>";
            } else {
                $content = stream_get_contents($fp, 1024, -1);
                $start = strpos($content, "{");
                $content = substr($content, $start);

                preg_match_all("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/is", $content,
                        $version);
                if (strpos($content, "version") > 0) {
                    $or = true;
                } else {
                    $or = false;
                }
                if ($or) {

                    return $version;
                } else {
                    return -1;
                }
            }
            fclose($fp);
        }
    }

    $zip->close();
    return -1;
}

/**
 * unzipFirst()
 *
 * @return
 */
function unzipFirst($filename, $ext) {

    $result = file_get_contents('zip://' . $filename . '#thumb.jpg');
    if ($result !== false) {
        return $result;
    }

    $f = str_replace(".zip", ".jpg", $filename);
    $result = file_get_contents('zip://' . $filename . '#' . $f);
    if ($result !== false) {
        return $result;
    }
    $zip = new ZipArchive;
    if ($zip->open($filename) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('#\.(' . $ext . ')$#i', $entry)) {

                $fp = $zip->getStream($entry);
                if (!$fp) {
                    echo "<font color=red>failed opening " . $filename . "</font><br>";
                } else {
                    $content = stream_get_contents($fp, -1, -1);
                    //header('Content-Type: image/jpeg');

                    fclose($fp);
                    $zip->close();
                    return $content;
                }
            }
        }
    } else {
        echo " <font color=red>ZIP archive failed: $filename</font><br>";
    }

    $zip->close();
    return -1;
}

/**
 * trackingZipUnity()
 *
 * @return
 */
function trackingZipUnity($filename, $ext, $idofuser) {
    ini_set('memory_limit', '4096M');

    if (filesize($filename) > 864572800) { //300 MB = 314572800 | 1GB = 1073741824 | 500Mb = 864572800
        return true;
    }

    $zip = new ZipArchive;
    if ($zip->open($filename) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            if (preg_match('#\.(' . $ext . ')$#i', $entry)) {

                $fp = $zip->getStream($entry);
                if (!$fp) {
                    echo "getstream failed. Contact the admin.";
                } else {
                    ini_set("memory_limit", "4096M");
                    $oldContents = $zip->getFromName($entry);
                    mb_internal_encoding("iso-8859-1");
                    //$pos = mb_strpos($oldContents, '{"link":{"id":') + 15;
                    $uid = sprintf("%07d", $idofuser);
                    $start = mb_substr($oldContents, 0, 1000);
                    $start = str_replace('"type":', '"hype":', $start);
                    $start = str_replace('"upload_id":', '"upload-id":', $start);
                    $start = str_replace('content', $uid, $start);
                    $end = mb_substr($oldContents, 1000, strlen($oldContents) - 1000);
                    $newContent = $start . $end;
                    //echo $end;exit;
                    //Delete the old...
                    $zip->deleteName($entry);
                    //Write the new...
                    $zip->addFromString($entry, $newContent);
                    fclose($fp);
                    $zip->close();

                    // echo "success! ".$b;
                    return true;
                }
            }
        }
    } else {
        return false;
    }
    return false;
}

/**
 * trackingZipUE4()
 *
 * @return
 */
function trackingZipUE4($filename, $idofuser) {
    ini_set('memory_limit', '4096M');

    if (filesize($filename) > 864572800) { //300 MB = 314572800 | 1GB = 1073741824 | 500Mb = 864572800
        return true;
    }
    $zip = new ZipArchive;

    if ($zip->open($filename) === true) {
        // for Unix
        //$z->addFromString($file, file_get_contents($file));
        //for windows
        $str = file_get_contents("defaultconfig.ini");
        $uid = sprintf("%07d", $idofuser);
        $str = str_replace("xxxx", $uid, $str);
        $entry = "/Config/DefaultConfig.ini";
        $zip->addFromString($entry, $str);
        $zip->close();
        //echo "debug ok";exit;
        return true;
    }

    /* 	$zip = new ZipArchive;
      if ($zip->open($filename) === true) {
      for ($i = 0; $i < $zip->numFiles; $i++) {
      $entry = $zip->getNameIndex($i);
      if (preg_match('#\.(' . $ext . ')$#i', $entry)) {

      $fp = $zip->getStream($entry);
      if (!$fp) {
      echo "getstream failed. Contact the admin.";
      } else {
      ini_set("memory_limit", "4096M");
      $oldContents = $zip->getFromName($entry);
      mb_internal_encoding("iso-8859-1");
      //$pos = mb_strpos($oldContents, '{"link":{"id":') + 15;
      $uid = sprintf("%07d", $idofuser);
      $start = mb_substr($oldContents, 0, 1000);
      $start = str_replace('"type":', '"hype":', $start);
      $start = str_replace('"upload_id":', '"upload-id":', $start);
      $start = str_replace('content', $uid, $start);
      $end = mb_substr($oldContents, 1000 , strlen($oldContents)-1000);
      $newContent = $start .  $end;
      //echo $end;exit;
      //Delete the old...
      $zip->deleteName($entry);
      //Write the new...
      $zip->addFromString($entry, $newContent);
      fclose($fp);
      $zip->close();

      // echo "success! ".$b;
      return true;

      }
      }
      }

      } else {
      return false;
      }
     */
    return false;
}

function mb_str_replace($haystack, $search, $replace, $offset = 0, $encoding = 'auto') {
    $len_sch = mb_strlen($search, $encoding);
    $len_rep = mb_strlen($replace, $encoding);

    while (($offset = mb_strpos($haystack, $search, $offset, $encoding)) !== false) {
        $haystack = mb_substr($haystack, 0, $offset, $encoding)
                . $replace
                . mb_substr($haystack, $offset + $len_sch, 1000, $encoding);
        $offset = $offset + $len_rep;
        if ($offset > mb_strlen($haystack, $encoding)) {
            break;
        }
    }
    return $haystack;
}

/**
 * checkZip()
 *
 * @return
 */
function checkZip($zipfile) {
    $nbr = 0;
    $zip = new ZipArchive;
    if ($zip->open($zipfile)) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $x = $zip->statIndex($i);
        }
        $nbr = $zip->numFiles;
        if ($nbr > 0) {
            $r = "Nbr files: " . $zip->numFiles;
        } else {
            $r = "<font color=red>Nbr files: " . $zip->numFiles .
                    "? Corrupted archive</font>";
        }
    } else {
        $r = "Cannot open Zip File";
    }
    $zip->close();
    if ($nbr > 0) {
        $r .= " Status: " . zipStatusString($zip->status);
    }
    return $r;
}

/**
 * zipStatusString()
 *
 * @return
 */
function zipStatusString($status) {
    switch ((int) $status) {
        case ZipArchive::ER_OK:
            return 'N No error in the ZIP';
        case ZipArchive::ER_MULTIDISK:
            return 'N Multi-disk zip archives not supported';
        case ZipArchive::ER_RENAME:
            return 'S Renaming temporary file failed';
        case ZipArchive::ER_CLOSE:
            return 'S Closing zip archive failed';
        case ZipArchive::ER_SEEK:
            return 'S Seek error';
        case ZipArchive::ER_READ:
            return 'S Read error';
        case ZipArchive::ER_WRITE:
            return 'S Write error';
        case ZipArchive::ER_CRC:
            return 'N CRC error';
        case ZipArchive::ER_ZIPCLOSED:
            return 'N Containing zip archive was closed';
        case ZipArchive::ER_NOENT:
            return 'N No such file';
        case ZipArchive::ER_EXISTS:
            return 'N File already exists';
        case ZipArchive::ER_OPEN:
            return 'S Can\'t open file';
        case ZipArchive::ER_TMPOPEN:
            return 'S Failure to create temporary file';
        case ZipArchive::ER_ZLIB:
            return 'Z Zlib error';
        case ZipArchive::ER_MEMORY:
            return 'N Malloc failure';
        case ZipArchive::ER_CHANGED:
            return 'N Entry has been changed';
        case ZipArchive::ER_COMPNOTSUPP:
            return 'N Compression method not supported';
        case ZipArchive::ER_EOF:
            return 'N Premature EOF';
        case ZipArchive::ER_INVAL:
            return 'N Invalid argument';
        case ZipArchive::ER_NOZIP:
            return 'N Not a zip archive';
        case ZipArchive::ER_INTERNAL:
            return 'N Internal error';
        case ZipArchive::ER_INCONS:
            return 'N Zip archive inconsistent';
        case ZipArchive::ER_REMOVE:
            return 'S Can\'t remove file';
        case ZipArchive::ER_DELETED:
            return 'N Entry has been deleted';

        default:
            return sprintf('Unknown status %s', $status);
    }
}

/**
 * displayDate()
 *
 * @return
 */
function displayDate($d) {
    $phpdate = strtotime($d);
    //return date( 'Y-m-d H:i:s', $phpdate );
    return date('Y-m-d', $phpdate);
}

// translation

/**
 * _t()
 *
 * @return
 */
function _t($text, $s = 2000, $l = "us", $convert = true) {
    //return strip_tags(clean($text),"<br><p><a><blockquote><code>");
    global $settings;
    //return $text;
    // memoize
    static $cache_t;
    $key = md5(serialize(func_get_args()) . $settings->language . md5($text));

    if (!$cache_t[$key]) {
        //echo "not in cache<br>";
        $db = new Database();
        $db1 = new Database();
        if ($settings->language <> "") {
            $l = $settings->language;
        }
        /*
          if ($l == "us") {
          $cache_t[$key] = clean($text);
          return $cache_t[$key];
          }
         */


        $hash = md5($text);

        $text = clean($text);
        $sql = "SELECT * FROM translate WHERE hash='" . $hash . "'";
        //if ($hash=="753241a1de08950661b07d385dc881a1") echo $sql."<br>";
        $db->query($sql);
        $db->single();
        $nbr = $db->nbr();
        $textus = $db->rs["us"];
        $textfr = $db->rs["fr"];
        $textru = $db->rs["ru"];
        $textsp = $db->rs["sp"];
        $textcn = $db->rs["cn"];
        $textpo = $db->rs["po"];
        $textar = $db->rs["ar"];
        $textth = $db->rs["th"];
        $textjp = $db->rs["jp"];
        if ($nbr == 0) {
            //echo "hash not found:".$hash."<br>";
            $source = 'en';
            $trans = new GoogleTranslate();
            $target = 'fr';
            $textfr = clean($trans->translate($source, $target, $text));
            //echo "<h1><font color=black>TRANS=".$textfr."</font></h1>";
            //exit;
            $target = 'ru';
            $textru = clean($trans->translate($source, $target, $text));
            $target = 'zh-CN';
            $textcn = clean($trans->translate($source, $target, $text));
            $target = 'ja';
            $textjp = clean($trans->translate($source, $target, $text));
            $target = 'es';
            $textsp = clean($trans->translate($source, $target, $text));
            $target = 'pt';
            $textpo = clean($trans->translate($source, $target, $text));
            $target = 'th';
            $textth = clean($trans->translate($source, $target, $text));
            $target = 'ar';
            $textar = clean($trans->translate($source, $target, $text));

            $sql = "INSERT INTO translate (`hash`, `base`, `us`, `fr`, `ru`, `cn`, `jp`, `sp`, `po`, `th`, `ar`) VALUES ('" . $hash . "', '" . $text . "', '" . $text . "', '" . $textfr . "', '" . $textru . "', '" . $textcn . "', '" . $textjp . "', 
			'" . $textsp . "', '" . $textpo . "', '" . $textth . "', '" . $textar . "'  )";
            $db->query($sql);
            //echo "SAVING:".$sql."<br>";
            //exit;
            //die("<h1>finished translation</h1>");
        }

        $text = $textus;

        if ($l == "fr") {
            $text = $textfr;
        }
        if ($l == "us") {
            $text = $textus;
        }
        if ($l == "ru") {
            $text = $textru;
        }
        if ($l == "cn") {
            $text = $textcn;
        }
        if ($l == "jp") {
            $text = $textjp;
        }
        if ($l == "sp") {
            $text = $textsp;
        }
        if ($l == "po") {
            $text = $textpo;
        }
        if ($l == "th") {
            $text = $textth;
        }
        if ($l == "ar") {
            $text = $textar;
        }
        if ($l != "us") {
            $text = strip_tags(clean($text), "<br><p><a><blockquote><code>");
        }
        if (strlen($text) <= 0) {
            $text = $textus;
        }
        //echo $l;exit;
        //if ($hash=="753241a1de08950661b07d385dc881a1") {echo $text;exit;}
        $db1->close();
        $db->close();
        //if ($convert) {

        $text = str_replace("`", "'", $text);
        $text = str_replace("&#8355;ranc", "<font color='#002395'><b>Kryptofranc</b></font>", $text);
        $text = str_replace("LeFranc", "<font color='#002395'><b>Kryptofranc</b></font>", $text);
        $text = str_replace("leFranc", "<font color='#002395'><b>Kryptofranc</b></font>", $text);
        $text = str_replace("KryptoFranc", "<font color='#002395'><b>KryptoFranc</b></font>", $text);
        $text = str_replace("KryptoFranc", "<font color='#002395'><b>Kryptofranc</b></font>", $text);
        $text = str_replace("Kryptofranc", "<font color='#002395'><b>Kryptofranc</b></font>", $text);
        $text = str_replace("bitCoin", "<font color='#f7931a'><b>BitCoin</b></font>", $text);
        $text = str_replace("BitCoin", "<font color='#f7931a'><b>BitCoin</b></font>", $text);
        $text = str_replace("₣ ranc", "Franc", $text);
        $text = str_replace("<br><br>", "<br>", $text);


        $text = htmlentities($text, null, 'utf-8');
        $text = str_replace("<br> <br>&#10;                               ", "<br>", $text);

        $text = str_replace("&nbsp;&nbsp;", "", $text);
        $text = str_replace("&#10;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", "", $text);
        $text = str_replace("       ", "", $text);
        $text = str_replace("&nbsp;&nbsp;", "", $text);
        $text = str_replace("&nbsp;&nbsp;", "", $text);
        $text = str_replace("&nbsp;&nbsp;", "", $text);
        $text = str_replace("&nbsp;&nbsp;", "", $text);
        $text = str_replace("&nbsp;&nbsp;", "", $text);
        $text = str_replace("&nbsp;&nbsp;", "", $text);
        $text = str_replace("&nbsp;&nbsp;", "", $text);
        $text = str_replace("<br>&#10;", "", $text);
        $text = str_replace("la piscine", "le pool", $text);
        $text = str_replace("d'exploitation", "de minage", $text);

        $text = str_replace("        ", "", $text);
        //}
        $text = html_entity_decode($text);
        $cache_t[$key] = $text;
    }

    return $cache_t[$key];
}

function get_topmost_script() {
    $backtrace = debug_backtrace(
            defined("DEBUG_BACKTRACE_IGNORE_ARGS") ? DEBUG_BACKTRACE_IGNORE_ARGS : false);
    $top_frame = array_pop($backtrace);
    return $top_frame['file'];
}

function truncate($string, $length = 100, $append = "&hellip;") {
    $string = trim($string);

    if (strlen($string) > $length) {
        $string = wordwrap($string, $length);
        $string = explode("\n", $string, 2);
        $string = $string[0] . $append;
    }

    return $string;
}

function grabunity($grab) {

    $curl = curl_init();
    // Setup headers - I used the same headers from Firefox version 2.0.0.6
    // below was split up because php.net said the line was too long. :/
    $header[0] = "Accept: application/json, text/javascript, */*; q=0.01";
    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 3000";
    $header[] = "X-Requested-With: UnityAssetStore";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";
    $header[] = "Referer: https://www.assetstore.unity3d.com/en/";
    //$header[] = "Referer: https://www.assetstore.unity3d.com/en/";
    $header[] = "Pragma: "; // browsers keep this blank.
    //$header[] = "X-Kharma-Version: 5.1.0-r84295";
    $header[] = "Age: 0";
    $header[] = "X-Unity-Session: 26c4202eb475d02864b40827dfff11a14657aa41";
    //$header[] = "X-Unity-Session: DT0QtkD0XtndqYqKiZRtf9Eb5_0-VD4ynLiVhdPEWzp2Td-_gs07pJGNzxwDjpQv_3riBpd2Kr5CP_mg4lrRnQ::Sausage::";

    curl_setopt($curl, CURLOPT_URL, $grab);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    //curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
    $cookie_jar = $_SERVER['DOCUMENT_ROOT'] . "/tmpdata/cookie.txt";
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_jar);

    $html = curl_exec($curl); // execute the curl command

    curl_close($curl); // close the connection

    $r = json_decode($html, true);
    //dump($r);
    return $r;
}

/*
 *
 */

function smtpmail($to, $subject, $body) {
    global $settings;

    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
    $mail->SMTPAuth = true;  // authentication enabled
    $mail->SMTPSecure = ''; // secure transfer enabled REQUIRED for GMail (can be ssl or tls)
    $mail->Host = SMTP_HOST;
    $mail->Port = SMTP_PORT;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASSWORD;
    $mail->IsHTML(true);
    $mail->SetFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);

    if (!$mail->Send()) {
        $settings->error = 'Mail error: ' . $mail->ErrorInfo;
        return false;
    } else {
        $settings->error = 'Message sent!';
        return true;
    }
}

function create_captcha() {
    global $image;
    $image = imagecreatetruecolor(200, 50) or die("Cannot Initialize new GD image stream");

    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 255, 255);
    $line_color = imagecolorallocate($image, 64, 64, 64);
    $pixel_color = imagecolorallocate($image, 0, 0, 255);

    imagefilledrectangle($image, 0, 0, 200, 50, $background_color);

    for ($i = 0; $i < 3; $i++) {
        imageline($image, 0, rand() % 50, 200, rand() % 50, $line_color);
    }

    for ($i = 0; $i < 1000; $i++) {
        imagesetpixel($image, rand() % 200, rand() % 50, $pixel_color);
    }

    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $len = strlen($letters);
    $letter = $letters[rand(0, $len - 1)];

    $word = "";
    $font = imageloadfont('fonts/consolas.gdf');
    if ($font == false) {
        echo "error";
        exit;
    }
    for ($i = 0; $i < 6; $i++) {
        $letter = $letters[rand(0, $len - 1)];
        $text_color = imagecolorallocate($image, rand(0, 100), rand(0, 100), rand(0, 100));
        imagestring($image, $font, 5 + ($i * 30), 20 + rand(0, -20), $letter, $text_color);
        $word .= $letter;
    }
    $_SESSION['captcha_string'] = $word;

    $images = glob("*.png");
    foreach ($images as $image_to_delete) {
        @unlink($image_to_delete);
    }
    imagepng($image, "image" . $_SESSION['count'] . ".png");
}

function getRandomName($short = false) {
    RandomData::registerAttribute('dept', 'randDeptName');
    RandomData::setConfig('birthdate', ['min' => 21, 'max' => 70]);

    $options = ['birthdate' => false, 'dateformat' => 'Y-m-d', 'middlename' => false];

    $person = RandomData::getPerson($options);
    if ($short == false) {
        $total = $person[lastname] . $person[firstname];
    } else {
        $total = $person[lastname];
    }
    return $total;
}

function startc($cachetime = CACHE_TIME, $opt = "") {
    global $settings, $user;

    if (!$settings->cache_active) {
        return true;
    }

    $url = $_SERVER["SCRIPT_NAME"];
    $break = explode('/', $url);
    $file = $break[count($break) - 1];

    mkdir(CACHE_PATH, 0777, true);
    $cachefile = CACHE_PATH . md5($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'] . $file . $user->rank . $user->is_admin . $user->is_editor . $settings->language .
                    $settings->is_stopped . $settings->is_locked . $settings->is_approved . $settings->is_silenced . $opt);
    if ($settings->cache_type == 0) {
        //echo $cachefile;
        // Serve from the cache if it is younger than $cachetime
        if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile) && $settings->resetcache == 0) {
            echo "<!-- Cached copy, generated mode 0 - " . date('D H:i', filemtime($cachefile)) . " $cachefile -->\n";
            @include_once($cachefile);

            return false;
        } else {
            echo "<!-- Not cached-->\n";
        }
    }
    if ($settings->cache_type == 1) {
        /* $settings->filecachecontent = file_get_contents(realpath(dirname(__FILE__)));
          $settings->cachehash = md5($filecachecontent);
          $settings->cachefile = md5($filepath).".txt";

          // Serve from the cache if it is younger than $cachetime
          if (file_exists($settings->cachefile)) {
          echo "<!-- Cached copy, generated mode 1" . date('H:i', time()) . " -->\n";
          include($settings->cachefile);
          return false;
          }
         */
    }

    ob_start(); // Start the output buffer
    return true;
}

function endc($opt = "") {
    global $settings, $user;

    if (!$settings->cache_active) {
        return true;
    }

    $url = $_SERVER["SCRIPT_NAME"];
    $break = explode('/', $url);
    $file = $break[count($break) - 1];

// Cache the contents to a file
    //echo $settings->cachefile;
    $cachefile = CACHE_PATH . md5($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING'] . $file . $user->rank . $user->is_admin . $user->is_editor . $settings->language . $opt);
    $cached = fopen($cachefile, 'w');
    fwrite($cached, ob_get_contents());
    fclose($cached);
    ob_end_flush(); // Send the output to the browser
}

function displayName($name, $url, $compatible, $exclu = false, $hide = false) {
    global $settings;
    $co = "";
    if ($compatible == 0) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/u4.png'> ";
    }
    if ($compatible == 1) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/u5.png'> ";
    }
    if ($compatible == 2) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/udk4.png'> ";
    }
    if ($compatible == 3) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/tutorial.png'> ";
    }
    if ($compatible == 4) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=img/compatible/magazine.png'> ";
    }
    if ($compatible == 5) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/3d.png'> ";
    }
    if ($compatible == 6) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/texture.png'> ";
    }
    if ($compatible == 7) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/applications.png'> ";
    }
    if ($compatible == 8) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/sounds.png'> ";
    }

    if ($exclu) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/exclu.png'> ";
    }
    if ($hide) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/compatible/hide.png'> ";
    }

    echo "<b><a href='" . $url . "'>" . _t($name) . $co . "</a></b>";
}

function getName($name, $url, $compatible, $exclu = false, $hide = false) {
    global $settings;
    $co = "";
    if ($compatible == 0) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/u4.png'> ";
    }
    if ($compatible == 1) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/u5.png'> ";
    }
    if ($compatible == 2) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/udk4.png'> ";
    }
    if ($compatible == 3) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/tutorial.png'> ";
    }
    if ($compatible == 4) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=img/compatible/magazine.png'> ";
    }
    if ($compatible == 5) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/3d.png'> ";
    }
    if ($compatible == 6) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/texture.png'> ";
    }
    if ($compatible == 7) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/applications.png'> ";
    }
    if ($compatible == 8) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/sounds.png'> ";
    }

    if ($exclu) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/img/compatible/exclu.png'> ";
    }
    if ($hide) {
        $co = " <img src='" . $settings->cdn . "/thumb.php?zc=0&w=16&h=12&src=/compatible/hide.png'> ";
    }

    return "<a href='" . $url . "'>" . _t($name) . $co . "</a>";
}

function getCompatible($compatible) {
    $co = "";
    if ($compatible == 0) {
        $co = " UNITY ";
    }
    if ($compatible == 1) {
        $co = " UNITY 5 ";
    }
    if ($compatible == 2) {
        $co = " UE4";
    }
    if ($compatible == 3) {
        $co = " Tutorial ";
    }
    if ($compatible == 4) {
        $co = " Magazine ";
    }
    if ($compatible == 5) {
        $co = " 3d Model ";
    }
    if ($compatible == 6) {
        $co = " Texture ";
    }
    if ($compatible == 7) {
        $co = " Application ";
    }
    if ($compatible == 8) {
        $co = " Sounds ";
    }

    if ($co == "") {
        "No Category Specified";
    }
    return $co;
}

function isLoggedin() {
    global $settings, $user;
    if ($user->is_logged) {
        return true;
    } else {
        header("location: /login.php");
    }
}

function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context) {
    if ($error_level == 8 || $error_level == 2) {
        return;
    }
    $error = "lvl: " . $error_level . " | msg:" . $error_message . " | file:" . $error_file . " | ln:" . $error_line;
    switch ($error_level) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_PARSE:
            mylog($error, "fatal");
            break;
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            mylog($error, "error");
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        //case E_USER_WARNING:
        //	mylog($error, "warn");
        //	break;
        case E_NOTICE:
        //case E_USER_NOTICE:
        //mylog($error, "info");
        //	break;
        case E_STRICT:
            mylog($error, "debug");
            break;
        //default:
        //	mylog($error, "warn");
    }
}

function shutdownHandler() {
    $lasterror = error_get_last();
    switch ($lasterror['type']) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
        //case E_CORE_WARNING:
        //case E_COMPILE_WARNING:
        case E_PARSE:
            $error = "[SHUTDOWN] lvl:" . $lasterror['type'] . " | msg:" . $lasterror['message'] . " | file:" . $lasterror['file'] . " | ln:" . $lasterror['line'];
            mylog($error, "fatal");
    }
}

function mylog($error, $errlvl) {
    echo $errlvl . " " . $error;
    exit;
}

// Converts linebreaks to <br>
function mynl2br($text) {
    return strtr($text, ["\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />']);
}

function refresh_session() {
    $_SESSION[md5(FSCUID) . "_REFRESH"] = 1;
}

function url_exists($u) {
    $file_headers = @get_headers($u);
    if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
        return false;
    } else {
        return true;
    }
}

function refreshSession() {
    global $settings, $user;

    $settings->FSCUID = md5(FSCUID);
    $s1 = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(SESSION_KEY), base64_encode(serialize($settings)), MCRYPT_MODE_CBC, md5(md5(SESSION_KEY)));
    setSession(md5(FSCUID), $s1);
    setCookies("email", encrypt($user->email)); // this mean that the customer is logged.
    setCookies("sessionid", $settings->sessionid);
    setCookies("password", $user->password);
}

function refreshCategories() {
    global $settings;

    $db = new Database();
    $sql = "SELECT * FROM categories ";
    $db->query($sql);
    while ($db->nextRecord()) {
        $settings->allcat[$db->Record['id']] = $db->Record['name'];
    }
    $db->close();
}

function refreshGeoIP() {
    global $settings, $user;

// geo city
    $gi = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIP.dat", GEOIP_STANDARD);
    $settings->countrycode = geoip_country_code_by_addr($gi, $user->ip);
    $settings->country = geoip_country_name_by_addr($gi, $user->ip);
    $gi2 = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIPCity.dat", GEOIP_STANDARD);
    $record = geoip_record_by_addr($gi2, $user->ip);
    $settings->metro = $record->metro_code;
    $settings->city = $record->city;
    $settings->zipcode = $record->postal_code;
    $settings->latitude = $record->latitude;
    $settings->longitude = $record->longitude;
    $settings->continent_code = $record->continent_code;
    $settings->area_code = $record->area_code;
    $giorg = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIPOrg.dat", GEOIP_STANDARD);
    $settings->org = geoip_org_by_addr($giorg, $user->ip);
    $giisp = geoip_open($_SERVER['DOCUMENT_ROOT'] . "/3rdparty/geoip/GeoIPISP.dat", GEOIP_STANDARD);
    $settings->domain = $settings->isp = geoip_org_by_addr($giisp, $user->ip);
    $settings->org = clean($settings->org);

    geoip_close($giorg);
    geoip_close($giisp);
    geoip_close($gi2);
    geoip_close($gi);
    $settings->domain = gethostbyaddr($user->ip);
    if (isset($_SERVER['HTTP_REFERER'])) {
        $ref = $_SERVER['HTTP_REFERER'];
    } else {
        $ref = "";
    }
    $settings->referer = $ref;
    if ($_SERVER["PHP_SELF"] == $_SERVER["REQUEST_URI"]) {
        $_SERVER["REQUEST_URI"] = "";
    }
    $settings->url = strtolower($_SERVER['SERVER_NAME'] . $_SERVER["PHP_SELF"] . $_SERVER["REQUEST_URI"]);
    $settings->url = "http://" . str_replace("https:", "http:", $settings->url);
    $settings->head = $head = $_SERVER['HTTP_USER_AGENT'];

    $settings->location = $settings->latitude . "," . $settings->longitude;
    if (($settings->country == "" && $settings->countrycode == "") || $settings->
            countrycode == "A1"
    ) {
        $settings->country = "Proxy";
    }

    // check blacklist
    $db = new Database();
    $sql = "SELECT * FROM blocklist WHERE lower(city)='" . strtolower($settings->city) . "' OR lower(org)='" . strtolower($settings->org) . "' OR lower(domain)='" . strtolower($settings->domain) . "' OR ip='" . $user->ip . "' OR countrycode='" . $settings->countrycode . "' OR  lower(country)='" . strtolower($settings->country) . "'";
    $db->query($sql);
    if ($db->nbr() > 0) {
        header("Location: /401locked.html");
        exit;
    }
}

function refreshThemes($theme = "") {
    global $settings, $theme;

    $db = new Database();
    if ($theme == "") {
        $darray = explode('.', $_SERVER['HTTP_HOST']);
        $narray = array_reverse($darray);
        $domain = $narray[1];
        unset($darray, $narray);
        $settings->servername = strtolower($domain);
    } else {
        $settings->servername = strtolower($theme);
    }

// grab the themes / templates
    $sql = "SELECT * FROM themes WHERE LOWER(name)='" . strtolower($settings->servername) . "'";
    $db->query($sql);
    if ($db->nbr() == 1) {
        $db->single();
        $settings->theme = $db->rs['name'];
        $settings->noregistration = $db->rs['noregistration'];
        $settings->theme_url = $db->rs['url'];
        $settings->theme_logo = $db->rs['logo'];
        $settings->sql_and = $settings->library_filter = $db->rs['library_filter'];
        $settings->bg = $settings->theme_color = $db->rs['nav_color'];
        $settings->httpmode = $settings->theme_httpmode = "http://";
        $settings->theme_css = $settings->css;

        $theme->name = $db->rs['name'];
        $theme->noregistration = $db->rs['noregistration'];
        $theme->url = $db->rs['url'];
        $theme->logo = $db->rs['logo'];
        $theme->sql_and = $settings->library_filter = $db->rs['library_filter'];
        $theme->bg = $db->rs['nav_color'];
        $theme->httpmode = $settings->theme_httpmode = "http://";
        $theme->css = $db->rs['css'];
    } else {
        header("Location: /404.html");
        exit;
    }

    $settings->url = full_url($_SERVER);
    $settings->root = $_SERVER['DOCUMENT_ROOT'];
    $db->close();

    return $settings->theme;
}

function refreshSettings() {
    global $settings;

    $db = new Database();
    // grab the global settings in the DB write them in $settings->???
    $sql = "SELECT * FROM settings";
    $db->query($sql);
    $i = 0;
    while ($db->nextRecord()) {
        $field = $db->rs['name'];
        $settings->$field = $db->rs['value'];
        $settings->desc[$field] = $db->rs['description'];
        $settings->name[$field] = $db->rs['name'];
        $i++;
    }
    $db->close();

    $settings->servername = $_SERVER['SERVER_NAME'];
}

function refreshUser() {
    global $settings, $user, $loader;

    $db = new Database();
    // grab the global settings in the DB write them in $settings->???
    $sql = "SELECT * FROM settings ORDER BY id ASC";
    $db->query($sql);
    $i = 0;
    while ($db->nextRecord()) {
        $field = $db->rs['name'];
        $user->$field = $db->rs['value'];
        $i++;
    }
    $db->close();

    $loader->buttons_squared = $user->buttons_squared;
}

function logout() {
    session_start();
    session_unset();
    unset($_SESSION);
    session_destroy();
    session_write_close();
    setcookie(session_name(), '', 0, '/');
    session_regenerate_id(true);
    $error = encrypt(_t("You have been logged out!"));

    //echo "<script>window.location = '/login.php?error=" . $error . "';</script>";
}

function toAscii($str, $replace = [], $delimiter = '-') {
    if (!empty($replace)) {
        $str = str_replace((array) $replace, ' ', $str);
    }

    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9/_|+ -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[/_|+ -]+/", $delimiter, $clean);

    return $clean;
}

function getPictureGrid($id, $image, $name, $action, $exclu, $rating = 0, $compatible = 0, $hide = 0, $forged = 0, $view = "view", $target = "_self", $iframe = false, $cl = "grid-thumb") {
    global $releasenotes, $settings, $user;

    $url = getSU($id, $view);

    $r = '<div class="ribbon-block grid-ribbon" >';

    if ($compatible == 0) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/u4.png" class="compatible-icon"/>';
    }
    if ($compatible == 1) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/u5.png" class="compatible-icon"/>';
    }
    if ($compatible == 2) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/udk4.png" class="compatible-icon"/>';
    }
    if ($compatible == 3) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/tutorial.png" class="compatible-icon"/>';
    }
    if ($compatible == 4) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/pdf.png" class="compatible-icon"/>';
    }
    if ($compatible == 5) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/3d.png" class="compatible-icon"/>';
    }
    if ($compatible == 6) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/texture.png" class="compatible-icon"/>';
    }
    if ($compatible == 7) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/img/applications.png" class="compatible-icon"/>';
    }
    if ($compatible == 8) {
        $r .= '<img width="32" height="28" src="' . $settings->cdn . '/thumb.php?zc=1&w=32&h=28&src=/img/compatible/img/sounds.png" class="compatible-icon"/>';
    }

    if ($user->rank < $settings->rank_hidden && $hide) {
        $name = "Only ranks >=" . $settings->rank_hidden . " can see. You are rank " . $user->rank;

        $r .= '<img  alt="' . $name . '"  title="' . $name . '"  src="' . $settings->cdn . '/thumb.php?zc=2&w=350&h=300&src=/img/hide.png"  class="grid-thumb">';
    } else {
        if ($releasenotes <> "") {
            $releasenotes = clean(strip_tags(substr($releasenotes, 0, 1000), "<br>"));
        } else {
            $releasenotes = clean($name);
        }
        $r .= '<a referrerpolicy="origin" target="' . $target . '" href="' . $url . '&iframe=' . $iframe . 'false"   alt="' . $name . '" title="' . $releasenotes . '">' .
                '<img  alt="' . $name . '"  title="' . $name . '"  src="' . $settings->cdn . '/thumb.php?src=' . $image . '&zc=1&w=350&h=300"  class="' . $cl . ' shadowed" style="margin:0;padding:0;">';
    }

    if ($action == 2 && !$exclu && !$hide) {

        $r .= '<div class="ribbon ribbon-red transparent ">
				<div class="banner">
					<div class="text">New Release</div>
				</div>
			</div>';
    }

    if ($action == 1 && !$exclu && !$hide) {

        $r .= '<div class="ribbon ribbon-green transparent">
				<div class="banner">
					<div class="text">Updated!</div>
				</div>
			</div>';
    }

    if ($exclu && !$hide && !$forged) {

        $r .= '<div class="ribbon ribbon-blue transparent">
				<div class="banner">
					<div class="text">Exclusive</div>
				</div>
			</div>';
    }

    if ($hide) {

        $r .= '<div class="ribbon ribbon-black transparent">
				<div class="banner">
					<div class="text">HIDDEN</div>
				</div>
			</div>';
    }

    if ($exclu == 0 && $action == 0 && $hide == 0) {
        if ($rating > 3) {
            $r .= '				
				<div class="ribbon ribbon-yellow transparent ">
					<div class="banner">
						<div class="text">Top Quality</div>
					</div>
				</div>';
        }

        if ($rating > 4) {

            $r .= '<div class="ribbon ribbon-red transparent">
					<div class="banner">
						<div class="text">Voted Best</div>
					</div>
				</div>';
        }
    }

    $r .= '</a></div>';

    return $r;
}

function cleanDescription($description) {
    $description = htmlspecialchars_decode($description);
    //$description =html_entity_decode($description);
    $description = str_replace("<br/>", "<br>", $description);
    $description = str_replace("<br />", "<br>", $description);
    $description = strip_tags($description, '<br><p><a>');
    $description = str_replace("`", "'", $description);
    $description = str_replace("&#039;", "", $description);
    //$description = str_replace(".<br> | ", " | ", $description);
    //$description = htmlspecialchars_decode($description);
    $description = str_replace("<BR>", "<br>", $description);

    $description = str_replace("\\r\\n", "<br>", $description);
    $description = str_replace("\r\n", "<br>", $description);
    $description = str_replace("P>", "p>", $description);
    $description = str_replace("<\b><br>", "</b>", $description);
    $description = str_replace("<\b>\r<br>", "</b>", $description);
    $description = str_replace("<\b>\n<br>", "</b>", $description);
    $description = str_replace("</b>" . PHP_EOL . "<br>", "</b>", $description);
    //$description = str_replace(". ", ".<br>", $description);
    $description = str_replace("Price:", "<span itemprop=\"offers\" itemscope itemtype=\"http://schema.org/Offer\"><b>Price:</b>", $description);
    $description = str_replace("Publisher:", " | <span itemprop=\"seller\" itemscope itemtype=\"http://schema.org/Organization\"><b>Publisher:</b>", $description);
    $description = str_replace("Description:", "<hr><b>Description:</b><br>", $description);
    $description = preg_replace('#<br />(\s*<br>)+#', '<br />', $description);
    $description = preg_replace('#<br>(\s*<br>)+#', '<br>', $description);

    $description = str_replace("\r\n", "<br>", $description);
    $description = str_replace("\n", "<br>", $description);
    $description = str_replace("\r", "<br>", $description);
    $description = str_replace("<p>", "<br>", $description);
    $description = str_replace("</p>", "", $description);
    $description = str_replace("<br>\n<br>", "<br>", $description);
    $description = str_replace("<br>\r<br>", "<br>", $description);
    $description = str_replace("<br>\r\n<br>", "<br>", $description);
    $description = str_replace("<br><br>", "<br>", $description);
    $description = str_replace("<br><br/><br>", "<br/>", $description);
    $description = str_replace("•", "<br>-", $description);

    for ($i = 0; $i < 10; $i++) {
        $description = str_replace("<br><br><br>", "<br><br>", $description);
    }

    //$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    //$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
    //$description=str_replace($search, $replace, $description);
    $description = str_replace("\"img", "\"/img", $description);
    $description = str_replace(".<br> | ", " | ", $description);

    return $description;
    //return "<span itemprop=\"description\">" . $description . "</span>";
}

function cleanDescriptionSimple($description) {
    $description = html_entity_decode($description, ENT_QUOTES | ENT_XML1, 'UTF-8');
    $description = strip_tags($description, '<br><p><a>');
    $description = substr($description, 0, 500) . "...";

    $description = str_replace(PHP_EOL, "<br>", $description);
    $description = str_replace("<br/><br/>", "<br/>", $description);
    $description = str_replace("<br>\n<br>", "<br>", $description);
    $description = str_replace("<br>\r<br>", "<br>", $description);
    $description = str_replace("<br>\r\n<br>", "<br>", $description);
    $description = str_replace("<br><br>", "<br>", $description);
    $description = str_replace("\r\n", "", $description);
    $description = str_replace("\n", "", $description);
    $description = str_replace("\r", "", $description);

    $search = ["\\", "\x00", "\n", "\r", "'", '"', "\x1a"];
    $replace = ["\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z"];
    $description = str_replace($search, $replace, $description);
    return $description;
}

function cleanReleaseNotes($description) {
    $description = str_replace(PHP_EOL, "<br>", $description);
    $description = str_replace("<br/><br/>", "<br/>", $description);
    $description = str_replace("<br>\n<br>", "<br>", $description);
    $description = str_replace("<br>\r<br>", "<br>", $description);
    $description = str_replace("<br>\r\n<br>", "<br>", $description);
    $description = str_replace("<br><br>", "<br>", $description);
    $description = str_replace("\r\n", "", $description);
    //$description = str_replace("\n", "", $description);
    $description = str_replace("\r", "", $description);
    $description = str_replace("<br>", "\n", $description);
    $description = str_replace(". ", "\n", $description);
    $description = str_replace("- ", "\n", $description);
    $description = str_replace("* ", "\n", $description);
    $description = str_replace("\n\n", "\n", $description);
    $description = str_replace("\n\n", "\n", $description);
    $description = str_replace("\n\n", "\n", $description);
    return $description;
}

function cleanLinks($url) {
    $U = explode(' ', $url);

    $W = [];
    foreach ($U as $k => $u) {
        if (stristr($u, 'http') || (count(explode('.', $u)) > 1)) {
            unset($U[$k]);
            return cleanLinks(implode(' ', $U));
        }
    }
    return implode(' ', $U);
}

function getPrice($s) {
    preg_match('#[\$\�\�](\d+(?:\.\d{1,2})?)#', strip_tags($s), $regs);
    $price = $regs[0];

    return toInt($price);
}

function toInt($str) {
    return (int) preg_replace("/\..+$/i", "", preg_replace("/[^0-9\.]/i", "", $str));
}

/*
 * Send a private message
 */

function sendPM($subject, $message, $destid) {

    if (strlen($_REQUEST['message']) < 10) {
        return false;
    } else {

        $save = new Database();

        $sql = 'INSERT INTO messages (
			   `sourceid`,
			   `destid`,
			   `subject`,
			   `message`,
			   `sourcedeleted`,
			   `destdeleted`,
			   `date`,
			   `isreadsource`,
			   `isreaddest`,
				`dady`,
			   `size`
			) VALUES (
			   ' . sql_val(1) . ',
			   ' . sql_val($destid) . ',
			   ' . sql_val($subject) . ',
			   ' . sql_val($message) . ',
			   ' . sql_val(0) . ',
			   ' . sql_val(0) . ',
			   ' . sql_val(time()) . ',
			   ' . sql_val(0) . ',
			   ' . sql_val(0) . ',
			   ' . sql_val(0) . ',
			   ' . sql_val(0) . '
			)';
    }
}

/*
 * Resize and image and crop the result.
 */

function resizeImageCrop($image, $width, $height) {

    $w = @imagesx($image); //current width

    $h = @imagesy($image); //current height
    if ((!$w) || (!$h)) {
        $GLOBALS['errors'][] = 'Image couldn\'t be resized because it wasn\'t a valid image.';
        return false;
    }
    if (($w == $width) && ($h == $height)) {
        return $image;
    }  //no resizing needed
    $ratio = $width / $w;       //try max width first...
    $new_w = $width;
    $new_h = $h * $ratio;
    if ($new_h < $height) {  //if that created an image smaller than what we wanted, try the other way
        $ratio = $height / $h;
        $new_h = $height;
        $new_w = $w * $ratio;
    }
    $image2 = imagecreatetruecolor($new_w, $new_h);
    imagecopyresampled($image2, $image, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
    if (($new_h != $height) || ($new_w != $width)) {    //check to see if cropping needs to happen
        $image3 = imagecreatetruecolor($width, $height);
        if ($new_h > $height) { //crop vertically
            $extra = $new_h - $height;
            $x = 0; //source x
            $y = round($extra / 2); //source y
            imagecopyresampled($image3, $image2, 0, 0, $x, $y, $width, $height, $width, $height);
        } else {
            $extra = $new_w - $width;
            $x = round($extra / 2); //source x
            $y = 0; //source y
            imagecopyresampled($image3, $image2, 0, 0, $x, $y, $width, $height, $width, $height);
        }
        imagedestroy($image2);
        return $image3;
    } else {
        return $image2;
    }
}

/*
 * Generate a random name
 */

function genName($id, $length = 5, $lower_case = true, $ucfirst = true, $upper_case = false) {
    //srand((int)$id);
    $done = false;
    $const_or_vowel = 1;
    $word = "";

    $vowels = ['a', 'e', 'i', 'o', 'u', 'y'];

    $consonants = [
        'b',
        'c',
        'd',
        'f',
        'g',
        'h',
        'j',
        'k',
        'l',
        'm',
        'n',
        'p',
        'r',
        's',
        't',
        'v',
        'w',
        'z',
        'ch',
        'qu',
        'th',
        'xy'
    ];
    $i = 1;
    while (!$done) {
        switch ($const_or_vowel) {
            case 1:
                $word .= $consonants[($id + 21 * 39 * $i * rand(1, 1000)) % sizeof($consonants)];
                $const_or_vowel = 2;
                break;
            case 2:
                $word .= $vowels[($id + 21 * 39 * $i * rand(1, 1000)) % sizeof($vowels)];
                $const_or_vowel = 1;
                break;
        }

        if (strlen($word) >= $length) {
            $done = true;
        }
        $i++;
    }

    $word = substr($word, 0, $length);

    if ($lower_case) {
        $word = strtolower($word);
    } else {
        if ($ucfirst) {
            $word = ucfirst(strtolower($word));
        } else {
            if ($upper_case) {
                $word = strtoupper($word);
            }
        }
    }
    return $word;
}

/*
 * Resize an image
 */

function imageResize($src, $dst, $width, $height, $crop = 0) {

    if (!list($w, $h) = getimagesize($src)) {
        return "Unsupported picture type!";
    }

    $type = strtolower(substr(strrchr($src, "."), 1));
    if ($type == 'jpeg') {
        $type = 'jpg';
    }
    switch ($type) {
        case 'bmp':
            $img = imagecreatefromwbmp($src);
            break;
        case 'gif':
            $img = imagecreatefromgif($src);
            break;
        case 'jpg':
            $img = imagecreatefromjpeg($src);
            break;
        case 'png':
            $img = imagecreatefrompng($src);
            break;
        default :
            return "Unsupported picture type!";
    }

    // resize
    if ($crop) {
        if ($w < $width or $h < $height) {
            return "Picture is too small!";
        }
        $ratio = max($width / $w, $height / $h);
        $h = $height / $ratio;
        $x = ($w - $width / $ratio) / 2;
        $w = $width / $ratio;
    } else {
        if ($w < $width and $h < $height) {
            return "Picture is too small!";
        }
        $ratio = min($width / $w, $height / $h);
        $width = $w * $ratio;
        $height = $h * $ratio;
        $x = 0;
    }

    $new = imagecreatetruecolor($width, $height);

    // preserve transparency
    if ($type == "gif" or $type == "png") {
        imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
        imagealphablending($new, false);
        imagesavealpha($new, true);
    }

    imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

    switch ($type) {
        case 'bmp':
            imagewbmp($new, $dst);
            break;
        case 'gif':
            imagegif($new, $dst);
            break;
        case 'jpg':
            imagejpeg($new, $dst);
            break;
        case 'png':
            imagepng($new, $dst);
            break;
    }
    return true;
}

/*
 *
 */

function croppedThumbnail($imgSrc, $thumbnail_width, $thumbnail_height) {
    //getting the image dimensions
    list($width_orig, $height_orig) = getimagesize($imgSrc);
    $myImage = imagecreatefromjpeg($imgSrc);
    $ratio_orig = $width_orig / $height_orig;

    if ($thumbnail_width / $thumbnail_height > $ratio_orig) {
        $new_height = $thumbnail_width / $ratio_orig;
        $new_width = $thumbnail_width;
    } else {
        $new_width = $thumbnail_height * $ratio_orig;
        $new_height = $thumbnail_height;
    }

    $x_mid = $new_width / 2;  //horizontal middle
    $y_mid = $new_height / 2; //vertical middle

    $process = imagecreatetruecolor(round($new_width), round($new_height));

    imagecopyresampled($process, $myImage, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);
    $thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
    imagecopyresampled($thumb, $process, 0, 0, ($x_mid - ($thumbnail_width / 2)), ($y_mid - ($thumbnail_height / 2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

    imagedestroy($process);
    imagedestroy($myImage);
    return $thumb;
}

function displaySwitchProfileAjax($id, $ajax_db, $ajax_var, $ajax_field, $ajax_id, $ajax_varid) {
    echo "<script>

		$('#" . $id . "').bootstrapSwitch().on('switchChange.bootstrapSwitch', function (e, data) {
	    e.preventDefault();	 
		$.ajax({
	        url: '/_ajax_profile.php',
	        type: 'POST',
	        data: {
	             ajax_db: '" . $ajax_db . "',
	              ajax_var: '" . $ajax_var . "',
	               ajax_field: $('#" . $id . "').is(':checked'),
	                ajax_id: '" . $ajax_id . "',
	                 ajax_varid: '" . $ajax_varid . "'
	
	        },
	        success: function(data, status, xhr) {
	        // handle success
	 
	        // alert(JSON.stringify(data));
	        $('" . $id . "').css('background-color', '#00ff00');
	        },
	        error: function(xhr, status, error) {
	        // handle error
	      
	         $('" . $id . "').css('background-color', '#ff0000');   
	        }
	        
	    }).done(function(data){

			//alert(JSON.stringify(data));
			  $('" . $id . "').css('background-color', '#0000ff');
		});
	  });
</script>
	";
}

function displaySwitchAjax($id, $ajax_db, $ajax_name) {
    echo "<script>
		$('#" . $id . "').bootstrapSwitch().on('switchChange.bootstrapSwitch', function (e, data) {
	
	    e.preventDefault();	        

		$.ajax({
	        url: '/_ajax_settings.php',
	        type: 'POST',
	        data: {
	             ajax_db: '" . $ajax_db . "',
	              ajax_name: '" . $ajax_name . "',
	                   ajax_value:  $('#" . $id . "').is(':checked')
	
	        },
	        success: function(data, status, xhr) {
	        // handle success	 
	         //alert(JSON.stringify(data));
	        $('" . $id . "').css('background-color', '#00ff00');
	        },
	        error: function(xhr, status, error) {
	        // handle error
	         //alert(JSON.stringify(data)+': '+error);
	         $('" . $id . "').css('background-color', '#ff0000');
	   
	        }
	        
	    }).done(function(data){
			//alert(JSON.stringify(data));
			  $('" . $id . "').css('background-color', '#0000ff');
		});
	  });
</script>";
}

function switchSettings($n, $colmd = 2, $colsm = 1) {
    Global $settings;

    echo "<div class='col-md-" . $colmd . " col-sm-" . $colsm . "'><label>" . ucfirst($n) . "</label> <br>";
    echo "<input id='" . $n . "' type='checkbox' class='bootstrap-switch' data-handle-width='40' data-size='small'  name='" . $n . "'";

    if ($settings->$n) {
        echo " checked='checked' ";
    }
    echo " >";
    echo "<br/><small>" . $settings->desc[$n] . "</small>";

    displaySwitchAjax($n, "settings", $n);
    echo "</div>";
}

function switchThemes($n, $colmd = 2, $colsm = 1) {
    Global $settings;

    echo "<div class='col-md-" . $colmd . " col-sm-" . $colsm . "'><label>" . ucfirst($n) . "</label> <br>";
    echo "<input id='" . $n . "' type='checkbox' class='bootstrap-switch' data-handle-width='40' data-size='small'  name='" . $n . "'";

    if ($settings->$n) {
        echo " checked='checked' ";
    }
    echo " >";
    echo "<br/>";

    displaySwitchAjax($n, "themes", $n);
    echo "</div>";
}

/*

  <div class="col-md-4">
  <label class="label  label-default"><b>Sparkles <br>(rank>=<?php echo $settings->rank_sparkle; ?>) </b></label><br>
  <input id="sp" type="checkbox" class="bootstrap-switch" data-size="mini" name="exclu"
  <?php if ($profile->Record['sparkle'] == 1) {
  echo " checked='checked' ";
  } ?>
  <?php if ($rank < $settings->rank_sparkle) {
  echo " disabled ";
  } ?>>

  </div>
 */

function switchProfile($n, $p, $colmd = 2, $colsm = 1, $r = 0, $desc = "") {
    Global $settings, $user;
    $rank = "";
    if ($r <> 0) {
        if ($r > $user->rank) {
            $rank = " disabled ";
        }
    }
    echo "<div class='col-md-" . $colmd . " col-sm-" . $colsm . "'><label><b>" . ucfirst($n) . "</b></label>";
    echo "<input " . $rank . " id='" . $n . "' type='checkbox' class='bootstrap-switch' data-handle-width='40' data-size='small'  name='" . $n . "'";

    if ($p->Record[$n]) {
        echo " checked='checked' ";
    }
    echo " >";
    if ($desc == "") {
        echo "<br/><small>" . $settings->desc[$n] . "</small>";
    } else {
        echo "<br/><small>" . $desc . "</small>";
    }
    //if ($p->Record[$n]) $n1 = 1; else $n1 = 0;
    //	displaySwitchAjax("sp", "update", "users", "sparkle", $n1 ?: "0", "user_id", $profile->rs['user_id']);
    if ($rank == "") {
        displaySwitchProfileAjax($n, "users", $n, $p->Record[$n], "id", $user->id);
    }
    echo "</div>";
    $user->refresh();
}

function sendDiscord($productid, $version) {
    Global $settings;
    $sql = "SELECT * FROM products WHERE id=$productid ";
    $p1 = new Database();
    $p1->query($sql);
    $p1->singleRecord();
    $name = $p1->rs['name'];
    $oldversion = $p1->rs['version'];
    $image = $p1->rs['image'];

    if ($settings->cdn == "") {
        $dimage = $settings->thisurl . "/thumb/2/150/130" . $image;
    } else {
        $dimage = $settings->cdn . "/thumb.php?zc=2&w=150&h=130&src=" . $image;
    }

    $dimage = str_replace("//", "/", $dimage);
    $dimage = str_replace("//", "/", $dimage);
    $dimage = str_replace("//", "/", $dimage);
    $dlink = $settings->thisurl . "view/_/_/_/" . $productid . ".html";
    $discord = new Discord($settings->discord_new_update);
    $message = "** " . $name . ": ** updated from" . $oldversion . " to version " . $version . ". [Check Here](" . $dlink . ")";
    $discord->send($message);
    $message = $settings->thisurl . "/thumb/2/150/130" . $image;
    if ($settings->cdn == "") {
        $message = $settings->thisurl . "/thumb/2/150/130" . $image;
    } else {
        $message = $settings->cdn . "/thumb.php?zc=2&w=150&h=130&src=" . $image;
    }

    $discord->send($message);
}

function makeSymLink($target, $link) {
    global $user;
    $ex = 'mklink /j \"' . str_replace('/', '\\', $link) . '\" \"' . str_replace('/', '\\', $target) . '\"';
    //if ($user->is_admin) {echo $ex;exit;}
    exec($ex);
}

function checkWindows() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // This is a server using Windows!
        return true;
    } else {
        // This is a server not using Windows!
        return false;
    }
}

function injectData($file, $data, $position) {
    $fpFile = fopen($file, "rw+");
    $fpTemp = fopen('php://temp', "rw+");

    $len = stream_copy_to_stream($fpFile, $fpTemp); // make a copy

    fseek($fpFile, $position); // move to the position
    fseek($fpTemp, $position); // move to the position

    fwrite($fpFile, $data); // Add the data

    stream_copy_to_stream($fpTemp, $fpFile); // @Jack

    fclose($fpFile); // close file
    fclose($fpTemp); // close tmp
}

function mycopy($s1, $s2) {
    $path = pathinfo($s2);
    if (!file_exists($path['dirname'])) {
        mkdir($path['dirname'], 0777, true);
    }
    if (!copy($s1, $s2)) {
        return false;
    }
    return true;
}

function deleteOldXimages($pid, $main = false) {
    if ($pid <= 0) {
        return false;
    }
    $db = new Database();
    // delete old image if there are
    if ($main == true) {
        $sql = "SELECT * FROM ximages WHERE (main is null or main=0) and productid=" . $pid;
    } else {
        $sql = "SELECT * FROM ximages WHERE productid=" . $pid;
    }
    //echo "deleteoldximages1=".$sql."<br>";
    $db->query($sql);
    while ($db->nextRecord()) {
        $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $db->Record['path'];
        unlink($path);
    }
    if ($main == true) {
        $sql = "DELETE FROM ximages WHERE main<>1 and productid=" . $pid;
    } else {
        $sql = "DELETE FROM ximages WHERE productid=" . $pid;
    }
    //echo  "deleteoldximages2=".$sql."<br>";
    $db->query($sql);
    $db->close();
    //exit;
}

function saveXimage($source, $pid, $isMain = 0) {
    if ($pid <= 0) {
        return false;
    }
    $db = new Database();
    $ext = strtolower(substr($source, strrpos($source, '.') + 1));
    $folder = $_SERVER["DOCUMENT_ROOT"] . "/img/products/" . date("Y", time()) . "/" . date("F", time());
    mkdir($folder, 0777, true);
    $dest = $folder . "/" . generateRandomString(20) . "." . $ext;
    $f = str_replace($_SERVER["DOCUMENT_ROOT"], "", $dest);

    $sql = "INSERT INTO ximages (productid,path,type,main) VALUES (" . $pid . ",'" . $f . "',1,$isMain)";
    // echo"normal=". $sql."<br>";
    $db->query($sql);
    $source = str_replace(" ", "%20", $source);
    $dest = str_replace(" ", "%20", $dest);
    if (copy($source, $dest) == 0) {
        echo "error copy xtra images: " . $source . " -> " . $dest . "<br>";
    }
    if ($isMain) {
        $sql = "UPDATE products SET  image='" . $f . "' WHERE id=" . $pid;
        $db->query($sql);
    }
    unlink($source);
    $db->close();
}

function saveAllXimages($grab, $pid) {
    if ($grab == "") {
        return false;
    }
    $db = new Database();

    $curl = curl_init();

    // Setup headers - I used the same headers from Firefox version 2.0.0.6
    // below was split up because php.net said the line was too long. :/
    $header[0] = "Accept: text/html,application/php,text/php, text/javascript, */*; q=0.01";
    $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 3000";
    $header[] = "X-Requested-With: Googlebot";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";
    $header[] = "Referer: https://www.google.com/";

    curl_setopt($curl, CURLOPT_URL, $grab);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Googlebot/2.1 (+http://www.google.com/bot.html)');
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);

    $html = curl_exec($curl); // execute the curl command
    curl_close($curl); // close the connection
    // grab pictures size >800 pixels

    $html = str_replace("//d2ujflorbtfzji", "https://d2ujflorbtfzji", $html);
    $parse = parse_url($grab);
    $host = $parse['host'];
    // extract images
    $r = '/([-a-z0-9_\/:.]+\.(jpg|jpeg|png))/i';

    $doc = new DOMDocument();
    @$doc->loadHTML($html);

    $tags = $doc->getElementsByTagName('img');

    // extract additionnal images
    foreach ($tags as $tag) {
        $url = $tag->getAttribute('src');
        if (strpos($url, "http") === false) {
            $url = "http://" . $host . $url;
        }
        $url = str_replace(" ", "%20", $url);
        $previous = "";
        $source = "";
        $picinfo = getimagesize($url);
        if ($picinfo !== false && strlen($url) < 256) {

            $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
            $x = $picinfo[0];
            $y = $picinfo[1];
            //echo "full path=".$url."   x=".$x."   y=".$y."<br>";
            if ($x >= 300 && $y >= $x / 2) {
                //echo "full path=".$url."<br>";

                $folder = $_SERVER["DOCUMENT_ROOT"] . "/img/products/" . date("Y", time()) . "/" . date("F", time());
                mkdir($folder, 0777, true);
                $dest = $folder . "/" . generateRandomString(20) . "." . $ext;
                $f = str_replace($_SERVER["DOCUMENT_ROOT"], "", $dest);
                $dest = strtok($dest, '?');

                if (copy($url, $dest) == 0) {
                    echo "error copy xtra images 3: " . $source . " -> " . $dest . "<br>";
                }
                //echo "OK: $previous -> ".$url." -> ".$dest."<br>";
                //echo "<img src='".$url."'><br>";
                //echo "<br>previous: ".$previous ." - dest: ". $dest;
                //echo "<br>".sha1_file ( $previous )." - ".sha1_file ( $dest);
                if (sha1_file($previous) !== sha1_file($dest)) {

                    $sql = "INSERT INTO ximages (productid,path,type,main) VALUES (" . $pid . ",'" . $f . "',1,0)";
                    //echo "saveall=".$sql."<br>";
                    $db->query($sql);
                }
                $previous = $dest;
            }
        }
    }
    $db->close();
}

function filesize64($file) {
    static $iswin;
    if (!isset($iswin)) {
        $iswin = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
    }

    static $exec_works;
    if (!isset($exec_works)) {
        $exec_works = (function_exists('exec') && !ini_get('safe_mode') && @exec('echo EXEC') == 'EXEC');
    }

    // try a shell command
    if ($exec_works) {
        $cmd = ($iswin) ? "for %F in (\"$file\") do @echo %~zF" : "stat -c%s \"$file\"";
        @exec($cmd, $output);
        if (is_array($output) && ctype_digit($size = trim(implode("\n", $output)))) {
            return $size;
        }
    }

    // try the Windows COM interface
    if ($iswin && class_exists("COM")) {
        try {
            $fsobj = new COM('Scripting.FileSystemObject');
            $f = $fsobj->GetFile(realpath($file));
            $size = $f->Size;
        } catch (Exception $e) {
            $size = null;
        }
        if (ctype_digit($size)) {
            return $size;
        }
    }

    // if all else fails
    return filesize($file);
}

function rrmdir($path) {
    // Open the source directory to read in files
    $i = new DirectoryIterator($path);
    foreach ($i as $f) {
        if ($f->isFile()) {
            unlink($f->getRealPath());
        } else {
            if (!$f->isDot() && $f->isDir()) {
                rrmdir($f->getRealPath());
            }
        }
    }
    //rmdir($path);
}

function is_32bit() {
    return PHP_INT_SIZE === 4;
}

/*
 * get youtube video ID from URL
 *
 * @param string $url
 * @return string Youtube video id or FALSE if none found.
 */

function youtube_id_from_url($url) {
    $pattern = '%^# Match any youtube URL
                (?:https?://)?  # Optional scheme. Either http or https
                (?:www\.)?      # Optional www subdomain
                (?:             # Group host alternatives
                  youtu\.be/    # Either youtu.be,
                | youtube\.com  # or youtube.com
                  (?:           # Group path alternatives
                    /embed/     # Either /embed/
                  | /v/         # or /v/
                  | /watch\?v=  # or /watch\?v=
                  )             # End path alternatives.
                )               # End host alternatives.
                ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
                $%x';
    $result = preg_match($pattern, $url, $matches);
    if ($result) {
        return $matches[1];
    }
    return false;
}

function categoriesCountNumbers($sql) {
    $dbx = new Database();
    $dbx1 = new Database();
    $db = new Database();
    $db->query($sql);
    while ($db->next()) {
        // update the number of items per category
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
            $sql = "SELECT count(*) AS total FROM products WHERE  " . $sql_section . $sql_category . $sql_subcategory . $sql_subcategory2 . $sql_subcategory3;
            $dbx1->query($sql);
            $dbx1->single();
            $nbr = $dbx1->rs['total'];
            $sql = "UPDATE qcategories SET nbr=" . $nbr . " WHERE id=" . $cid;
            $dbx->query($sql);
        }
    }
    $db->close();
    $dbx->close();
    $dbx1->close();
}

function RandomToken($length = 32) {
    if (!isset($length) || intval($length) <= 8) {
        $length = 32;
    }
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($length));
    }
    if (function_exists('mcrypt_create_iv')) {
        return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($length));
    }
}

function Salt() {
    return substr(strtr(base64_encode(hex2bin(RandomToken(32))), '+', '.'), 0, 44);
}

function get_string_between($string, $start, $end) {
    $string = 'x' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) {
        return '';
    }
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function setupBlockUI() {
    global $settings;
    if ($settings->setupblockui == 0) {
        ?>
        <script>

            // Keep all submit buttons from working and block the page. Will be unblock when the topbar ils loaded.
            $.blockUI({
                message: '<div><img width="80" height="80" align="middle" src="/img/preloader3.gif"/></div>',
                /*                css: { backgroundColor: '#f00', color: '#fff',width: '100%',textAlign: 'center'  },*/
                overlayCSS: {backgroundColor: '#012', color: '#fff', textAlign: 'center', cursor: 'wait'},
                css: {
                    padding: 0,
                    margin: 'auto',
                    width: '30%',
                    left: '35%',
                    textAlign: 'center',
                    color: '#fff',
                    border: '0 solid #aaa',
                    backgroundColor: 'transparent',
                    top: '40%',
                    cursor: 'wait'
                }
            });

            $.blockUI.defaults.message = '<div style="margin:auto;text-align:center"><img width="80" height="80" align="center" src="/img/preloader3.gif"/></div>';
            $.blockUI.defaults.css = "padding:0,margin:'auto',width:'30%',left:'35%',textAlign:'center',color:'#fff',border:'0 solid #aaa',backgroundColor:'transparent',top:'40%',cursor:'wait'";


        </script>
        <?php
    }
    $settings->setupblockui = true;
}

function logtofile($log_msg, $filename = "") {
    if ($filename == "") {
        $log_filename = __DIR_ . "/log/" . basename($_SERVER["SCRIPT_FILENAME"]) . ".txt";
    } else {
        $log_filename = __DIR__ . "/log/" . $filename;
    }
//echo $log_filename."<br>";exit;
    file_put_contents($log_filename, $log_msg . "\n", FILE_APPEND);
}

function searchfilteradjust() {
    global $settings, $theme;

    switch ($settings->qsection) {
        case COMPATIBLE_UNITY_ID:
            $settings->search_compatible_unity = "on";
            setSession("search_compatible_unity", $settings->search_compatible_unity);
            break;
        case COMPATIBLE_UE4_ID:
            $settings->search_compatible_ue4 = "on";
            setSession("search_compatible_ue4", $settings->search_compatible_ue4);
            break;
        case COMPATIBLE_PDF_ID:
            $settings->search_compatible_pdf = "on";
            setSession("search_compatible_pdf", $settings->search_compatible_pdf);
            break;
        case COMPATIBLE_MOVIE_ID:
            $settings->search_compatible_movie = "on";
            setSession("search_compatible_movie", $settings->search_compatible_movie);
            break;
        case COMPATIBLE_TEXTURE_ID:
            $settings->search_compatible_texture = "on";
            setSession("search_compatible_texture", $settings->search_compatible_texture);
            break;
        case COMPATIBLE_3DMODEL_ID:
            $settings->search_compatible_3dmodel = "on";
            setSession("search_compatible_3dmodel", $settings->search_compatible_3dmodel);
            break;
        case COMPATIBLE_APPLICATION_ID:
            $settings->search_compatible_app = "on";
            setSession("search_compatible_app", $settings->search_compatible_app);
            break;
        case COMPATIBLE_AUDIO_ID:
            $settings->search_compatible_sound = "on";
            setSession("search_compatible_sound", $settings->search_compatible_sound);
            break;
    }
    if ($theme->name == "ue4club") {
        $settings->search_compatible_ue4 = "on";
    }
    if ($theme->name == "unity3dclub") {
        $settings->search_compatible_unity = "on";
    }
}

function get_dateid() {
    return intval(date("Ymd", time()));
}

function get_dateid_from_time($t) {
    return intval(date("Ymd", $t));
}

function getUserIpAddr() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function a_number_format($number_in_iso_format, $no_of_decimals = 3, $decimals_separator = '.', $thousands_separator = '', $digits_grouping = 3) {
    // Check input variables
    if (!is_numeric($number_in_iso_format)) {
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$number_in_iso_format is not a number.");
        return false;
    }
    if (!is_numeric($no_of_decimals)) {
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$no_of_decimals is not a number.");
        return false;
    }
    if (!is_numeric($digits_grouping)) {
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$digits_grouping is not a number.");
        return false;
    }


    // Prepare variables
    $no_of_decimals = $no_of_decimals * 1;


    // Explode the string received after DOT sign (this is the ISO separator of decimals)
    $aux = explode(".", $number_in_iso_format);
    // Extract decimal and integer parts
    $integer_part = $aux[0];
    $decimal_part = isset($aux[1]) ? $aux[1] : '';

    // Adjust decimal part (increase it, or minimize it)
    if ($no_of_decimals > 0) {
        // Check actual size of decimal_part
        // If its length is smaller than number of decimals, add trailing zeros, otherwise round it
        if (strlen($decimal_part) < $no_of_decimals) {
            $decimal_part = str_pad($decimal_part, $no_of_decimals, "0");
        } else {
            $decimal_part = substr($decimal_part, 0, $no_of_decimals);
        }
    } else {
        // Completely eliminate the decimals, if there $no_of_decimals is a negative number
        $decimals_separator = '';
        $decimal_part = '';
    }

    // Format the integer part (digits grouping)
    if ($digits_grouping > 0) {
        $aux = strrev($integer_part);
        $integer_part = '';
        for ($i = strlen($aux) - 1; $i >= 0; $i--) {
            if ($i % $digits_grouping == 0 && $i != 0) {
                $integer_part .= "{$aux[$i]}{$thousands_separator}";
            } else {
                $integer_part .= $aux[$i];
            }
        }
    }

    $processed_number = "{$integer_part}{$decimals_separator}{$decimal_part}";
    return $processed_number;
}

function APICall()
{
$get_data = callAPI('GET', 'https://api.crex24.com/v2/public'.$user['User']['customer_id'], false);
$response = json_decode($get_data, true);
$errors = $response['response']['errors'];
$data = $response['response']['data'][0];    
    
}

function callAPI($method="GET", $url, $data){
   $curl = curl_init();
   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "GET":
         curl_setopt($curl, CURLOPT_GET, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;         
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }
   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'APIKEY: 434471fb-60b9-457c-b42c-e718fe177951',
      'Content-Type: application/json',
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}
?>