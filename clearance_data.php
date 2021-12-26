<?php
require 'includes/functions.php';
require 'includes/connection.php';

$status = getStatus();
if($status == null) {
    $status = 'all';
}
$nat = getNAT();
if($nat == null) {
    $nat = 'All';
}

if($nat == 'All' && $status == 'all') {
    $stmt = $pdo->prepare("SELECT * FROM clearances");
    $stmt->execute();
} else if($nat == 'All' && $status != 'all') {
    $stmt = $pdo->prepare("SELECT * FROM clearances WHERE rep_status = ?");
    $stmt->execute([$status]);
}  else if($nat != 'All' && $status == 'all') {
    $stmt = $pdo->prepare("SELECT * FROM clearances WHERE nat = ?");
    $stmt->execute([$nat]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM clearances WHERE rep_status = ? and nat = ?");
    $stmt->execute([$status, $nat]);
}
while ($row = $stmt->fetch())
{
?>

<tr>
  <td><a href="edit_clearance.php?id=<?php echo $row['id']; ?>"><i class="far fa-edit"></i></a></td>
  <td><?php echo strtoupper($row['rep_status']); ?></td>
  <td><?php echo $row['callsign']; ?></td>
  <td><?php echo $row['nat']; ?></td>
  <td><?php echo $row['random_route']; ?></td>
  <td><?php echo $row['entry_fix']; ?></td>
  <td><?php echo $row['estimating_time']; ?></td>
  <td><?php echo $row['flight_level']; ?></td>
  <td><?php echo $row['mach']; ?></td>
  <td><?php echo $row['tmi']; ?></td>
  <td><?php echo $row['request_time']; ?></td>
  <td><a href="deleteclearance.php?id=<?php echo $row['id']; ?>"><i class="far fa-trash-alt"></i></a></td>
</tr>

<?php
}
?>
