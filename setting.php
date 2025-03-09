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
                                            <label><span class="text-danger">*</span> Tax</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Tax" value="<?php echo $set['tax']; ?>" name="tax" required="">
                                        </div>




                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Show Add Property Button ? </label>
                                            <select class="form-control" name="show_property">
                                                <option value="">Select Option</option>
                                                <option value="1" <?php if ($set['show_property'] == 'Yes') {
                                                                        echo 'selected';
                                                                    } ?>>Yes</option>
                                                <option value="0" <?php if ($set['show_property'] == 'No') {
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
                                        <div class="form-group mb-3 col-4">
                                            <label><span class="text-danger">*</span> Notification email </label>
                                            <input type="text" class="form-control " placeholder="Enter Notification email" value="<?php echo $set['notification_email']; ?>" name="nemail" required="">
                                        </div>

                                        





                                        <div class="form-group mb-3 col-12">
                                            <h5 class="h5_set"><i class="fa fa-user-plus" aria-hidden="true"></i>  Fees Information</h5>
                                        </div>

                                        
                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span>  Payment Gateway Fees(Percent)</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Payment Gateway Fees as Percent" value="<?php echo $set['gateway_percent_fees']; ?>" name="perfees" required="">
                                        </div>

                                        
                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span>  Payment Gateway Fees(Money EGP)</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Payment Gateway Fees  as Money EGP " value="<?php echo $set['gateway_money_fees']; ?>" name="mfees" required="">
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Owner Fees</label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Owner Fees" value="<?php echo $set['owner_fees']; ?>" name="ofees" required="">
                                        </div>

                                        <div class="form-group mb-3 col-6">
                                            <label><span class="text-danger">*</span> Property manager Fees </label>
                                            <input type="text" class="form-control numberonly" placeholder="Enter Property manager Fees" value="<?php echo $set['property_manager_fees']; ?>" name="pfees" required="">

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

<?php
require 'include/footer.php';
?>
</body>

</html>