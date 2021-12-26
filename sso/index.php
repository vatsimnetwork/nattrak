<?php
require '../environment.php';
require '../includes/functions.php';
require '../includes/connection.php';
/**
 *  Integration of VATSIM Connect and NATTRAK
 */

/**
 *  Check that the user does not already have a current session
 */

if (!is_null(getSession())) {
    header('location: https://nattrak.vatsim.net');
}
/**
 * IF they have come back from VATSIM Connect with a authentication code, use it to gain an access_token.
 */
if (isset($_GET['code'])) {
    /**
     *  Utilise the 'code' returned from VATSIM Connect to request an access_token
     */
    $fields = array(
        'client_id' => urlencode(getenv('CONNECT_CLIENT_ID')),
        'client_secret' => urlencode(getenv('CONNECT_CLIENT_SECRET')),
        'grant_type' => urlencode('authorization_code'),
        'redirect_uri' => urlencode(getenv('CONNECT_REDIRECT_URI')),
        'code' => urlencode($_GET['code']),
    );
    $fields_string = "";
    foreach ($fields as $key => $value) {
        $fields_string .= $key.'='.$value.'&';
    }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, getenv('CONNECT_OAUTH_TOKEN'));
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $data = json_decode($result);
    curl_close($ch);
    if ($httpcode != 200) {
        echo('Error. Cannot get VATSIM access_token');
        die();
    }


    /**
     * Utilise the Access Token to make a request to the VATSIM Connect API to get the users information.
     */
    $ch = curl_init(getenv('CONNECT_USER_API'));
    $authorization = "Authorization: Bearer ".$data->access_token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', $authorization));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    $result = json_decode($result);
    curl_close($ch);

    if ($httpcode != 200) {
        echo('Error. Cannot get VATSIM User Information');
        die();
    }
    /**
     * Verify that the required information has been returned.
     * Set the session of the user and redirect them to the NATTRAK index page.
     */
    try {
        if (!isset($result->data->cid, $result->data->personal->name_first, $result->data->personal->name_last)) {
            echo('Please ensure that you allow NATTRAK access to your full name when authorising at VATSIM. <br> <a href="' . getenv('BASE_URL') . '/sso/index.php" type="button">Login Again</a>');
            die();
        }
        $cid = $result->data->cid;
        $name = $result->data->personal->name_first.' '.$result->data->personal->name_last;
        $time = time();
        $logintime = date('Y-m-d H:i:s', time());
        $sessionid = $cid.$time;

        setSession($cid, $name, $sessionid, $logintime);
    } catch (Exception $e) {
        echo('Error: Cannot set session');
    }

    header('Location: ' . getenv('BASE_URL') . '/index.php');
} /**
 * Compile the required information that VATSIM Connect required and redirect the user there.
 */
else {
    $query = http_build_query([
        'client_id' => getenv('CONNECT_CLIENT_ID'),
        'redirect_uri' => getenv('CONNECT_REDIRECT_URI'),
        'response_type' => 'code',
        'scope' => getenv('CONNECT_SCOPE'),
    ]);

    header("Location: ".getenv('CONNECT_AUTH')."?".$query);
}
