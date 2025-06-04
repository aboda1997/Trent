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
                Pending Booking Management</h3>
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
                <?php if (isset($_GET['cn_id'])) { ?>
                  <form method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                      <label id="basic-addon1">Enter Reason</label>
                      <textarea type="text" class="form-control" placeholder="Enter Reason" name="reason" aria-label="Username" aria-describedby="basic-addon1" style="resize:none;"></textarea>
                      <input type="hidden" name="type" value="update_reason" />
                      <input type="hidden" name="id" value="<?php echo $_GET['cn_id']; ?>" />
                    </div>
                    <button type="submit" class="btn btn-primary">Cancel Booking</button>
                  </form>
                <?php } else { ?>
                  <!-- Search Form -->
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
                          <th>Property Image</th>
                          <th>Property Price</th>
                          <th>Property Total Day</th>
                          <?php if (in_array('Update_Booking', $per) || in_array('Delete_Booking', $per)): ?>
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
                        $query = "SELECT * FROM tbl_book WHERE book_status='Booked'";

                        // Add search condition if search term exists
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                          $search_term = $rstate->real_escape_string($_GET['search']);
                          $query .= " AND (prop_id LIKE '%$search_term%' OR prop_title LIKE '%$search_term%')";
                        }

                        // Get total number of records
                        $count_query = "SELECT COUNT(*) as total FROM tbl_book WHERE book_status='Booked'";
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                          $count_query .= " AND (prop_id LIKE '%$search_term%' OR prop_title LIKE '%$search_term%')";
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
                        ?>
                            <tr>
                              <td><?php echo $i; ?></td>
                              <td><?php echo $row['prop_id']; ?></td>
                              <td class="align-middle">
                                <?php echo json_decode($row['prop_title'])->en ?? ''; ?>
                              </td>
                              <td class="align-middle">
                                <img src="<?php
                                          $imageArray = explode(',', $row['prop_img']);
                                          echo !empty($imageArray[0]) ? $imageArray[0] : 'default_image.jpg';
                                          ?>" width="70" height="80" />
                              </td>
                              <td class="align-middle">
                                <?php echo $row['prop_price'] . 'EGP'; ?>
                              </td>
                              <td class="align-middle">
                                <?php echo $row['total_day'] . ' Days'; ?>
                              </td>
                              <?php if (in_array('Update_Booking', $per) || in_array('Delete_Booking', $per)): ?>
                                <td style="white-space: nowrap; width: 15%;">
                                  <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                    <div class="btn-group btn-group-sm" style="float: none;">
                                      <button class="btn btn-info preview_d" style="float: none; margin: 5px;"
                                        data-id="<?php echo $row['id']; ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#myModal">View Details</button>
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
                <?php } ?>
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

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg ">


    <div class="modal-content gray_bg_popup">
      <div class="modal-header">
        <h4>Order Preivew</h4>
        <button type="button" class="close popup_open" data-bs-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body p_data">

      </div>

    </div>

  </div>
</div>
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