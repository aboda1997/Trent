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
                                Setting Management</h3>
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
                            <div class="card-body">


                                <h5 class="h5_set"><i class="fa fa-gear fa-spin"></i> General Information</h5>
                                <form onsubmit="return submitform(validateForm(true)) ;"
                                    method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Website Name</label>
                                            <input type="text" class="form-control " placeholder="Enter Website Name" value="<?php echo $set['webname']; ?>" name="webname" required="">
                                            <input type="hidden" name="type" value="edit_setting" />
                                            <input type="hidden" name="id" value="1" />
                                            <div class="invalid-feedback" id="name_feedback" style="display: none;">
                                                <?= $lang_en['setting_name'] ?>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-4" style="margin-bottom: 48px;">
                                            <label><span class="text-danger">*</span> Website Image</label>
                                            <div class="custom-file">
                                                <input type="file" accept=".jpg, .jpeg, .png, .gif" name="weblogo" class="custom-file-input form-control">
                                                <label class="custom-file-label">Choose Website Image</label>
                                                <br>
                                                <img src="<?php echo $set['weblogo']; ?>" width="60" height="60" />
                                            </div>
                                            <div class="invalid-feedback" id="img_feedback" style="display: none;">
                                                <?= $lang_en['setting_img'] ?>
                                            </div>
                                        </div>


                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Tax (Percent)</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Tax" value="<?php echo $set['tax']; ?>" name="tax" required="">
                                            <div class="invalid-feedback" id="tax_feedback" style="display: none;">
                                                <?= $lang_en['setting_tax'] ?>
                                            </div>
                                        </div>




                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Show Add Property Button ? </label>
                                            <select class="form-control" name="show_property" required>
                                                <option value="">Select Option</option>
                                                <option value="1" <?php if ($set['show_property'] == 1) {
                                                                        echo 'selected';
                                                                    } ?>>Yes</option>
                                                <option value="0" <?php if ($set['show_property'] == 0) {
                                                                        echo 'selected';
                                                                    } ?>>No</option>

                                            </select>
                                            <div class="invalid-feedback" id="property_feedback" style="display: none;">
                                                <?= $lang_en['setting_property'] ?>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Enable Gallery Mode</label>
                                            <select class="form-control" name="gmode" required>
                                                <option value="">Select Option</option>
                                                <option value="1" <?php if ($set['gallery_mode'] == 1) {
                                                                        echo 'selected';
                                                                    } ?>>Yes</option>
                                                <option value="0" <?php if ($set['gallery_mode'] == 0) {
                                                                        echo 'selected';
                                                                    } ?>>No</option>

                                            </select>
                                            <div class="invalid-feedback" id="gmod_feedback" style="display: none;">
                                                <?= $lang_en['setting_gmod'] ?>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Notification Email </label>

                                            <input type="text" class="form-control " placeholder="Enter Notification email" value="<?php echo $set['notification_email']; ?>" name="nemail" required="">
                                            <div class="invalid-feedback" id="nemail_feedback" style="display: none;">
                                                <?= $lang_en['setting_nemail'] ?>
                                            </div>
                                        </div>







                                        <div class="form-group mb-3 col-12">
                                        <h5 class="finance-fees-header"><i class="fa fa-money-bill-wave" aria-hidden="true"></i> Fees Information</h5>                                        </div>


                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Payment Gateway Fees(Percent)</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Payment Gateway Fees as Percent" value="<?php echo $set['gateway_percent_fees']; ?>" name="perfees" required="">
                                            <div class="invalid-feedback" id="perfees_feedback" style="display: none;">
                                                <?= $lang_en['setting_perfees'] ?>
                                            </div>
                                        </div>


                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Payment Gateway Fees(Money EGP)</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Payment Gateway Fees  as Money EGP " value="<?php echo $set['gateway_money_fees']; ?>" name="mfees" required="">
                                            <div class="invalid-feedback" id="mfees_feedback" style="display: none;">
                                                <?= $lang_en['setting_mfees'] ?>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Owner Fees(Percent)</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Owner Fees" value="<?php echo $set['owner_fees']; ?>" name="ofees" required="">
                                            <div class="invalid-feedback" id="ofees_feedback" style="display: none;">
                                                <?= $lang_en['setting_ofees'] ?>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Property Manager Fees(Percent) </label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Property manager Fees" value="<?php echo $set['property_manager_fees']; ?>" name="pfees" required="">
                                            <div class="invalid-feedback" id="pfees_feedback" style="display: none;">
                                                <?= $lang_en['setting_pfees'] ?>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-12">
                                            <h5 class="h5_set"><i class="fa fa-user-plus" aria-hidden="true"></i> Contact Information</h5>
                                        </div>
                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Contact Email </label>

                                            <input type="text" class="form-control " placeholder="Enter contact email" value="<?php echo $set['contact_us_email']; ?>" name="cemail" required="">
                                            <div class="invalid-feedback" id="cemail_feedback" style="display: none;">
                                                <?= $lang_en['setting_cemail'] ?>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Contact Mobile </label>

                                            <input type="text" class="form-control " placeholder="Enter Contact mobile" value="<?php echo $set['contact_us_mobile']; ?>" name="cmobile" required="">
                                            <div class="invalid-feedback" id="cmobile_feedback" style="display: none;">
                                                <?= $lang_en['setting_cmobile'] ?>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-12">
                                            
                                            <h5 class="finance-alert-header"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alert Text</h5>
                                        </div>
                                        <?php
                                        $alert = json_decode($set['alert_text'], true);
                                        ?>
                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Alert Text (English) </label>

                                            <input type="text" class="form-control " placeholder="Enter Alert Text (English)" value="<?php echo $alert['en'] ?? ''; ?>" name="ealert" required="">
                                            <div class="invalid-feedback" id="ealert_feedback" style="display: none;">
                                                <?= $lang_en['setting_ealert'] ?>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Alert Text (Arabic) </label>

                                            <input type="text" class="form-control " placeholder="Enter Alert Text (Arabic)" value="<?php echo $alert['ar'] ?? ''; ?>" name="aalert" required="">
                                            <div class="invalid-feedback" id="aalert_feedback" style="display: none;">
                                                <?= $lang_en['setting_aalert'] ?>
                                            </div>
                                        </div>



                                        <div class="form-group mb-3 col-12">
                                        <h5 class="finance-credentials-header"><i class="fa fa-credit-card-alt" aria-hidden="true"></i> Fawry Credentials</h5>                                        </div>
                                        <?php
                                        $alert = json_decode($set['alert_text'], true);
                                        ?>
                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span>  Merchant Code </label>

                                            <input type="text" class="form-control " placeholder="Enter Merchant Code " value="<?php echo $set['merchant_code']; ?>" name="mcode" required="">
                                            <div class="invalid-feedback" id="mcode_feedback" style="display: none;">
                                                <?= $lang_en['setting_mcode'] ?>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Secure Key </label>

                                            <input type="text" class="form-control " placeholder="Enter Secure key" value="<?php echo $set['secure_key']; ?>" name="skey" required="">
                                            <div class="invalid-feedback" id="skey_feedback" style="display: none;">
                                                <?= $lang_en['setting_skey'] ?>
                                            </div>
                                        </div>

                                        <?php
													if (in_array('Update_Setting', $per) ) {
														?>
                                        <div class="col-12">
                                            <button onclick="return validateForm(true)"  name="edit_setting" class="btn btn-primary mb-2">Edit Setting</button>
                                        </div>
                                        <?php
												}
												?>
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
<!-- latest jquery-->
<script>
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

    document.querySelector('input[name="weblogo"]').addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            // Check if the file type is valid
            if (!allowedTypes.includes(file.type)) {
                this.value = ''; // Clear invalid file
            }
        }
    });

    function validateEmail(email) {
        var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailPattern.test(email);
    }

    function validateEgyptianPhone(phone) {
        // Remove spaces, dashes, or any non-numeric characters
        phone = phone.replace(/\s|-/g, "");

        // Egyptian phone number regex pattern
        let regex = /^(?:\+20|0)?1[0-2,5]\d{8}$/;

        return regex.test(phone);
    }

    function getCurrentLanguage() {
        // Get the active tab
        /// const activeTab = document.querySelector('.nav-link.active').getAttribute('href').substring(1);
        return 'en'
    }

    function validateRange(value, min, max) {
        return value >= min && value <= max;
    }

    function validateForm(edit = false) {
        document.querySelectorAll("div[id$='_feedback']").forEach(function(div) {
            div.style.display = 'none';
        });
        const webname = document.querySelector('input[name="webname"]').value;
        const tax = document.querySelector('input[name="tax"]').value;
        const ealert = document.querySelector('input[name="ealert"]').value;
        const aalert = document.querySelector('input[name="aalert"]').value;

        const mcode = document.querySelector('input[name="mcode"]').value;
        const skey = document.querySelector('input[name="skey"]').value;

        const nemail = document.querySelector('input[name="nemail"]').value;
        const cemail = document.querySelector('input[name="cemail"]').value;
        const cmobile = document.querySelector('input[name="cmobile"]').value;
        const perfees = document.querySelector('input[name="perfees"]').value;
        const mfees = document.querySelector('input[name="mfees"]').value;
        const ofees = document.querySelector('input[name="ofees"]').value;
        const pfees = document.querySelector('input[name="pfees"]').value;
        const webLogo = document.querySelector('input[name="weblogo"]').value;
        const show_property = document.querySelector('select[name="show_property"]').value;
        const gmode = document.querySelector('select[name="gmode"]').value;

        let isValid = true;
        let isArabicValid = true;
        let isEnglishValid = true;
        let alertMessage = '';
        let lang = getCurrentLanguage();
        if (!webLogo) {

            if (edit) {
                isValid = true;

            } else {
                document.getElementById('img_feedback').style.display = 'block';
                isValid = false;
            }
        }

        if (!webname) {
            document.getElementById('name_feedback').style.display = 'block';
            isValid = false;
        }
        if (!ealert) {
            document.getElementById('ealert_feedback').style.display = 'block';
            isValid = false;
        }
        if (!aalert) {
            document.getElementById('aalert_feedback').style.display = 'block';
            isValid = false;
        }

        if (!mcode) {
            document.getElementById('mcode_feedback').style.display = 'block';
            isValid = false;
        }
        if (!skey) {
            document.getElementById('skey_feedback').style.display = 'block';
            isValid = false;
        }
        if (!tax || !validateRange(tax, 1, 100)) {
            document.getElementById('tax_feedback').style.display = 'block';
            isValid = false;

        }

        if (!nemail) {
            document.getElementById('nemail_feedback').style.display = 'block';
            isValid = false;

        }
        if (!validateEmail(nemail)) {
            document.getElementById('nemail_feedback').style.display = 'block';
            isValid = false;
        }
        if (!cemail || !validateEmail(cemail)) {
            document.getElementById('cemail_feedback').style.display = 'block';
            isValid = false;

        }
        if (!cmobile || !validateEgyptianPhone(cmobile)) {
            document.getElementById('cmobile_feedback').style.display = 'block';
            isValid = false;

        }
        if (!perfees || !validateRange(perfees, 1, 100)) {
            document.getElementById('perfees_feedback').style.display = 'block';
            isValid = false;

        }
        if (!mfees || !validateRange(mfees, 0, 5)) {
            document.getElementById('mfees_feedback').style.display = 'block';
            isValid = false;

        }
        if (!ofees || !validateRange(ofees, 1, 100)) {
            document.getElementById('ofees_feedback').style.display = 'block';
            isValid = false;

        }
        if (!pfees || !validateRange(pfees, 1, 100)) {
            document.getElementById('pfees_feedback').style.display = 'block';
            isValid = false;

        }
        if (!show_property) {
            document.getElementById('property_feedback').style.display = 'block';
            isValid = false;

        }
        if (!gmode) {
            document.getElementById('gmod_feedback').style.display = 'block';
            isValid = false;

        }

        if (!isValid) {
            return false;
        }
        return true; // Allow form submission
    }
</script>

<?php
require 'include/footer.php';
?>
</body>

</html>