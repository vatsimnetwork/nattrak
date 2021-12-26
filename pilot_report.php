<? require 'header.php';




if (isset($_POST['submit'])) {
    $stmt = $pdo->prepare('SELECT count(*) FROM position_reports WHERE cid = ?');
    $stmt->execute([$cid]);
    $count = $stmt->fetchColumn();

    // $count = "0";

    if ($count == '0') {
        $time = date('Hi e');
        $via = 'Pilot';

        try {
            $sql = 'INSERT INTO position_reports (track, cid, callsign, selcal, type, nat_entry, nat_entry_eta, destination, request_mach, request_fl, report_time, via) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
            $pdo->prepare($sql)->execute([$_POST['track'], $cid, $_POST['callsign'], $_POST['selcal'], $_POST['type'], $_POST['nat_entry'], $_POST['nat_entry_eta'], $_POST['destination'], $_POST['request_mach'], $_POST['request_fl'], $time, $via]);
            ?>

      <div class="row py-3 px-4">
        <div class="col-lg">

          <div class="alert alert-success alert-dismissible fade show" role="alert">
            Position report recieved!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

        </div>
      </div>

      <?php
        } catch (Exception $e) {
            ?>

      <div class="row py-3 px-4">
        <div class="col-lg">

          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Something went wrong! We didn't recieve your report!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

        </div>
      </div>

      <?php
        }
    } else {
        $time = date('Hi e');
        $via = 'Pilot';

        try {
            $sql = 'UPDATE position_reports SET track=?, selcal=?, type=?, nat_reporting_fix=?, nat_reporting_at_time=?, nat_reporting_at_fl=?, nat_reporting_at_mach=?, nat_next_fix=?, nat_next_at_time=?, nat_next_thereafter=?, destination=?, request_mach=?, request_fl=?, report_time=?, via=?, `read`=false WHERE cid=?';
            $pdo->prepare($sql)->execute([$_POST['track'], $_POST['selcal'], $_POST['type'], $_POST['nat_reporting_fix'], $_POST['nat_reporting_at_time'], $_POST['nat_reporting_at_fl'], $_POST['nat_reporting_at_mach'], $_POST['nat_next_fix'], $_POST['nat_next_at_time'], $_POST['nat_next_thereafter'], $_POST['destination'], $_POST['request_mach'], $_POST['request_fl'], $time, $via, $cid]);
            ?>

      <div class="row py-3 px-4">
        <div class="col-lg">

          <div class="alert alert-success alert-dismissible fade show" role="alert">
            Position report recieved!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

        </div>
      </div>

      <?php
        } catch (Exception $e) {
            ?>

      <div class="row py-3 px-4">
        <div class="col-lg">

          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Something went wrong! We didn't recieve your report!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

        </div>
      </div>

      <?php
        }
    }
}

?>

<!-- <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'"> -->

        <div class="row inside shadow pb-5">
          <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            </body>

            <p class="header">
              Submit Pilot Report
            </p><hr />

            <?php
              $json = file_get_contents('http://us.data.vatsim.net/vatsim-data.json');
              $decoded = json_decode($json, true);

              foreach ($decoded['clients'] as $user) {

                // $cid = $user['member']['cid'];
                // $callsign = $user['callsign'];
                // $type = $user['plan']['aircraft'];
                // $altitude = $user['altitude'];
                // $speed = $user['speed'];
                // $arrival = $user['plan']['arrival'];
                //
                // echo $cid . "<br />";
                // echo $callsign . "<br />";
                // echo $type . "<br />";
                // echo $altitude . "<br />";
                // echo $speed . "<br />";
                // echo $arrival . "<br /><br /><br />";


                // $decoded[$callsign]['CID'];






                  if ($user['cid'] == $cid) {
                      // echo $objects['Callsign'];?>

                  <form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST" class="needs-validation" novalidate>

                    <p class="lead">
                    Aircraft Data
                    </p>
                    <div class="form-row">

                      <div class="col-md mb-3">

                        <select class="custom-select" id="validationCustom01" name="track" required>
                          <option selected disabled>Select Track</option>
                          <?php
                          $stmt = $pdo->query('SELECT DISTINCT identifier FROM `nats` WHERE validFrom >= now() - INTERVAL 1 DAY');
                      while ($row = $stmt->fetch()) {
                          ?>
                              <option value="<?php echo $row['identifier']; ?>"><?php echo $row['identifier']; ?></option>
                          <?php
                      } ?>
                        </select>
                        <!-- <input style="text-transform: uppercase;" maxlength="4" type="text" id="validationCustom01" name ="icao" class="form-control" placeholder="Track" autofocus required>
                        <label>Track</label> -->
                        <div class="invalid-feedback">
                          Select your NAT Track
                        </div>
                        <div class="valid-feedback">
                          Looks good!
                        </div>

                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom01" name="callsign" placeholder="Callsign" value="<?php echo $user['callsign']; ?>" maxlength="10" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom02" name="selcal" placeholder="SELCAL" maxlength="5" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom03" name="type" placeholder="Aircraft Type & Equipment" value="<?php echo $user['planned_aircraft']; ?>" maxlength="10" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>
                    </div>


                    <?php

                    $stmt = $pdo->prepare('SELECT count(*) FROM position_reports WHERE cid = ?');
                      $stmt->execute([$cid]);
                      $count = $stmt->fetchColumn();

                      // $count = "0";

                      if ($count == '0') {
                          ?>

                    <p class="lead pt-3">
                    NAT Entry
                    </p>
                    <div class="form-row">

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom04" name="nat_entry" placeholder="NAT Entry Fix" maxlength="5" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom05" name="nat_entry_eta" placeholder="NAT Entry ETA" maxlength="4" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>
                    </div>

                    <?php
                      } elseif ($count >= '1') {
                          ?>

                    <p class="lead pt-3">
                    NAT Reporting
                    </p>
                    <div class="form-row">

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom06" name="nat_reporting_fix" placeholder="Reporting At Fix" maxlength="5" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom07" name="nat_reporting_at_time" placeholder="Reporting at Time" maxlength="4" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom08" name="nat_reporting_at_fl" placeholder="Reporting at Flight Level" value="<?php echo substr($user['altitude'], 0, 2).'0'; ?>" maxlength="3" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom09" name="nat_reporting_at_mach" placeholder="Reporting at Mach" value="<?php echo  substr(($user['groundspeed'] / 666.739), 0, 4); ?>" maxlength="4" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                    </div>

                    <p class="lead pt-3">
                    NAT Next Fix Estimation
                    </p>
                    <div class="form-row">

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom10" name="nat_next_fix" placeholder="Next Reporting Fix" maxlength="5" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom11" name="nat_next_at_time" placeholder="Next Reporting Fix ETA" maxlength="4" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom12" name="nat_next_thereafter" placeholder="Next Reporting Fix Thereafter" maxlength="5" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                    </div>

                    <?php
                      } ?>



                    <p class="lead pt-3">
                    Destination & Requests
                    </p>
                    <div class="form-row">

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom13" name="destination" placeholder="Destination" value="<?php echo $user['planned_destairport']; ?>" maxlength="4" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom14" name="request_mach" placeholder="Requested Mach" maxlength="4">
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom15" name="request_fl" placeholder="Requested Flight Level" maxlength="3">
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                    </div>

                    <br />

                    <button class="btn btn-primary" name ="submit" type="submit">Submit Position Report</button>
                  </form>

                  <?php
                } //else { echo 'Nothing Here'; break; } // ?>

                <!-- <p class="lead">
                It appears you have nothing to report! 1
                </p> -->

                <?php //break;
              //}
            }

?>


          <script>
          // Example starter JavaScript for disabling form submissions if there are invalid fields
          (function() {
            'use strict';
            window.addEventListener('load', function() {
              // Fetch all the forms we want to apply custom Bootstrap validation styles to
              var forms = document.getElementsByClassName('needs-validation');
              // Loop over them and prevent submission
              var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                  if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                  }
                  form.classList.add('was-validated');
                }, false);
              });
            }, false);
          })();
          </script>

          </div>
        </div>

<? require ('footer.php') ?>
