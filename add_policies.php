<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];

if (isset($_GET['id'])) {
    if ( !in_array('Update_Cancellation_Policy', $per)) {



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
    if (!in_array('Create_Cancellation_Policy', $per)) {



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
                                <?= $lang_en['privacy_policy'] ?>
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
                                $data = $rstate->query("select * from  tbl_cancellation_policy where id=" . $_GET['id'] . "")->fetch_assoc();
                                $title = json_decode($data['title'], true);
                                $descrption = json_decode($data['description'], true);

                            ?>

                                <form
                                    onsubmit="return submitform(true)"
                                    method="post" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div id="alert-container" class="mb-3" style="display: none;">
                                            <div class="alert alert-danger" id="alert-message"></div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group mb-3 col-6">
                                                <label id="recommended_label"><span class="text-danger">*</span>
                                                    <?= $lang_en['is_recommended'] ?>
                                                </label>
                                                <select class="form-control" id="inputGroupSelect0" name="is_recommended" required>
                                                    <option value=""><?= $lang_en['Choose'] ?>...</option>
                                                    <option value="1" <?php if ($data['is_recommended'] == 1) {
                                                                            echo 'selected';
                                                                        } ?>><?= $lang_en['yes'] ?></option>
                                                    <option value="0" <?php if ($data['is_recommended'] == 0) {
                                                                            echo 'selected';
                                                                        } ?>><?= $lang_en['no'] ?></option>

                                                </select>
                                                <input type="hidden" name="type" value="edit_cancallation_policy" />
                                                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />


                                                <div class="invalid-feedback" id="is_recommended_feedback" style="display: none;">
                                                <?= $lang_en['is_recommended_'] ?>
                                                </div>
                                                </div>

                                                <div class="form-group mb-3 col-6">
                                                    <label id="status-label" for="inputGroupSelect01">
                                                    <span class="text-danger">*</span>    
                                                    <?= $lang_en['Select_Status'] ?></label>
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
                                                        <div class="invalid-feedback" id="status_feedback" style="display: none;">
                                                            <?= $lang_en['policy_status'] ?>
                                                        </div>
                                                    </select>
                                                </div>
                                        </div>

                                        <div class="tab-content">
                                            <!-- English Tab -->
                                            <div class="tab-pane fade show active" id="en">
                                                <div class="row">
                                                    <div class="form-group mb-3 col-6">
                                                        <label id="basic-addon1">
                                                        <span class="text-danger">*</span>    
                                                        <?= $lang_en['policy_title'] ?></label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            value="<?php echo $title['en']; ?>"
                                                            placeholder="<?= $lang_en['policy_title'] ?>"
                                                            name="policy_title_en"
                                                            required=""
                                                            aria-describedby="basic-addon1" />
                                                        <div class="invalid-feedback" id="privacy_title_en_feedback" style="display: none;">
                                                            <?= $lang_en['policy_title_'] ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3 col-6">
                                                        <label id="basic-addon1">
                                                        <span class="text-danger">*</span>    
                                                        <?= $lang_en['policy_description'] ?></label>
                                                        <textarea
                                                            class="form-control"
                                                            placeholder="<?= $lang_en['policy_description'] ?>"
                                                            required=""
                                                            aria-describedby="basic-addon1"
                                                            name="policy_description_en" rows="3"><?php echo ltrim(htmlspecialchars(preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $descrption['en']), ENT_QUOTES, 'UTF-8')); ?></textarea>

                                                        <div class="invalid-feedback" id="policy_description_en_feedback" style="display: none;">
                                                            <?= $lang_en['policy_description_'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Arabic Tab -->
                                            <div class="tab-pane fade show " id="ar">
                                                <div class="row">
                                                    <div class="form-group mb-3 col-6">
                                                        <label id="basic-addon1">
                                                        <span class="text-danger">*</span>    
                                                        <?= $lang_ar['policy_title'] ?></label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            value="<?php echo $title['ar']; ?>"
                                                            placeholder="<?= $lang_ar['policy_title'] ?>"
                                                            name="policy_title_ar"
                                                            required=""
                                                            aria-describedby="basic-addon1" />
                                                        <div class="invalid-feedback" id="policy_title_ar_feedback" style="display: none;">
                                                            <?= $lang_ar['policy_title_'] ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3 col-6">

                                                        <label id="basic-addon1">
                                                        <span class="text-danger">*</span>    
                                                        <?= $lang_ar['policy_description'] ?></label>

                                                        <textarea
                                                            class="form-control"
                                                            placeholder="<?= $lang_ar['policy_description'] ?>"
                                                            required=""
                                                            aria-describedby="basic-addon1"
                                                            name="policy_description_ar" rows="3"><?php echo rtrim(htmlspecialchars(preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $descrption['ar']), ENT_QUOTES, 'UTF-8')); ?></textarea>
                                                        <div class="invalid-feedback" id="policy_description_ar_feedback" style="display: none;">
                                                            <?= $lang_ar['policy_description_'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <button onclick="return validateForm(true)" id="edit_privacy_policy" type="submit" class="btn btn-primary mb-2">
                                                <?= $lang_en['edit_privacy_policy'] ?>

                                            </button>
                                        </div>
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
                                        <div class="row">
                                            <div class="form-group mb-3 col-6">
                                                <label id="recommended_label"><span class="text-danger">*</span>
                                                    <?= $lang_en['is_recommended'] ?>
                                                </label>
                                                <select class="form-control" id="inputGroupSelect0" name="is_recommended" required>
                                                    <option value=""><?= $lang_en['Choose'] ?>...</option>
                                                    <option value="1"><?= $lang_en['yes'] ?></option>
                                                    <option value="0"><?= $lang_en['no'] ?></option>

                                                </select>
                                                <input type="hidden" name="type" value="add_cancallation_policy" />

                                                <div class="invalid-feedback" id="is_recommended_feedback" style="display: none;">
                                                    <?= $lang_en['is_recommended_'] ?>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3 col-6">
                                                <label id="status-label" for="inputGroupSelect01">
                                                <span class="text-danger">*</span>    
                                                <?= $lang_en['Select_Status'] ?></label>
                                                <select class="form-control" name="status" id="inputGroupSelect01" required>
                                                    <option value=""><?= $lang_en['Choose'] ?>...</option>
                                                    <option value="1"><?= $lang_en['Publish'] ?></option>
                                                    <option value="0"><?= $lang_en['Unpublish'] ?></option>
                                                </select>
                                                <div class="invalid-feedback" id="status_feedback" style="display: none;">
                                                    <?= $lang_en['policy_status'] ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-content">
                                            <!-- English Tab -->
                                            <div class="tab-pane fade show active" id="en">
                                                <div class="row">
                                                    <div class="form-group mb-6 col-6">
                                                        <label id="basic-addon1">
                                                        <span class="text-danger">*</span>    
                                                        <?= $lang_en['policy_title'] ?></label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            placeholder="<?= $lang_en['policy_title'] ?>"
                                                            name="policy_title_en"
                                                            required=""
                                                            aria-describedby="basic-addon1" />
                                                        <div class="invalid-feedback" id="policy_title_en_feedback" style="display: none;">
                                                            <?= $lang_en['policy_title_'] ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-6 col-6">
                                                        <label id="basic-addon1">
                                                        <span class="text-danger">*</span>    
                                                        <?= $lang_en['policy_description'] ?></label>
                                                        <textarea
                                                            class="form-control"
                                                            placeholder="<?= $lang_en['policy_description'] ?>"
                                                            required=""
                                                            aria-describedby="basic-addon1"
                                                            name="policy_description_en" rows="3"></textarea>

                                                        <div class="invalid-feedback" id="policy_description_en_feedback" style="display: none;">
                                                            <?= $lang_en['policy_description_'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Arabic Tab -->
                                            <div class="tab-pane fade show " id="ar">
                                                <div class="row">
                                                    <div class="form-group mb-6 col-6">
                                                        <label id="basic-addon1">
                                                        <span class="text-danger">*</span>    
                                                        <?= $lang_ar['policy_title'] ?></label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            placeholder="<?= $lang_ar['policy_title'] ?>"
                                                            name="policy_title_ar"
                                                            required=""
                                                            aria-describedby="basic-addon1" />
                                                        <div class="invalid-feedback" id="policy_title_ar_feedback" style="display: none;">
                                                            <?= $lang_ar['policy_title_'] ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-6 col-6">

                                                        <label id="basic-addon1">
                                                        <span class="text-danger">*</span>    
                                                        <?= $lang_ar['policy_description'] ?></label>

                                                        <textarea
                                                            class="form-control"
                                                            placeholder="<?= $lang_ar['policy_description'] ?>"
                                                            required=""
                                                            aria-describedby="basic-addon1"
                                                            name="policy_description_ar" rows="3"></textarea>
                                                        <div class="invalid-feedback" id="policy_description_ar_feedback" style="display: none;">
                                                            <?= $lang_ar['policy_description_'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <button onclick="return validateForm()" id="add_privacy_policy" type="submit" class="btn btn-primary mb-2">
                                                <?= $lang_en['add_privacy_policy'] ?>

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
<script>
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

    document.querySelector('input[name="why_choose_us_img"]').addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            // Check if the file type is valid
            if (!allowedTypes.includes(file.type)) {
                this.value = ''; // Clear invalid file
            }
        }
    });
</script>

<script>
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

        const policy_description_en = document.querySelector('textarea[name="policy_description_en"]').value;
        const policy_description_ar = document.querySelector('textarea[name="policy_description_ar"]').value;

        const policy_title_en = document.querySelector('input[name="policy_title_en"]').value;
        const policy_title_ar = document.querySelector('input[name="policy_title_ar"]').value;
        const is_recommended = document.querySelector('select[name="is_recommended"]').value;
        const status = document.querySelector('select[name="status"]').value;

        let isValid = true;
        let isArabicValid = true;
        let isEnglishValid = true;
        let alertMessage = '';
        let lang = getCurrentLanguage();

        if (!policy_description_en) {
            document.getElementById('policy_description_en_feedback').style.display = 'block';
            isEnglishValid = false;

        }
        if (!policy_description_ar) {
            document.getElementById('policy_description_ar_feedback').style.display = 'block';
            isArabicValid = false;

        }
        if (!policy_title_en) {
            document.getElementById('policy_title_en_feedback').style.display = 'block';
            isEnglishValid = false;

        }
        if (!policy_title_ar) {
            document.getElementById('policy_title_ar_feedback').style.display = 'block';
            isArabicValid = false;

        }
        if (!is_recommended) {
            document.getElementById('is_recommended_feedback').style.display = 'block';
            isValid = false;
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
        document.getElementById('is_recommended_feedback').textContent = langData.is_recommended_;
        document.getElementById('recommended_label').textContent = langData.is_recommended;
        document.getElementById('status_feedback').textContent = langData.policy_status;
    document.getElementById('status-label').textContent = langData.Select_Status;


        if (document.getElementById('edit_privacy_policy')) {
            document.querySelector('button[type="submit"]').textContent = langData.edit_privacy_policy;

        } else {
            document.querySelector('button[type="submit"]').textContent = langData.add_privacy_policy;

        }

        const Select = document.getElementById('inputGroupSelect0');
        Select.querySelector('option[value=""]').textContent = langData.Choose;
        Select.querySelector('option[value="1"]').textContent = langData.yes;
        Select.querySelector('option[value="0"]').textContent = langData.no;

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