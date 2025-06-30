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
                Booking Rating list Management</h3>
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
                   <div class="mb-3 row">
                  <form id="exportForm" method="get" class="col-sm-12">
                    <div class="row justify-content-end align-items-start">
                      <input type="hidden" name="type" value="export_rating_data" />

                      <!-- Export Button -->
                      <div class="col-md-2">
                        <button type="button" id="exportExcel" class="btn btn-success w-100">
                          <i class="fa fa-file-excel-o"></i> Export Excel
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="row justify-content-center mb-3">
                  <div class="col-md-8">
                    <div class="search-container">
                      <form method="get" action="">
                        <div class="input-group">
                          <input type="text" name="search" class="form-control" placeholder="Search by Property ID or Title..."
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
                        <th>Property ID</th>
                        <th>Property Title</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>User Name</th>
                        <?php if (in_array('Update_Booking', $per) || in_array('Delete_Booking', $per)): ?>
                          <th><?= $lang['Action'] ?></th>
                        <?php endif; ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // Pagination configuration
                      $records_per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
                      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                      $page = max($page, 1);

                      // Base query
                      $query = "SELECT r.*, b.prop_title, b.prop_id as property_id FROM tbl_rating r 
                          INNER JOIN tbl_book b ON FIND_IN_SET(b.id, r.book_id) > 0";

                      // Add search condition if search term exists
                      if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search_term = $rstate->real_escape_string($_GET['search']);
                        $query .= " WHERE (b.prop_id LIKE '%$search_term%' OR b.prop_title LIKE '%$search_term%')";
                      }

                      // Get total number of records
                      $count_query = "SELECT COUNT(*) as total FROM tbl_rating r 
                                INNER JOIN tbl_book b ON FIND_IN_SET(b.id, r.book_id) > 0";
                      if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $count_query .= " WHERE (b.prop_id LIKE '%$search_term%' OR b.prop_title LIKE '%$search_term%')";
                      }

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
                          $guest_id = $row['uid'];
                          $guest = $rstate->query("SELECT name, mobile FROM tbl_user WHERE id = $guest_id")->fetch_assoc();
                      ?>
                          <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $row['property_id']; ?></td>
                            <td class="align-middle">
                              <?php
                              $type = json_decode($row['prop_title'], true);
                              echo $type[$lang_code] ?? '';
                              ?>
                            </td>
                            <td class="align-middle">
                              <?php echo $row['rating']; ?>
                            </td>
                            <td class="align-middle">
                              <?php echo $row['comment']; ?>
                            </td>
                            <td class="align-middle">
                              <?php echo $guest['name']; ?>
                            </td>
                            <?php if (in_array('Update_Booking', $per) || in_array('Delete_Booking', $per)): ?>
                              <td style="white-space: nowrap; width: 15%;">
                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                  <div class="btn-group btn-group-sm" style="float: none;">
                                    <button type="button"
                                      style="background: none; border: none; padding: 0; cursor: pointer;"
                                      data-toggle="modal"
                                      data-target="#approveModal"
                                      data-id="<?php echo $row['id']; ?>"
                                      title="Delete">
                                      <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="30" height="30" rx="15" fill="#FF6B6B" />
                                        <path d="M10 10L20 20M20 10L10 20" stroke="#FFFFFF" stroke-width="2" />
                                      </svg>
                                    </button>
                                  </div>
                                </div>
                              </td>
                            <?php endif; ?>
                          </tr>
                      <?php
                          $i++;
                        }
                      }

                      if (!$has_records) {
                        $colspan = 6; // Default number of columns
                        if (in_array('Update_Booking', $per) || in_array('Delete_Booking', $per)) {
                          $colspan = 7; // Add one more column if action column is present
                        }
                        echo '<tr><td colspan="' . $colspan . '" class="text-center"><div >No records found</div></td></tr>';
                      }
                      ?>
                    </tbody>
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

                      <!-- Pagination Links -->
                      <div class="pagination">
                        <?php if ($page > 1): ?>
                          <a href="?page=1&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">First</a>
                          <a href="?page=<?php echo $page - 1; ?>&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Previous</a>
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
                            <a href="?page=<?php echo $p; ?>&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $p; ?></a>
                          <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                          <a href="?page=<?php echo $page + 1; ?>&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Next</a>
                          <a href="?page=<?php echo $total_pages; ?>&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Last</a>
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
        <input type="hidden" name="type" value="delete_rating" />
      </form>
      <div class="modal-body">
        Are you sure you want to delete this rating?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="confirmApproveBtn">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>
</body>

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
<script>
  $(document).ready(function() {
    // Remove invalid class when user starts typing
    $('#denyReason').on('input', function() {
      if ($(this).val().trim() !== '') {
        $(this).removeClass('is-invalid');
      }
    });



    $('#approveModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');

      var modal = $(this);
      modal.find('#approveId').val(id);
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
            alert("'Error saving payout Approval.");
          }
        }
      });
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

<?php
require 'include/footer.php';
?>
</body>

</html>