<?php

function hasPerm($cid)
{
    require 'connection.php';

    $stmt = $pdo->prepare('SELECT * FROM controllers WHERE cid = ?');
    $stmt->execute([$cid]);
    $row = $stmt->fetch();

    return $row['permission'];
}

function getNAT()
{

    $cookie_name = 'nattrak';
    if (isset($_COOKIE[$cookie_name])) {
        require 'connection.php';

        $sid = $_COOKIE[$cookie_name];

        $stmt = $pdo->prepare('SELECT * FROM sessions WHERE sid = ?');
        $stmt->execute([$sid]);
        $row = $stmt->fetch();

        return $row['nat'];
    }

}

function setStatus($status,$cid,$track)
{

    require 'connection.php';

    $sql = 'UPDATE sessions SET rep_status=?,nat=? WHERE cid=?';
    $pdo->prepare($sql)->execute([$status, $track, $cid]);
}


function getStatus()
{

    $cookie_name = 'nattrak';
    if (isset($_COOKIE[$cookie_name])) {
        require 'connection.php';

        $sid = $_COOKIE[$cookie_name];

        $stmt = $pdo->prepare('SELECT * FROM sessions WHERE sid = ?');
        $stmt->execute([$sid]);
        $row = $stmt->fetch();

        return $row['rep_status'];
    }

}

function setNAT($nat, $cid)
{
    require 'connection.php';

    $sql = 'UPDATE sessions SET nat=? WHERE cid=?';
    $pdo->prepare($sql)->execute([$nat, $cid]);
}

function setCID($cid)
{
    require 'connection.php';

    // Set the data into the dB

    $sql = 'INSERT INTO sessions (cid) VALUES (?)';
    $pdo->prepare($sql)->execute([$cid]);
}

function setSession($cid, $name, $sessionid, $logintime)
{
    require 'connection.php';

    // Set the Cookie with the Session ID
    $cookie_name = 'nattrak';
    setcookie($cookie_name, $sessionid, time() + 86400, '/'); // 86400 = 1 day

    // Set the data into the dB

    $sql = 'INSERT INTO sessions (cid, name, sid, logintime) VALUES (?,?,?,?)';
    $pdo->prepare($sql)->execute([$cid, $name, $sessionid, $logintime]);
}

function getSession()
{
    $cookie_name = 'nattrak';
    if (isset($_COOKIE[$cookie_name])) {
        require 'connection.php';

        $sid = $_COOKIE[$cookie_name];
        $stmt = $pdo->prepare('SELECT * FROM sessions WHERE sid = ?');
        $stmt->execute([$sid]);
        $row = $stmt->fetch();

        return $row['sid'];
    }
}

function getCID($sid)
{
    require 'connection.php';

    $stmt = $pdo->prepare('SELECT * FROM sessions WHERE sid = ?');
    $stmt->execute([$sid]);
    $row = $stmt->fetch();

    return $row['cid'];
}

function getUser($cid)
{
    require 'connection.php';

    $stmt = $pdo->prepare('SELECT * FROM controllers WHERE cid = ?');
    $stmt->execute([$cid]);
    $row = $stmt->fetch();

    return $row['name'];
}

function destroySes($sid)
{
    require 'connection.php';

    setcookie('NatTrak', '', time() - 86400);

    $stmt = $pdo->prepare('DELETE FROM sessions WHERE sid = ?');
    $stmt->execute([$sid]);
}

function isLoggedIn($sid)
{
    require 'connection.php';

    $stmt = $pdo->prepare("SELECT count(*) FROM sessions WHERE sid = ?");
    $stmt->execute([$sid]);
    $count = $stmt->fetchColumn();
    if ($count == '1') {

      $stmt = $pdo->prepare('SELECT * FROM sessions WHERE sid = ?');
      $stmt->execute([$sid]);
      $row = $stmt->fetch();

      // $cid = $row['cid'];
      return $cid;

    }
}

function isPilotConnectedToVATSIM($cid)
{

    $json = file_get_contents('https://data.vatsim.net/v3/vatsim-data.json');
    $decoded = json_decode($json);

    foreach ($decoded->pilots as $user) {
        if ($user->cid == $cid) {
            return true;
        }
    }
}


function isControllerOceanic($cid)
{

    $json = file_get_contents('https://data.vatsim.net/v3/vatsim-data.json');
    $decoded = json_decode($json);

    foreach ($decoded->controllers as $user) {
        if ($user->cid == $cid && (substr($user->callsign, 0, 4) == "EGGX" || substr($user->callsign, 0, 4) == "CZQX" || substr($user->callsign, 0, 4) == "CZQM" || substr($user->callsign, 0, 4) == "CZQO" || substr($user->callsign, 0, 4) == "NAT_")) {
            return true;
        }
    }
    return false;
}



function oceanicCallsign($cid)
{
    $json = file_get_contents('https://data.vatsim.net/v3/vatsim-data.json');
    $decoded = json_decode($json);

    foreach ($decoded->controllers as $user) {
        if ($user->cid == $cid) {
            return substr($user->callsign, 0, 4);
        }
    }

    return '';
}
