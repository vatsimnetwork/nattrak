<?php require 'header.php'; ?>

<img src="img/newsandnotams.png" class="img-fluid pb-3" />

<?php
$stmt = $pdo->query('SELECT * FROM newsnotams ORDER BY date DESC');
while ($row = $stmt->fetch()) {
  $date = new DateTime($row['date']); ?>

  <p class="lead"><?php echo $row['title']; ?></p>
  <p>
    <?php echo $row['body']; ?>
    <!-- ... <small>[Read More...]</small> -->
  </p>

  <small class="float-left text-light">Posted <?php echo $date->format('F jS, Y'); ?> by <?php echo $row['user']; ?> </small>
  <br />
  <br />

<?php
}
?>

<?php require 'footer.php'; ?>
