<?php

require '../includes/connection.php';

$stmt = $pdo->prepare('DELETE FROM `position_reports` WHERE report_time < NOW() - INTERVAL 8 HOUR');
$stmt->execute([]);
