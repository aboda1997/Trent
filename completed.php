<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

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
                Completed Booking Management</h3>
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
                  <!-- Search Form -->
                  <div class="row justify-content-center mb-3">
                    <div class="col-md-8">
                      <div class="search-container">
                        <form method="get" action="">
                          <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by property title or ID..."
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

                  <table class="display" id="active-users-table">
                    <thead>
                      <tr>
                        <th>Sr No.</th>
                        <th>Property ID</th>
                        <th>Property Title</th>
                        <th>Property Image</th>
                        <th>Property Price</th>
                        <th>Property Total Day</th>
                        <th>User Rating</th>
                        <th>User FeedBack</th>
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

                      // Base query with LEFT JOIN to tbl_rating
                      $query = "SELECT b.*, r.rating as user_rating, r.comment as user_comment 
                                FROM tbl_book b
                                LEFT JOIN tbl_rating r ON b.id = r.book_id
                                WHERE b.book_status='Completed'";

                      // Add search condition if search term exists
                      if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search_term = $rstate->real_escape_string($_GET['search']);
                        $query .= " AND (b.prop_title LIKE '%$search_term%' OR b.prop_id LIKE '%$search_term%')";
                      }

                      // Get total number of records
                      $count_query = "SELECT COUNT(*) as total 
                                     FROM tbl_book b
                                     WHERE b.book_status='Completed'";
                      if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $count_query .= " AND (b.prop_title LIKE '%$search_term%' OR b.id LIKE '%$search_term%')";
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
                            <td class="align-middle"><?php echo json_decode($row['prop_title']??'')->en??''; ?></td>
                            <td class="align-middle">
                              <img src="<?php
                                        $imageArray = explode(',', $row['prop_img']);
                                        echo !empty($imageArray[0]) ? $imageArray[0] : 'default_image.jpg';
                                        ?>" width="70" height="80" />
                            </td>
                            <td class="align-middle"><?php echo $row['prop_price'] . 'EGP'; ?></td>
                            <td class="align-middle"><?php echo $row['total_day'] . ' Days'; ?></td>
                            <td class="align-middle">
                              <?php
                              if (empty($row['user_rating'])) {
                                echo '<b>No Rating Provided</b>';
                              } else {
                                echo str_repeat('★', $row['user_rating']);
                              }
                              ?>
                            </td>
                            <td class="align-middle">
                              <?php
                              if (empty($row['user_comment'])) {
                                echo '<b>No Feedback Provided</b>';
                              } else {
                                echo '<b>' . htmlspecialchars($row['user_comment']) . '</b>';
                              }
                              ?>
                            </td>
                            <?php if (in_array('Update_Booking', $per) || in_array('Delete_Booking', $per)): ?>
                              <td style="white-space: nowrap; width: 15%;">
                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                  <div class="btn-group btn-group-sm" style="float: none;">
                                    <button class="btn btn-info preview_d" style="float: none; margin: 5px;" data-id="<?php echo $row['id']; ?>" data-bs-toggle="modal" data-bs-target="#myModal">View Details</button>
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
                        echo '<tr><td colspan="9" class="text-center">No records found</td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>

                  <!-- Manual Pagination Links -->
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