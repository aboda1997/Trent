<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Read_Booking', $per)) {
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
                                Temporal Booking List Management
                            </h3>
                        </div>
                        <div class="col-6">
                            <div id="sendMessageBtnContainer" style="display: none; float: right;">
                                <?php if ( in_array('Delete_Booking', $per)): ?>

                                    <button class="btn btn-primary" id="sendMessageBtn">Delete Temporal Booking</button>
                                <?php endif; ?>

                            </div>
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
                                                    <input type="checkbox" id="selectAllCheckbox">
                                                </th>
                                                <th>
                                                    <?= $lang['Sr_No'] ?>
                                                    .
                                                </th>
                                                <th>
                                                    Property Id
                                                </th>
                                                <th>
                                                    Property Title
                                                </th>
                                                <th>
                                                    From Date
                                                </th>
                                                <th>
                                                    To Date
                                                </th>
                                                <th>
                                                    Guest Name
                                                </th>
                                                <th>
                                                    Guest Mobile
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            date_default_timezone_set('Africa/Cairo');

                                            // Calculate the timestamp 3 hours ago in Cairo time
                                            $three_hours_ago = date('Y-m-d H:i:s', strtotime('-3 hours'));

                                            // Build the SQL query
                                            $sql = "SELECT *
                                            FROM tbl_non_completed 
                                            WHERE 
                                            status = 1
                                            and completed = 0 
                                            AND created_at > '" . $GLOBALS['rstate']->real_escape_string($three_hours_ago) . "'";
                                            $result = $rstate->query($sql);
                                            $i = 0;
                                            while ($row = $result->fetch_assoc()) {
                                                $i = $i + 1;
                                                $client_id = $row['uid'];
                                                $client_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$client_id)->fetch_assoc();
                                                $prop_id = $row['prop_id'];
                                                $propert_data = $rstate->query("SELECT add_user_id , title FROM tbl_property WHERE id=" . (int)$prop_id)->fetch_assoc();
                                                $title = json_decode($propert_data['title'] ?? '', true);

                                            ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="userCheckbox" data-user-id="<?php echo $row['id']; ?>">
                                                    </td>
                                                    <td>
                                                        <?php echo $i; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $prop_id; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($title[$lang_code] ?? ''); ?></td>
                                                    <td>
                                                        <?php echo $row['f1']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['f2']; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo $client_data['name']; ?>
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo $client_data['ccode'] . $client_data['mobile']; ?>
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

        <!-- Message Modal -->
        <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="messageModalLabel">Delete Temporal Booking</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this temporal booking?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmSendBtn">Yes, Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer start-->
    </div>
</div>
<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
<!-- Previous HTML remains the same until the script section -->

<script>
    $(document).ready(function() {
        // Remove invalid class when user starts typing
        $('#messageText').on('input', function() {
            if ($(this).val().trim() !== '') {
                $(this).removeClass('is-invalid');
            }
        });

        var table = $('#basic-1').DataTable();
        var allUserIds = [];
        var selectedUserIds = [];
        var isSelectAll = false;

        // Function to collect all user IDs from the DataTable data
        function getAllUserIds() {
            allUserIds = [];
            table.rows().every(function() {
                var rowNode = this.node();
                var checkbox = $(rowNode).find('.userCheckbox');
                if (checkbox.length) {
                    allUserIds.push(checkbox.data('user-id'));
                }
            });
            return allUserIds;
        }

        // Initialize with all user IDs
        getAllUserIds();

        // Select/Deselect all checkboxes across all pages
        $('#selectAllCheckbox').on('click', function() {
            isSelectAll = this.checked;

            // Update all checkboxes on all pages
            table.$('.userCheckbox').prop('checked', isSelectAll);
            // Update selected IDs
            if (isSelectAll) {
                debugger;

                selectedUserIds = [...allUserIds];
            } else {
                selectedUserIds = [];
            }

            toggleSendButton();
        });

        // When user checks/unchecks individual checkboxes
        $(document).on('change', '.userCheckbox', function() {
            var userId = $(this).data('user-id');

            if (this.checked) {
                if (!selectedUserIds.includes(userId)) {
                    selectedUserIds.push(userId);
                }
            } else {
                selectedUserIds = selectedUserIds.filter(id => id !== userId);
                isSelectAll = false;
                $('#selectAllCheckbox').prop('checked', false);
            }

            // Check if all visible checkboxes are selected
            var allVisibleChecked = table.$('.userCheckbox:checked').length === table.$('.userCheckbox').length;
            $('#selectAllCheckbox').prop('checked', allVisibleChecked);

            toggleSendButton();
        });

        // Update checkboxes when changing pages
        table.on('draw', function() {
            if (isSelectAll) {
                table.$('.userCheckbox').prop('checked', true);
            }
        });

        // Toggle Send Message button visibility
        function toggleSendButton() {
            var hasSelections = isSelectAll || selectedUserIds.length > 0;
            $('#sendMessageBtnContainer').toggle(hasSelections);
        }

        // Send Message button click
        $('#sendMessageBtn').click(function() {
            var userIdsToSubmit = isSelectAll ? allUserIds : selectedUserIds;
            // Disable the button to prevent multiple clicks

            // For form submission
            $('#selectedUserIds').val(JSON.stringify(userIdsToSubmit));

            $('#messageModal').modal('show');
        });
        $('#confirmSendBtn').click(function() {
            var reasonInput = $('#messageText');
            // Disable the button to prevent multiple clicks
            var saveButton = $(this);
            saveButton.prop('disabled', true);
            var selectedUserIds = [];

            if ($('#selectAllCheckbox').prop('checked')) {
                // If "Select All" is checked, use all user IDs
                selectedUserIds = allUserIds;
                debugger;
            } else {

                // Otherwise use only the checked checkboxes
                $('.userCheckbox:checked').each(function() {
                    selectedUserIds.push($(this).data('user-id'));
                });
            }



            if (selectedUserIds.length === 0) {
                alert('Please select at least one row');
                return;
            }

            // Here you would typically make an AJAX call to send the message
            $.ajax({
                url: "include/property.php",
                type: "POST",
                data: {
                    user_ids: selectedUserIds,
                    type: 'delete_on_hold_booking'
                },
                success: function(response) {
                    let res = JSON.parse(response); // Parse the JSON response
                    if (res.ResponseCode === "200" && res.Result === "true") {
                        $('#messageModal').modal('hide');
                        // Reset all selections
                        $('.userCheckbox').prop('checked', false);
                        $('#selectAllCheckbox').prop('checked', false).data('select-all', false);
                        toggleSendButton();
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
                        $.notify('<i class="fas fa-exclamation-circle"></i> Error sending request. Please try again', {
                            type: 'danger',
                            allow_dismiss: true,
                            delay: 5000
                        });
                    }

                },
                error: function() {
                    $.notify('<i class="fas fa-exclamation-circle"></i> Error sending request. Please try again.', {
                        type: 'danger',
                        allow_dismiss: true,
                        delay: 5000
                    });
                }
            });
        });
        // Proper modal dismissal handlers
        $('#messageModal .close, #messageModal .btn-secondary').on('click', function() {
            $('#messageModal').modal('hide');
        });
    });
</script>
</body>

</html>