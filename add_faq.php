<?php
require 'include/main_head.php';
$faq_per = ['Create', 'Update', 'Read', 'Delete'];
$lang = loadLanguage('');

if (isset($_GET['id'])) {
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Update', $faq_per)) {



    header('HTTP/1.1 401 Unauthorized');
?>
    <style>
      .loader-wrapper {
        display: none;
      }
    </style>
  <?php
    require 'auth.php';
    exit();
  }
} else {
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Write', $faq_per)) {



    header('HTTP/1.1 401 Unauthorized');
  ?>
    <style>
      .loader-wrapper {
        display: none;
      }
    </style>
<?php
    require 'auth.php';
    exit();
  }
}
?>
<!-- Loader ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper compact-wrapper" id="pageWrapper">
  <!-- Page Header Start-->
  <?php
  require 'include/inside_top.php';
  ?>
  <!-- Page Header Ends                              -->
  <!-- Page Body Start-->
  <div class="page-body-wrapper">
    <!-- Page Sidebar Start-->
    <?php
    require 'include/sidebar.php';
    ?>
    <!-- Page Sidebar Ends-->
    <div class="page-body">
      <div class="container-fluid">
        <div class="page-title">
          <div class="row">
            <div class="col-6">
              <h3>
              <?= $lang['FAQ_Management'] ?>
              </h3>
            </div>
            <div class="col-6">

            </div>
          </div>
        </div>
      </div>
      <!-- Container-fluid starts-->
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-body">
                <?php
                if (isset($_GET['id'])) {
                  $data = $rstate->query("select * from tbl_faq where id=" . $_GET['id'] . "")->fetch_assoc();
                ?>
                  <form method="POST" enctype="multipart/form-data">

                    <div class="form-group mb-3">

                      <label id="basic-addon1">
                      <?= $lang['Enter_Question'] ?>
  
                      </label>

                      <input type="text" class="form-control" placeholder="Enter Question" value="<?php echo $data['question']; ?>" name="question" aria-label="Username" aria-describedby="basic-addon1">

                    </div>

                    <div class="form-group mb-3">

                      <label id="basic-addon1">
                      <?= $lang['Enter_Answer'] ?>
  
                      </label>

                      <input type="text" class="form-control" placeholder="Enter Answer" value="<?php echo $data['answer']; ?>" name="answer" aria-label="Username" aria-describedby="basic-addon1">
                      <input type="hidden" name="type" value="edit_faq" />
                      <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                    </div>


                    <div class="form-group mb-3">

                      <label for="inputGroupSelect01">
                      <?= $lang['Select_Status'] ?>
  
                      </label>

                      <select class="form-control" name="status" id="inputGroupSelect01" required>
                        <option value="">
                        <?= $lang['Choose'] ?>...</option>
                        <option value="1" <?php if ($data['status'] == 1) {
                                            echo 'selected';
                                          } ?>>
                                           <?= $lang['Publish'] ?>
                                          </option>
                        <option value="0" <?php if ($data['status'] == 0) {
                                            echo 'selected';
                                          } ?>>
                                          <?= $lang['Unpublish'] ?>

                                          </option>

                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                    <?= $lang['Edit_FAQ'] ?>
  
                    </button>
                  </form>
                <?php
                } else {
                ?>
                  <form method="POST" enctype="multipart/form-data">

                    <div class="form-group mb-3">

                      <label id="basic-addon1">
                      <?= $lang['Enter_Question'] ?>
  
                      </label>

                      <input type="text" class="form-control" placeholder="Enter Question" name="question" aria-label="Username" aria-describedby="basic-addon1">

                    </div>

                    <div class="form-group mb-3">

                      <label id="basic-addon1">                      <?= $lang['Enter_Answer'] ?>
                      </label>

                      <input type="text" class="form-control" placeholder="Enter Answer" name="answer" aria-label="Username" aria-describedby="basic-addon1">
                      <input type="hidden" name="type" value="add_faq" />
                    </div>


                    <div class="form-group mb-3">

                      <label for="inputGroupSelect01">                      <?= $lang['Select_Status'] ?>
                      </label>

                      <select class="form-control" name="status" id="inputGroupSelect01" required>
                        <option value="">
                        <?= $lang['Choose'] ?>
                        ...</option>
                        <option value="1">                                           <?= $lang['Publish'] ?>
                        </option>
                        <option value="0">                                          <?= $lang['Unpublish'] ?>
                        </option>

                      </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                    <?= $lang['Add_FAQ'] ?>  
                    </button>
                  </form>
                <?php } ?>
              </div>

            </div>


          </div>





        </div>
      </div>
      <!-- Container-fluid Ends-->
    </div>
    <!-- footer start-->

  </div>
</div>
<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
</body>

</html>