<?php

require 'header.php';

if (isControllerOceanic($cid) == true || hasPerm($cid) >= "2") {

if (isset($_GET['id'])) {
    $report_id = $_GET['id'];

    if (hasPerm($cid) >= 2) {
        try {
            $stmt = $pdo->prepare('DELETE FROM clearances WHERE id = ?');
            $stmt->execute([$report_id]);

            header('Location: delivery.php');
        } catch (Exception $e) {
            echo 'Something went wrong.';
        }
    }
}

}
