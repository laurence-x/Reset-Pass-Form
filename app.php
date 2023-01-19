<?php

setlocale(LC_ALL, 'US');
date_default_timezone_set("America/Los_Angeles");

header("Content-Type:text/html; charset=UTF-8");
mb_internal_encoding('UTF-8');
$utf_set = ini_set('default_charset', 'utf-8');
if (!$utf_set) {
    throw new Exception('ensure utf-8 is set default_charset');
}
mb_http_output('UTF-8');

header("Pragma:no-cache");
header("Expires:Mon,26 Jul 1997 05:00:00 GMT");
header("Last-Modified:" . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control:no-store, no-cache, must-revalidate, max-age=0");

/* ---------------------------------- VARS ---------------------------------- */

$ntl = microtime(true);
$nt = strstr($ntl, '.', true);
$tm = date("m/d h:i:sA"); // -> 06/27 10:13PM

$cua = "mozilla/5.0 (compatible;googlebot/2.1)";
$block = array('CN', 'RU', 'IR', 'KP', 'UA');

$uag = $_SERVER['HTTP_USER_AGENT'];
$ip = $_SERVER['REMOTE_ADDR'];

// used to write visitor data in log
if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $rq = false;
} else {
    $rq = $_SERVER['HTTP_X_REQUESTED_WITH'];
}

$jres = "e"; // standards
$ms = false;
$uex = false;
$pm = false;
$em = false;

/* ---------------------------------- PATHS --------------------------------- */

$sa = $_SERVER['SERVER_ADDR']; // -> 142.11.192.51 or 127.0.0.1
$hs = $_SERVER['REQUEST_SCHEME']; // -> https || http

$dmo = $_SERVER['SERVER_NAME']; // -> www.domain.com or localhost
$dm = mb_substr($dmo, mb_strpos($dmo, '.') + 1); // -> domain.com or localhost

$wa = $hs . "://" . $dm; // on real server -> https://www.domain.com
if ($dm === "localhost") {
    $wa = $hs . "://" . $dm . ":3000"; // VSC App on port 3000
} // on local -> http://localhost:3000

$bf = basename(__FILE__) . "/"; // -> app.php/
$ps = $_SERVER["PHP_SELF"]; // $_SERVER['SCRIPT_NAME'] -> File name: /app.php
$p = $_SERVER['REQUEST_URI']; // -> /php/vic.php /index.php?p=contact
$rt = $_SERVER['DOCUMENT_ROOT'] . "/"; // -> /home/XXX_USER/public_html/
//~ add to htaccess error_log path, where XXX_USER

/* ---------------------------------- LOGS ---------------------------------- */

$lg = $rt . "logs.log";
if (!file_exists($lg)) {
    $fp = fopen($lg, 'a');
    fwrite($fp, PHP_EOL);
    fclose($fp);
    shell_exec("chmod 755 $lg");
}
error_reporting(E_ALL);
ini_set('ignore_repeated_errors', true);
ini_set('display_errors', true);
ini_set('log_errors', true);
ini_set('error_log', $lg);
ini_set('log_errors_max_len', 1024);

/* ----------------------------------- DB ----------------------------------- */

$conn = false;
$sv = "localhost";
if ($dm === $sv) {
    $un = "root";
    $pw = "";
} else {
    $un = "USERNAME";
    $pw = "PASSWORD";
}

//~ User
if ($dm === $sv) {
    $db = "users";
} else {
    $db = "uiczvggi_Users";
}
$tn = "users"; // table to work with

//~ Create db & table
// crdb($sv, $un, $pw, $db);
// $carr = [
//     'user',
//     'email',
//     'password',
//     'unix',
//     'ulog',
//     'trial',
//     'end',
// ];
// crtb($sv, $un, $pw, $db, $tn, $carr);

/* --------------------------------- CONTACT -------------------------------- */

$tel = "+1(323)325.5950";
$ae = "Info@" . $dm; // from server email
$re = "lpemailx@gmail.com"; // reply to email
$from = "From:$ae \r\nReply-To:$re";

/* -------------------------------- FUNCTIONS ------------------------------- */

require $_SERVER['DOCUMENT_ROOT'] . "/php/fns.php";
