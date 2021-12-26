<? require 'header.php';

if(isset($_POST['submit'])) {

$nat = $_POST['track'];
setNAT($nat,$cid);
}

$nat = getNAT();

if (isControllerOceanic($cid) == true || hasPerm($cid) >= "3") {

?>

<!-- <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'"> -->

        <div class="row inside shadow pb-5">
          <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            </body>

            <p class="header">
              NAT Position Reports
            </p><hr />

            <form action ="<? echo $_SERVER['PHP_SELF'] ?>" method ="POST">
              <div class="row d-flex justify-content-center pt-3">


                <div class="col-6">
                  <select class="custom-select shadow-sm" name="track">
                    <option selected disabled>Select NAT Track</option>
                    <?php
                    $stmt = $pdo->query('SELECT DISTINCT identifier FROM `nats` WHERE validFrom >= now() - INTERVAL 1 DAY');
                    while ($row = $stmt->fetch())
                    {
                    ?>
                        <option value="<?php echo $row['identifier']; ?>"><?php echo $row['identifier']; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>

                <div class="col-2">
                  <button class="btn btn-outline-primary btn-md shadow-sm" name ="submit" type="submit">Select Track</button>
                </div>


              </div>
            </form>

            <p class="lead mt-4">
              NAT Track: NAT <?= $nat ?>
            </p>

            <table class="table table-striped table-borderless">
              <thead>
                <tr>
                  <th scope="col">&nbsp;</th>
                  <th scope="col" colspan="4" style="background-color: #cdf0ff">Aircraft</th>
                  <th scope="col" colspan="3" style="background-color: #fff4d1">NAT Entry</th>
                  <th scope="col" colspan="4" style="background-color: #f9e4ff">Reporting</th>
                  <th scope="col" colspan="2" style="background-color: #d2ffe0">Estimating</th>
                  <th scope="col" style="background-color: #ffe5cd">Next</th>
                  <th scope="col" colspan="2" style="background-color: #ffcddc">Request</th>
                  <th scope="col" colspan="2" style="background-color: #ececec">Comments</th>
                  <th scope="col">&nbsp;</th>
                </tr>
              </thead>
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">Track</th>
                  <th scope="col">Callsign</th>
                  <th scope="col">SELCAL</th>
                  <th scope="col">Type</th>
                  <th scope="col">Point</th>
                  <th scope="col">ETA</th>
                  <th scope="col">Restrict</th>
                  <th scope="col">Point</th>
                  <th scope="col">At</th>
                  <th scope="col">FL</th>
                  <th scope="col">Mach</th>
                  <th scope="col">Point</th>
                  <th scope="col">ETA</th>
                  <th scope="col">Point</th>
                  <th scope="col">FL</th>
                  <th scope="col">Mach</th>
                  <th scope="col">Other Details</th>
                  <th scope="col">Destination</th>
                  <th scope="col">Read</th>
                </tr>
              </thead>

              <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->

              <script type="text/javascript">

                  $(document).ready(function(){
                    refreshTable();
                  });

                  function refreshTable(){
                      $('#results').load('reports_data.php', function(){
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
