<?php
require 'include/main_head.php';
$lang_code = load_language_code()["language_code"];
$per = $_SESSION['permissions'];

if (!in_array('Read_Payout', $per)) {



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
                <div class="mb-3 row">
                  <form id="exportForm" method="get" class="col-sm-12">
                    <div class="row justify-content-end align-items-start">
                      <input type="hidden" name="type" value="export_payout_data" />
                      <input type="hidden" name="payout_status" value="Rejected" />

                      <!-- Export Button -->
                      <div class="col-md-2">
                        <button type="button" id="exportExcel" class="btn btn-success w-100">
                          <i class="fa fa-file-excel-o"></i> Export Excel
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
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