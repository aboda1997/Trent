<?php
require 'include/main_head.php';
if ($_SESSION['stype'] == 'Staff') {
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
                                <?= $lang_en['why_choose_us'] ?>

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
                            <div class="card-body">


                                <form method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div id="alert-container" class="mb-3" style="display: none;">
                                            <div class="alert alert-danger" id="alert-message"></div>
                                        </div>

                                        <div class="form-group mb-3 col-6" style="margin-bottom: 48px;">
                                            <label><span class="text-danger">*</span>
                                                <?= $lang_en['why_choose_us_image'] ?>

                                            </label>
                                            <div class="custom-file">
                                                <input type="file" accept=".jpg, .jpeg, .png, .gif" name="why_choose_us_img" class="custom-file-input form-control">
                                                <label class="custom-file-label">
                                                    <?= $lang_en['choose_image'] ?>

                                                </label>
                                                <br>
                                                <img src="<?php echo $set['why_choose_us_img']; ?>" width="60" height="60" />
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span>
                                                <?= $lang_en['background_color'] ?>

                                            </label>
                                            <input type="text" class="form-control " placeholder="<?= $lang_en['background_color'] ?>" value="<?php echo $set['why_choose_us_bg']; ?>" name="why_choose_us_bg" required="">
                                            <input type="hidden" name="type" value="edit_why_choose_us" />
                                            <input type="hidden" name="id" value="1" />
                                        </div>
                                        <?php
                                        $title = json_decode($set['why_choose_us_title'], true);
                                        $descrption = json_decode($set['why_choose_us_description'], true);

                                        ?>

                                        <div class="tab-content">
                                            <!-- English Tab -->
                                            <div class="tab-pane fade show active" id="en">
                                                <div class="row">
                                                    <div class="form-group mb-3 col-6">
                                                        <label id="basic-addon1"><?= $lang_en['why_choose_us_title'] ?></label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            value="<?php echo $title['en']; ?>"
                                                            placeholder="<?= $lang_en['why_choose_us_title'] ?>"
                                                            name="why_choose_us_title_en"
                                                            required=""
                                                            aria-describedby="basic-addon1" />
                                                        <div class="invalid-feedback" id="why_choose_us_title_en_feedback" style="display: none;">
                                                            <?= $lang_en['faq_question'] ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3 col-6">
                                                        <label id="basic-addon1"><?= $lang_en['why_choose_us_description'] ?></label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            value="<?php echo $descrption['ar']; ?>"
                                                            placeholder="<?= $lang_en['why_choose_us_description'] ?>"
                                                            name="why_choose_us_description_en"
                                                            required=""
                                                            aria-describedby="basic-addon1" />
                                                        <div class="invalid-feedback" id="why_choose_us_description_en_feedback" style="display: none;">
                                                            <?= $lang_en['faq_answer'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Arabic Tab -->
                                            <div class="tab-pane fade show " id="ar">
                                                <div class="row">
                                                    <div class="form-group mb-3 col-6">
                                                        <label id="basic-addon1"><?= $lang_ar['why_choose_us_title'] ?></label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            value="<?php echo $title['ar']; ?>"
                                                            placeholder="<?= $lang_ar['why_choose_us_title'] ?>"
                                                            name="why_choose_us_title_ar"
                                                            required=""
                                                            aria-describedby="basic-addon1" />
                                                        <div class="invalid-feedback" id="why_choose_us_title_ar_feedback" style="display: none;">
                                                            <?= $lang_en['faq_question'] ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-3 col-6">
                                                        <label id="basic-addon1"><?= $lang_ar['why_choose_us_description'] ?></label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            value="<?php echo $descrption['ar']; ?>"
                                                            placeholder="<?= $lang_ar['why_choose_us_description'] ?>"
                                                            name="why_choose_us_description_ar"
                                                            required=""
                                                            aria-describedby="basic-addon1" />
                                                        <div class="invalid-feedback" id="why_choose_us_description_ar_feedback" style="display: none;">
                                                            <?= $lang_en['faq_answer'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>






                                        <div class="col-12">
                                            <button type="submit" name="edit_setting" class="btn btn-primary mb-2">Edit Why Choose US </button>
                                        </div>
                                    </div>
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


<?php
require 'include/footer.php';
?>
</body>

</html>