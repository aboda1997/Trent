<?php
require 'include/main_head.php';
$faq_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_ar = load_specific_langauage('ar');
$lang_en = load_specific_langauage('en');

if (isset($_GET['id'])) {
    if ($_SESSION['restatename'] == 'Staff' && !in_array('Update', $package_per)) {



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
    if ($_SESSION['restatename'] == 'Staff' && !in_array('Write', $package_per)) {



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
                                <?= $lang['Package_Management'] ?></h3>
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
                                $data = $rstate->query("select * from tbl_package where id=" . $_GET['id'] . "")->fetch_assoc();
                                $title = json_decode($data['title'], true);
                                $description = json_decode($data['description'], true);
                            ?>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="card-body">

                                        <div class="tab-content">

                                            <!-- English Tab -->
                                            <div class="tab-pane fade show active" id="en">
                                                <div class="form-group mb-3">
                                                    <label id="basic-addon1"><?= $lang_en['Package_Name'] ?></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="<?php echo $title['en']; ?>"
                                                        placeholder="<?= $lang_en['Enter_Package'] ?>"
                                                        name="title_en"
                                                        required=""
                                                        aria-describedby="basic-addon1" />
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label><?= $lang_en['Package_Description'] ?></label>
                                                    <textarea class="form-control" rows="5" name="description_en" id="cdesc" style="resize: none;"><?php echo $description['en']; ?></textarea>
                                                </div>
                                            </div>

                                            <!-- Arabic Tab -->
                                            <div class="tab-pane fade  show " id="ar">
                                                <div class="form-group mb-3">
                                                    <label id="basic-addon1"><?= $lang_ar['Package_Name'] ?></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        value="<?php echo $title['ar']; ?>"
                                                        placeholder="<?= $lang_ar['Enter_Package'] ?>"
                                                        name="title_ar"
                                                        required=""
                                                        aria-describedby="basic-addon1" />
                                                </div>


                                                <div class="form-group mb-3">
                                                    <label><?= $lang_ar['Package_Description'] ?></label>
                                                    <textarea class="form-control" rows="5" name="description_ar" id="cdesc_ar" style="resize: none;"><?php echo $description['ar']; ?></textarea>
                                                </div>



                                            </div>
                                            <div class="form-group mb-3">
                                                <label id="Package-Total-Day" ><?= $lang_en['Package_Total_Day'] ?></label>
                                                <input
                                                    type="text"
                                                    class="form-control numberonly"
                                                    placeholder="<?= $lang_en['Enter_Total_Day'] ?>"
                                                    name="day"
                                                    value="<?php echo $data['day']; ?>"
                                                    required="" />
                                            </div>

                                            <div class="form-group mb-3">
                                                <label id="Package-Price" ><?= $lang_en['Package_Price'] ?></label>
                                                <input
                                                    type="text"
                                                    class="form-control numberonly"
                                                    placeholder="<?= $lang_en['Enter_Price'] ?>"
                                                    name="price"
                                                    value="<?php echo $data['price']; ?>"
                                                    required="" />
                                            </div>
                                            <div class="form-group mb-3">
                                                <label id="Package-Image"><?= $lang_en['Package_Image'] ?></label>
                                                <input type="file" class="form-control" name="cat_img">
                                                <br>
                                                <img src="<?php echo $data['image'] ?>" width="100px" />
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label id="status-label" for="inputGroupSelect01"><?= $lang_en['Select_Status'] ?></label>
                                            <select class="form-control" name="status" id="inputGroupSelect01" required>
                                                <option value=""><?= $lang_en['Choose'] ?>...</option>
                                                <option value="1" <?php if ($data['status'] == 1) echo 'selected'; ?>><?= $lang_en['Publish'] ?></option>
                                                <option value="0" <?php if ($data['status'] == 0) echo 'selected'; ?>><?= $lang_en['Unpublish'] ?></option>
                                            </select>
                                        </div>

                                        <input type="hidden" name="type" value="edit_package" />
                                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                                    </div>
                                    <div class="card-footer text-left">

                                        <button id="edit-package" type="submit" class="btn btn-primary">
                                            <?= $lang_en['Edit_Package'] ?>
                                        </button>
                                    </div>
                                </form>
                            <?php
                            } else {
                            ?>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="tab-content">

                                            <!-- English Tab -->
                                            <div class="tab-pane fade show active" id="en">
                                                <div class="form-group mb-3">
                                                    <label id="basic-addon1"><?= $lang_en['Package_Name'] ?></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        placeholder="<?= $lang_en['Enter_Package'] ?>"
                                                        name="title_en"
                                                        required=""
                                                        aria-describedby="basic-addon1" />
                                                </div>


                                                <div class="form-group mb-3">
                                                    <label><?= $lang_en['Package_Description'] ?></label>
                                                    <textarea class="form-control" rows="5" name="description_en" id="cdesc" style="resize: none;"></textarea>
                                                </div>

                                            </div>

                                            <!-- Arabic Tab -->
                                            <div class="tab-pane fade show " id="ar">
                                                <div class="form-group mb-3">
                                                    <label id="basic-addon1"><?= $lang_ar['Package_Name'] ?></label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        placeholder="<?= $lang_ar['Enter_Package'] ?>"
                                                        name="title_ar"
                                                        required=""
                                                        aria-describedby="basic-addon1" />
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label><?= $lang_ar['Package_Description'] ?></label>
                                                    <textarea class="form-control" rows="5" name="description_ar" id="cdesc_ar" style="resize: none;"></textarea>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="form-group mb-3">
                                            <label id="Package-Total-Day"><?= $lang_en['Package_Total_Day'] ?></label>
                                            <input
                                                type="text"
                                                class="form-control numberonly"
                                                placeholder="<?= $lang_en['Enter_Total_Day'] ?>"
                                                name="day"
                                                required="" />
                                        </div>

                                        <div class="form-group mb-3">
                                            <label id="Package-Price"><?= $lang_en['Package_Price'] ?></label>
                                            <input
                                                type="text"
                                                class="form-control numberonly"
                                                placeholder="<?= $lang_en['Enter_Price'] ?>"
                                                name="price"
                                                required="" />
                                        </div>
                                        <div class="form-group mb-3">
                                            <label id="Package-Image" ><?= $lang_ar['Package_Image'] ?></label>
                                            <input type="file" class="form-control" name="cat_img">
                                            <br>
                                            <img src="" width="100px" />
                                        </div>
                                        <div class="form-group mb-3">
                                            <label id="status-label" for="inputGroupSelect01"><?= $lang_en['Select_Status'] ?></label>
                                            <select class="form-control" name="status" id="inputGroupSelect01" required>
                                                <option value=""><?= $lang_en['Choose'] ?>...</option>
                                                <option value="1" ><?= $lang_en['Publish'] ?></option>
                                                <option value="0" ><?= $lang_en['Unpublish'] ?></option>
                                            </select>
                                        </div>

                                        <input type="hidden" name="type" value="add_package" />
                                    </div>
                                    <div class="card-footer text-left">

                                        <button id="add-package" type="submit" class="btn btn-primary">
                                            <?= $lang_en['Add_Package'] ?>
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
    function changeLanguage(lang) {
        var langData = (lang === "ar") ? langDataAR : langDataEN;

        document.getElementById('status-label').textContent = langData.Select_Status;
        document.getElementById('Package-Image').textContent = langData.Package_Image;
        document.getElementById('Package-Price').textContent = langData.Package_Price;
        document.getElementById('Package-Total-Day').textContent = langData.Package_Total_Day;

        if (document.getElementById('add-package')) {
            document.querySelector('button[type="submit"]').textContent = langData.Add_Package;

        } else {
            document.querySelector('button[type="submit"]').textContent = langData.Edit_Package;

        }

        const statusSelect = document.getElementById('inputGroupSelect01');
        statusSelect.querySelector('option[value=""]').textContent = langData.Choose;
        statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
        statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

    }
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>