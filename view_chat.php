<?php
require 'include/reconfig.php';
$lang = load_language();
$pid = $_POST['pid'];

$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

?>

<!-- Container-fluid starts-->
<div class="container-fluid bg-white mb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">


                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="row justify-content-end align-items-start">
                            <div class="col-md-4">
                                <button type="button"
                                    data-id="<?php echo $pid; ?>"
                                    data-status="1"
                                    title="Approve All"
                                    class="btn all-btn btn-success w-100">
                                    Approve All
                                </button>
                            </div>

                            <div class="col-md-4">
                                <button type="button"

                                    data-id="<?php echo $pid; ?>"
                                    data-status="0"
                                    title="Reject All"
                                    class="btn all-btn btn-danger w-100">
                                    Reject All
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered" id="cancelReasonsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th><?= $lang['Sr_No'] ?></th>
                                    <th>Message</th>
                                    <th>image</th>

                                    <th>Sender Contact</th>
                                    <th>Receiver Contact</th>
                                    <th> Message Status </th>
                                    <th width="10%" class="text-center"><?= $lang['Action'] ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $city = $rstate->query("SELECT p.*, 
                                 sender.ccode as sender_ccode , sender.mobile  as sender_mobile , receiver.ccode as receiver_ccode , receiver.mobile as receiver_mobile
                                 FROM tbl_messages p
                                 INNER JOIN tbl_user sender ON p.sender_id = sender.id
                          INNER JOIN tbl_user receiver ON p.receiver_id = receiver.id
                                 where p.chat_id = $pid ORDER BY p.id ASC");
                                $i = 0;
                                while ($row = $city->fetch_assoc()) {
                                    $message = json_decode($row['message'], true);
                                    $i++;
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i ?></td>
                                        <td class="align-middle"><?php echo $message["message"] ?></td>

                                        <td class="align-middle">
                                            <img src="<?php echo $row['img']; ?>" width="70" height="80" />
                                        </td>
                                        <td class="align-middle"><?php echo $row["sender_ccode"] . $row["sender_mobile"] ?></td>
                                        <td class="align-middle"><?php echo $row["receiver_ccode"] . $row["receiver_mobile"] ?></td>
                                        <td class="text-center status-cell" data-id="<?= $row['id'] ?>">
                                            <span class="badge badge-<?= $row['is_approved'] == 1 ? 'success' : 'danger' ?>">
                                                <?= $row['is_approved'] == 1 ? 'Approved' : 'Unapproved' ?>
                                            </span>
                                        </td>
                                        <?php if (in_array('Update_Cancel_Reason', $per) || in_array('Delete_Cancel_Reason', $per)): ?>
                                            <td class="align-middle">
                                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                    <div class="btn-group btn-group-sm " style="float: none;">
                                                        <button class="btn btn-success approve-btn" style="float: none; margin: 5px;"
                                                            type="button"
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-status="1"
                                                            title="Approve">
                                                            Approve
                                                        </button>
                                                        <button type="button" class="btn btn-danger approve-btn" style="float: none; margin: 5px;"

                                                            data-status="0"

                                                            data-id="<?php echo $row['id']; ?>">
                                                            Reject
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Container-fluid Ends-->



<style>
    /* Responsive table styles */
    @media (max-width: 768px) {
        #cancelReasonsTable {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .card-header .btn {
            margin-top: 10px;
        }
    }

    .table th {
        white-space: nowrap;
    }

    .table td {
        vertical-align: middle;
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize DataTable with responsive features
        $('#cancelReasonsTable').DataTable({
            responsive: true,
            language: {
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">'
        });

        // Delete button handler
        $('.delete-btn').click(function() {
            const id = $(this).data('id');
            if (confirm('<?= $lang['Are_you_sure_you_want_to_delete'] ?>')) {
                window.location.href = 'delete_cancel_reason.php?id=' + id;
            }
        });
    });
</script>

<script>
    // When save button is clicked

    $(document).on('click', '.approve-btn', function(e) {

        // Get the button that was clicked
        var button = $(this);
        // Check if button is already disabled (prevent multiple clicks)
        if (button.prop('disabled')) {
            return false;
        }
        // Disable the button to prevent multiple clicks
        button.prop('disabled', true);
        var formData = {
            id: button.data('id'),
            status: button.data('status'),
            type: 'toggle_message_approval'
        };
        // Here you would typically make an AJAX call to save the data
        $.ajax({
            url: "include/property.php",
            type: "POST",
            data: formData,
            success: function(response) {
                let res = JSON.parse(response); // Parse the JSON response

                if (res.ResponseCode === "200" && res.Result === "true") {

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
                    alert('Success: ' + (res.title || 'Action completed successfully'));
                    $(`td.status-cell[data-id="${button.data('id')}"] .badge`)
                        .removeClass('badge-success badge-danger')
                        .addClass(button.data('status') === 1 ? 'badge-success' : 'badge-danger')
                        .text(button.data('status') === 1 ? 'Approved' : 'Unapproved');
                    button.prop('disabled', false);

                } else {
                    alert("'Error saving  Approval or denial.");
                }
            },
            complete: function() {
                // button.prop('disabled', false); // Re-enable button on success/fail
            }
        });
        e.preventDefault();
        e.stopPropagation();
        return false;
    });



    $(document).on('click', '.all-btn', function(e) {

        // Get the button that was clicked
        var button = $(this);
        // Check if button is already disabled (prevent multiple clicks)
        if (button.prop('disabled')) {
            return false;
        }
        // Disable the button to prevent multiple clicks
        button.prop('disabled', true);
        var formData = {
            id: button.data('id'),
            status: button.data('status'),
            type: 'toggle_all_message_approval'
        };
        // Here you would typically make an AJAX call to save the data
        $.ajax({
            url: "include/property.php",
            type: "POST",
            data: formData,
            success: function(response) {
                let res = JSON.parse(response); // Parse the JSON response

                if (res.ResponseCode === "200" && res.Result === "true") {

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
                    alert('Success: ' + (res.title || 'Action completed successfully'));
                    // Redirect after a delay if an action URL is provided

                    if (res.action) {
                        setTimeout(function() {
                            button.prop('disabled', false);

                            window.location.href = res.action;
                        }, 2000);
                    }

                } else {
                    alert("'Error saving  Approval or denial.");
                }
            },
            complete: function() {
                //button.prop('disabled', false); // Re-enable button on success/fail
            }

        });
        e.preventDefault();
        e.stopPropagation();
        return false;
    });
</script>