<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Read_Why_Choose_Us', $per)) {



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
                                <?= $lang['Why_Choose_List_Management'] ?>

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
                                <div class="table-responsive">
                                    <table class="display" id="basic-1">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <?= $lang['Sr_No'] ?>

                                                    .</th>
                                                <th>
                                                    <?= $lang['Why_Chooser_Title'] ?>

                                                </th>
                                                <th>
                                                    <?= $lang['Why_Choose_Image'] ?>

                                                </th>
                                                <th>
                                                    <?= $lang['Why_Choose_bg'] ?>

                                                </th>

                                                <th>
                                                    <?= $lang['Why_Choose_is_Header'] ?>

                                                </th>
                                                <?php
                                                if (in_array('Update_Why_Choose_Us', $per) || in_array('Delete_Why_Choose_Us', $per)) {
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
                                            $city = $rstate->query("select * from tbl_why_choose_us");
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

                                                    <td class="align-middle">
                                                        <img src="<?php echo $row['img']; ?>" width="70" height="80" />
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo $row['background_color']; ?>
                                                    </td>


                                                    <td class="align-middle">
                                                        <span class="badge status-toggle <?php echo $row['is_header'] ? 'badge-success' : 'badge-danger'; ?>"
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-status="<?php echo $row['is_header']; ?>">
                                                            <?php echo $row['is_header']  ? "Yes" : "No"; ?>
                                                        </span>
                                                    </td>
                                                    <?php
                                                if (in_array('Update_Why_Choose_Us', $per) || in_array('Delete_Why_Choose_Us', $per)) {
                                                    ?>

                                                            <td style="white-space: nowrap; width: 15%;">
                                                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                                    <div class="btn-group btn-group-sm" style="float: none;">
                                                                    <?php
                                                if (in_array('Update_Why_Choose_Us', $per) ) {
                                                    ?>
                                                                        <!-- Update Button -->
                                                                        <a href="add_why_choose_us.php?id=<?php echo $row['id']; ?>" class="tabledit-edit-button" style="float: none; margin: 5px;">
                                                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect width="30" height="30" rx="15" fill="#79F9B4" />
                                                                                <path d="M22.5168 9.34109L20.6589 7.48324C20.0011 6.83703 18.951 6.837 18.2933 7.49476L16.7355 9.06416L20.9359 13.2645L22.5052 11.7067C23.163 11.0489 23.163 9.99885 22.5168 9.34109ZM15.5123 10.2873L8 17.8342V22H12.1658L19.7127 14.4877L15.5123 10.2873Z" fill="#25314C" />
                                                                            </svg>
                                                                        </a>

                                                                        <?php } ?>
                                                                        <?php
                                                if ( in_array('Delete_Why_Choose_Us', $per)) {
                                                    ?>
                                                                            <button type="button"
                                                                        style="background: none; border: none; padding: 0; cursor: pointer;"
                                                                        data-toggle="modal"
                                                                        data-target="#approveModal"
                                                                        data-id="<?php echo $row['id']; ?>"
                                                                        title="Delete">
                                                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <rect width="30" height="30" rx="15" fill="#FF6B6B" />
                                                                            <path d="M10 10L20 20M20 10L10 20" stroke="#FFFFFF" stroke-width="2" />
                                                                        </svg>
                                                                    </button>

                                                                        <?php } ?>
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

<!-- Confirmation Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Confirm Approval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approveForm">

                <input type="hidden" id="approveId" name="id">
                <input type="hidden" name="type" value="delete_why_choose_us" />
            </form>
            <div class="modal-body">
                Are you sure you want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmApproveBtn">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- latest jquery-->
<script>
    $('#approveModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');

        var modal = $(this);
        modal.find('#approveId').val(id);
    });
    // When save button is clicked
    $('#confirmApproveBtn').click(function() {


        var formData = $('#approveForm').serialize();

        // Here you would typically make an AJAX call to save the data
        $.ajax({
            url: "include/property.php",
            type: "POST",
            data: formData,
            success: function(response) {
                let res = JSON.parse(response); // Parse the JSON response

                if (res.ResponseCode === "200" && res.Result === "true") {
                    $('#approveModal').removeClass('show');
                    $('#approveModal').css('display', 'none');
                    $('.modal-backdrop').remove(); // Remove the backdrop

                    // Display notification
                    $.notify('<i class="fas fa-bell"></i>' + res.title, {
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

                    // Redirect after a delay if an action URL is provided
                    if (res.action) {
                        setTimeout(function() {
                            window.location.href = res.action;
                        }, 2000);
                    }
                } else {
                    alert("'Error saving payout Approval.");
                }
            }
        });
    });
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>