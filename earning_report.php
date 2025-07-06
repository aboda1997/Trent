<?php
require 'include/main_head.php';
$lang_code = load_language_code()["language_code"];
$per = $_SESSION['permissions'];

if (!in_array('Read_Report', $per)) {



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
                                Earning Report</h3>
                        </div>
                        <div class="col-6">

                        </div>
                    </div>
                </div>
            </div><!-- Container-fluid starts-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Date Filter Form and Export Button -->
                                <div class="mb-3 row">
                                    <form id="exportForm" method="get" class="col-sm-12">
                                        <div class="row align-items-end">
                                            <!-- Date Filters -->
                                            <div class="col-md-3">
                                                <label>From Date</label>
                                                <input type="date" name="from_date" class="form-control" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label>To Date</label>
                                                <input type="date" name="to_date" class="form-control" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
                                            </div>
                                            <input type="hidden" name="type" value="Earning_report" />

                                            <!-- Filter and Clear Buttons -->
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                                            </div>
                                            <div class="col-md-2">
                                                <?php if (isset($_GET['from_date']) || isset($_GET['to_date'])): ?>
                                                    <a href="?" class="btn btn-secondary w-100">Clear</a>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Export Button (always visible) -->
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
                                                    <input type="hidden" name="type" value="Earning_report" />
                                                    <div class="input-group">
                                                        <input type="text" name="search" class="form-control" placeholder="Search by book ID..."
                                                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="submit">Search</button>
                                                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                                                <a href="?type=Earning_report" class="btn btn-secondary">Clear</a>
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
                                                <th>Book ID</th>
                                                <th>Property ID</th>
                                                <th>Trent Fees (EGP)</th>
                                                <th>Service Fees (EGP)</th>
                                                <th>Subtotal (EGP)</th>
                                                <th>Total (EGP)</th>
                                                <th>Booking Date</th>
                                                <th>Guest Name</th>
                                                <th>Guest Contact </th>
                                                <th>Host Name</th>
                                                <th>Host Contact </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Pagination configuration
                                            $records_per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
                                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                            $page = max($page, 1);

                                            // Build the base query
                                            $query = "SELECT * FROM tbl_book WHERE book_status IN ('Check_in', 'Confirmed')";

                                            // Add date filter if provided
                                            if (isset($_GET['from_date']) && !empty($_GET['from_date'])) {
                                                $from_date = $rstate->real_escape_string($_GET['from_date']);
                                                $query .= " AND book_date >= '$from_date'";
                                            }

                                            if (isset($_GET['to_date']) && !empty($_GET['to_date'])) {
                                                $to_date = $rstate->real_escape_string($_GET['to_date']);
                                                $query .= " AND book_date <= '$to_date'";
                                            }

                                            // Add search condition if search term exists
                                            if (isset($_GET['search']) && !empty($_GET['search'])) {
                                                $search_term = $rstate->real_escape_string($_GET['search']);
                                                $query .= " AND (id LIKE '%$search_term%')";
                                            }

                                            // Get total number of records
                                            $count_query = "SELECT COUNT(*) as total FROM ($query) as count_table";
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
                                            $totalTrentFees = 0;

                                            if ($result->num_rows > 0) {
                                                $has_records = true;
                                                while ($row = $result->fetch_assoc()) {
                                                    $totalTrentFees += $row['trent_fees'];
                                                    $host_id = $row['uid'];
                                                    $guest_id = $row['add_user_id'];
                                                    $host = $rstate->query("SELECT name, mobile , ccode FROM tbl_user WHERE id = $host_id")->fetch_assoc();
                                                    $guest = $rstate->query("SELECT name, mobile ,ccode FROM tbl_user WHERE id = $guest_id")->fetch_assoc();

                                            ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo htmlspecialchars(json_decode($row['id'])); ?></td>
                                                        <td><?php echo htmlspecialchars(json_decode($row['prop_id'])); ?></td>
                                                        <td><?php echo number_format($row['trent_fees'], 2) . ' EGP'; ?></td>
                                                        <td><?php echo number_format($row['service_fees'], 2) . ' EGP'; ?></td>
                                                        <td><?php echo number_format($row['subtotal'], 2) . ' EGP'; ?></td>
                                                        <td><?php echo number_format($row['total'], 2) . ' EGP'; ?></td>
                                                        <td><?php echo htmlspecialchars($row['book_date']); ?></td>
                                                        <td class="align-middle"><?php echo $guest['name']; ?></td>
                                                        <td class="align-middle"><?php echo  $guest['ccode'] . $guest['mobile']; ?></td>
                                                        <td class="align-middle"><?php echo $host['name']; ?></td>
                                                        <td class="align-middle"><?php echo $host['ccode'] . $host['mobile']; ?></td>
                                                    </tr>
                                            <?php
                                                    $i++;
                                                }
                                            }

                                            if (!$has_records) {
                                                echo '<tr><td colspan="14" class="text-center">No records found</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                        <?php if ($has_records): ?>
                                            <tfoot>
                                                <tr class="fw-bold" style="background-color: #f8f9fa;">
                                                    <td colspan="2" class="text-end">Total:</td>
                                                    <td class="align-middle"><?php echo number_format($totalTrentFees, 2) . ' EGP'; ?></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        <?php endif; ?>
                                    </table>

                                    <!-- Manual Pagination Links -->
                                    <?php if ($total_records > 0): ?>
                                        <div class="pagination-container">
                                            <!-- Per Page Dropdown -->
                                            <div class="per-page-selector">
                                                <label for="per_page">Items per page:</label>
                                                <select id="per_page" name="per_page" onchange="updatePerPage(this.value)">
                                                    <?php
                                                    $per_page_options = [10, 20, 25,  50, 100, 200];
                                                    $current_per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : $records_per_page;
                                                    foreach ($per_page_options as $option):
                                                    ?>
                                                        <option value="<?php echo $option; ?>" <?php echo $option == $current_per_page ? 'selected' : ''; ?>>
                                                            <?php echo $option; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="pagination">
                                                <?php if ($page > 1): ?>
                                                    <a href="?type=Earning_report&page=1&per_page=<?php echo $current_per_page; ?><?php
                                                                                                                                    echo isset($_GET['from_date']) ? '&from_date=' . urlencode($_GET['from_date']) : '';
                                                                                                                                    echo isset($_GET['to_date']) ? '&to_date=' . urlencode($_GET['to_date']) : '';
                                                                                                                                    echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                                                                                                                                    ?>">First</a>
                                                    <a href="?type=Earning_report&page=<?php echo $page - 1; ?>&per_page=<?php echo $current_per_page; ?><?php
                                                                                                                                                            echo isset($_GET['from_date']) ? '&from_date=' . urlencode($_GET['from_date']) : '';
                                                                                                                                                            echo isset($_GET['to_date']) ? '&to_date=' . urlencode($_GET['to_date']) : '';
                                                                                                                                                            echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                                                                                                                                                            ?>">Previous</a>
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
                                                        <a href="?type=Earning_report&page=<?php echo $p; ?>&per_page=<?php echo $current_per_page; ?><?php
                                                                                                                                                        echo isset($_GET['from_date']) ? '&from_date=' . urlencode($_GET['from_date']) : '';
                                                                                                                                                        echo isset($_GET['to_date']) ? '&to_date=' . urlencode($_GET['to_date']) : '';
                                                                                                                                                        echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                                                                                                                                                        ?>"><?php echo $p; ?></a>
                                                    <?php endif; ?>
                                                <?php endfor; ?>

                                                <?php if ($page < $total_pages): ?>
                                                    <a href="?type=Earning_report&page=<?php echo $page + 1; ?>&per_page=<?php echo $current_per_page; ?><?php
                                                                                                                                                            echo isset($_GET['from_date']) ? '&from_date=' . urlencode($_GET['from_date']) : '';
                                                                                                                                                            echo isset($_GET['to_date']) ? '&to_date=' . urlencode($_GET['to_date']) : '';
                                                                                                                                                            echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                                                                                                                                                            ?>">Next</a>
                                                    <a href="?type=Earning_report&page=<?php echo $total_pages; ?>&per_page=<?php echo $current_per_page; ?><?php
                                                                                                                                                            echo isset($_GET['from_date']) ? '&from_date=' . urlencode($_GET['from_date']) : '';
                                                                                                                                                            echo isset($_GET['to_date']) ? '&to_date=' . urlencode($_GET['to_date']) : '';
                                                                                                                                                            echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
                                                                                                                                                            ?>">Last</a>
                                                <?php else: ?>
                                                    <span class="disabled">Next</span>
                                                    <span class="disabled">Last</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Results Count -->
                                    <?php if ($total_records > 0): ?>
                                        <div class="results-count">
                                            Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $current_per_page, $total_records); ?> of <?php echo $total_records; ?> records
                                            <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                                (filtered by "<?php echo htmlspecialchars($_GET['search']); ?>")
                                            <?php endif; ?>
                                            <?php if (isset($_GET['from_date']) || isset($_GET['to_date'])): ?>
                                                (date range: <?php echo isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : 'start'; ?> to <?php echo isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : 'end'; ?>)
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
    function updatePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        // Reset to first page when changing items per page
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
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

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 20px 0;
    }

    .per-page-selector {
        margin-right: 20px;
    }

    .per-page-selector select {
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ddd;
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