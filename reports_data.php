<?php
require 'includes/functions.php';
require 'includes/connection.php';

$nat = getNAT();

$stmt = $pdo->prepare("SELECT * FROM position_reports WHERE track = ? ORDER BY `nat_reporting_at_fl`");
$stmt->execute([$nat]);
while ($row = $stmt->fetch())
{
?>

<tr>
  <td><a href="edit_report.php?id=<?php echo $row['id']; ?>"><i class="far fa-edit"></i></a></td>
  <td><?php echo $row['track']; ?></td>
  <td><?php echo $row['callsign']; ?></td>
  <td><?php echo $row['selcal']; ?></td>
  <td><?php echo $row['type']; ?></td>
  <td><?php echo $row['nat_entry']; ?></td>
  <td><?php echo $row['nat_entry_eta']; ?></td>
  <td><?php echo $row['controller_restriction']; ?></td>
  <td><?php echo $row['nat_reporting_fix']; ?></td>
  <td><?php echo $row['nat_reporting_at_time']; ?></td>
  <td><?php echo $row['nat_reporting_at_fl']; ?></td>
  <td><?php echo $row['nat_reporting_at_mach']; ?></td>
  <td><?php echo $row['nat_next_fix']; ?></td>
  <td><?php echo $row['nat_next_at_time']; ?></td>
  <td><?php echo $row['nat_next_thereafter']; ?></td>
  <td><?php echo $row['request_fl']; ?></td>
  <td><?php echo $row['request_mach']; ?></td>
  <td><?php echo $row['other']; ?></td>
  <td><?php echo $row['destination']; ?></td>
  <td>
    <input id="read" type="checkbox" onclick="markAsRead(<?php echo $row['id']; ?>)" <?php if ($row['read']) {echo 'checked';} ?>/>
  </td>
  <td><a href="deletereport.php?id=<?php echo $row['id']; ?>"><i class="far fa-trash-alt"></i></a></td>
</tr>

<script>
    function markAsRead(id) {
        // Get the checkbox
        var checkBox = document.getElementById("read");

        // If the checkbox is checked, display the output text
        if (checkBox.checked === true){
            $.ajax({
                type: "POST",
                url: 'markread.php',
                data: { id: id },
            });
        }

        if (checkBox.checked === false){
            $.ajax({
                type: "POST",
                url: 'unmarkread.php',
                data: { id: id },
            });
        }
    }
</script>

<?php
}
?>
