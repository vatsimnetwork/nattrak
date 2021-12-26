<?php
require 'includes/functions.php';
require 'includes/connection.php';

$clearances = [];

$stmt = $pdo->prepare("SELECT * FROM clearances WHERE rep_status = ?");
$stmt->execute(['cleared']);

while ($row = $stmt->fetch())
{
    $clearances[] = [
        'callsign' => $row['callsign'],
        'status' => 'CLEARED',
        'nat' => $row['nat'],
        'fix' => $row['entry_fix'],
        'level' => $row['flight_level'],
        'mach' => $row['mach'],
        'estimating_time' => $row['estimating_time'],
        'clearance_issued' => $row['clearance_time'],
        'extra_info' => $row['freestyle'],
    ];
}

$stmt = $pdo->prepare("SELECT * FROM clearances WHERE rep_status = ?");
$stmt->execute(['pending']);

while ($row = $stmt->fetch())
{
    $clearances[] = [
        'callsign' => $row['callsign'],
        'status' => 'PENDING',
        'nat' => $row['nat'],
        'fix' => $row['entry_fix'],
        'level' => $row['flight_level'],
        'mach' => $row['mach'],
        'estimating_time' => $row['estimating_time'],
        'clearance_issued' => null,
        'extra_info' => null,
    ];
}

header("Content-Type: application/json");
echo json_encode($clearances);
