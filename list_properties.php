<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Read_Property', $per)) {


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
                Property List Management</h3>
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
                <div class="mb-3 row">
                  <form id="exportForm" method="get" class="col-sm-12">
                    <div class="row justify-content-end align-items-start">
                      <input type="hidden" name="type" value="export_properties_data" />
                      <input type="hidden" name="approved" value="1" />

                      <!-- Export Button -->
                      <div class="col-md-2">
                        <button type="button" id="exportExcel" class="btn btn-success w-100">
                          <i class="fa fa-file-excel-o"></i> Export Excel
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
                <!-- Search Form -->
                <div class="row justify-content-center">
                  <div class="col-md-8">
                    <div class="search-container" style="margin-bottom: 20px;">
                      <form method="get" action="">
                        <div class="input-group">
                          <input type="text" name="search" class="form-control" placeholder="Search by property title, ID, or type..."
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
                  <table class="table" id="properties-table">
                    <thead>
                      <tr>
                        <th>Sr No.</th>
                        <th>Property ID</th>
                        <th>Property Title</th>
                        <th>Property Type</th>
                        <th>Property Image</th>
                        <th>Price (/Night)</th>
                        <th>Beds</th>
                        <th>Bathrooms</th>
                        <th>SQFT</th>
                        <th>Facility</th>
                        <th>Rent/Buy</th>
                        <th>Person Limit</th>
                        <th>Status</th>
                        <th>Government</th>
                        <th>City</th>
                        <th>Compound</th>
                        <th>Added by</th>
                        <th> Mobile Number </th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <?php if (in_array('Update_Property', $per) || in_array('Delete_Property', $per)): ?>
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
                      $query = "SELECT tbl_property.*,
                                          (SELECT GROUP_CONCAT(JSON_UNQUOTE(JSON_EXTRACT(`title`, '$.$lang_code')))
                                           FROM `tbl_facility`
                                           WHERE FIND_IN_SET(tbl_facility.id, tbl_property.facility)
                                          ) AS facility_select
                                          FROM tbl_property
                                          WHERE is_approved = 1 ";

                      // Add search condition if search term exists
                      if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search_term = $rstate->real_escape_string($_GET['search']);
                        if (is_numeric($search_term)) {
                          $query .= " AND tbl_property.id = " . (int)$search_term;
                        } else {
                          $query .= " AND (JSON_EXTRACT(tbl_property.title, '$.$lang_code') LIKE '%$search_term%'  COLLATE utf8mb4_unicode_ci
                                                  OR EXISTS (
                                                      SELECT 1 FROM tbl_category 
                                                      WHERE tbl_category.id = tbl_property.ptype 
                                                      AND JSON_EXTRACT(tbl_category.title, '$.$lang_code') LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci
                                                  ))";
                        }
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

                      if ($result->num_rows > 0) {
                        $has_records = true;
                        while ($row = $result->fetch_assoc()) {
                          $title = json_decode($row['title'], true);
                          $government = $row['government'];
                          $added_by = $row['add_user_id'];
                          $user_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$added_by)->fetch_assoc();

                          $city = json_decode($row['city'] ?? '', true);
                          $compound_name = json_decode($row['compound_name'] ?? "", true);
                      ?>
                          <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $row["id"]; ?></td>
                            <td><?php echo htmlspecialchars($title[$lang_code] ?? ''); ?></td>
                            <td>
                              <?php
                              $type = $rstate->query("SELECT * FROM tbl_category WHERE id=" . (int)$row['ptype'])->fetch_assoc();
                              $typeTitle = json_decode($type['title'] ?? '', true);
                              echo htmlspecialchars($typeTitle[$lang_code] ?? '');
                              ?>
                            </td>
                            <td>
                              <?php
                              $imageArray = explode(',', $row['image']);
                              $imageSrc = !empty($imageArray[0]) ? $imageArray[0] : 'default_image.jpg';
                              ?>
                              <img src="<?php echo htmlspecialchars($imageSrc); ?>" width="70" height="80" />
                            </td>
                            <td><?php echo htmlspecialchars($row['price']) . " EGP"; ?></td>
                            <td><?php echo htmlspecialchars($row['beds']); ?></td>
                            <td><?php echo htmlspecialchars($row['bathroom']); ?></td>
                            <td><?php echo htmlspecialchars($row['sqrft']); ?></td>
                            <td>
                              <?php if (!empty($row['facility_select'])): ?>
                                <span class="badge badge-dark tag-pills-sm-mb"><?php
                                                                                echo str_replace(
                                                                                  ',',
                                                                                  '</span><span class="badge badge-dark tag-pills-sm-mb">',
                                                                                  htmlspecialchars($row['facility_select'])
                                                                                );
                                                                                ?></span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if ($row['pbuysell'] == 1): ?>
                                <span class="badge badge-success">Rent</span>
                              <?php else: ?>
                                <span class="badge badge-danger">Buy</span>
                                <?php if ($row['is_sell'] == 1): ?>
                                  <span class="badge badge-info">Property Selled</span>
                                <?php endif; ?>
                              <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['plimit']); ?></td>
                            <td>
                              <?php if ((in_array('Update_Property', $per) || in_array('Delete_Property', $per)) && $row['is_deleted'] == '0'): ?>
                                <span class="badge status-toggle <?php echo $row['status'] ? 'badge-danger' : 'badge-success'; ?>"
                                  data-id="<?php echo $row['id']; ?>"
                                  data-status="<?php echo $row['status']; ?>"
                                  style="cursor: pointer;">
                                  <?php echo $row['status'] ? "make unpublish" : "make publish"; ?>
                                </span>
                              <?php else: ?>
                                <!-- Empty cell if no permissions -->
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php
                              $type = $rstate->query("SELECT * FROM tbl_government WHERE id=" . (int)$government)->fetch_assoc();
                              $typeTitle = json_decode($type['name'] ?? '', true);
                              echo htmlspecialchars($typeTitle[$lang_code] ?? '');
                              ?>
                            </td>
                            <td><?php echo htmlspecialchars($city[$lang_code] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($compound_name[$lang_code] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($user_data['name'] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars(($user_data['ccode']??'') .($user_data['mobile'] ??"") ); ?></td>

                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['updated_at']); ?></td>

                            <?php if ((in_array('Update_Property', $per) || in_array('Delete_Property', $per)) && $row['is_deleted'] == '0'): ?>
                              <td style="white-space: nowrap; width: 15%;">
                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                  <div class="btn-group btn-group-sm" style="float: none;">
                                    <?php if (in_array('Update_Property', $per)): ?>
                                      <a href="add_properties.php?id=<?php echo $row['id']; ?>" data-toggle="tooltip" title="edit property" class="tabledit-edit-button" style="float: none; margin: 5px;">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <rect width="30" height="30" rx="15" fill="#79F9B4" />
                                          <path d="M22.5168 9.34109L20.6589 7.48324C20.0011 6.83703 18.951 6.837 18.2933 7.49476L16.7355 9.06416L20.9359 13.2645L22.5052 11.7067C23.163 11.0489 23.163 9.99885 22.5168 9.34109ZM15.5123 10.2873L8 17.8342V22H12.1658L19.7127 14.4877L15.5123 10.2873Z" fill="#25314C" />
                                        </svg>
                                      </a>
                                    <?php endif; ?>

                                    <?php if (in_array('Delete_Property', $per)): ?>
                                      <button type="button"
                                        style="background: none; border: none; padding: 0; cursor: pointer;"
                                        data-toggle="modal"
                                        data-target="#approveModal"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-status="1"
                                        title="Delete">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <rect width="30" height="30" rx="15" fill="#FF6B6B" />
                                          <path d="M10 10L20 20M20 10L10 20" stroke="#FFFFFF" stroke-width="2" />
                                        </svg>
                                      </button>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </td>
                            <?php else: ?>
                              <td></td>
                            <?php endif; ?>
                          </tr>
                      <?php
                          $i++;
                        }
                      }

                      if (!$has_records) {
                        $colspan = in_array('Update_Property', $per) || in_array('Delete_Property', $per) ? 16 : 15;
                        echo '<tr><td colspan="' . $colspan . '" class="text-center">No records found</td></tr>';
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
        <h5 class="modal-title" id="approveModalLabel">Confirm delete</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="approveForm">

        <input type="hidden" id="approveId" name="id">
        <input type="hidden" id="status" name="status">
        <input type="hidden" name="type" value="delete_property" />
      </form>
      <div class="modal-body">
        Are you sure you want to delete this property?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="confirmApproveBtn">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>
<!-- latest jquery-->
<script>
  $(document).ready(function() {
    $(document).on('click', '.status-toggle', function(e) {


      let $this = $(this);
      let propertyId = $this.data("id");
      let currentStatus = $this.data("status");
      let newStatus = currentStatus === 1 ? 0 : 1; // Toggle status
      $this.css('pointer-events', 'none');

      $.ajax({
        url: "include/property.php",
        type: "POST",
        data: {
          id: propertyId,
          type: "toggle_status",
          status: newStatus
        },
        success: function(response) {
          let res = JSON.parse(response); // Parse the JSON response

          if (res.ResponseCode === "200" && res.Result === "true") {
            // Toggle text and badge color
            $this.text(newStatus === 1 ? "make unpublish" : "make publish");
            $this.data("status", newStatus); // Update status in data attribute

            // Remove previous badge class and add new one
            $this.removeClass("badge-success badge-danger")
              .addClass(newStatus === 1 ? "badge-danger" : "badge-success");

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
            alert("Failed to update status.");
          }
        }
      });
    });
  });
</script>
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
          alert("'Error saving property deletion.");
        }
      }
    });
  });

  function updatePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    // Reset to first page when changing items per page
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
  }
</script>

<!-- CSS Styles -->
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

  .badge-success {
    background-color: #28a745;
  }

  .badge-danger {
    background-color: #dc3545;
  }

  .badge-info {
    background-color: #17a2b8;
  }

  .badge-dark {
    background-color: #343a40;
  }

  .tag-pills-sm-mb {
    display: inline-block;
    margin-bottom: 5px;
    margin-right: 3px;
    padding: 3px 7px;
    font-size: 12px;
  }
</style>

<!-- Prevent DataTables initialization -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.DataTable === 'function') {
      $('#properties-table').DataTable({
        paging: false,
        searching: false,
        info: false
      });
    }
  });
</script>
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
<?php
require 'include/footer.php';
?>
</body>

</html>