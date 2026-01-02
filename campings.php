<?php
require 'include/main_head.php';

?>
<!-- Loader ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper compact-wrapper" id="pageWrapper">
    <!-- CryptoJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <style>
        .vh-100 {
            height: 100vh;
        }

        .btn-excel {
            background-color: #1d6f42;
            color: white;
            border: none;
        }

        .btn-excel:hover {
            background-color: #165834;
            color: white;
        }
    </style>
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
                            <h3>Campings</h3>
                        </div>
                        <div class="col-6">
                            <div class="float-end">
                                <button class="btn btn-excel me-2" id="downloadExcel">
                                    <i class="fa fa-file-excel-o me-2"></i>Download Excel
                                </button>
                                <button class="btn btn-excel" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                                    <i class="fa fa-upload me-2"></i>Upload Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            // Base query - start with status condition
            $query = "SELECT * FROM `tbl_uploaded_excel_data` where item_id = 65665 ";


            // Get total number of records
            $count_query = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
            $count_result = $rstate->query($count_query);
            $total_records = $count_result->fetch_assoc()['total'];

             if ($total_records > 0): ?>
            <!-- Container-fluid starts-->

            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <!-- Centered Search Form -->
									<div   style="position: relative; z-index: 0;" class="row justify-content-center">
                                        <div class="col-md-6 text-center">
                                            <div class="search-container" style="margin-bottom: 20px;">
                                                <button id="sendWhatsAppBtn" class="btn btn-primary" type="button">Send Whatsup Message</button>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- User Table -->
                                    <table class="table" id="users-table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Mobile</th>
                                                <th>Message</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Pagination configuration
                                            $records_per_page = 10;
                                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                            $page = max($page, 1);

                                            // Base query - start with status condition
                                            $query = "SELECT * FROM `tbl_uploaded_excel_data` where item_id = 65665 ";


                                            // Get total number of records
                                            $count_query = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
                                            $count_result = $rstate->query($count_query);
                                            $total_records = $count_result->fetch_assoc()['total'];
                                            $total_pages = ceil($total_records / $records_per_page) == 0 ? 1 : ceil($total_records / $records_per_page);
                                            $page = min($page, $total_pages);

                                            // Add LIMIT to query for pagination
                                            $offset = ($page - 1) * $records_per_page;
                                            $query .= " LIMIT $offset, $records_per_page";
                                            $stmt = $rstate->query($query);
                                            $i = $offset + 1;

                                            if ($total_records > 0) {
                                                while ($row = $stmt->fetch_assoc()) {
                                            ?>
                                                    <tr>
                                                        <td>

                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['f1']) ?></td>
                                                        <td><?php echo htmlspecialchars($row['f2']) ?></td>



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
<?php endif; ?>

        </div>

        <!-- Upload Excel Modal -->
        <div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadExcelModalLabel">Upload Excel File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="excelUploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="excelFile" class="form-label">Select Excel File</label>
                                <input class="form-control" type="file" id="excelFile" name="excelFile" accept=".xlsx, .xls, .csv">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-excel" id="uploadExcelBtn">Upload</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer start-->
    </div>
</div>

<script>
    // Download Excel functionality
    document.getElementById('downloadExcel').addEventListener('click', function() {
        // Disable the button to prevent multiple clicks
        var saveButton = $(this);
        saveButton.prop('disabled', true);

        // Here you would typically make an AJAX call to save the data
        $.ajax({
            url: "include/property.php",
            type: "POST",
            data: "type=download_excel-template",
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


                $.notify('<i class="fas fa-bell"></i> Download Completed Successfully!', {
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

    // Upload Excel functionality
    document.getElementById('uploadExcelBtn').addEventListener('click', function() {
        const fileInput = document.getElementById('excelFile');

        if (fileInput.files.length === 0) {
            alert('Please select an Excel file first');
            return;
        }

        const formData = new FormData();
        formData.append('excelFile', fileInput.files[0]);
        formData.append('type', 'upload_whats-up-campings'); // Add the type parameter

        fetch("include/property.php", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.ResponseCode == '200' && data.Result == 'true') {
                    $.notify('<i class="fas fa-bell"></i> ' + data.title, {
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
                    $('#uploadExcelModal').modal('hide');
                    // Optional: refresh the table/data
                    location.reload();
                } else {
                    $.notify('<i class="fas fa-exclamation-circle"></i> Error upload Excel Sheet ', {
                        type: 'danger',
                        allow_dismiss: true,
                        delay: 5000
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $.notify('<i class="fas fa-exclamation-circle"></i> Error upload Excel Sheet ', {
                    type: 'danger',
                    allow_dismiss: true,
                    delay: 5000
                });
            });
    });

    $('#sendWhatsAppBtn').click(function() {
        // Get any search parameters you need (if you have input fields)
        // Disable the button to prevent multiple clicks
        var saveButton = $(this);
        saveButton.prop('disabled', true);

        const searchParams = {
            type: "Send_whatsup_message"
        };

        // Make API request
        $.ajax({
            url: 'include/property.php', // Your API endpoint
            type: 'POST',
            data: searchParams,
            success: function(response) {
                let res = JSON.parse(response); // Parse the JSON response
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
                location.reload();

            },
            error: function(xhr, status, error) {
                $.notify('<i class="fas fa-exclamation-circle"></i> Error sending message: ' + error, {
                    type: 'danger',
                    allow_dismiss: true,
                    delay: 5000
                });
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


<?php
require 'include/footer.php';
?>
</body>

</html>