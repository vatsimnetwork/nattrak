<?php

require '../includes/connection.php';

$stmt = $pdo->prepare('DELETE FROM `clearances` WHERE request_time < NOW() - INTERVAL 2 HOUR');
$stmt->execute([]);
