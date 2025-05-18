<?php
require 'include/main_head.php';
$property_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_code = load_language_code()["language_code"];

if ($_SESSION['restatename'] == 'Staff' && !in_array('Read', $property_per)) {



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
                                Pending Chat List </h3>
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
                                                <th>Sr No.</th>

                                                <th>Sender Name</th>
                                                <th>Receiver Name</th>
                                                <th>Message</th>

                                                <th>updated at</th>
                                                <th>Created at</th>

                                                <?php
                                                if ($_SESSION['restatename'] == 'Staff') {
                                                    if (in_array('Update', $property_per)) {
                                                ?>

                                                        <th>
                                                            <?= $lang['Action'] ?></th>
                                                    <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <th>
                                                        <?= $lang['Action'] ?>

                                                    </th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "
SELECT 
    p.*, 
    sender.name AS sender_name,
    receiver.name AS receiver_name
FROM 
    tbl_messages p
INNER JOIN 
    tbl_user sender ON p.sender_id = sender.id
INNER JOIN 
    tbl_user receiver ON p.receiver_id = receiver.id
";

                                            $city = $rstate->query($query);
                                            $i = 0;
                                            while ($row = $city->fetch_assoc()) {
                                            $i++;
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i; ?>
                                                    </td>

                                                    <td class="align-middle">
                                                        <?php echo $row["sender_name"] ?>
                                                    </td>


                                                    <td class="align-middle">
                                                        <?php echo $row["receiver_name"] ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo $row["message"] ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo $row["updated_at"] ?>
                                                    </td>

                                                    <td class="align-middle">
                                                        <?php echo $row["created_at"] ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                            <div class="btn-group btn-group-sm" style="float: none;">

                                                                <button class="btn btn-success " style="float: none; margin: 5px;"
                                                                    type="button"
                                                                    data-toggle="modal" data-target="#approveModal"
                                                                    data-id="<?php echo $row['id']; ?>"
                                                                    data-status="<?php echo "1"; ?>"

                                                                    title="Approve">
                                                                    Approve
                                                                </button>
                                                                <button type="button" class="btn btn-danger" style="float: none; margin: 5px;"
                                                                    data-toggle="modal" data-target="#rejectModal"
                                                                    data-status="<?php echo "0"; ?>"

                                                                    data-id="<?php echo $row['id']; ?>">
                                                                    Reject
                                                                </button>
                                                            </div>

                                                        </div>

                                                    </td>


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

<?php
require 'include/footer.php';
?>


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
                <input type="hidden" id="approveStatus" name="status">

                <input type="hidden" name="type" value="toggle_message_approval" />
            </form>
            <div class="modal-body">
                Are you sure you want to approve this message?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmApproveBtn">Yes, Approve</button>
            </div>
        </div>
    </div>
</div>


<!-- Confirmation Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Confirm reject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm">

                <input type="hidden" id="rejectId" name="id">
                <input type="hidden" id="rejectStatus" name="status">

                <input type="hidden" name="type" value="toggle_message_approval" />
            </form>
            <div class="modal-body">
                Are you sure you want to reject this message?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmRejectBtn">Yes, Reject</button>
            </div>
        </div>
    </div>
</div>



<script>
    $('#approveModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var status = button.data('status');

        var modal = $(this);
        modal.find('#approveId').val(id);
        modal.find('#approveStatus').val(status);


    });
    $('#rejectModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var status = button.data('status');

        var modal = $(this);
        modal.find('#rejectId').val(id);
        modal.find('#rejectStatus').val(status);


    });
    // When save button is clicked
    $('#confirmApproveBtn').click(function() {

        // Disable the button to prevent multiple clicks
        var saveButton = $(this);
        saveButton.prop('disabled', true);
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
                    alert("'Error saving  Approval.");
                }
            }
            ,
        complete: function() {
            saveButton.prop('disabled', false); // Re-enable button on success/fail
        }
        });
    });

    // When save button is clicked
    $('#confirmRejectBtn').click(function() {

        // Disable the button to prevent multiple clicks
        var saveButton = $(this);
        saveButton.prop('disabled', true);
        var formData = $('#rejectForm').serialize();

        // Here you would typically make an AJAX call to save the data
        $.ajax({
            url: "include/property.php",
            type: "POST",
            data: formData,
            success: function(response) {
                let res = JSON.parse(response); // Parse the JSON response

                if (res.ResponseCode === "200" && res.Result === "true") {
                    $('#rejectModal').removeClass('show');
                    $('#rejectModal').css('display', 'none');
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
                    alert("'Error saving rejection .");
                }
            }
            ,
        complete: function() {
            saveButton.prop('disabled', false); // Re-enable button on success/fail
        }
        });
    });
</script>

</html>