<? require 'header.php';


if (isset($_POST['submit'])) {
    $stmt = $pdo->prepare('SELECT count(*) FROM clearances WHERE cid = ?');
    $stmt->execute([$cid]);
    $count = $stmt->fetchColumn();

    // $count = "0";

    if ($count == '0') {
        $request_time = date('Y-m-d H:i:s');
        $status = "pending";

        $timepost = $_POST['estimating_time'];
        $timeformat = substr_replace($timepost, ':', 2, 0);

        $nat = $_POST['nat'];
        if (! $nat) {
            $nat = 'RR';
        }

        try {
            $sql = 'INSERT INTO clearances (cid, callsign, flight_level, mach, nat, random_route, entry_fix, estimating_time, tmi, rep_status, request_time, destination) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
            $pdo->prepare($sql)->execute([$cid, $_POST['callsign'], $_POST['flight_level'], $_POST['mach'], $nat, strtoupper($_POST['random_route']), strtoupper($_POST['entry_fix']), $timeformat, $_POST['tmi'], $status, $request_time, strtoupper($_POST['destination'])]);
            ?>

      <div class="row py-3 px-4">
        <div class="col-lg">

          <div class="alert alert-success alert-dismissible fade show" role="alert">
            Clearance request recieved!
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
            Something went wrong! You may have requested clearance already or we didn't recieve your report! <?= $e ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

        </div>
      </div>

      <?php
        }
    } else { ?>


      <div class="row py-3 px-4">
        <div class="col-lg">

          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Something went wrong! You may have requested clearance already! <?= $e ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

        </div>
      </div>



    <? }
}

?>

<!-- <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'"> -->

        <div class="row inside shadow pb-5">
          <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            </body>

            <p class="header">
              Oceanic Clearance
            </p><hr />

            <?php
$status = 'pending';

$stmt = $pdo->prepare('SELECT * FROM clearances WHERE cid = ? ');
$stmt->execute([$cid]);
$row = $stmt->fetch();
//$count = $stmt->fetchColumn();

// $count = "0";

//if ($count == '0') {
if ($row['rep_status'] == '') {




              $json = file_get_contents('https://data.vatsim.net/v3/vatsim-data.json');
              $decoded = json_decode($json,);

              foreach ($decoded->pilots as $user) {

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






                  if ($user->cid == $cid) {
                      // echo $objects['Callsign'];?>

                  <form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST" class="needs-validation" novalidate>

                    <p class="lead">
                    Aircraft Data
                    </p>
                    <div class="form-row">


                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom01" name="callsign" placeholder="Callsign" value="<?php echo $user->callsign; ?>" maxlength="10" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>
                    </div>


                    <p class="lead pt-3">
                    Routing
                    </p>
                    <div class="form-row">

                      <div class="col-md mb-3">

                        <select class="custom-select" id="validationCustom01" name="nat" required>
                          <option selected disabled>Select Track</option>
                          <?php
                          $stmt = $pdo->query('SELECT * FROM `nats`');
                        while ($row = $stmt->fetch()) {
                          $to = $row['validTo'];
                          $fromdt = strtotime($row['validFrom']) - 5400;
                          $from = date('Y-m-d H:i:s', $fromdt);
                                           
                          if($to > date("Y-m-d H:i:s") && date("Y-m-d H:i:s") > $from) {
                          ?>
                              <option value="<?php echo $row['identifier']; ?>"><?php echo $row['identifier']; ?></option>
                          <?php
                        }} ?>
                        <option value="RR">RR</option>
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

                    </div>
                    <div class="form-row">




                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom04" name="random_route" placeholder="Random Route" maxlength="50">
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>


                    </div>
                    <div class="form-row">
                      <br>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom044" name="entry_fix" placeholder="NAT Entry Fix" maxlength="5" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom05" name="estimating_time" placeholder="NAT Entry ETA" maxlength="4" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>


                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom06" name="tmi" placeholder="TMI" maxlength="5" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>



                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom08" name="flight_level" placeholder="Flight Level" value="<?php echo substr($user->flight_plan->altitude, 0, 2).'0'; ?>" maxlength="3" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                      <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom09" name="mach" placeholder="Mach" value="<?php echo substr(($user->flight_plan->cruise_tas / 666.739), 0, 4); ?>" maxlength="4" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>

                        <div class="col-md mb-3">
                        <input type="text" class="form-control" id="validationCustom099" name="destination" placeholder="Destination" value="<?php echo $user->flight_plan->arrival; ?>" maxlength="4" required>
                        <div class="valid-feedback">
                          Looks good!
                        </div>
                      </div>



                    </div>

                    </div>

                    <br />

                    <button class="btn btn-primary mt-3" name ="submit" type="submit">Request Oceanic Clearance</button>
                  </form>

                  <?php
                }



                } //else { echo 'Nothing Here'; break; } // ?>

                <!-- <p class="lead">
                It appears you have nothing to report! 1
                </p> -->

                <?php //break;
              } elseif ($row['rep_status'] == 'pending') { ?>



                    <br><p class="lead">
                    We've received your clearance request! Standby for your clearance!
                    </p>

                    <script>
                        setTimeout(function() {
                            location.reload();
                        }, 30000);
                    </script>

                <?



              } elseif ($row['rep_status'] == 'cleared') { ?>



                    <br>
                    <?php if($row['nat'] == 'RR') { ?>
                    <p class="lead">
                        <?php echo $row['controller'] == 'EGGX' ? 'Shanwick' : 'Gander'; ?> clears you to <?php echo $row['destination']; ?> via Random Routing; <?php echo $row['random_route']; ?>. From <?php echo $row['entry_fix']; ?> maintain Flight Level <?php echo $row['flight_level']; ?>, Mach <?php echo $row['mach']; ?>. <?php echo $row['freestyle']; ?>
                    </p>
                    <?php } else { ?>
                    <p class="lead">
                        <?php echo $row['controller'] == 'EGGX' ? 'Shanwick' : 'Gander'; ?> clears you to <?php echo $row['destination']; ?> via Track <?php echo $row['nat']; ?>. From <?php echo $row['entry_fix']; ?>, maintain Flight Level <?php echo $row['flight_level']; ?>, Mach <?php echo $row['mach']; ?>. <?php echo $row['freestyle']; ?>
                    </p>
                    <?php } ?>

                <?

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
