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
                                <form method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Website Name</label>
                                            <input type="text" class="form-control " placeholder="Enter Store Name" value="<?php echo $set['webname']; ?>" name="webname" required="">
                                            <input type="hidden" name="type" value="edit_setting" />
                                            <input type="hidden" name="id" value="1" />
                                        </div>

                                        <div class="form-group mb-3 col-4" style="margin-bottom: 48px;">
                                            <label><span class="text-danger">*</span> Website Image</label>
                                            <div class="custom-file">
                                                <input type="file" accept=".jpg, .jpeg, .png, .gif" name="weblogo" class="custom-file-input form-control">
                                                <label class="custom-file-label">Choose Website Image</label>
                                                <br>
                                                <img src="<?php echo $set['weblogo']; ?>" width="60" height="60" />
                                            </div>
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label for="cname">Select Timezone</label>
                                            <select name="timezone" class="form-control" required>
                                                <option value="">Select Timezone</option>
                                                <?php
                                                $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                                $limit =  count($tzlist);
                                                ?>
                                                <?php
                                                for ($k = 0; $k < $limit; $k++) {
                                                ?>
                                                    <option <?php echo $tzlist[$k]; ?> <?php if ($tzlist[$k] == $set['timezone']) {
                                                                                            echo 'selected';
                                                                                        } ?>><?php echo $tzlist[$k]; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Currency</label>
                                            <input type="text" class="form-control" placeholder="Enter Currency" value="<?php echo $set['currency']; ?>" name="currency" required="">
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Tax</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Tax" value="<?php echo $set['tax']; ?>" name="tax" required="">
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Sms Type</label>
                                            <select class="form-control" name="sms_type">
                                                <option value="">select sms type</option>
                                                <option value="Msg91" <?php if ($set['sms_type'] == 'Msg91') {
                                                                            echo 'selected';
                                                                        } ?>>Msg91</option>
                                                <option value="Twilio" <?php if ($set['sms_type'] == 'Twilio') {
                                                                            echo 'selected';
                                                                        } ?>>Twilio</option>

                                            </select>
                                        </div>




                                        <div class="form-group mb-3 col-12">
                                            <h5 class="h5_set"><i class="fas fa-sms"></i> Msg91 Sms Configurations</h5>
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span>Msg91 Auth Key</label>
                                            <input type="text" class="form-control " placeholder="Msg91 Auth Key" value="<?php echo $set['auth_key']; ?>" name="auth_key" required="">
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Msg91 Otp Template Id</label>
                                            <input type="text" class="form-control " placeholder="Msg91 Otp Template Id" value="<?php echo $set['otp_id']; ?>" name="otp_id" required="">
                                        </div>


                                        <div class="form-group mb-3 col-12">
                                            <h5 class="h5_set"><i class="fas fa-sms"></i> Twilio Sms Configurations </h5>
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span>Twilio Account SID</label>
                                            <input type="text" class="form-control " placeholder="Twilio Account SID" value="<?php echo $set['acc_id']; ?>" name="acc_id" required="">
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Twilio Auth Token</label>
                                            <input type="text" class="form-control " placeholder="Twilio Auth Token" value="<?php echo $set['auth_token']; ?>" name="auth_token" required="">
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Twilio Phone Number</label>
                                            <input type="text" class="form-control " placeholder="Twilio Phone Number" value="<?php echo $set['twilio_number']; ?>" name="twilio_number" required="">
                                        </div>


                                        <div class="form-group mb-3 col-12">
                                            <h5 class="h5_set"><i class="fa fa-phone"></i> Otp Configurations</h5>
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Otp Auth In Sign up ? </label>
                                            <select class="form-control" name="otp_auth">
                                                <option value="">Select Option</option>
                                                <option value="Yes" <?php if ($set['otp_auth'] == 'Yes') {
                                                                        echo 'selected';
                                                                    } ?>>Yes</option>
                                                <option value="No" <?php if ($set['otp_auth'] == 'No') {
                                                                        echo 'selected';
                                                                    } ?>>No</option>

                                            </select>
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Show Add Property Button ? </label>
                                            <select class="form-control" name="show_property">
                                                <option value="">Select Option</option>
                                                <option value="Yes" <?php if ($set['show_property'] == 'Yes') {
                                                                        echo 'selected';
                                                                    } ?>>Yes</option>
                                                <option value="No" <?php if ($set['show_property'] == 'No') {
                                                                        echo 'selected';
                                                                    } ?>>No</option>

                                            </select>
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Enable gallery mode</label>
                                            <select class="form-control" name="gmode">
                                                <option value="">Select Option</option>
                                                <option value="1" <?php if ($set['gallery_mode'] == 1) {
                                                                        echo 'selected';
                                                                    } ?>>Yes</option>
                                                <option value="0" <?php if ($set['gallery_mode'] == 0) {
                                                                        echo 'selected';
                                                                    } ?>>No</option>

                                            </select>
                                        </div>

                                        <div class="form-group mb-3 col-12">
                                            <h5 class="h5_set"><i class="fa fa-signal"></i> Onesignal Information</h5>
                                        </div>
                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> User App Onesignal App Id</label>
                                            <input type="text" class="form-control " placeholder="Enter User App Onesignal App Id" value="<?php echo $set['one_key']; ?>" name="one_key" required="">
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> User App Onesignal Rest Api Key</label>
                                            <input type="text" class="form-control " placeholder="Enter User Boy App Onesignal Rest Api Key" value="<?php echo $set['one_hash']; ?>" name="one_hash" required="">
                                        </div>





                                        <div class="form-group mb-3 col-12">
                                            <h5 class="h5_set"><i class="fa fa-user-plus" aria-hidden="true"></i> Refer And Earn Information</h5>
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Notification email </label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Notification email" value="<?php echo $set['notification_email']; ?>" name="nemail" required="">
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Owner Fees</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Owner Fees" value="<?php echo $set['owner_fees']; ?>" name="ofees" required="">
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Property manager Fees </label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Property manager Fees" value="<?php echo $set['property_manager_fees']; ?>" name="pfees" required="">

                                        </div>
                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Sign Up Credit</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Sign Up Credit" value="<?php echo $set['scredit']; ?>" name="scredit" required="">
                                        </div>


                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Refer Credit</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Refer Credit" value="<?php echo $set['rcredit']; ?>" name="rcredit" required="">
                                        </div>

                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Payout Withdraw Limit</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Withdraw Limit" value="<?php echo $set['wlimit']; ?>" name="wlimit" required="">
                                        </div>








                                        <div class="col-12">
                                            <button type="submit" name="edit_setting" class="btn btn-primary mb-2">Edit Setting</button>
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
</script>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        /* Adjusted width for smaller toggle */
        height: 24px;
        /* Adjusted height for smaller toggle */
        margin-left: 10px;
        /* Space between label and toggle */
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
        /* Adjusted for smaller toggle */
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        /* Adjusted knob height */
        width: 20px;
        /* Adjusted knob width */
        left: 2px;
        /* Adjusted knob position */
        bottom: 2px;
        /* Adjusted knob position */
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:checked+.slider:before {
        transform: translateX(16px);
        /* Adjusted for smaller toggle */
    }
</style>

<?php
require 'include/footer.php';
?>
</body>

</html>