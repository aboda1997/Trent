<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Read_Wallet', $per)) {


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
                                Wallet List Management</h3>
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
                                    <!-- Centered Search Form -->
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="search-container" style="margin-bottom: 20px;">
                                                <form method="get" action="">
                                                    <div class="input-group">
                                                        <input type="text" name="search" class="form-control" placeholder="Search by name, or mobile..."
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

                                    <!-- User Table -->
                                    <table class="table" id="users-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <?= $lang['Sr_No'] ?>

                                                    .</th>

                                                <th>User Name</th>
                                                <th>Mobile </th>
                                                <th>Event</th>
                                                <th>Amount</th>
                                                <th>Created At</th>
                                                <th>Employee Name</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Pagination configuration
                                            $records_per_page = 10;
                                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                            $page = max($page, 1);
                                            $query = "SELECT 
                                                wr.*,
                                                u.mobile, u.ccode, u.name,
                                                a.username AS admin_username
                                            FROM 
                                                wallet_report wr
                                            LEFT JOIN 
                                                tbl_user u ON wr.uid = u.id
                                            LEFT JOIN 
                                                admin a ON wr.EmployeeId = a.id";


                                            // Add search condition if search term exists
                                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                                $search_term = $rstate->real_escape_string($_GET['search']);


                                                // For other searches, use LIKE with wildcards
                                                $query .= " WHERE (u.name LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci 
               OR CONCAT(u.ccode, u.mobile) LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci
               OR a.username LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci)";
                                            }

                                            // Get total number of records
                                            $count_query = "SELECT COUNT(*) as total FROM (" . $query . ") as count_table";
                                            // Execute count query
                                            $count_result = $rstate->query($count_query);
                                            $total_records = $count_result->fetch_assoc()['total'];
                                            $total_pages = ceil($total_records / $records_per_page) == 0 ? 1 : ceil($total_records / $records_per_page);
                                            $page = min($page, $total_pages);

                                            // Add LIMIT to query for pagination
                                            $offset = ($page - 1) * $records_per_page;
                                            $query .= " LIMIT $offset, $records_per_page";
                                            $stmt = $rstate->query($query);
                                            $i = $offset + 1;
                                            $j = 0;

                                            if ($total_records > 0) {
                                                while ($row = $stmt->fetch_assoc()) {
                                                    $j += 1;
                                            ?>
                                                    <tr>

                                                        <td>
                                                            <?php echo $i; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['name'] ??''); ?></td>
                                                        <td><?php echo htmlspecialchars($row['ccode'] . $row['mobile']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['message'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($row['amt'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($row['tdate'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($row['admin_username'] ?? ''); ?></td>


                                                    </tr>
                                            <?php
                                                    $i++;
                                                }
                                            } else {
                                                echo '<tr><td colspan="9" class="text-center">No records found</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <!-- Manual Pagination Links - Only show if we have records -->
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

                                    <!-- Results Count - Only show if we have records -->
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
        </div>
        <!-- footer start-->

    </div>
</div>
<!-- latest jquery-->


<?php
require 'include/footer.php';
?>
</body>
<script>
    $('#approveModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var status = button.data('status');

        var modal = $(this);
        modal.find('#approveId').val(id);
        modal.find('#status').val(status);
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
                    alert("'Error saving user deletion.");
                }
            }
        });
    });
</script>


<!-- Add this CSS -->
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .table th,
    .table td {
        padding: 8px;
        border: 1px solid #ddd;
    }

    .search-container {
        margin-bottom: 20px;
    }

    .input-group {
        max-width: 500px;
    }

    .pagination {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        padding: 5px 10px;
        border: 1px solid #ddd;
        text-decoration: none;
    }

    .pagination .current {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .pagination .disabled {
        color: #aaa;
        cursor: not-allowed;
    }

    .badge-success {
        background: #28a745;
        color: white;
        padding: 3px 6px;
        border-radius: 4px;
    }

    .badge-danger {
        background: #dc3545;
        color: white;
        padding: 3px 6px;
        border-radius: 4px;
    }

    .results-count {
        margin-top: 10px;
        font-size: 0.9em;
        color: #666;
    }

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
</style>

<!-- Prevent DataTables initialization -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.DataTable === 'function') {
            $('#users-table').DataTable({
                paging: false,
                searching: false,
                info: false
            });
        }
    });
</script>

</html>