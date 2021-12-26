<?php require 'header.php';

if (isControllerOceanic($cid) == true || hasPerm($cid) >= "3") {

    if (isset($_GET['id'])) {
        $report_id = $_GET['id'];

        if (isset($_POST['submit'])) {
            $time = date('Y-m-d H:i:s');
            $via = "$cid";
            $rep_status ="cleared";

            $callsign = oceanicCallsign($cid);
            if($callsign = '') {
                $callsign = 'EGGX';
            }

            try {
                $sql = 'UPDATE clearances SET flight_level=?, mach=?, nat=?, random_route=?, entry_fix=?, estimating_time=?, tmi=?, rep_status=?, controller=?, controller_cid=?, clearance_time=?, freestyle=? WHERE id=?';
                $pdo->prepare($sql)->execute([$_POST['flight_level'], $_POST['mach'], $_POST['nat'], $_POST['random_route'], $_POST['entry_fix'], $_POST['estimating_time'], $_POST['tmi'], $rep_status, $callsign, $via, $time, $_POST['freestyle'], $report_id]); ?>

        <div class="row py-3 px-4">
          <div class="col-lg">

            <div class="alert alert-success alert-dismissible fade show" role="alert">
              Clearance Issued!
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

          </div>
        </div>

        <?php

        header('Location: edit_clearance.php?id=$report_id');
            } catch (Exception $e) {
                echo $e; ?>

        <div class="row py-3 px-4">
          <div class="col-lg">

            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              Something went wrong! We didn't recieve your clearance!
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

          </div>
        </div>

      <?php
            }
        } ?>


        <div class="row inside shadow pb-5">
          <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            </body>

            <p class="header">
              Manage Clearance
            </p><hr />

            <?php
            $stmt = $pdo->prepare('SELECT * FROM clearances WHERE id = ?');
            $stmt->execute([$report_id]);
            while ($row = $stmt->fetch()) {
            ?>

                    <form action ="<?php echo $_SERVER['PHP_SELF'].'?id='.$row['id']; ?>" method ="POST" class="needs-validation" novalidate>

                      <p class="lead">
                      Clearance Data
                      </p>


                      <div class="form-row">

                        <div class="col-md mb-3">
                          <input type="text" class="form-control" id="validationCustom01" name="callsign" placeholder="Callsign" value="<?php echo $row['callsign']; ?>" maxlength="10" required>
                          <div class="valid-feedback">
                            Looks good!
                          </div>
                        </div>


                        <div class="col-md mb-3">

                          <select class="custom-select" id="validationCustom01" name="nat">
                              <?php
                              $stmt = $pdo->query('SELECT * FROM `nats`');
                              while ($trackrow = $stmt->fetch()) {
                              $to = $trackrow['validTo'];
                              $fromdt = strtotime($trackrow['validFrom']) - 5400;
                              $from = date('Y-m-d H:i:s', $fromdt);

                              if($to > date("Y-m-d H:i:s") && date("Y-m-d H:i:s") > $from) {
                                  ?>
                                  <option value="<?php echo $trackrow['identifier']; ?>" <?php echo $trackrow['identifier'] == $row['nat'] ? 'selected' : '' ?>><?php echo $trackrow['identifier']; ?></option>
                                  <?php
                              }} ?>
                          </select>

                          <div class="invalid-feedback">
                            Select NAT Track
                          </div>
                          <div class="valid-feedback">
                            Looks good!
                          </div>

                        </div>

                        <div class="col-md mb-3">
                          <input type="text" class="form-control" id="validationCustom02" name="random_route" placeholder="Random Route" value="<?php echo $row['random_route']; ?>" maxlength="50">
                          <div class="valid-feedback">
                            Looks good!
                          </div>
                        </div>

                      </div>


                      <div class="form-row">

                        <div class="col-md mb-3">
                          <input type="text" class="form-control" id="validationCustom04" name="entry_fix" placeholder="NAT Entry Fix" value="<?php echo $row['entry_fix']; ?>" maxlength="5" required>
                          <div class="valid-feedback">
                            Looks good!
                          </div>
                        </div>

                        <div class="col-md mb-3">
                          <input type="text" class="form-control" id="validationCustom05" name="estimating_time" placeholder="NAT Entry ETA" value="<?php echo $row['estimating_time']; ?>" maxlength="4" required>
                          <div class="valid-feedback">
                            Looks good!
                          </div>
                        </div>





                        <div class="col-md mb-3">
                          <input type="text" class="form-control" id="validationCustom08" name="flight_level" placeholder="Flight Level" value="<?php echo $row['flight_level']; ?>" maxlength="3" required>
                          <div class="valid-feedback">
                            Looks good!
                          </div>
                        </div>

                        <div class="col-md mb-3">
                          <input type="text" class="form-control" id="validationCustom09" name="mach" placeholder="Mach" value="<?php echo $row['mach']; ?>" maxlength="4" required>
                          <div class="valid-feedback">
                            Looks good!
                          </div>
                        </div>


                        <div class="col-md mb-3">
                          <input type="text" class="form-control" id="validationCustom10" name="tmi" placeholder="TMI" value="<?php echo $row['tmi']; ?>" maxlength="5" required>
                          <div class="valid-feedback">
                            Looks good!
                          </div>
                        </div>

                        </div>


<div class="form-row">

                        <div class="col-md mb-6">
                          <input type="text" class="form-control" id="validationCustom10" name="freestyle" placeholder="Controller Comments or Restrictions" value="<?php echo $row['freestyle']; ?>" maxlength="500">
                          <div class="valid-feedback">
                            Looks good!
                          </div>
                        </div>


                      </div>

                      <br />

                      <button class="btn btn-primary" name ="submit" type="submit">Issue Clearance</button>
                    </form>

                    <br><hr>

            <? $stmt = $pdo->prepare('SELECT * FROM clearances WHERE id = ?');
            $stmt->execute([$report_id]);
            while ($row = $stmt->fetch()) { ?>

                <?php if ($row['status'] == 'cleared') { ?>
                    <?php if ($row['nat'] == 'RR') { ?>
                        .msg <?php echo $row['callsign']; ?> <?php echo $row['controller'] == 'EGGX' ? 'Shanwick' : 'Gander'; ?> clears you to <?php echo $row['destination']; ?> via Random Routing; <?php echo $row['random_route'] ?>. From <?php echo $row['entry_fix']; ?> maintain Flight Level <?php echo $row['flight_level']; ?>, Mach <?php echo $row['mach']; ?>. <?php echo $row['freestyle']; ?>
                    <?php }else{ ?>
                        .msg <?php echo $row['callsign']; ?> <?php echo $row['controller'] == 'EGGX' ? 'Shanwick' : 'Gander'; ?> clears you to <?php echo $row['destination']; ?> via Track <?php echo $row['nat']; ?>, from <?php echo $row['entry_fix']; ?> maintain Flight Level <?php echo $row['flight_level']; ?>, Mach <?php echo $row['mach']; ?>. <?php echo $row['freestyle']; ?>
                    <?php } ?>
            <?php
                }
                $nat = $row['nat'];
                $fl = $row['flight_level'];
                }
            ?>
                    <br><hr>

                    <table class="table table-striped table-borderless">
              <thead>
                <tr>
                  <th scope="col">&nbsp;</th>
                  <th scope="col" colspan="1" style="background-color: #cdf0ff">Aircraft</th>
                  <th scope="col" colspan="2" style="background-color: #fff4d1">NAT/Route</th>
                  <th scope="col" colspan="6" style="background-color: #f9e4ff"></th>

                  <th scope="col">&nbsp;</th>
                </tr>
              </thead>
              <thead>
                <tr>
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

                    <?php



            $stmt = $pdo->prepare("SELECT * FROM clearances WHERE  id != ? AND nat = ? order by estimating_time asc");
            $stmt->execute([$report_id, $nat]);
            while ($row = $stmt->fetch()) {

              ?>



              <tr>
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
  <td></td>
</tr>



            <?




              };


            ?>

</table>

            <?php
                }
            }
            ?>



            <!-- SELECT * FROM clearances WHERE estimating_time <= NOW() - INTERVAL 15 MINUTE OR estimating_time >= NOW() - INTERVAL 15 MINUTE -->








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

      <?php
} ?>

<?php require('footer.php') ?>
