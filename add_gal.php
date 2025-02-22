<?php
require 'include/main_head.php';
$gal_per = ['Create', 'Update', 'Read', 'Delete'];

if (isset($_GET['id'])) {
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Update', $gal_per)) {



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
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Write', $gal_per)) {



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
                <?= $lang['Gallery_Management'] ?>

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
              <?php
              if (isset($_GET['id'])) {
                $data = $rstate->query("select * from tbl_gallery where id=" . $_GET['id'] . "")->fetch_assoc();
              ?>
                <form method="post" enctype="multipart/form-data">
                  <div class="card-body">


                    <div class="form-group mb-3">
                      <label>
                        <?= $lang['Select_Property'] ?>

                      </label>
                      <select 
                      data-placeholder="<?= $lang['Select_Property'] ?>"

                      name="property" id="property" class="select2-multi-selects form-control" required>
                      <option value="" disabled selected ></option>
                        <?php
                        $zone = $rstate->query("select * from tbl_property where add_user_id=0");
                        while ($row = $zone->fetch_assoc()) {
                        ?>
                          <option value="<?php echo $row['id']; ?>" <?php if ($data['pid'] == $row['id']) {
                                                                      echo 'selected';
                                                                    } ?>><?php echo $row['title']; ?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>

                    <div class="form-group mb-3">
                      <label>
                        <?= $lang['Select_Gallery_Category'] ?>

                      </label>
                      <select 
                      data-placeholder="<?= $lang['Select_Gallery_Category'] ?>"
                      name="galcat" id="galcat" class="select2-multi-cat form-control" required>
                      <option value="" disabled selected ></option>
                        <?php
                        $zone = $rstate->query("select * from tbl_gal_cat");
                        while ($row = $zone->fetch_assoc()) {
                        ?>
                          <option value="<?php echo $row['id']; ?>" <?php if ($data['cat_id'] == $row['id']) {
                                                                      echo 'selected';
                                                                    } ?>><?php echo $row['title']; ?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>




                    <div class="form-group mb-3">
                      <label>
                        <?= $lang['Gallery_Image'] ?>

                      </label>
                      <input type="file" name="cat_img" class="form-control" accept=".jpg, .jpeg, .png, .gif"  >
                      <br>
                      <img src="<?php echo $data['img'] ?>" width="100px" />
                      <input type="hidden" name="type" value="edit_gal" />

                      <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                    </div>


                    <div class="form-group mb-3">
                      <label>
                        <?= $lang['Gallery_Status'] ?>

                      </label>
                      <select 
                      data-placeholder="<?= $lang['Select_Status'] ?>" 

                      name="status" class="select2-multi-select form-control" required>
                      <option value="" disabled selected ></option>
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


                  </div>
                  <div class="card-footer text-left">
                    <button class="btn btn-primary">
                      <?= $lang['Edit_Gallery_Category'] ?>

                    </button>
                  </div>
                </form>
              <?php
              } else {
              ?>
                <form method="post" enctype="multipart/form-data">

                  <div class="card-body">


                    <div class="form-group mb-3">
                      <label><?= $lang['Select_Property'] ?></label>
                      <select 
                      data-placeholder="<?= $lang['Select_Property'] ?>" 
                      name="property" id="property" class="select2-multi-select form-control" required>
                      <option value="" disabled selected ></option>
                        <?php
                        $zone = $rstate->query("select * from tbl_property where add_user_id=0");
                        while ($row = $zone->fetch_assoc()) {
                        ?>
                          <option value="<?php echo $row['id']; ?>"><?php echo $row['title']; ?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>
                    
                    <div class="form-group mb-3">
                      <label>
                        <?= $lang['Select_Gallery_Category'] ?>

                      </label>
                      <select
                      data-placeholder="<?= $lang['Select_Gallery_Category'] ?>" 
                      name="galcat" id="galcat" class="select2-multi-select form-control" required>
                      <option value="" disabled selected ></option>
                        <?php
                        $zone_ = $rstate->query("select * from tbl_gal_cat");
                        while ($row_ = $zone_->fetch_assoc()) {
                        ?>
                          <option value="<?php echo $row_['id']; ?>"><?php echo $row_['title']; ?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </div>
                    <div class="form-group mb-3">
                      <label>
                        <?= $lang['Gallery_Image'] ?>

                      </label>
                      <input type="file" name="cat_img" class="form-control" accept=".jpg, .jpeg, .png, .gif" required="" >

                      <input type="hidden" name="type" value="add_gal" />
                    </div>


                    <div class="form-group mb-3">
                      <label>
                        <?= $lang['Gallery_Status'] ?>

                      </label>
                      <select 
                      data-placeholder="<?= $lang['Gallery_Status'] ?>" 

                      name="status" class="select2-multi-select form-control" required>
                      <option value=""  ></option>
                        <option value="1">
                          <?= $lang['Publish'] ?>

                        </option>
                        <option value="0">
                          <?= $lang['Unpublish'] ?>

                        </option>
                      </select>
                    </div>


                  </div>
                  <div class="card-footer text-left">
                    <button class="btn btn-primary">
                      <?= $lang['Add_Gallery'] ?>

                    </button>
                  </div>
                </form>
              <?php } ?>
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
 

<script>
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

  document.querySelector('input[name="cat_img"]').addEventListener('change', function() {
    const file = this.files[0];

    if (file) {
      // Check if the file type is valid
      if (!allowedTypes.includes(file.type)) {
        this.value = ''; // Clear invalid file
      }
    }
  });
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>