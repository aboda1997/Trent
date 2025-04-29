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
                Payout Request Management</h3>
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
                        <th></th>
                        <th>Sr No.</th>
                        <th class="align-middle d-none">payout id</th>

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
                        <th> Payout Approval </th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $city = $rstate->query("SELECT  p.id as pid,p.requested_at,p.profile_id,b.id, b.total, b.prop_title, b.uid FROM tbl_payout_list p INNER JOIN tbl_book b ON FIND_IN_SET(b.id, p.book_id) > 0 WHERE p.payout_status = 'Pending'");
                      $i = 0;
                      while ($row = $city->fetch_assoc()) {
                        $i = $i + 1;
                        $guest_id = $row['uid'];
                        $profile_id = $row['profile_id'];
                        $guest = $rstate->query("select name  , mobile from tbl_user where id= $guest_id")->fetch_assoc();
                        $payment_data = $rstate->query("select pf.uid ,pf.bank_name , pf.bank_account_number , pf.wallet_number , pm.name  from tbl_payout_profiles pf LEFT JOIN tbl_payout_methods pm  on pf.method_id = pm.id   where pf.id= $profile_id")->fetch_assoc();

                      ?>
                        <tr>
                          <td><input type="checkbox" class="row-checkbox"></td>
                          <td>
                            <?php echo $i; ?>
                          </td>

                          <td class="align-middle d-none">
                            <?php echo $row['pid']; ?>
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

                          <td style="white-space: nowrap; width: 15%;">
                            <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                              <div class="btn-group btn-group-sm" style="float: none;">
                                <button class="btn btn-success " style="float: none; margin: 5px;"
                                  type="button"
                                  data-toggle="modal" data-target="#approveModal"
                                  data-id="<?php echo $row['pid']; ?>"
                                  data-uid="<?php echo $payment_data['uid']; ?>"
                                  title="Approve">
                                  <i class="fas fa-check"></i>
                                </button>

                                <button type="button" class="btn btn-danger" style="float: none; margin: 5px;"
                                  data-toggle="modal" data-target="#denyModal"
                                  data-id="<?php echo $row['pid']; ?>"
                                  data-uid="<?php echo $payment_data['uid']; ?>"
                                  title="Deny"
                                  data-title="<?php echo  json_decode($row['prop_title'], true)['ar']; ?>">

                                  <i class="fas fa-times"></i>
                                </button>
                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php
                      }
                      ?>

                    </tbody>
                    <!-- Action button (initially hidden) -->
                    <button id="action-btn" class="btn btn-primary" style="display:none; margin-top:20px;">
                      Approve And Generate Payout File </button>
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



<!-- latest jquery-->

<!-- Deny Modal -->
<div class="modal fade" id="denyModal" tabindex="-1" role="dialog" aria-labelledby="denyModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="denyModalLabel">Reason for Denial</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="denyForm">
          <input type="hidden" id="denyId" name="id">
          <input type="hidden" id="denyTitle" name="property_title">
          <input type="hidden" id="denyUid" name="uid">
          <input type="hidden" name="type" value="deny_payout_reason" />


          <div class="form-group">
            <label for="denyReason">Please provide a reason:</label>
            <textarea class="form-control" id="denyReason" name="reason" rows="3" required></textarea>
            <div class="invalid-feedback">Please provide a denial reason.</div>


          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveDeny">Save</button>
      </div>
    </div>
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
        <input type="hidden" id="approveUid" name="uid">
        <input type="hidden" name="type" value="approve_payout" />
      </form>
      <div class="modal-body">
        Are you sure you want to approve this payout?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="confirmApproveBtn">Yes, Approve</button>
      </div>
    </div>
  </div>
</div>
</body>
<script>
  $(document).ready(function() {
    // Remove invalid class when user starts typing
    $('#denyReason').on('input', function() {
      if ($(this).val().trim() !== '') {
        $(this).removeClass('is-invalid');
      }
    });

    $('#denyModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var title = button.data('title');
      var uid = button.data('uid');

      var modal = $(this);
      modal.find('#denyId').val(id);
      modal.find('#denyUid').val(uid);
      modal.find('#denyTitle').val(title);
      modal.find('#propertyTitlePlaceholder').text(title);
    });

    $('#approveModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var uid = button.data('uid');

      var modal = $(this);
      modal.find('#approveId').val(id);
      modal.find('#approveUid').val(uid);
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

    // When save button is clicked
    $('#saveDeny').click(function() {
      var reasonInput = $('#denyReason');
      var reason = reasonInput.val().trim();

      // Validate
      if (reason === '') {
        reasonInput.addClass('is-invalid');
        return; // Stop submission
      }

      var formData = $('#denyForm').serialize();

      // Here you would typically make an AJAX call to save the data
      $.ajax({
        url: "include/property.php",
        type: "POST",
        data: formData,
        success: function(response) {
          let res = JSON.parse(response); // Parse the JSON response

          if (res.ResponseCode === "200" && res.Result === "true") {
            $('#denyModal').removeClass('show');
            $('#denyModal').css('display', 'none');
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
            alert("'Error saving denial reason.");
          }
        }
      });
    });
  });
</script>
<script>
  $(document).ready(function() {
    // Initialize DataTable
    var table = $('#basic-1').DataTable();

    // Select all/none functionality
    $('#select-all').on('click', function() {
      $('.row-checkbox').prop('checked', this.checked);
      toggleActionButton();
    });

    // Row checkbox change handler
    $('#basic-1 tbody').on('change', '.row-checkbox', function() {
      // Deselect "select all" if any row is unchecked
      if (!this.checked) {
        $('#select-all').prop('checked', false);
      }
      toggleActionButton();
    });

    // Function to show/hide action button
    function toggleActionButton() {
      var anyChecked = $('.row-checkbox:checked').length > 0;
      $('#action-btn').toggle(anyChecked);
    }

    // Action button click handler
    $('#action-btn').on('click', function() {
      var selectedIds = [];
      $('.row-checkbox:checked').each(function() {
        // Get data from the row (example gets name from 2nd column)
        var rowData = table.row($(this).closest('tr')).data();
        selectedIds.push(rowData[2]); // Push the name (or your ID)
      });
      var formData = {
        type: 'approve_payout_and_generate_payout',
        selected_ids: selectedIds,
        // Include any other form data you need to send
        // other_field: $('#other-field').val()
      };
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

          // Cleanup
          setTimeout(function() {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
          }, 100);

          // Handle modal and notifications
          $('#approveModal').removeClass('show').hide();
          $('.modal-backdrop').remove();

         
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
        },
        error: function() {
          $.notify('<i class="fas fa-exclamation-circle"></i> Error Export Excel Sheet ', {
            type: 'danger',
            allow_dismiss: true,
            delay: 5000
          });
        }
      });
    });
  });
  // Highlight selected rows
  $('#basic-1 tbody').on('change', '.row-checkbox', function() {
    $(this).closest('tr').toggleClass('selected-row', this.checked);
  });
</script>
<style>
  .row-checkbox {
    margin: 0;
    transform: scale(1.2);
  }

  .selected-row {
    background-color: #e3f2fd !important;
  }

  #select-all {
    transform: scale(1.2);
  }

  #action-btn {
    transition: all 0.3s ease;
  }
</style>
<?php
require 'include/footer.php';
?>
</body>

</html>