<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];

if (isset($_GET['id'])) {
  if ( !in_array('Update_Facility', $per)) {

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
  if ( !in_array('Create_Facility', $per)) {



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
                <?= $lang['Facility_Management'] ?>

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
                $data = $rstate->query("select * from tbl_facility where id=" . $_GET['id'] . "")->fetch_assoc();
                $title = json_decode($data['title'], true);

              ?>
                <form
                onsubmit="return submitform(true)" 

                method="post" enctype="multipart/form-data">

                  <div class="card-body">
                    <div id="alert-container" class="mb-3" style="display: none;">
                      <div class="alert alert-danger" id="alert-message"></div>
                    </div>
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show active " id="en">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Facility_Name'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            value="<?php echo $title['en']; ?>"
                            placeholder="<?= $lang_en['Facility_Name'] ?>"
                            name="title_en"
                            required=""
                            aria-describedby="basic-addon1" />
                          <div class="invalid-feedback" id="facility_name_en_feedback" style="display: none;">
                            <?= $lang_en['facility_name'] ?>
                          </div>
                        </div>


                      </div>
                      <!-- Arabic Tab -->
                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Facility_Name'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            value="<?php echo $title['ar']; ?>"
                            placeholder="<?= $lang_ar['Facility_Name'] ?>"
                            name="title_ar"
                            required=""
                            aria-describedby="basic-addon1" />
                          <div class="invalid-feedback" id="facility_name_ar_feedback" style="display: none;">
                            <?= $lang_ar['facility_name'] ?>
                          </div>
                        </div>


                      </div>
                    </div>


                    <div class="form-group mb-3">
                      <label id='Facility-Image'>
                        <?= $lang_en['Facility_Image'] ?>

                      </label>
                      <input type="file" class="form-control" accept=".jpg, .jpeg, .png, .gif" name="facility_img">
                      <div class="invalid-feedback" id="facility_img_feedback" style="display: none;">
                        <?= $lang_en['facility_img'] ?>
                      </div>
                      <br>
                      <img src="<?php echo $data['img'] ?>" width="100px" />
                      <input type="hidden" name="type" value="edit_facility" />

                      <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                    </div>



                    <div class="form-group mb-3">
                      <label id="status-label" for="inputGroupSelect01"><?= $lang_en['Facility_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>

                        <option value="">
                          <?= $lang_en['Choose'] ?>...</option>
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
                        <?= $lang_en['cat_status'] ?>
                      </div>
                    </div>


                  </div>
                  <div class="card-footer text-left">
                    <button onclick="return validateForm(true)" type="submit" id="edit-facility" class="btn btn-primary">
                      <?= $lang_en['Edit_Facility'] ?>

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
                      <!-- English Tab -->
                      <div class="tab-pane fade show active " id="en">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Facility_Name'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            placeholder="<?= $lang_en['Facility_Name'] ?>"
                            name="title_en"
                            required=""
                            aria-describedby="basic-addon1" />
                          <div class="invalid-feedback" id="facility_name_en_feedback" style="display: none;">
                            <?= $lang_en['facility_name'] ?>
                          </div>
                        </div>


                      </div>
                      <!-- Arabic Tab -->
                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Facility_Name'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            placeholder="<?= $lang_ar['Facility_Name'] ?>"
                            name="title_ar"
                            required=""
                            aria-describedby="basic-addon1" />
                          <div class="invalid-feedback" id="facility_name_ar_feedback" style="display: none;">
                            <?= $lang_ar['facility_name'] ?>
                          </div>
                        </div>


                      </div>
                    </div>


                    <div class="form-group mb-3">
                      <label id='Facility-Image'>
                        <?= $lang_en['Facility_Image'] ?>

                      </label>
                      <input type="file" class="form-control" name="facility_img" accept=".jpg, .jpeg, .png, .gif" required="">
                      <div class="invalid-feedback" id="facility_img_feedback" style="display: none;">
                        <?= $lang_en['facility_img'] ?>
                      </div>
                      <input type="hidden" name="type" value="add_facility" />
                    </div>
                    <div class="form-group mb-3">
                      <label id="status-label" for="inputGroupSelect01"><?= $lang_en['Facility_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>
                        <option value=""><?= $lang_en['Choose'] ?>...</option>
                        <option value="1"><?= $lang_en['Publish'] ?></option>
                        <option value="0"><?= $lang_en['Unpublish'] ?></option>
                      </select>
                      <div class="invalid-feedback" id="status_feedback" style="display: none;">
                        <?= $lang_en['facility_status'] ?>
                      </div>
                    </div>

                  </div>
                  <div class="card-footer text-left">
                    <button onclick="return validateForm()" id="add-facility" type="submit" class="btn btn-primary"><?= $lang_en['Add_Facility'] ?></button>
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

<script>
  function getCurrentLanguage() {
    // Get the active tab
    const activeTab = document.querySelector('.nav-link.active').getAttribute('href').substring(1);
    return activeTab === 'en' ? 'en' : 'ar';
  }

  function validateForm(edit = false) {
    // Clear previous feedback
    document.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
      feedback.style.display = 'none';
    });
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

    document.querySelector('input[name="facility_img"]').addEventListener('change', function() {
      const file = this.files[0];

      if (file) {
        // Check if the file type is valid
        if (!allowedTypes.includes(file.type)) {
          this.value = ''; // Clear invalid file
        }
      }
    });

    const titleEn = document.querySelector('input[name="title_en"]').value;
    const titleAr = document.querySelector('input[name="title_ar"]').value;
    const facilityImage = document.querySelector('input[name="facility_img"]').value;
    const status = document.querySelector('select[name="status"]').value;

    let isValid = true;
    let isArabicValid = true;
    let isEnglishValid = true;
    let alertMessage = '';
    let lang = getCurrentLanguage();

    if (!titleEn) {
      document.getElementById('facility_name_en_feedback').style.display = 'block';
      isEnglishValid = false;

    }
    if (!titleAr) {
      document.getElementById('facility_name_ar_feedback').style.display = 'block';
      isArabicValid = false;

    }
    if (!facilityImage) {

      if (edit) {
        isValid = true;

      } else {
        document.getElementById('facility_img_feedback').style.display = 'block';
        isValid = false;
      }
    }
    if (!status) {
      document.getElementById('status_feedback').style.display = 'block';
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

    document.getElementById('facility_name_ar_feedback').textContent = langData.facility_name;
    document.getElementById('facility_img_feedback').textContent = langData.facility_img;
    document.getElementById('status_feedback').textContent = langData.facility_status;
    document.getElementById('Facility-Image').textContent = langData.Facility_Image;
    document.getElementById('status-label').textContent = langData.Select_Status;


    if (document.getElementById('add-facility')) {
      document.querySelector('button[type="submit"]').textContent = langData.Add_Facility;

    } else {
      document.querySelector('button[type="submit"]').textContent = langData.Edit_Facility;

    }

    const statusSelect = document.getElementById('inputGroupSelect01');
    statusSelect.querySelector('option[value=""]').textContent = langData.Choose;
    statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
    statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

  }
</script>

<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
</body>

</html>