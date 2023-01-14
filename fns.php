<?php

/* ---------------------------------- LOGS ---------------------------------- */

function lg($lg, $p, $tm, $ms)
{
    $msx = "Mess type: " . gettype($ms) . "\n" . $ms;
    $data = "\n" . $tm . " -> " . $p . "\n" . $msx . PHP_EOL;
    file_put_contents($lg, $data . file_get_contents($lg));
} // lg($lg,$p,$tm,$ms);

/* ----------------------------------- GET ---------------------------------- */

function req($r)
{
    if (
        !isset($_GET[$r]) || $_GET[$r] == null
        || trim($_GET[$r] == '') || empty($_GET[$r])
    ) {
        return false;
    } else {
        return $_GET[$r];
    }
} // $r is .php?r= // if (req('r')=="x"){}

/* --------------------------------- COOKIES -------------------------------- */

//~ SET COOKIE
function dur($dur)
{
    $opt = array(
        // void=valid on whole domain, else: xxx.com xxx.com/xxx folder.xxx.com
        'domain' => '',
        // '/' means for entire domain, else empty '' valid only for that path
        'path' => '/',
        'expires' => time() + $dur,
        // if true, no js access, only through php
        'httponly' => false,
        // if true, only https allowed to set
        'secure' => true,
        'samesite' => 'Lax', // None || Lax || Strict  => no 3rd party
    );
    return $opt;
} // setcookie('cnm',$cvl,dur(60*60));

//~ CHECK IF COOKIE IS SET
function cset($c)
{
    if (
        !isset($_COOKIE[$c])
        || (isset($_COOKIE[$c])
            && (mb_strlen(trim($_COOKIE[$c])) < 1
                || trim($_COOKIE[$c]) == ""))
        || $_COOKIE[$c] == null
        || empty($_COOKIE[$c])
    ) {
        return false;
    } else {
        return true;
    }
} // check if set: if (cset('name')) { echo 'ok'; } else { echo 'no'; }

//~ DELETE A COOKIE
function delc($c)
{
    unset($_COOKIE[$c]);
} // delete a cookie: delc($c);

//~ DELETE ALL COOKIES
function dallc()
{
    foreach ($_COOKIE as $cn => $cv) {
        setcookie($cn, null, -1, '/');
    }
} // delete all cookies: dallc();

/* ---------------------------------- VARS ---------------------------------- */

//~ CLEAN A VARIABLE
function clean($var, $act)
{
    // remove line breaks
    $var = str_replace(array("\n", "\r\n", "\r", "\t", ""), '', $var);
    $var = preg_replace(
        " / [ ^ a - z0 - 9_ <> \\s!@#$%^&*()+={}\\[\\]|\\/:;\\'?.,®-]/i",
        '',
        $var
    ); // leaves only keyboard chars

    $lo = strtolower($var);
    if ($act == "lo") {
        $var = $lo;
    }
    if ($act == "fl") {
        $var = ucfirst($lo);
    }
    if ($act == "up") {
        $var = strtoupper($lo);
    }
    return trim($var);
} // $y=clean($x,false);

//~ SPECIAL ENCODE/DECODE A VARIABLE
function spe($var, $act)
{
    if ($act == 'e') {
        $var = addslashes($var); // add \
        $var = htmlspecialchars($var, ENT_QUOTES); // to &lt; &quot;
        $var = urlencode($var); // to %5C
    }
    if ($act == 'd') {
        $var = urldecode($var);
        $var = htmlspecialchars_decode($var, ENT_QUOTES);
        $var = stripslashes($var);
    }
    return $var;
} // $y=spe($x,'e');

//~ ENCRYPT/DECRYPT A VARIABLE
function enc($var, $act)
{
    $skey = 'das3452gasga';
    $sx = 'sfad95452fad';
    $mtd = "AES-256-CBC";
    $key = hash('sha256', $skey);
    $res = 'task error';
    $iv = mb_substr(hash('sha256', $sx), 0, 16);
    if ($act == 'e') {
        $res = base64_encode(openssl_encrypt($var, $mtd, $key, 0, $iv));
    }
    if ($act == 'd') {
        $res = openssl_decrypt(base64_decode($var), $mtd, $key, 0, $iv);
    }
    return $res;
} // $y=enc($x,'e');

//~ HASH A VARIABLE
function xhash($var)
{
    $hash = password_hash($var, PASSWORD_DEFAULT);
    return $hash;
} // $y=xhash($x);

//~ VERIFY A VARIABLE HASH
function vhash($var, $dbh)
{
    if (password_verify($var, $dbh)) {
        return true;
    } else {
        return false;
    }
} // $y=vhash($var,$dbh); if ($y){ true or false }

/* ----------------------------------- DB ----------------------------------- */

//~ CREATE A DB
function crdb($sv, $un, $pw, $db)
{
    $conn = mysqli_connect($sv, $un, $pw);
    mysqli_query($conn, "CREATE DATABASE " . $db);
    mysqli_close($conn);
}

//~ CREATE A TABLE
function crtb($sv, $un, $pw, $db, $tn, $carr)
{
    $conn = mysqli_connect($sv, $un, $pw, $db);
    $cols = implode(' VARCHAR(255) NOT NULL,', $carr);
    $cols = mb_substr($cols, 0, strrpos($cols, 'end'));
    mysqli_query($conn, 'CREATE TABLE IF NOT EXISTS ' . $tn .
        ' (id int(5) NOT NULL AUTO_INCREMENT,' . $cols .
        'date TIMESTAMP,PRIMARY KEY(id))');
    mysqli_close($conn);
}

//~ CHECK DB ROW FOR A ENTRY
function ckex($sv, $un, $pw, $db, $sel, $tn, $whr, $val)
{
    $conn = mysqli_connect($sv, $un, $pw, $db);
    if (
        $res = mysqli_query(
            $conn,
            "SELECT " . $sel . " FROM " . $tn .
            " WHERE " . $whr . "='" . $val . "'"
        )
    ) {
        $ex = false;
        if (mysqli_num_rows($res) > 0) {
            $ex = true;
        }
    }
    mysqli_close($conn);
    return $ex;
}