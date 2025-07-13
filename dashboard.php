<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
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
                Report Dashboard</h3>
            </div>
            <div class="col-6">

            </div>
          </div>
        </div>
      </div>
      <!-- Container-fluid starts-->
      <div class="container-fluid ecommerce-page">
        <div class="row">

          <!-- Total Category Card -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #7367f0;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-primary">
                      <i data-feather="list" class="text-primary"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Total Category</h3>
                    <p class="stats-value mb-0 text-primary"><?php echo $rstate->query("select * from tbl_category")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Coupon Card -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #28c76f;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-success">
                      <i data-feather="gift" class="text-success"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Total Coupon</h3>
                    <p class="stats-value mb-0 text-success"><?php echo $rstate->query("select * from tbl_coupon")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Property Card -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #00cfe8;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-info">
                      <i data-feather="home" class="text-info"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Total Property</h3>
                    <p class="stats-value mb-0 text-info"><?php echo $rstate->query("select * from tbl_property where is_deleted = 0")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Extra Images Card -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #ea5455;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-danger">
                      <i data-feather="camera" class="text-danger"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Total Extra Images</h3>
                    <p class="stats-value mb-0 text-danger"><?php echo $rstate->query("select * from tbl_extra")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Facility Card -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #ff9f43;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-warning">
                      <i data-feather="bluetooth" class="text-warning"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Total Facility</h3>
                    <p class="stats-value mb-0 text-warning"><?php echo $rstate->query("select * from tbl_facility")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Slider Card -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #9c8dff;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-purple">
                      <i data-feather="image" class="text-purple"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Total Slider</h3>
                    <p class="stats-value mb-0 text-purple"><?php echo $rstate->query("select * from tbl_slider")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Booking Status Cards -->
          <!-- Waiting Booking -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #82868b;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-secondary">
                      <i data-feather="calendar" class="text-secondary"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Waiting Booking</h3>
                    <p class="stats-value mb-0 text-secondary"><?php echo $rstate->query("select * from tbl_book where book_status='Booked'")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Confirmed Booking -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #00cfe8;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-info">
                      <i data-feather="check-square" class="text-info"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Confirmed Booking</h3>
                    <p class="stats-value mb-0 text-info"><?php echo $rstate->query("select * from tbl_book where book_status='Confirmed'")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Check In Booking -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #28c76f;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-success">
                      <i data-feather="eye" class="text-success"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Check In Booking</h3>
                    <p class="stats-value mb-0 text-success"><?php echo $rstate->query("select * from tbl_book where book_status='Check_in'")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Completed Booking -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #7367f0;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-primary">
                      <i data-feather="check-circle" class="text-primary"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Completed Booking</h3>
                    <p class="stats-value mb-0 text-primary"><?php echo $rstate->query("select * from tbl_book where book_status='Completed'")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Users Card -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #ff9f43;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-warning">
                      <i data-feather="users" class="text-warning"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Total Users</h3>
                    <p class="stats-value mb-0 text-warning"><?php echo $rstate->query("select * from tbl_user")->num_rows; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
            <div class="card stats-card h-100" style="border-left-color: #28a745;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-3">
                    <div class="stats-icon bg-light-success">
                      <i data-feather="credit-card" class="text-success"></i>
                    </div>
                  </div>
                  <div class="sale-content" style="min-width: 0;">
                    <h3 class="stats-title mb-1">Total Booked Earnings</h3>
                    <p class="stats-value mb-0 text-success">
                      <?php
                      $earn = $rstate->query("SELECT SUM(`total`) AS total FROM tbl_book WHERE book_status='Completed'")->fetch_assoc();
                      echo number_format((float)$earn['total'], 2, '.', ',') . ' <small class="text-muted">EGP</small>';
                      ?>
                    </p>
                  </div>
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
<style>
  .stats-card {
    border-left: 4px solid;
    transition: all 0.3s ease;
  }

  .stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .stats-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.4rem;
  }

  .stats-value {
    font-size: 1.4rem;
    font-weight: 700;
    font-family: 'Segoe UI', Roboto, sans-serif;
  }

  .stats-title {
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #5e5873;
  }
</style>
<?php
require 'include/footer.php';
?>
</body>

</html>