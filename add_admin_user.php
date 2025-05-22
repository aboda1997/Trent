<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];

if (isset($_GET['id'])) {
    if (!in_array('Update_Admin_User', $per)) {



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
    if (!in_array('Create_Admin_User', $per)) {



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
                                <?= $lang_en['Admin_User_Management'] ?>

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
                                $data = $rstate->query("select * from admin where id=" . $_GET['id'] . "")->fetch_assoc();
                                $username = $data['username'];
                                $type = $data['type'];
                                $password = $data['password'];

                            ?>
                                <form
                                    onsubmit="return submitform(true)"

                                    method="post" enctype="multipart/form-data">

                                    <div class="card-body">
                                        <div id="alert-container" class="mb-3" style="display: none;">
                                            <div class="alert alert-danger" id="alert-message"></div>
                                        </div>


                                        <input type="hidden" name="type" value="edit_admin_user" />
                                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />

                                        <div class="row">
                                            <!-- Username Field (New) -->
                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label for="admin_username"><?= $lang_en['Username'] ?></label>

                                                    <input type="text" class="form-control" id="admin_username" name="username" required
                                                        value="<?php echo $username; ?>"
                                                        placeholder="<?= $lang_en['Enter_Username'] ?>" minlength="3" maxlength="20">
                                                    <div class="invalid-feedback" id="username_feedback" style="display: none;">
                                                        <?= $lang_en['Username_Requirements'] ?>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Added Password Field -->
                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label for="admin_password"><?= $lang_en['Password'] ?></label>
                                                    <input type="password" class="form-control" id="admin_password" name="password" required
                                                        value="<?php echo $password; ?>"
                                                        placeholder="<?= $lang_en['Enter_Password'] ?>" minlength="8">
                                                    <div class="invalid-feedback" id="password_feedback" style="display: none;">
                                                        <?= $lang_en['Password_Requirements'] ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label id="Admin_Type" for="inputGroupSelect01"><?= $lang_en['Admin_Type'] ?></label>
                                                    <select class="form-control" name="user_type" id="inputGroupSelect01" required>
                                                        <option value=""><?= $lang_en['Admin_Type'] ?>...</option>
                                                        <option value="1"
                                                            <?php if ($type == 'Staff') {
                                                                echo 'selected';
                                                            } ?>><?= $lang_en['Staff'] ?></option>
                                                        <option value="0"
                                                            <?php if ($type == 'Admin') {
                                                                echo 'selected';
                                                            } ?>><?= $lang_en['Admin'] ?></option>
                                                    </select>
                                                    <div class="invalid-feedback" id="status_feedback" style="display: none;">
                                                        <?= $lang_en['Admin_type'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="card-footer text-left">
                                        <button onclick="return validateForm(true)" type="submit" class="btn btn-primary">
                                            <?= $lang_en['edit_admin_user'] ?>

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



                                        <input type="hidden" name="type" value="add_admin_user" />

                                        <div class="row">
                                            <!-- Username Field (New) -->
                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label for="admin_username"><?= $lang_en['Username'] ?></label>
                                                    <input type="text" class="form-control" id="admin_username" name="username" required
                                                        placeholder="<?= $lang_en['Enter_Username'] ?>" minlength="3" maxlength="20">
                                                    <div class="invalid-feedback" id="username_feedback" style="display: none;">
                                                        <?= $lang_en['Username_Requirements'] ?>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Added Password Field -->
                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label for="admin_password"><?= $lang_en['Password'] ?></label>
                                                    <input type="password" class="form-control" id="admin_password" name="password" required
                                                        placeholder="<?= $lang_en['Enter_Password'] ?>" minlength="8">
                                                    <div class="invalid-feedback" id="password_feedback" style="display: none;">
                                                        <?= $lang_en['Password_Requirements'] ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                                <div class="form-group mb-3">
                                                    <label id="Admin_Type" for="inputGroupSelect01"><?= $lang_en['Admin_Type'] ?></label>
                                                    <select class="form-control" name="user_type" id="inputGroupSelect01" required>
                                                        <option value=""><?= $lang_en['Admin_Type'] ?>...</option>
                                                        <option value="1"><?= $lang_en['Staff'] ?></option>
                                                        <option value="0"><?= $lang_en['Admin'] ?></option>
                                                    </select>
                                                    <div class="invalid-feedback" id="status_feedback" style="display: none;">
                                                        <?= $lang_en['Admin_type'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-left">
                                            <button onclick="return validateForm()" id="add-new-admin" type="submit" class="btn btn-primary">
                                                <?= $lang_en['add_admin_user'] ?>

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

        const username = document.querySelector('input[name="username"]').value;
        const password = document.querySelector('input[name="password"]').value;
        const type = document.querySelector('select[name="user_type"]').value;

        let isValid = true;

        let alertMessage = '';

        if ((!username || !validateUsername(username))) {
            document.getElementById('username_feedback').style.display = 'block';
            isValid = false;

        }

        if ((!password || !validatePassword(password))) {
            document.getElementById('password_feedback').style.display = 'block';
            isValid = false;

        }
        if (!type) {
            document.getElementById('status_feedback').style.display = 'block';
            isValid = false;
        }

        if (!isValid) {
            return false;
        }

        return true; // Allow form submission
    }

    
    function validateUsername(username) {
        const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/; // Alphanumeric + underscore, 3-20 chars


        if (!usernameRegex.test(username)) {
            return false;
        }

        return true;
    }

    // Password validation rules
    function validatePassword(password) {

        if (password.trim().length < 8) {
            return false;
        }

        return true;
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