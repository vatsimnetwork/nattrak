<?php

require '../includes/connection.php';

// Empty the table to prepare for the new data, this gets rid of any controller BELOW admin
$stmt = $pdo->prepare('DELETE FROM `controllers` WHERE `permission` < 3');
$stmt->execute([]);

$insert_sql = 'INSERT INTO controllers (name, cid, permission) VALUES (?,?,?)';

/////////// Shanwick Cron ///////////
$json = file_get_contents('https://www.vatsim.uk/api/validations?position=EGGX');
$decoded = json_decode($json);

foreach ($decoded->validated_members as $validated_member) {

    $stmt = $pdo->prepare('SELECT count(*) FROM controllers WHERE cid = ?');
    $stmt->execute([$validated_member->id]);
    $count = $stmt->fetchColumn();

    if ($count == '0') {
        $pdo->prepare($insert_sql)->execute([$validated_member->name, $validated_member->id, '2']);
    }
}

/////////// Gander Cron ///////////
$json = file_get_contents('https://ganderoceanic.com/api/roster');
$decoded = json_decode($json, true);

// loop thru the data
foreach ($decoded as $member) {

    if($member['certification'] == 'certified') {

        $stmt = $pdo->prepare('SELECT count(*) FROM controllers WHERE cid = ?');
        $stmt->execute([$member['cid']]);
        $count = $stmt->fetchColumn();
        if ($count == '0') {

            $api_json = file_get_contents('https://api.vatsim.net/api/ratings/' . (string)$member['cid']);
            $api_decoded = json_decode($api_json, true);

            $name = $api_decoded['name_first'] . ' ' . $api_decoded['name_last'];

            $pdo->prepare($insert_sql)->execute([$name, $member['cid'], '2']);
        }
    }
}
