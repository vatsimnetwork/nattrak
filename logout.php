<?php

require 'includes/connection.php';
require 'includes/functions.php';

$sid = getSession();
$cid = getCID($sid);

$stmt = $pdo->prepare('DELETE FROM sessions WHERE sid = ?');
$stmt->execute([$sid]);

setcookie('nattrak', '', 1);
unset($_COOKIE['nattrak']);

header('Location: index.php');
