<?php

require $_SERVER['DOCUMENT_ROOT'] . '/php/app.php';

if (
    array_key_exists('pass', $_POST)
    && isset($_POST['pass'])
    && !empty($_POST['pass'])
    && $_POST['pass'] !== null
    && $_POST['pass'] !== ''
) {
    /* check if cookie q with the encoded random number,
    previously set while on recovery page, exists */
    if (cset('q')) {
        $cq = $_COOKIE['q'];

        /* clean the encoded->hashed->reencoded random number
        > sent in the email link as h > grabbed from the url by js
        > decoded & sent through post */
        $hurl = clean(req('h'), false);

        /* compare encoded random number set as cookie,
        with the hash from url link sent by email */
        $match = vhash($cq, $hurl);

        // cookie matchs the hash from url link
        if ($match) {
            $umatch = ckex(
                // check if hash from url exists in db ulog column
                $sv,
                $un,
                $pw,
                $db,
                $sel = 'ulog',
                $tn,
                $whr = 'ulog',
                $val = $hurl
            );

            if ($umatch) {
                // url hash exist in db column ulog

                $conn = mysqli_connect($sv, $un, $pw, $db);
                if (mysqli_connect_errno()) {
                    $ms = 'ERROR: No conn to db "' . $db
                        . '"-' . mysqli_connect_error();
                    goto end;
                } else {
                    /* clean, special, encode & hash the pass from input sent
                    through fetch post */
                    $pass = clean($_POST['pass'], false);
                    $pass = spe($pass, 'e');
                    $pass = enc($pass, 'e');
                    $pass = xhash($pass);

                    // update unix db column with actual unix
                    mysqli_query(
                        $conn,
                        "UPDATE " . $tn . " SET unix='"
                            . $nt . "' WHERE ulog ='" . $hurl . "'"
                    );

                    // update password in row where ulog mathes h from url
                    mysqli_query(
                        $conn,
                        "UPDATE " . $tn . " SET password='"
                            . $pass . "' WHERE ulog ='" . $hurl . "'"
                    );

                    // reset/empty ulog column where ulog is the h from url
                    mysqli_query(
                        $conn,
                        "UPDATE " . $tn
                            . " SET ulog='' WHERE ulog ='" . $hurl . "'"
                    );
                }

                // delete q cookie
                if (cset('q')) {
                    delc('q');
                }
                // delete trial cookie
                if (cset('t')) {
                    delc('t');
                }

                $jres = "rs"; //~ reseted: ok
                goto end;
            } else {
                $ms = "Url hash no db ulog match";
                $jres = "rd";
                goto end;
            }
        } else {
            $ms = "Cookie and hash dont match";
            $jres = "rd"; //~ fraud attempt
            goto end;
        }
    } else {
        $ms = "Cookie q not set";
        $jres = "ex";
        goto end;
    }
}

end:

if ($ms) {
    lg($lg, $p, $tm, $ms);
}

if (isset($conn->server_info)) {
    mysqli_close($conn);
}

/* if response is rd, write cookie rd so visitor
gets redirected to google for the next 24h */
if ($jres === "rd") {
    setcookie('rd', '1', dur(60 * 60 * 24));
}

echo $jres;
