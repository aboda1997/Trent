<?php
require 'include/main_head.php';
$lang_code = load_language_code()["language_code"];

if ($_SESSION['stype'] == 'Staff' && !in_array('Read', $booking_per)) {



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
                Cancelled Payout Management</h3>
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
                        <th>Property Name </th>
                        <th>Guest Name </th>

                        <th>Payout Requested At</th>

                        <th>Total Amount </th>

                        <th>Payout To</th>
                        <th> Bank Name</th>
                        <th>Wallet Number </th>
                        <th>Bank Account No </th>
                        <th>Formal Name ( for bank ) </th>
                        <th>Reject Reason </th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $city = $rstate->query("SELECT  p.id as pid,p.cancel_reason,p.requested_at,p.profile_id,b.id, b.total, b.prop_title, b.uid FROM tbl_payout_list p INNER JOIN tbl_book b ON FIND_IN_SET(b.id, p.book_id) > 0 WHERE p.payout_status = 'Rejected'");
                      $i = 0;
                      while ($row = $city->fetch_assoc()) {
                        $i = $i + 1;
                        $guest_id = $row['uid'];
                        $profile_id = $row['profile_id'];
                        $guest = $rstate->query("select name  , mobile from tbl_user where id= $guest_id")->fetch_assoc();
                        $payment_data = $rstate->query("select pf.uid ,pf.bank_name , pf.bank_account_number , pf.wallet_number , pm.name  from tbl_payout_profiles pf LEFT JOIN tbl_payout_methods pm  on pf.method_id = pm.id   where pf.id= $profile_id")->fetch_assoc();

                      ?>
                        <tr>
                          <td>
                            <?php echo $i; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['id']; ?>
                          </td>
                          <td class="align-middle">
                            <?php
                            $type = json_decode($row['prop_title'], true);

                            echo $type[$lang_code]; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $guest['name']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['requested_at']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['total']; ?>
                          </td>
                          <td class="align-middle">
                            <?php
                            $type = json_decode($payment_data['name'] ?? "", true);

                            echo $type[$lang_code] ?? ""; ?>
                          </td>
                          <td class="align-middle">
                            <?php echo $payment_data['bank_name'] ?? ''; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $payment_data['wallet_number'] ?? ''; ?>
                          </td>
                          <td class="align-middle">
                            <?php echo $payment_data['bank_account_number'] ?? ''; ?>
                          </td>
                          <td class="align-middle">
                            <?php echo $payment_data['bank_name'] ?? ''; ?>
                          </td>
                          <td class="align-middle">
                            <?php echo $row['cancel_reason'] ?? ''; ?>
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



<?php
require 'include/footer.php';
?>
</body>

</html>