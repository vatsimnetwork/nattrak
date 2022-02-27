<?php

$dotenv = \Dotenv\Dotenv::createImmutable('../');
$dotenv->load();

$host = $_ENV('MYSQL_HOST');
$db = $_ENV('MYSQL_DB');
$username = $_ENV('MYSQL_USER');
$pass = $_ENV('MYSQL_PASS');

$dsn = "mysql:host=$host;dbname=$db";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $username, $pass, $opt);
?>