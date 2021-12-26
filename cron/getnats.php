<?php

// Define the files we need

// source file
$src = 'https://api.flightplandatabase.com/nav/NATS';
// nats file
$nats = '../nats.json';

// Check if the file exists, if it does, we delete it
if (file_exists($nats)) {
    unlink($nats);
    echo 'File deleted <br />';
}

// Copy new nats file
if (!copy($src, $nats)) {
    echo 'Failed to copy';
} else {
    echo 'Copy successful';
}

// Now we handle the database

require __DIR__ . '/../includes/connection.php';

///////////////////////////////////////////////////////

// Empty the current table
$sql = 'TRUNCATE TABLE `nats`';
$statement = $pdo->prepare($sql);
$statement->execute();

///////////////////////////////////////////////////////

// sort the JSON
$json = file_get_contents('../nats.json');
$decoded = json_decode($json);

foreach ($decoded as $nat) {

    $route = '';
    foreach($nat->route->nodes as $node) {
        $route .= $node->ident . ' ';
    }

    $route = trim($route);

    $validFrom = strtotime($nat->validFrom);
    $validFrom = date('Y-m-d H:i:s', $validFrom);

    $validTo = strtotime($nat->validTo);
    $validTo = date('Y-m-d H:i:s', $validTo);

    // Insert into dB
    $sql = 'INSERT INTO nats (identifier, validFrom, validTo, route) VALUES (?,?,?,?)';
    $pdo->prepare($sql)->execute([$nat->ident, $validFrom, $validTo, $route]);
}

///////////////////////////////////////////////////////
