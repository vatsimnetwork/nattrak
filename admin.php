<?
require 'header.php';

if (isset($_POST['delete'])) {
    try {
        $id = $_POST['id'];
        $stmt = $pdo->prepare('DELETE FROM controllers WHERE id = ?');
        $stmt->execute([$id]);
    } catch (Exception $e) {
    }
}

if (isset($_POST['edit'])) {
    try {
        $level = $_POST['level'];
        $id = $_POST['id'];
        $sql = 'UPDATE controllers SET permission=? WHERE id=?';
        $pdo->prepare($sql)->execute([$level, $id]);
    } catch (Exception $e) {
    }
}

if (isset($_POST['add'])) {
    try {
        $controller_cid = $_POST['controller_cid'];
        $controller_name = $_POST['controller_name'];
        $level = $_POST['level'];

        $sql = 'INSERT INTO controllers (cid, name, permission) VALUES (?,?,?)';
        $pdo->prepare($sql)->execute([$controller_cid, $controller_name, $level]);
    } catch (Exception $e) {
    }
}

if (hasPerm($cid) >= "3") {
?>

<!-- <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'"> -->

        <div class="row inside shadow pb-5">
          <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            </body>

            <p class="header">
              Manage Users
            </p><hr />

            <!-- <p class="small">
              Permissions Levels <br />0/1 - Pilot | 2 - Controller | 3 - Admin | 4 - Root
            </p> -->



            <table class="table table-borderless table-striped">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">CID</th>
                  <th scope="col">Name</th>
                  <th scope="col">Permission Level</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>

                <?php
                $stmt = $pdo->query('SELECT * FROM controllers');
                while ($row = $stmt->fetch()) {
                    ?>

                <!-- Table Row -->

                <tr>
                  <td>
                    <?php
                    if ($row['permission'] > 3) { } else {
                      ?><a href ="" data-toggle="modal" data-target="#editpermission<?php echo $row['id']; ?>"><i class="far fa-edit"></i></a><?php
                    } ?>
                  </td>
                  <td><?php echo $row['cid']; ?></td>
                  <td><?php echo $row['name']; ?></td>
                  <td>
                  <?php
                  if ($row['permission'] == 0) {
                    echo "Pilot";
                  } elseif ($row['permission'] == 1) {
                    echo "Pilot";
                  } elseif ($row['permission'] == 2) {
                    echo "Controller";
                  } elseif ($row['permission'] == 3) {
                    echo "Administrator";
                  } elseif ($row['permission'] == 4) {
                    echo "Root";
                  }
                  ?>
                  </td>
                  <td>
                    <?php
                    if ($row['permission'] > 3) {
                    } else {
                        ?>
                      <form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST">
                      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                      <button class="btn btn-danger btn-sm" name ="delete" type="submit">Delete Controller</button>
                      </form>
                      <?php
                    } ?>
                  </td>
                </tr>

                <!-- End Table Row -->


                <!-- Start Edit Modal -->
                <form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST">
                  <div class="modal fade" id="editpermission<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Edit Controller Permissions - <?php echo $row['cid']; ?></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">

                          <select class="custom-select" name="level">
                            <option selected disabled>
                              <?php
                              if ($row['permission'] == 0) {
                                echo "Pilot";
                              } elseif ($row['permission'] == 1) {
                                echo "Pilot";
                              } elseif ($row['permission'] == 2) {
                                echo "Controller";
                              } elseif ($row['permission'] == 3) {
                                echo "Administrator";
                              } elseif ($row['permission'] == 4) {
                                echo "Root";
                              }
                              ?>
                            </option>
                            <option value="2">Controller</option>
                            <option value="3">Admin</option>
                          </select>

                          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button class="btn btn-primary" name ="edit" type="submit">Save Changes</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
                <!-- End Edit Modal -->

                <!-- Start Add Controller Modal -->
                <form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST">
                  <div class="modal fade" id="addcontrollermodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Add New Controller to NAT-TRAK</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">

                          <input class="form-control" type="text" name="controller_cid" placeholder="Controller CID">
                          <br />
                          <input class="form-control" type="text" name="controller_name" placeholder="Controller Name">
                          <br />

                          <select class="custom-select" name="level">
                            <option selected disabled>Permission Level</option>
                            <option value="2">Controller</option>
                            <option value="3">Administrator</option>
                          </select>

                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button class="btn btn-primary" name ="add" type="submit">Add Controller</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
                <!-- End Add Controller Modal -->

                <?php
                }
                ?>
              </tbody>
            </table>

            <br />


            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addcontrollermodal">
              Add New Controller
            </button>



          </div>
        </div>

      <? } ?>

<? require ('footer.php') ?>
