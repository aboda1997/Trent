<?php
require 'include/main_head.php';
if ($_SESSION['restatename'] == 'Staff') {
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
            <!-- Container-fluid starts-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Your content here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Container-fluid Ends-->
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
                                <input class="form-control" type="file" id="excelFile" name="excelFile" accept=".xlsx, .xls">
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
        
        fetch('import_campings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('File uploaded successfully!');
                $('#uploadExcelModal').modal('hide');
                // Optional: refresh the table/data
                // location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during upload');
        });
    });
</script>

<?php
require 'include/footer.php';
?>
</body>
</html>