<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];

if (isset($_GET['id'])) {
  if ( !in_array('Update_Gallery_Category', $per)) {


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
  if (!in_array('Create_Gallery_Category', $per)) {


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
                <?= $lang['Gallery_Category_Management'] ?>

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
                $data = $rstate->query("select * from tbl_gal_cat where id=" . $_GET['id'] . "")->fetch_assoc();
                $title_gal = json_decode($data['title'], true);

              ?>
                <form 
                onsubmit="return submitform(true)" 
                method="post" enctype="multipart/form-data">
                  <div class="card-body">

                    <div id="alert-container" class="mb-3" style="display: none;">
                      <div class="alert alert-danger" id="alert-message"></div>
                    </div>
                    <div class="tab-content">

                      <div class="form-group mb-3">
                        <label id='Select_Property'>
                          <?= $lang_en['Select_Property'] ?>

                        </label>
                        <select name="property" id="product" class="form-control" required>
                          <option value=""> <?= $lang_en['Select_Property'] ?>
                          </option>
                          <?php
                          $zone = $rstate->query("select * from tbl_property ");
                          while ($row = $zone->fetch_assoc()) {
                            $title = json_decode($row['title'], true);

                          ?>
                            <option value="<?php echo $row['id']; ?>"
                              <?php if ($data['pid'] == $row['id']) {
                                echo 'selected';
                              } ?>><?php echo $title['en']; ?></option>
                          <?php
                          }
                          ?>
                        </select>
                        <div class="invalid-feedback" id="property_feedback" style="display: none;">
                          <?= $lang_en['Gal_prop'] ?>
                        </div>
                      </div>

                      <div class="tab-pane fade show active" id="en">

                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_en['Gallery_Category_Name'] ?>
                          </label>
                          
                          <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                          <input type="hidden" name="type"  value="edit_gal_category" />
                          <input value="<?php echo $title_gal['en'] ?>" type="text" class="form-control" placeholder="<?= $lang_en['Gallery_Category_Name'] ?>" name="title_en" required="">
                          <div class="invalid-feedback" id="title_en_feedback" style="display: none;">
                            <?= $lang_en['gal_cat_title_en'] ?>

                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_ar['Gallery_Category_Name'] ?> </label>
                          <input value="<?php echo $title_gal['ar']; ?>" type="text" class="form-control" placeholder="<?= $lang_ar['Gallery_Category_Name'] ?>" name="title_ar" required="">
                          <div class="invalid-feedback" id="title_ar_feedback" style="display: none;">
                            <?= $lang_ar['gal_cat_title_ar'] ?>

                          </div>
                        </div>
                      </div>
                      <div class="form-group mb-3">
                        <label id="Category-Status" for="inputGroupSelect01"><?= $lang_en['Gallery_Category_Status'] ?></label>
                        <select class="form-control" name="status" id="inputGroupSelect01" required>

                          <option value="">
                            <?= $lang_en['Gallery_Category_Status'] ?>...</option>
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
                        <div class="invalid-feedback" id="status_feedback" style="display: none;">
                          <?= $lang_en['Gal_cat_status'] ?>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="card-footer text-left">
                    <button onclick="return validateForm()" type="submit" class="btn btn-primary">
                      <?= $lang_en['Edit_Gallery_Category'] ?>

                    </button>
                  </div>
                </form>
              <?php
              } else {
              ?>
                <form
                onsubmit="return submitform(true)" 

                method="post" enctype="multipart/form-data">

                  <div class="card-body">

                    <div id="alert-container" class="mb-3" style="display: none;">
                      <div class="alert alert-danger" id="alert-message"></div>
                    </div>
                    <div class="tab-content">

                      <div class="form-group mb-3">
                        <label id='Select_Property'>
                          <?= $lang_en['Select_Property'] ?>

                        </label>
                        <select name="property" id="product" class="form-control" required>
                          <option value=""> <?= $lang_en['Select_Property'] ?>
                          </option>
                          <?php
                          $zone = $rstate->query("select * from tbl_property ");
                          while ($row = $zone->fetch_assoc()) {
                            $title = json_decode($row['title'], true);

                          ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $title['en']; ?></option>
                          <?php
                          }
                          ?>
                        </select>
                        <div class="invalid-feedback" id="property_feedback" style="display: none;">
                          <?= $lang_en['Gal_prop'] ?>
                        </div>
                      </div>
                      <div class="tab-pane fade show active" id="en">

                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_en['Gallery_Category_Name'] ?>
                          </label>
                          <input type="hidden" name="type" value="add_gal_category" />
                          <input type="text" class="form-control" placeholder="<?= $lang_en['Gallery_Category_Name'] ?>" name="title_en" required="">
                          <div class="invalid-feedback" id="title_en_feedback" style="display: none;">
                            <?= $lang_en['gal_cat_title_en'] ?>

                          </div>
                        </div>
                      </div>
                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_ar['Gallery_Category_Name'] ?> </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_ar['Gallery_Category_Name'] ?>" name="title_ar" required="">
                          <div class="invalid-feedback" id="title_ar_feedback" style="display: none;">
                            <?= $lang_ar['gal_cat_title_ar'] ?>

                          </div>
                        </div>
                      </div>

                      <div class="form-group mb-3">
                        <label id="Category-Status" for="inputGroupSelect01"><?= $lang_en['Gallery_Category_Status'] ?></label>
                        <select class="form-control" name="status" id="inputGroupSelect01" required>

                          <option value="">
                            <?= $lang_en['Gallery_Category_Status'] ?>...</option>
                          <option value="1">
                            <?= $lang_en['Publish'] ?>
                          </option>
                          <option value="0">
                            <?= $lang_en['Unpublish'] ?>

                          </option>
                        </select>
                        <div class="invalid-feedback" id="status_feedback" style="display: none;">
                          <?= $lang_en['Gal_cat_status'] ?>
                        </div>
                      </div>
                    </div>

                  </div>
                  <div class="card-footer text-left">
                    <button onclick="return validateForm()" type="submit" id="add-cat" class="btn btn-primary">
                      <?= $lang_en['Add_Gallery_Category'] ?>

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
  document.addEventListener('DOMContentLoaded', function() {

    const $selectpro = $('#product');
    $selectpro.select2({
      allowClear: true,
      placeholder: langDataEN.Select_Property,
    });

  });

  function getCurrentLanguage() {
    // Get the active tab
    const activeTab = document.querySelector('.nav-link.active').getAttribute('href').substring(1);
    return activeTab === 'en' ? 'en' : 'ar';
  }

  function validateForm() {
    // Clear previous feedback
    document.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
      feedback.style.display = 'none';
    });

    const titleEn = document.querySelector('input[name="title_en"]').value;
    const titleAr = document.querySelector('input[name="title_ar"]').value;
    const status = document.querySelector('select[name="status"]').value;
    const property = document.querySelector('select[name="property"]').value;

    let isValid = true;
    let isArabicValid = true;
    let isEnglishValid = true;
    let alertMessage = '';
    let lang = getCurrentLanguage();

    if (!titleEn) {
      document.getElementById('title_en_feedback').style.display = 'block';
      isEnglishValid = false;

    }
    if (!titleAr) {
      document.getElementById('title_ar_feedback').style.display = 'block';
      isArabicValid = false;

    }



    if (!status) {
      document.getElementById('status_feedback').style.display = 'block';
      isValid = false;
    }


    if (!property) {
      document.getElementById('property_feedback').style.display = 'block';
      isValid = false;
    }



    if (!isArabicValid && isEnglishValid) {
      // Show alert if there are required fields missing
      if (lang == "en") {
        alertMessage = langDataEN.alert_en;

      } else {
        alertMessage = langDataAR.alert_en;

      }
      isValid = false;
    }
    if (!isEnglishValid && isArabicValid) {
      // Show alert if there are required fields missing
      if (lang == "ar") {
        alertMessage = langDataAR.alert_ar;

      } else {
        alertMessage = langDataEN.alert_ar;

      }
      isValid = false;
    }
    if (isArabicValid && isEnglishValid) {
      alertMessage = '';
    }
    if (alertMessage) {
      document.getElementById('alert-message').innerHTML = alertMessage;
      document.getElementById('alert-container').style.display = 'block';

    } else {
      document.getElementById('alert-container').style.display = 'none';

    }
    if (!isValid) {
      return false;
    }

    return true; // Allow form submission
  }

  function changeLanguage(lang) {
    var langData = (lang === "ar") ? langDataAR : langDataEN;
    document.getElementById('property_feedback').textContent = langData.Gal_prop;
    document.getElementById('status_feedback').textContent = langData.Gal_cat_status;
    document.getElementById('Category-Status').textContent = langData.Gallery_Category_Status;
    document.getElementById('Select_Property').textContent = langData.Select_Property;

    if (document.getElementById('add-cat')) {
      document.querySelector('button[type="submit"]').textContent = langData.Add_Gallery_Category;

    } else {
      document.querySelector('button[type="submit"]').textContent = langData.Edit_Gallery_Category;

    }

    const statusSelect = document.getElementById('inputGroupSelect01');
    statusSelect.querySelector('option[value=""]').textContent = langData.Category_Status;
    statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
    statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;


    $('#product').select2('destroy');
    $('#product').select2({
      placeholder: langData.Select_Property,
      allowClear: true,
    });

  }
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>