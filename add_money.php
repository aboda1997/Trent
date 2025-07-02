<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Create_Wallet', $per)) {

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
                                Wallet Management

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


                            <form
                                onsubmit="return submitform(true)"

                                method="post" enctype="multipart/form-data">

                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
                                            <div class="form-group mb-3">
                                                <label id="prop_owner">
                                                    <span class="text-danger">*</span>
                                                    <?= $lang_en['Select_Owner'] ?>

                                                </label>
                                                <select
                                                    name="propowner" id="owner" class=" form-control" required="">
                                                    <option value="">
                                                        <?= $lang_en['Choose'] ?>...</option>
                                                    <?php
                                                    $zone = $rstate->query("select * from tbl_user where status = 1 ");
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
                                                    please enter wallet Owner

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">

                                            <div class="form-group mb-3 ">
                                                <label><span class="text-danger">*</span> Amount</label>
                                                <input type="text"
                                                    class="form-control money-input"
                                                    placeholder="Enter Amount value"
                                                    name="money"
                                                    required>
                                                <div class="invalid-feedback" id="money_feedback" style="display: none;">
                                                    please enter Amount value that range between -99999 to 99999 </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">

                                            <div class="form-group mb-3 ">
                                                <label><span class="text-danger">*</span> Notes</label>
                                                <input type="text" class="form-control " placeholder="Enter Notes To Owner " name="notes" required="">
                                                <input type="hidden" name="type" value="add_money" />
                                                <div class="invalid-feedback" id="note_feedback" style="display: none;">
                                                    please Enter Notes to Owner
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                </div>
                                <div class="card-footer text-left">
                                    <button onclick="return validateForm()" id="add-money" type="submit" class="btn btn-primary"> Add Money </button>
                                </div>
                            </form>
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
    });


    function validateForm(edit = false) {
        // Clear previous feedback
        document.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
            feedback.style.display = 'none';
        });

        const money = document.querySelector('input[name="money"]').value;
        const notes = document.querySelector('input[name="notes"]').value;
        const owner = document.querySelector('select[name="propowner"]').value;

        let isValid = true;

        if (!money || money > 99999 || money < -99999) {
            document.getElementById('money_feedback').style.display = 'block';
            isValid = false;

        }
        if (!notes) {
            document.getElementById('note_feedback').style.display = 'block';
            isValid = false;

        }
        if (!owner) {
            document.getElementById('owner_feedback').style.display = 'block';
            isValid = false;

        }

        if (!isValid) {
            return false;
        }

        return true; // Allow form submission
    }
</script>
<script>
    $(document).on('input', '.money-input', function() {
        // Allow: numbers, single decimal point, and single minus at start
        this.value = this.value.replace(/[^0-9\.\-]/g, '');

        // Only allow minus at the start
        if (this.value.indexOf('-') > 0) {
            this.value = this.value.replace('-', '');
        }

        // Only allow one minus
        if ((this.value.match(/-/g) || []).length > 1) {
            this.value = '-' + this.value.replace(/-/g, '');
        }

        // Only allow one decimal point
        if ((this.value.match(/\./g) || []).length > 1) {
            this.value = this.value.substring(0, this.value.lastIndexOf('.'));
        }
    });
</script>
<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
</body>

</html>