<?php

require '../includes/connection.php';

$stmt = $pdo->prepare('DELETE FROM `sessions` WHERE logintime < NOW() - INTERVAL 24 HOUR');
$stmt->execute([]);
