<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Read_Chat', $per)) {



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
                                <!-- Search Form -->
                                <div class="row justify-content-center mb-3">
                                    <div class="col-md-8">
                                        <div class="search-container">
                                            <form method="get" action="">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Search by Sender, Receiver or Message..."
                                                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="submit">Search</button>
                                                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                                            <a href="?" class="btn btn-secondary">Clear</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="display" id="active-users-table">
                                        <thead>
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>Sender Name</th>
                                                <th>Receiver Name</th>
                                                <th>Message</th>
                                                <th>Updated At</th>
                                                <th>Created At</th>
                                                <?php if (in_array('Update_Chat', $per) || in_array('Delete_Chat', $per)): ?>
                                                    <th><?= $lang['Action'] ?></th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Pagination configuration
                                            $records_per_page = 10;
                                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                            $page = max($page, 1);

                                            // Base query
                                            $query = "SELECT p.*, sender.name AS sender_name, receiver.name AS receiver_name 
                          FROM tbl_messages p
                          INNER JOIN tbl_user sender ON p.sender_id = sender.id
                          INNER JOIN tbl_user receiver ON p.receiver_id = receiver.id";

                                            // Add search condition if search term exists
                                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                                $search_term = $rstate->real_escape_string($_GET['search']);
                                                $query .= " WHERE (sender.name LIKE '%$search_term%' 
                              OR receiver.name LIKE '%$search_term%'
                              OR p.message LIKE '%$search_term%')";
                                            }

                                            // Get total number of records
                                            $count_query = "SELECT COUNT(*) as total FROM tbl_messages p
                                INNER JOIN tbl_user sender ON p.sender_id = sender.id
                                INNER JOIN tbl_user receiver ON p.receiver_id = receiver.id";
                                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                                $count_query .= " WHERE (sender.name LIKE '%$search_term%' 
                                      OR receiver.name LIKE '%$search_term%'
                                      OR p.message LIKE '%$search_term%')";
                                            }

                                            $count_result = $rstate->query($count_query);
                                            $total_records = $count_result->fetch_assoc()['total'];
                                            $total_pages = ceil($total_records / $records_per_page) == 0 ? 1 : ceil($total_records / $records_per_page);
                                            $page = min($page, $total_pages);

                                            // Add LIMIT to query for pagination
                                            $offset = ($page - 1) * $records_per_page;
                                            $query .= " LIMIT $offset, $records_per_page";

                                            $city = $rstate->query($query);
                                            $i = $offset;
                                            $has_records = false;

                                            if ($city->num_rows > 0) {
                                                $has_records = true;
                                                while ($row = $city->fetch_assoc()) {
                                                    $i++;
                                            ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td class="align-middle"><?php echo $row["sender_name"] ?></td>
                                                        <td class="align-middle"><?php echo $row["receiver_name"] ?></td>
                                                        <td class="align-middle"><?php echo $row["message"] ?></td>
                                                        <td class="align-middle"><?php echo $row["updated_at"] ?></td>
                                                        <td class="align-middle"><?php echo $row["created_at"] ?></td>
                                                        <?php if (in_array('Update_Chat', $per) || in_array('Delete_Chat', $per)): ?>
                                                            <td class="align-middle">
                                                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                                    <div class="btn-group btn-group-sm" style="float: none;">
                                                                        <button class="btn btn-success" style="float: none; margin: 5px;"
                                                                            type="button"
                                                                            data-toggle="modal" data-target="#approveModal"
                                                                            data-id="<?php echo $row['id']; ?>"
                                                                            data-status="1"
                                                                            title="Approve">
                                                                            Approve
                                                                        </button>
                                                                        <button type="button" class="btn btn-danger" style="float: none; margin: 5px;"
                                                                            data-toggle="modal" data-target="#rejectModal"
                                                                            data-status="0"
                                                                            data-id="<?php echo $row['id']; ?>">
                                                                            Reject
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                            <?php
                                                }
                                            }

                                            if (!$has_records) {
                                                $colspan = 6; // Default number of columns
                                                if (in_array('Update_Chat', $per) || in_array('Delete_Chat', $per)) {
                                                    $colspan = 7; // Add one more column if action column is present
                                                }
                                                echo '<tr><td colspan="' . $colspan . '" class="text-center"><div>No records found</div></td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <!-- Pagination Links -->
                                    <?php if ($total_records > 0 && $total_pages > 1): ?>
                                        <div class="pagination">
                                            <?php if ($page > 1): ?>
                                                <a href="?page=1<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">First</a>
                                                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Previous</a>
                                            <?php else: ?>
                                                <span class="disabled">First</span>
                                                <span class="disabled">Previous</span>
                                            <?php endif; ?>

                                            <?php
                                            $start_page = max(1, $page - 2);
                                            $end_page = min($total_pages, $page + 2);

                                            for ($p = $start_page; $p <= $end_page; $p++):
                                            ?>
                                                <?php if ($p == $page): ?>
                                                    <span class="current"><?php echo $p; ?></span>
                                                <?php else: ?>
                                                    <a href="?page=<?php echo $p; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $p; ?></a>
                                                <?php endif; ?>
                                            <?php endfor; ?>

                                            <?php if ($page < $total_pages): ?>
                                                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Next</a>
                                                <a href="?page=<?php echo $total_pages; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Last</a>
                                            <?php else: ?>
                                                <span class="disabled">Next</span>
                                                <span class="disabled">Last</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Results Count -->
                                    <?php if ($total_records > 0): ?>
                                        <div class="results-count">
                                            Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $records_per_page, $total_records); ?> of <?php echo $total_records; ?> records
                                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                                (filtered by "<?php echo htmlspecialchars($_GET['search']); ?>")
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Container-fluid Ends-->
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
            },
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
            },
            complete: function() {
                saveButton.prop('disabled', false); // Re-enable button on success/fail
            }
        });
    });
</script>
<style>
    .search-container .input-group {
        max-width: 600px;
        margin: 0 auto;
    }

    .pagination {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 5px;
        margin: 20px 0;
    }

    .pagination a,
    .pagination span {
        padding: 5px 10px;
        border: 1px solid #dee2e6;
        text-decoration: none;
    }

    .pagination .current {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination .disabled {
        color: #6c757d;
        pointer-events: none;
    }

    .results-count {
        text-align: center;
        color: #6c757d;
        margin-bottom: 20px;
    }

    .text-center {
        text-align: center;
        padding: 20px;
        font-size: 1.1em;
        color: #6c757d;
        font-style: italic;
    }
</style>

<!-- JavaScript for Excel Export -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prevent DataTables initialization
        if (typeof $.fn.DataTable === 'function') {
            $('#active-users-table').DataTable({
                paging: false,
                searching: false,
                info: false
            });
        }
    });
</script>

</html>