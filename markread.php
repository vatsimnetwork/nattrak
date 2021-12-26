<?php

require 'header.php';

if (isset($_POST['id'])) {
    $report_id = $_POST['id'];

    if (hasPerm($cid) >= 2) {
        try {
            $stmt = $pdo->prepare('UPDATE `position_reports` SET `read`=true WHERE `id`=?');
            $stmt->execute([$report_id]);
        } catch (Exception $e) {
            echo 'Something went wrong.';
        }
    }
}
