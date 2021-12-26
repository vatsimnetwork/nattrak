<?php

$host = getenv('MYSQL_HOST');
$db = getenv('MYSQL_DB');
$username = getenv('MYSQL_USER');
$pass = getenv('MYSQL_PASS');

$dsn = "mysql:host=$host;dbname=$db";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $username, $pass, $opt);
?>