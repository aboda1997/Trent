<?php
require 'include/main_head.php';
$lang_code = load_language_code()["language_code"];
$per = $_SESSION['permissions'];

if ( !in_array('Read_Booking', $per)) {



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
                Cancelled Booking Management</h3>
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
                  <table class="display" id="basic-1">
                    <thead>
                      <tr>
                        <th>Sr No.</th>
                        <th>Booking ID</th>
                        <th>Guest Name </th>

                        <th>Guest Mobile</th>

                        <th>Host Name </th>

                        <th>Host Mobile</th>
                        <th>Cancelled By</th>
                        <th>Cancel Reason

                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $city = $rstate->query("select * from tbl_book where book_status='Cancelled'");
                      $i = 0;
                      while ($row = $city->fetch_assoc()) {
                        $i = $i + 1;
                        $host_id = $row['uid'];
                        $guest_id = $row['add_user_id'];
                        $cancel_id = $row['cancle_reason'];
                        $cancel_by = $row['cancel_by'] == "H" ? "Host" : "Guest";
                        $host = $rstate->query("select name  , mobile from tbl_user where id= $host_id")->fetch_assoc();

                        $guest = $rstate->query("select name  , mobile from tbl_user where id= $guest_id")->fetch_assoc();
                        $cancel_reason = $rstate->query("select reason  from tbl_cancel_reason where id= $cancel_id")->fetch_assoc();

                      ?>
                        <tr>
                          <td>
                            <?php echo $i; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['id']; ?>
                          </td>
                          <td class="align-middle">
                            <?php echo $guest['name']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $guest['mobile']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $host['name']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $host['mobile']; ?>
                          </td>
                          <td class="align-middle">
                            <?php echo $cancel_by; ?>
                          </td>
                          <td class="align-middle">
                            <?php echo 
                            json_decode($cancel_reason['reason'], true)[$lang_code]
                            ; ?>
                          </td>
                        </tr>
                      <?php
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

<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
</body>

</html>