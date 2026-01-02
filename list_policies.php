<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Read_Cancellation_Policy', $per)) {


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
                                <?= $lang['privacy_policy_management'] ?>

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
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <form id="exportForm" method="get" class="col-sm-12">
                                        <div class="row justify-content-end align-items-start">
                                            <input type="hidden" name="type" value="export_cancellation_policy_data" />

                                            <!-- Export Button -->
                                            <div class="col-md-2">
                                                <button type="button" id="exportExcel" class="btn btn-success w-100">
                                                    <i class="fa fa-file-excel-o"></i> Export Excel
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="display" id="basic-1">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <?= $lang['Sr_No'] ?>

                                                    .</th>
                                                <th>
                                                    <?= $lang['policy_title'] ?>

                                                </th>

                                                <th>
                                                    <?= $lang['is_recommended'] ?>

                                                </th>


                                                <th>
                                                    <?= $lang['privacy_status'] ?>

                                                </th>
                                                <?php
                                                if (in_array('Update_Cancellation_Policy', $per) || in_array('Delete_Cancellation_Policy', $per)) {
                                                ?>

                                                    <th>
                                                        <?= $lang['Action'] ?></th>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $city = $rstate->query("select * from tbl_cancellation_policy");
                                            $i = 0;
                                            while ($row = $city->fetch_assoc()) {
                                                $title = json_decode($row['title'], true);
                                                $description = json_decode($row['description'], true);

                                                $i = $i + 1;
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i; ?>
                                                    </td>

                                                    <td class="align-middle">
                                                        <?php echo $title[$lang_code]; ?>
                                                    </td>


                                                    <td>
                                                        <span class="badge status-toggle <?php echo $row['is_recommended'] ? 'badge-success' : 'badge-danger'; ?>"
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-status="<?php echo $row['is_recommended']; ?>"
                                                            style="cursor: pointer;">
                                                            <?php echo $row['is_recommended']  ? "Yes" : "No"; ?>
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <span class="badge status-toggle <?php echo $row['status'] ? 'badge-success' : 'badge-danger'; ?>"
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-status="<?php echo $row['status']; ?>"
                                                            style="cursor: pointer;">
                                                            <?php echo $row['status']  ? "Publish" : "Unpublish"; ?>
                                                        </span>
                                                    </td>

                                                    <?php
                                                    if (in_array('Update_Cancellation_Policy', $per) || in_array('Delete_Cancellation_Policy', $per)) {
                                                    ?>

                                                        <td style="white-space: nowrap; width: 15%;">
                                                            <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                                <div class="btn-group btn-group-sm" style="float: none;">

                                                                    <!-- Update Button -->
                                                                    <a href="add_policies.php?id=<?php echo $row['id']; ?>" class="tabledit-edit-button" style="float: none; margin: 5px;">
                                                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <rect width="30" height="30" rx="15" fill="#79F9B4" />
                                                                            <path d="M22.5168 9.34109L20.6589 7.48324C20.0011 6.83703 18.951 6.837 18.2933 7.49476L16.7355 9.06416L20.9359 13.2645L22.5052 11.7067C23.163 11.0489 23.163 9.99885 22.5168 9.34109ZM15.5123 10.2873L8 17.8342V22H12.1658L19.7127 14.4877L15.5123 10.2873Z" fill="#25314C" />
                                                                        </svg>
                                                                    </a>


                                                                </div>
                                                            </div>
                                                        </td>

                                                    <?php } ?>

                                                </tr>
                                            <?php
                                            }
                                            ?>

                                        </tbody>
                                    </table>
                                </div>
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
    $('#exportExcel').click(function() {

        // Disable the button to prevent multiple clicks
        var saveButton = $(this);
        saveButton.prop('disabled', true);
        var formData = $('#exportForm').serialize();

        // Here you would typically make an AJAX call to save the data
        $.ajax({
            url: "include/property.php",
            type: "POST",
            data: formData,
            xhrFields: {
                responseType: 'blob' // Important for binary response
            },
            success: function(blob, status, xhr) {
                // Check for filename in headers
                var filename = '';
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }

                // Create download link
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(blob);
                a.href = url;
                a.download = filename || 'download.csv';
                document.body.appendChild(a);
                a.click();


                $.notify('<i class="fas fa-bell"></i> Export completed successfully!', {
                    type: 'theme',
                    allow_dismiss: true,
                    delay: 2000,
                    showProgressbar: true,
                    timer: 300,
                    animate: {
                        enter: 'animated fadeInDown',
                        exit: 'animated fadeOutUp',
                    },
                });
                saveButton.removeAttr('disabled');

            },
            error: function() {
                $.notify('<i class="fas fa-exclamation-circle"></i> Error Export Excel Sheet ', {
                    type: 'danger',
                    allow_dismiss: true,
                    delay: 5000
                });
                saveButton.removeAttr('disabled');

            }
        });
    });
</script>

<?php
require 'include/footer.php';
?>
</body>

</html>