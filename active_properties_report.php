<?php
require 'include/main_head.php';
$lang_code = load_language_code()["language_code"];
$per = $_SESSION['permissions'];

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
                                Active Properties Report</h3>
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                </div>
            </div>
            <!-- Container-fluid starts-->
            <!-- Container-fluid starts-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Date Filter Form and Export Button -->
                                <div class="mb-3 row">
                                    <form id="exportForm" method="get" class="col-sm-12">
                                        <div class="row align-items-end">
                                            <input type="hidden" name="type" value="Active_Prop_report" />

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
                                    <!-- Search Form -->
                                    <div class="row justify-content-center mb-3">
                                        <div class="col-md-8">
                                            <div class="search-container">
                                                <form method="get" action="">
                                                    <input type="hidden" name="type" value="Active_Prop_report" />
                                                    <div class="input-group">
                                                        <input type="text" name="search" class="form-control" placeholder="Search by property ID, owner name or mobile..."
                                                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="submit">Search</button>
                                                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                                                <a href="?type=Active_Prop_report" class="btn btn-secondary">Clear</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table" id="active-users-table">
                                        <thead>
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>Property ID</th>
                                                <th>Property Title</th>
                                                <th>Owner Full Name</th>
                                                <th>Owner Mobile</th>
                                                <th>Booking Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Pagination configuration
                                            $records_per_page = 10;
                                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                            $page = max($page, 1);

                                            // Base query - changed to start from tbl_book and LEFT JOIN tbl_user
                                            $query = "SELECT 
                                        b.id AS property_id,
                                        b.prop_title AS title,
                                        b.add_user_id,
                                        u.name,
                                        u.ccode,
                                        u.mobile,
                                        COUNT(b.id) AS booking_count
                                    FROM 
                                        tbl_book b
                                    LEFT JOIN 
                                        tbl_user u ON b.add_user_id = u.id
                                    WHERE 
                                        b.book_status IN ('Check_in', 'Confirmed' ,'Completed')";

                                            // Add search condition if search term exists
                                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                                $search_term = $rstate->real_escape_string($_GET['search']);
                                                $query .= " AND (
                                        b.id LIKE '%$search_term%' OR 
                                        u.name LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci OR 
                                        CONCAT(u.ccode, u.mobile) LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci
                                    )";
                                            }

                                            $query .= " GROUP BY b.add_user_id ORDER BY booking_count DESC";

                                            // Get total number of records
                                            $count_query = "SELECT COUNT(*) as total FROM (
                                    SELECT b.add_user_id 
                                    FROM tbl_book b
                                    LEFT JOIN tbl_user u ON b.add_user_id = u.id
                                    WHERE b.book_status IN ('Check_in', 'Confirmed')";

                                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                                $count_query .= " AND (
                                        b.id LIKE '%$search_term%' OR 
                                        u.name LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci OR 
                                        CONCAT(u.ccode, u.mobile) LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci
                                    )";
                                            }
                                            $count_query .= " GROUP BY b.add_user_id) as count_table";

                                            $count_result = $rstate->query($count_query);
                                            $total_records = $count_result->fetch_assoc()['total'];
                                            $total_pages = ceil($total_records / $records_per_page) == 0 ? 1 : ceil($total_records / $records_per_page);
                                            $page = min($page, $total_pages);

                                            // Add LIMIT to query for pagination
                                            $offset = ($page - 1) * $records_per_page;
                                            $query .= " LIMIT $offset, $records_per_page";

                                            $result = $rstate->query($query);
                                            $i = $offset + 1;
                                            $has_records = false;

                                            if ($result->num_rows > 0) {
                                                $has_records = true;
                                                while ($row = $result->fetch_assoc()) {
                                            ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo htmlspecialchars($row['property_id']); ?></td>
                                                        <td><?php echo htmlspecialchars(json_decode($row['title']??'', true)['en'] ?? ''); ?></td>
                                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['ccode'] . $row['mobile']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['booking_count']); ?></td>
                                                    </tr>
                                            <?php
                                                    $i++;
                                                }
                                            }

                                            if (!$has_records) {
                                                echo '<tr><td colspan="6" class="text-center">No records found</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <!-- Manual Pagination Links -->
                                    <?php if ($total_records > 0 && $total_pages > 1): ?>
                                        <div class="pagination">
                                            <?php if ($page > 1): ?>
                                                <a href="?type=Active_Prop_report&page=1<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">First</a>
                                                <a href="?type=Active_Prop_report&page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Previous</a>
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
                                                    <a href="?type=Active_Prop_report&page=<?php echo $p; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $p; ?></a>
                                                <?php endif; ?>
                                            <?php endfor; ?>

                                            <?php if ($page < $total_pages): ?>
                                                <a href="?type=Active_Prop_report&page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Next</a>
                                                <a href="?type=Active_Prop_report&page=<?php echo $total_pages; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Last</a>
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

        </div>
        <!-- footer start-->

    </div>
</div>
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

<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
</body>

</html>