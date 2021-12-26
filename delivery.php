<? require 'header.php';

if(isset($_POST['submit'])) {

  $status = $_POST['status'];
  $track = $_POST['track'];
  setStatus($status,$cid,$track);
  header('Location: delivery.php');
}

$status = getStatus();
if($status == null) {
    $status = 'pending';
}
$nat = getNAT();
if($nat == null) {
    $nat = 'All';
}

if (isControllerOceanic($cid) == true || hasPerm($cid) >= "3") {

?>

<!-- <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'"> -->

        <div class="row inside shadow pb-5">
          <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            </body>

            <p class="header">
              Oceanic Clearance Delivery
            </p><hr />

            <form action ="<? echo $_SERVER['PHP_SELF'] ?>" method ="POST">
              <div class="row d-flex justify-content-center pt-3">


                <div class="col-5">
                  <select class="custom-select shadow-sm" name="track">
                    <option selected disabled>Select Track</option>
                      <option value="All" <?php echo $nat == 'All' ? 'selected' : '' ?>>All</option>
                      <option value="RR" <?php echo $nat == 'RR' ? 'selected' : '' ?>>RR</option>
                      <?php
                          $stmt = $pdo->query('SELECT * FROM `nats`');
                          while ($row = $stmt->fetch()) {
                          $to = $row['validTo'];
                          $fromdt = strtotime($row['validFrom']) - 5400;
                          $from = date('Y-m-d H:i:s', $fromdt);

                          if($to > date("Y-m-d H:i:s") && date("Y-m-d H:i:s") > $from) {
                          ?>
                              <option value="<?php echo $row['identifier']; ?>" <?php echo $nat == $row['identifier'] ? 'selected' : '' ?>><?php echo $row['identifier']; ?></option>
                          <?php
                        }} ?>
                  </select>
                </div>
                <div class="col-5">
                  <select class="custom-select shadow-sm" name="status">
                    <option selected disabled>Select Clearance Status</option>
                        <option value="pending" <?php echo $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="cleared" <?php echo $status == 'pending' ? 'selected' : '' ?>>Cleared</option>
                        <option value="all" <?php echo $status == 'all' ? 'selected' : '' ?>>All</option>
                  </select>
                </div>

                <div class="col-2">
                  <button class="btn btn-outline-primary btn-md shadow-sm" name ="submit" type="submit">View Clearances</button>
                </div>


              </div>
            </form>

            <p class="lead mt-4">
              <?php echo ucfirst($status); ?> Clearances
            </p>

            <table class="table table-striped table-borderless">
              <thead>
                <tr>
                  <th scope="col" colspan="2">&nbsp;</th>
                  <th scope="col" colspan="1" style="background-color: #cdf0ff">Aircraft</th>
                  <th scope="col" colspan="2" style="background-color: #fff4d1">NAT/Route</th>
                  <th scope="col" colspan="6" style="background-color: #f9e4ff"></th>

                  <th scope="col">&nbsp;</th>
                </tr>
              </thead>
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">Status</th>
                  <th scope="col">Callsign</th>
                  <th scope="col">Track</th>
                  <th scope="col">Route</th>
                  <th scope="col">Entry Point</th>
                  <th scope="col">ETA</th>
                  <th scope="col">FL</th>
                  <th scope="col">Mach</th>
                  <th scope="col">TMI</th>
                  <th scope="col">Request Time</th>
                </tr>
              </thead>

              <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

              <script type="text/javascript">

                  $(document).ready(function(){
                    refreshTable();
                  });

                  function refreshTable(){
                      $('#results').load('clearance_data.php', function(){
                         setTimeout(refreshTable, 5000);
                      });
                  }
              </script>

              <tbody id="results">
              </tbody>

            </table>






          </div>
        </div>

      <? } ?>

<? require ('footer.php') ?>
