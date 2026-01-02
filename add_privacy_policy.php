<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];

if (!in_array('Read_Setting', $per)) {


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
                                Privacy Policy</h3>
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
                            <div class="card-body">


                                <form onsubmit="return submitform(validateForm(true)) ;"
                                    method="post" enctype="multipart/form-data">
                                    <div id="alert-container" class="mb-3" style="display: none;">
                                        <div class="alert alert-danger" id="alert-message"></div>
                                    </div>

                                    <div class="tab-content">
                                        <?php
                                        $privacy = json_decode($set['privacy_policy'], true);
                                        ?>
                                        <!-- English Tab -->
                                        <div class="tab-pane fade show active" id="en">
                                            <div class="row" style="height: calc(100vh - 150px);"> <!-- Adjust 150px based on your header/footer -->
                                                <div class="form-group mb-3 col-12 h-100"> <!-- Full width + full height -->
                                                    <label><span class="text-danger">*</span>
                                                    <?= $lang_en['privacy_policy'] ?>
                                                    </label>

                                                    <!-- Editor Container (Flexbox for full height) -->
                                                    <div class="d-flex flex-column border rounded" style="height: calc(100% - 40px);">

                                                        <!-- Full-screen Textarea -->
                                                        <textarea
                                                            class="form-control flex-grow-1 border-0 resize-none"
                                                            placeholder="<?= $lang_en['Enter_Policy'] ?>"

                                                            name="privacy_en"
                                                            required
                                                            style="min-height: 300px;"><?php echo htmlspecialchars($privacy['en'] ?? ''); ?></textarea>
                                                    </div>

                                                    <input type="hidden" name="type" value="edit_privacy" />
                                                    <input type="hidden" name="id" value="1" />
                                                    <div class="invalid-feedback" id="privacy_en_feedback" style="display: none;">
                                                        <?= $lang_en['privacy_feedback'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade show " id="ar">
                                            <div class="row" style="height: calc(100vh - 150px);"> <!-- Adjust 150px based on your header/footer -->
                                                <div class="form-group mb-3 col-12 h-100"> <!-- Full width + full height -->
                                                    <label><span class="text-danger">*</span>                                                        
                                                    <?= $lang_ar['privacy_policy'] ?>

                                                </label>

                                                    <!-- Editor Container (Flexbox for full height) -->
                                                    <div class="d-flex flex-column border rounded" style="height: calc(100% - 40px);">

                                                        <!-- Full-screen Textarea -->
                                                        <textarea
                                                            class="form-control flex-grow-1 border-0 resize-none"
                                                            placeholder="<?= $lang_ar['Enter_Policy'] ?>"

                                                            name="privacy_ar"
                                                            required
                                                            style="min-height: 300px;"><?php echo htmlspecialchars($privacy['ar'] ?? ''); ?></textarea>
                                                    </div>

                                                    <input type="hidden" name="type" value="edit_privacy" />
                                                    <input type="hidden" name="id" value="1" />
                                                    <div class="invalid-feedback" id="privacy_ar_feedback" style="display: none;">
                                                        <?= $lang_ar['privacy_feedback'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
													if (in_array('Update_Setting', $per) ) {
														?>
                                    <div class="card-footer text-left">
                                        <button onclick="return validateForm(true)" type="submit" id="edit_privacy" name="edit_privacy" class="btn btn-primary mb-2">
                                        <?= $lang_en['edit_privacy'] ?>

                                        </button>
                                    </div>
                                    <?php
												}
												?>
                                </form>
                                </form>

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
<script>
    function getCurrentLanguage() {
        // Get the active tab
        const activeTab = document.querySelector('.nav-link.active').getAttribute('href').substring(1);
        return activeTab;
    }



    function validateForm(edit = false) {
        document.querySelectorAll("div[id$='_feedback']").forEach(function(div) {
            div.style.display = 'none';
        });
        const privacy_ar = document.querySelector('textarea[name="privacy_ar"]').value;
        const privacy_en = document.querySelector('textarea[name="privacy_en"]').value;


        let isValid = true;
        let isArabicValid = true;
        let isEnglishValid = true;
        let alertMessage = '';

        
        if (!privacy_ar) {
            document.getElementById('privacy_ar_feedback').style.display = 'block';
            isArabicValid = false;
        }
        if (!privacy_en) {
            document.getElementById('privacy_en_feedback').style.display = 'block';
             isEnglishValid = false;
        }
        let lang = getCurrentLanguage();

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


    if (document.getElementById('edit_privacy')) {
      document.querySelector('button[type="submit"]').textContent = langData.edit_privacy;

    } 

  }
</script>

<?php
require 'include/footer.php';
?>
</body>

</html>