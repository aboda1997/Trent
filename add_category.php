<?php
require 'include/main_head.php';
$category_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_ar = load_specific_langauage('ar');
$lang_en = load_specific_langauage('en');

if (isset($_GET['id'])) {
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Update', $category_per)) {



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
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Write', $category_per)) {



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
                <?= $lang['Category_Management'] ?>

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
              <div class="card-header card-header-tabs-line d-flex justify-content-between align-items-center">
                <div></div>
                <div class="card-toolbar">
                  <!-- Add any toolbar buttons or icons here -->
                  <ul class="nav nav-tabs nav-bold nav-tabs-line">
                    <li class="nav-item">
                      <a class="nav-link " data-toggle="tab" href="#ar" onclick="changeLanguage('ar')">العربية</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" href="#en" onclick="changeLanguage('en')">English</a>
                    </li>
                  </ul>
                </div>
              </div>
              <?php
              if (isset($_GET['id'])) {
                $data = $rstate->query("select * from tbl_category where id=" . $_GET['id'] . "")->fetch_assoc();
                $title = json_decode($data['title'], true);

              ?>
                <form method="post" enctype="multipart/form-data">

                  <div class="card-body">
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_en['Category_Name'] ?>

                          </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_en['Category_Name'] ?>"
                            value="<?php echo $title['en']; ?>" name="title_en" required="">
                        </div>
                      </div>
                      <!-- Arabic Tab -->

                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_ar['Category_Name'] ?>

                          </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_ar['Category_Name'] ?>"
                            value="<?php echo $title['ar']; ?>" name="title_ar" required="">
                        </div>
                      </div>
                    </div>



                    <div class="form-group mb-3">
                      <label id='Category-Image'>
                        <?= $lang['Category_Image'] ?>

                      </label>
                      <input type="file" class="form-control" name="cat_img">
                      <br>
                      <img src="<?php echo $data['img'] ?>" width="100px" />
                      <input type="hidden" name="type" value="edit_category" />

                      <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                    </div>



                    <div class="form-group mb-3">
                      <label id="Category-Status" for="inputGroupSelect01"><?= $lang_en['Category_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>

                        <option value="">
                          <?= $lang_en['Category_Status'] ?>...</option>
                        <option value="1" <?php if ($data['status'] == 1) {
                                            echo 'selected';
                                          } ?>>
                          <?= $lang_en['Publish'] ?>
                        </option>
                        <option value="0" <?php if ($data['status'] == 0) {
                                            echo 'selected';
                                          } ?>>
                          <?= $lang_en['Unpublish'] ?>

                        </option>
                      </select>
                    </div>


                  </div>
                  <div class="card-footer text-left">
                    <button type="submit" class="btn btn-primary">
                      <?= $lang_en['Edit_Category'] ?>

                    </button>
                  </div>
                </form>
              <?php
              } else {
              ?>
                <form method="post" enctype="multipart/form-data">

                <div class="card-body">
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_en['Category_Name'] ?>

                          </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_en['Category_Name'] ?>"
                            name="title_en" required="">
                        </div>
                      </div>
                      <!-- Arabic Tab -->

                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_ar['Category_Name'] ?>

                          </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_ar['Category_Name'] ?>"
                             name="title_ar" required="">
                        </div>
                      </div>
                    </div>



                    <div class="form-group mb-3">
                      <label id='Category-Image'>
                        <?= $lang['Category_Image'] ?>

                      </label>
                      <input type="file" class="form-control" name="cat_img">
                      <input type="hidden" name="type" value="add_category" />

                    </div>



                    <div class="form-group mb-3">
                      <label id="Category-Status" for="inputGroupSelect01"><?= $lang_en['Category_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>

                        <option value="">
                          <?= $lang_en['Category_Status'] ?>...</option>
                        <option value="1" >
                          <?= $lang_en['Publish'] ?>
                        </option>
                        <option value="0" >
                          <?= $lang_en['Unpublish'] ?>

                        </option>
                      </select>
                    </div>
                    <div class="card-footer text-left">
                    <button id="add-cat" type="submit" class="btn btn-primary">
                      <?= $lang_en['Add_Category'] ?>

                    </button>
                  </div>

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
  function changeLanguage(lang) {
    var langData = (lang === "ar") ? langDataAR : langDataEN;

    document.getElementById('Category-Status').textContent = langData.Category_Status;
    document.getElementById('Category-Image').textContent = langData.Category_Image;

    if (document.getElementById('add-cat')) {
      document.querySelector('button[type="submit"]').textContent = langData.Add_Category;

    } else {
      document.querySelector('button[type="submit"]').textContent = langData.Edit_Category;

    }

    const statusSelect = document.getElementById('inputGroupSelect01');
    statusSelect.querySelector('option[value=""]').textContent = langData.Category_Status;
    statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
    statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

  }
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>