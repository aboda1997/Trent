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
                                Earning Report</h3>
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

                                    <!-- Date Filter Form and Export Button -->
                                    <div class="mb-3 row">
                                        <form id="exportForm" method="get" class="col-sm-12">
                                            <div class="row align-items-end"> <!-- Added align-items-end for vertical alignment -->
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

                                    <!-- Rest of your table code remains the same -->

                                    <table class="display" id="basic-1">
                                        <thead>
                                            <tr>
                                                <th>Sr No.</th>
                                                <th>Book ID</th>
                                                <th>Trent Fees (EGP)</th>
                                                <th>Booking Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
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

                                            $city = $rstate->query($query);
                                            $i = 0;

                                            if ($city->num_rows > 0) {
                                                while ($row = $city->fetch_assoc()) {
                                                    $totalTrentFees += $row['trent_fees']; // Add to total

                                                    $i = $i + 1;
                                            ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td class="align-middle"><?php echo json_decode($row['id']); ?></td>
                                                        <td class="align-middle"><?php echo number_format($row['trent_fees'], 2) . ' EGP'; ?></td>
                                                        <td class="align-middle"><?php echo $row['book_date']; ?></td>
                                                    </tr>
                                            <?php
                                                }
                                                // Display total row
                                                echo '<tr class="fw-bold" style="background-color: #f8f9fa;">
              <td colspan="2" class="text-end">Total:</td>
              <td class="align-middle">' . number_format($totalTrentFees, 2) . ' EGP</td>
              <td></td>
            </tr>';
                                            } else {
                                                echo '<tr><td colspan="4" class="text-center">No records found</td></tr>';
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

<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
</body>

</html>