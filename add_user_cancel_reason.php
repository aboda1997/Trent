<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];

if (isset($_GET['id'])) {
  if ( !in_array('Update_Cancel_Reason', $per)) {



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
  if (!in_array('Create_Cancel_Reason', $per)) {



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
                <?= $lang['Cancel_Reason_Management'] ?>

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
                $data = $rstate->query("select * from tbl_user_cancel_reason where id=" . $_GET['id'] . "")->fetch_assoc();
                $reason = json_decode($data['reason'], true);

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
                      <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_en['Cancel_Reason'] ?>

                          </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_en['Cancel_Reason'] ?>"
                            value="<?php echo $reason['en']; ?>" name="reason_en" required="">
                          <div class="invalid-feedback" id="reason_en_feedback" style="display: none;">
                            <?= $lang_en['Cancel_Reason_en'] ?>

                          </div>
                        </div>
                      </div>
                      <!-- Arabic Tab -->

                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_ar['Cancel_Reason'] ?>

                          </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_ar['Cancel_Reason'] ?>"
                            value="<?php echo $reason['ar']; ?>" name="reason_ar" required="">
                          <div class="invalid-feedback" id="reason_ar_feedback" style="display: none;">
                            <?= $lang_ar['Cancel_Reason_ar'] ?>

                          </div>
                        </div>
                      </div>
                    </div>

                    <input type="hidden" name="type" value="edit_user_cancel_reason" />
                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />


                    <div class="form-group mb-3">
                      <label id="Cancel_Reason_Status" for="inputGroupSelect01"><?= $lang_en['Cancel_Reason_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>

                        <option value="">
                          <?= $lang_en['Cancel_Reason_Status'] ?>...</option>
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
                        <?= $lang_en['Cancel_Reason_status'] ?>
                      </div>
                    </div>


                  </div>
                  <div class="card-footer text-left">
                    <button onclick="return validateForm(true)" type="submit" class="btn btn-primary">
                      <?= $lang_en['Edit_Cancel_Reason'] ?>

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
                      <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_en['Cancel_Reason'] ?>

                          </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_en['Cancel_Reason'] ?>"
                            name="reason_en" required="">
                          <div class="invalid-feedback" id="reason_en_feedback" style="display: none;">
                            <?= $lang_en['Cancel_Reason_en'] ?>

                          </div>

                        </div>
                      </div>
                      <!-- Arabic Tab -->

                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label>
                            <?= $lang_ar['Cancel_Reason'] ?>

                          </label>
                          <input type="text" class="form-control" placeholder="<?= $lang_ar['Cancel_Reason'] ?>"
                            name="reason_ar" required="">
                          <div class="invalid-feedback" id="reason_ar_feedback" style="display: none;">
                            <?= $lang_ar['Cancel_Reason_en'] ?>

                          </div>

                        </div>
                      </div>
                    </div>


                    <input type="hidden" name="type" value="add_user_cancel_reason" />



                    <div class="form-group mb-3">
                      <label id="Cancel_Reason_Status" for="inputGroupSelect01"><?= $lang_en['Cancel_Reason_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>

                        <option value="">
                          <?= $lang_en['Cancel_Reason_Status'] ?>...</option>
                        <option value="1">
                          <?= $lang_en['Publish'] ?>
                        </option>
                        <option value="0">
                          <?= $lang_en['Unpublish'] ?>

                        </option>
                      </select>
                      <div class="invalid-feedback" id="status_feedback" style="display: none;">
                        <?= $lang_en['Cancel_Reason_status'] ?>
                      </div>
                    </div>
                    <div class="card-footer text-left">
                      <button onclick="return validateForm()" id="add-cancel-reason" type="submit" class="btn btn-primary">
                        <?= $lang_en['Add_Cancel_Reason'] ?>

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

    const reasonEn = document.querySelector('input[name="reason_en"]').value;
    const reasonAr = document.querySelector('input[name="reason_ar"]').value;
    const status = document.querySelector('select[name="status"]').value;

    let isValid = true;
    let isArabicValid = true;
    let isEnglishValid = true;
    let alertMessage = '';
    let lang = getCurrentLanguage();

    if (!reasonEn) {
      document.getElementById('reason_en_feedback').style.display = 'block';
      isEnglishValid = false;

    }
    if (!reasonAr) {
      document.getElementById('reason_ar_feedback').style.display = 'block';
      isArabicValid = false;

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
    document.getElementById('status_feedback').textContent = langData.Cancel_Reason_status;
    document.getElementById('Cancel_Reason_Status').textContent = langData.Cancel_Reason_Status;

    if (document.getElementById('add-cancel-reason')) {
      document.querySelector('button[type="submit"]').textContent = langData.Add_Cancel_Reason;

    } else {
      document.querySelector('button[type="submit"]').textContent = langData.Edit_Cancel_Reason;

    }

    const statusSelect = document.getElementById('inputGroupSelect01');
    statusSelect.querySelector('option[value=""]').textContent = langData.Cancel_Reason_Status;
    statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
    statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

  }
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>