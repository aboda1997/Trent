<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (isset($_GET['id'])) {
    if (!in_array('Update_Slider', $per)) {

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
    if (!in_array('Create_Slider', $per)) {

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
                                <?= $lang['Slider_Management'] ?>

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
                                $data = $rstate->query("select * from tbl_slider where id=" . $_GET['id'] . "")->fetch_assoc();
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
                                                    <label id="basic-addon1"><?= $lang_en['Slider_Name'] ?></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="<?php echo $title['en']; ?>"
                                                        placeholder="<?= $lang_en['Slider_Name'] ?>"
                                                        name="title_en"
                                                        required=""
                                                        aria-describedby="basic-addon1" />
                                                    <div class="invalid-feedback" id="slider_name_en_feedback" style="display: none;">
                                                        <?= $lang_en['slider_name'] ?>
                                                    </div>
                                                </div>


                                            </div>
                                            <!-- Arabic Tab -->
                                            <div class="tab-pane fade show " id="ar">
                                                <div class="form-group mb-3">
                                                    <label id="basic-addon1"><?= $lang_ar['Slider_Name'] ?></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="<?php echo $title['ar']; ?>"
                                                        placeholder="<?= $lang_ar['Slider_Name'] ?>"
                                                        name="title_ar"
                                                        required=""
                                                        aria-describedby="basic-addon1" />
                                                    <div class="invalid-feedback" id="slider_name_ar_feedback" style="display: none;">
                                                        <?= $lang_ar['slider_name'] ?>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">

                                                <div class="form-group mb-3">
                                                    <label id='Slider-Image'>
                                                        <?= $lang_en['Slider_Image'] ?>

                                                    </label>
                                                    <input type="file" class="form-control" accept=".jpg, .jpeg, .png, .gif" name="slider_img">
                                                    <div class="invalid-feedback" id="slider_img_feedback" style="display: none;">
                                                        <?= $lang_en['Slider_img'] ?>
                                                    </div>
                                                    <br>
                                                    <img src="<?php echo $data['img'] ?>" width="100px" />
                                                    <input type="hidden" name="type" value="edit_slider" />

                                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                                                </div>
                                            </div>


                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">

                                                <div class="form-group mb-3">
                                                    <label id="status-label" for="inputGroupSelect01"><?= $lang_en['Slider_Status'] ?></label>
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


                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label id="prop_owner">
                                                        <?= $lang_en['Select_Owner'] ?>

                                                    </label>
                                                    <select
                                                        name="propowner[]" id="owner" class=" form-control" multiple>

                                                        <?php
                                                        $zone = $rstate->query("select * from tbl_user");
                                                        while ($row = $zone->fetch_assoc()) {
                                                            $title = $row['name'];
                                                            $id = $row['id'];
                                                            $ccode = $row['ccode'];
                                                            $mobile = $row['mobile'];
                                                            $display_text = "$title (ID: $id) | Mobile: $ccode$mobile";
                                                            $isSelected = in_array($row['id'],  explode(',', $data['uid'])) ? 'selected' : '';

                                                        ?>
                                                            <option value="<?php echo $row['id']; ?>"
                                                                <?php echo $isSelected; ?>><?php echo $display_text ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback" id="owner_feedback" style="display: none;">
                                                        <?= $lang_en['prop_owner'] ?>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label id="prop_governemnt">
                                                        <?= $lang_en['Select_Government'] ?>

                                                    </label>
                                                    <select name="pgov" id="government" class=" form-control">
                                                        <option value="" disabled selected>

                                                            <?= $lang_en['Select_Government'] ?>

                                                        </option>
                                                        <?php
                                                        $zone = $rstate->query("select * from tbl_government");
                                                        while ($row = $zone->fetch_assoc()) {
                                                            $title = json_decode($row['name'], true);
                                                            $isSelected = in_array($row['id'],  explode(',', $data['government_id'])) ? 'selected' : '';

                                                        ?>
                                                            <option value="<?php echo $row['id']; ?>"
                                                                <?php echo $isSelected; ?>><?php echo $title[$lang_code]; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback" id="government_feedback" style="display: none;">
                                                        <?= $lang_en['prop_governemnt'] ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label id="prop_type">
                                                        <?= $lang_en['Select_Property_Type'] ?>

                                                    </label>
                                                    <select name="ptype" id="propt_type" class=" form-control">
                                                        <option value="" disabled selected>
                                                            <?= $lang_en['Select_Property_Type'] ?>

                                                        </option>
                                                        <?php
                                                        $zone = $rstate->query("select * from tbl_category");

                                                        while ($row = $zone->fetch_assoc()) {
                                                            $title = json_decode($row['title'], true);
                                                            $isSelected = in_array($row['id'],  explode(',', $data['cat_id'])) ? 'selected' : '';

                                                        ?>
                                                            <option value="<?php echo $row['id']; ?>"
                                                                <?php echo $isSelected; ?>><?php echo $title[$lang_code]; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback" id="prop_type_feedback" style="display: none;">
                                                        <?= $lang_en['prop_type'] ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="card-footer text-left">
                                        <button onclick="return validateForm(true)" type="submit" id="edit-slider" class="btn btn-primary">
                                            <?= $lang_en['Edit_Slider'] ?>

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
                                                    <label id="basic-addon1"><?= $lang_en['Slider_Name'] ?></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        placeholder="<?= $lang_en['Slider_Name'] ?>"
                                                        name="title_en"
                                                        required=""
                                                        aria-describedby="basic-addon1" />
                                                    <div class="invalid-feedback" id="slider_name_en_feedback" style="display: none;">
                                                        <?= $lang_en['slider_name'] ?>
                                                    </div>
                                                </div>


                                            </div>
                                            <!-- Arabic Tab -->
                                            <div class="tab-pane fade show " id="ar">
                                                <div class="form-group mb-3">
                                                    <label id="basic-addon1"><?= $lang_ar['Slider_Name'] ?></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        placeholder="<?= $lang_ar['Slider_Name'] ?>"
                                                        name="title_ar"
                                                        required=""
                                                        aria-describedby="basic-addon1" />
                                                    <div class="invalid-feedback" id="slider_name_ar_feedback" style="display: none;">
                                                        <?= $lang_ar['slider_name'] ?>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">

                                                <div class="form-group mb-3">
                                                    <label id='Slider-Image'>
                                                        <?= $lang_en['Slider_Image'] ?>

                                                    </label>
                                                    <input type="file" class="form-control" name="slider_img" accept=".jpg, .jpeg, .png, .gif" required="">
                                                    <div class="invalid-feedback" id="slider_img_feedback" style="display: none;">
                                                        <?= $lang_en['slider_img'] ?>
                                                    </div>
                                                    <input type="hidden" name="type" value="add_slider" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">

                                                <div class="form-group mb-3">
                                                    <label id="status-label" for="inputGroupSelect01"><?= $lang_en['Slider_Status'] ?></label>
                                                    <select class="form-control" name="status" id="inputGroupSelect01" required>
                                                        <option value=""><?= $lang_en['Choose'] ?>...</option>
                                                        <option value="1"><?= $lang_en['Publish'] ?></option>
                                                        <option value="0"><?= $lang_en['Unpublish'] ?></option>
                                                    </select>
                                                    <div class="invalid-feedback" id="status_feedback" style="display: none;">
                                                        <?= $lang_en['slider_status'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label id="prop_owner">
                                                        <?= $lang_en['Select_Owner'] ?>

                                                    </label>
                                                    <select
                                                        name="propowner[]" id="owner" class=" form-control" multiple>

                                                        <?php
                                                        $zone = $rstate->query("select * from tbl_user");
                                                        while ($row = $zone->fetch_assoc()) {
                                                            $title = $row['name'];
                                                            $id = $row['id'];
                                                            $ccode = $row['ccode'];
                                                            $mobile = $row['mobile'];
                                                            $display_text = "$title (ID: $id) | Mobile: $ccode$mobile";

                                                        ?>
                                                            <option value="<?php echo $row['id']; ?>"><?php echo $display_text ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback" id="owner_feedback" style="display: none;">
                                                        <?= $lang_en['prop_owner'] ?>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label id="prop_governemnt">
                                                        <?= $lang_en['Select_Government'] ?>

                                                    </label>
                                                    <select name="pgov" id="government" class=" form-control">
                                                        <option value="" disabled selected>

                                                            <?= $lang_en['Select_Government'] ?>

                                                        </option>
                                                        <?php
                                                        $zone = $rstate->query("select * from tbl_government");
                                                        while ($row = $zone->fetch_assoc()) {
                                                            $title = json_decode($row['name'], true);

                                                        ?>
                                                            <option value="<?php echo $row['id']; ?>"><?php echo $title[$lang_code]; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback" id="government_feedback" style="display: none;">
                                                        <?= $lang_en['prop_governemnt'] ?>

                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label id="prop_type">
                                                        <?= $lang_en['Select_Property_Type'] ?>

                                                    </label>
                                                    <select name="ptype" id="propt_type" class=" form-control">
                                                        <option value="" disabled selected>
                                                            <?= $lang_en['Select_Property_Type'] ?>

                                                        </option>
                                                        <?php
                                                        $zone = $rstate->query("select * from tbl_category");

                                                        while ($row = $zone->fetch_assoc()) {
                                                            $title = json_decode($row['title'], true);

                                                        ?>
                                                            <option value="<?php echo $row['id']; ?>"><?php echo $title[$lang_code]; ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback" id="prop_type_feedback" style="display: none;">
                                                        <?= $lang_en['prop_type'] ?>

                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                    </div>
                                    <div class="card-footer text-left">
                                        <button onclick="return validateForm()" id="add-slider" type="submit" class="btn btn-primary"><?= $lang_en['Add_Facility'] ?></button>
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
    document.addEventListener('DOMContentLoaded', function() {


        const $selectOwner = $('#owner');
        $selectOwner.select2({
            placeholder: langDataEN.Select_Owner,
            allowClear: true,
            mutiple: true

        });


        const $selectProp = $('#propt_type');
        $selectProp.select2({
            placeholder: langDataEN.Select_Property_Type,
            allowClear: true
        });
        const $selectgov = $('#government');
        $selectgov.select2({
            placeholder: langDataEN.Select_Government,
            allowClear: true
        });



    });

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

        document.querySelector('input[name="slider_img"]').addEventListener('change', function() {
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
        const sliderImage = document.querySelector('input[name="slider_img"]').value;
        const status = document.querySelector('select[name="status"]').value;

        let isValid = true;
        let isArabicValid = true;
        let isEnglishValid = true;
        let alertMessage = '';
        let lang = getCurrentLanguage();

        if (!titleEn) {
            document.getElementById('slider_name_en_feedback').style.display = 'block';
            isEnglishValid = false;

        }
        if (!titleAr) {
            document.getElementById('slider_name_ar_feedback').style.display = 'block';
            isArabicValid = false;

        }
        if (!sliderImage) {

            if (edit) {
                isValid = true;

            } else {
                document.getElementById('slider_img_feedback').style.display = 'block';
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

        document.getElementById('slider_name_ar_feedback').textContent = langData.slider_name;
        document.getElementById('slider_img_feedback').textContent = langData.slider_img;
        document.getElementById('status_feedback').textContent = langData.slider_status;
        document.getElementById('Slider-Image').textContent = langData.Slider_Image;
        document.getElementById('status-label').textContent = langData.Select_Status;
        document.getElementById('government_feedback').textContent = langData.prop_governemnt;
        document.getElementById('owner_feedback').textContent = langData.prop_owner;
        document.getElementById('prop_type_feedback').textContent = langData.prop_type;
        document.getElementById('prop_governemnt').textContent = langData.Select_Government;
        document.getElementById('prop_owner').textContent = langData.Select_Owner;
        document.getElementById('prop_type').textContent = langData.Select_Property_Type;

        if (document.getElementById('add-slider')) {
            document.querySelector('button[type="submit"]').textContent = langData.Add_Slider;

        } else {
            document.querySelector('button[type="submit"]').textContent = langData.Edit_Slider;

        }

        const statusSelect = document.getElementById('inputGroupSelect01');
        statusSelect.querySelector('option[value=""]').textContent = langData.Choose;
        statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
        statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

        $('#propt_type').select2('destroy');
        $('#propt_type').select2({
            placeholder: langData.Select_Property_Type,
            allowClear: true
        });

        $('#government').select2('destroy');
        $('#government').select2({
            placeholder: langData.Select_Government,
            allowClear: true
        });

        $('#owner').select2('destroy');
        $('#owner').select2({
            placeholder: langData.Select_Owner,
            allowClear: true
        });

    }
</script>

<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
</body>

</html>