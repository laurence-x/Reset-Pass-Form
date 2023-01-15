<?php

require $_SERVER['DOCUMENT_ROOT'] . '/php/app.php';

if (
    array_key_exists('c', $_POST)
    && isset($_POST['c'])
    && !empty($_POST['c'])
    && $_POST['c'] !== null
    && $_POST['c'] !== ''
) {
    /* check if cookie q with the encoded random number,
    previously set while on recovery page, exists */
    if (cset('q')) {
        $cq = $_COOKIE['q'];

        /* clean the encoded->hashed->reencoded random number >
        sent in the email link as h > grabbed from the url by js >
        decoded & sent through post */
        $hurl = clean($_POST['c'], false);

        /* compare encoded random number set as cookie,
        with the hash from url link sent by email */
        $match = vhash($cq, $hurl);

        // cookie matches the hash from url link sent by email
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
                // url hash exist in db col: ulog > check if link still valid

                $conn = mysqli_connect($sv, $un, $pw, $db);
                if (mysqli_connect_errno()) {
                    $ms = 'ERROR: No conn to db "' . $db
                        . '"-' . mysqli_connect_error();
                    goto end;
                } else {
                    // get unix from db row where ulog w hashed link from em
                    $res = mysqli_query(
                        $conn,
                        "SELECT unix FROM "
                            . $tn . " WHERE ulog='" . $hurl . "'"
                    );
                    $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
                    $uunix = $row['unix'];
                }

                // compare nt w unix from db:
                if ((($nt - $uunix) / 60) > 10) {
                    // to see if hash reset link from email is stil valid
                    $ms = "reset link expired";
                    $jres = "ex";
                    goto end;
                } else {
                    $jres = "ag"; //~ leave so. important. nothing else
                    goto end;
                }
            } else {
                $ms = "Url hash: no db ulog match";
                $jres = "rd";
                goto end;
            }
        } else {
            $ms = "Cookie and hash dont match";
            $jres = "rd";
            goto end;
        }
    } else {
        $ms = "Cookie q not set or expired";
        $jres = "nc";
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
