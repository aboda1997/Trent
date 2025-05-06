<?php
require 'include/main_head.php';
$property_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_code = load_language_code()["language_code"];

if ($_SESSION['restatename'] == 'Staff' && !in_array('Read', $property_per)) {



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
                Pending Property List </h3>
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
                        <th>Property ID</th>

                        <th>Property Title</th>

                        <th>Property Image</th>
                        <th>Property Type</th>

                        <th>
                          Property Approval</th>

                        <?php
                        if ($_SESSION['restatename'] == 'Staff') {
                          if (in_array('Update', $property_per)) {
                        ?>

                            <th>
                              <?= $lang['Action'] ?></th>
                          <?php
                          }
                        } else {
                          ?>
                          <th>
                            <?= $lang['Action'] ?>

                          </th>
                        <?php } ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "
SELECT 
    p.*
FROM 
    tbl_property p
WHERE 
    p.is_approved = 0 
";

                      $city = $rstate->query($query);
                      $i = 0;
                      while ($row = $city->fetch_assoc()) {
                        $title = json_decode($row['title'], true);

                        $i = $i + 1;
                      ?>
                        <tr>
                          <td>
                            <?php echo $i; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row["id"] ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $title[$lang_code]; ?>
                          </td>


                          <td class="align-middle">
                            <img src="<?php
                                      $imageArray = explode(',', $row['image']);

                                      if (!empty($imageArray[0])) {
                                        echo $imageArray[0];
                                      } else {
                                        echo 'default_image.jpg';
                                      }
                                      ?>" width="70" height="80" />
                          </td>
                          <td class="align-middle">
                            <?php $type = $rstate->query("select * from tbl_category where id=" . $row['ptype'] . "")->fetch_assoc();
                            $type = json_decode($type['title'], true);

                            echo $type[$lang_code]; ?>
                          </td>
                          <td class="align-middle">
                            <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                              <div class="btn-group btn-group-sm" style="float: none;">

                                <button class="btn btn-success " style="float: none; margin: 5px;"
                                  type="button"
                                  data-toggle="modal" data-target="#approveModal"
                                  data-id="<?php echo $row['id']; ?>"
                                  data-uid="<?php echo $row['add_user_id']; ?>"
                                  data-title="<?php echo $title['ar']; ?>"

                                  data-status="<?php echo "1"; ?>"
                                  title="Approve">
                                  Approve
                                </button>
                                <button type="button" class="btn btn-danger" style="float: none; margin: 5px;"
                                  data-toggle="modal" data-target="#denyModal"
                                  data-id="<?php echo $row['id']; ?>"
                                  data-title="<?php echo $title['ar']; ?>"
                                  data-uid="<?php echo $row['add_user_id']; ?>">
                                  Deny
                                </button>
                              </div>

                            </div>

                          </td>

                          <?php
                          if ($_SESSION['restatename'] == 'Staff') {
                            if (in_array('Update', $property_per)) {
                          ?>

                              <td style="white-space: nowrap; width: 15%;">
                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                  <div class="btn-group btn-group-sm" style="float: none;">
                                    <a href="add_properties.php?id=<?php echo $row['id']; ?>" data-toggle="tooltip" title="edit property" class="tabledit-edit-button" style="float: none; margin: 5px;">
                                      <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="30" height="30" rx="15" fill="#79F9B4" />
                                        <path d="M22.5168 9.34109L20.6589 7.48324C20.0011 6.83703 18.951 6.837 18.2933 7.49476L16.7355 9.06416L20.9359 13.2645L22.5052 11.7067C23.163 11.0489 23.163 9.99885 22.5168 9.34109ZM15.5123 10.2873L8 17.8342V22H12.1658L19.7127 14.4877L15.5123 10.2873Z" fill="#25314C" />
                                      </svg></a>

                                  </div>

                                </div>
                              </td>
                            <?php
                            }
                          } else {
                            ?>

                            <td style="white-space: nowrap; width: 15%;">
                              <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                <div class="btn-group btn-group-sm" style="float: none;">
                                  <a href="add_properties.php?id=<?php echo $row['id']; ?>" data-toggle="tooltip" title="edit property" class="tabledit-edit-button" style="float: none; margin: 5px;">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                      <rect width="30" height="30" rx="15" fill="#79F9B4" />
                                      <path d="M22.5168 9.34109L20.6589 7.48324C20.0011 6.83703 18.951 6.837 18.2933 7.49476L16.7355 9.06416L20.9359 13.2645L22.5052 11.7067C23.163 11.0489 23.163 9.99885 22.5168 9.34109ZM15.5123 10.2873L8 17.8342V22H12.1658L19.7127 14.4877L15.5123 10.2873Z" fill="#25314C" />
                                    </svg></a>

                                </div>

                              </div>
                            </td>
                          <?php
                          }
                          ?>

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
<!-- latest jquery-->

<?php
require 'include/footer.php';
?>

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
          <input type="hidden" name="type" value="deny_reason" />


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
        <input type="hidden" id="approveTitle" name="property_title">

        <input type="hidden" id="approveStatus" name="status">
        <input type="hidden" name="type" value="toggle_approval" />
      </form>
      <div class="modal-body">
        Are you sure you want to approve this property?
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
      var status = button.data('status');
      var title = button.data('title');

      var modal = $(this);
      modal.find('#approveId').val(id);
      modal.find('#approveUid').val(uid);
      modal.find('#approveStatus').val(status);
      modal.find('#approveTitle').val(title);

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

</html>