<?
require 'header.php';

if (isset($_POST['delete'])) {
    try {
        $id = $_POST['id'];
        $stmt = $pdo->prepare('DELETE FROM newsnotams WHERE id = ?');
        $stmt->execute([$id]);
    } catch (Exception $e) {
    }
}

if (isset($_POST['edit'])) {
    try {
        $title = $_POST['title'];
        $body = $_POST['body'];
        $id = $_POST['id'];
        $sql = 'UPDATE newsnotams SET title=?, body=? WHERE id=?';
        $pdo->prepare($sql)->execute([$title, $body, $id]);
    } catch (Exception $e) {
    }
}

if (isset($_POST['add'])) {
    try {
        $title = $_POST['title'];
        $body = $_POST['body'];
        $time = date("Y-m-d H:i:s");
        $user = getUser($cid);
        $type = "news";

        $sql = 'INSERT INTO newsnotams (type, title, body, user, date) VALUES (?,?,?,?,?)';
        $pdo->prepare($sql)->execute([$type, $title, $body, $user, $time]);
    } catch (Exception $e) {
    }
}


if (hasPerm($cid) > "3") {
?>

<script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=89dwf362wyahw0veky4kbevgh4suvd44knf4ipxfteslif1w"></script>
<script>
tinymce.init({
  selector: '#content',
  plugins: 'print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help autosave',
  toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat spellchecker'
});
</script>
<script>
tinymce.init({
  selector: '#article',
  plugins: 'print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help autosave',
  toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat spellchecker'
});
</script>

<!-- <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'"> -->

        <div class="row inside shadow pb-5">
          <div class="col-lg-12 bg-light rounded pt-3 pb-3">
            </body>

            <p class="header">
              Manage News
            </p><hr />

            <!-- <p class="small">
              Permissions Levels <br />0/1 - Pilot | 2 - Controller | 3 - Admin | 4 - Root
            </p> -->



            <table class="table table-borderless table-striped">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">Article</th>
                  <th scope="col">Publish Date</th>
                  <th scope="col">Published By</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>

                <?php
                $stmt = $pdo->query('SELECT * FROM newsnotams');
                while ($row = $stmt->fetch()) {
                    ?>

                <!-- Table Row -->

                <tr>
                  <td>
                    <a href ="" data-toggle="modal" data-target="#editarticle<?php echo $row['id']; ?>"><i class="far fa-edit"></i></a>
                  </td>
                  <td><?php echo $row['title']; ?></td>
                  <td><?php echo $row['date']; ?></td>
                  <td><?php echo $row['user']; ?></td>
                  <td>

                      <form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST">
                      <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                      <button class="btn btn-danger btn-sm" name ="delete" type="submit">Delete Article</button>
                      </form>

                  </td>
                </tr>

                <!-- End Table Row -->


                <!-- Start Edit Modal -->
                <form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST">
                  <div class="modal fade" id="editarticle<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLabel">Edit News Story - <?php echo $row['title']; ?></h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">

                          <input class="form-control" type="text" name="title" value="<?= $row['title']; ?>">
                          <br />

                          <textarea id="content" name="body">
                            <?= htmlspecialchars_decode(stripslashes($row['body'])); ?>

                          </textarea>

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

                <?php
                }
                ?>
              </tbody>
            </table>

            <br />


            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addarticlemodal">
              Add New Article
            </button>

              <!-- Start Add Controller Modal -->
              <form action ="<?php echo $_SERVER['PHP_SELF'] ?>" method ="POST">
                  <div class="modal fade" id="addarticlemodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Add New News Article</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">

                                  <input class="form-control" type="text" name="title" placeholder="Article Title">
                                  <br />

                                  <textarea id="article" name="body">


                          </textarea>

                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button class="btn btn-primary" name ="add" type="submit">Add News</button>
                              </div>
                          </div>
                      </div>
                  </div>
              </form>
              <!-- End Add Controller Modal -->



          </div>
        </div>

      <? } ?>

<? require ('footer.php') ?>
