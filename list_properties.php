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
                <div class="table-responsive">
                  <table class="display" id="basic-1">
                    <thead>
                      <tr>
                        <th>Sr No.</th>
                        <th>Property ID</th>
                        <th>Property Title</th>
                        <th>Property Type</th>
                        <th>Property Image</th>
                        <th>Property Price(/Night)</th>
                        <th>Total Beds</th>
                        <th>Total Bathrooms</th>
                        <th>Total SQFT.</th>
                        <th>Property Facility</th>
                        <th>Property Rent or Buy?</th>
                        <th>Person Limit?</th>
                        <th>Property Status</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <?php
                        if ($_SESSION['restatename'] == 'Staff') {
                          if (in_array('Update', $property_per)) {
                        ?>
                            <th>
                              <?= $lang['Action'] ?></th>

                            </th>
                          <?php
                          }
                        } else {
                          ?>
                          <th>

                          </th>
                        <?php } ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $city = $rstate->query("
SELECT tbl_property.*,
       (SELECT GROUP_CONCAT(JSON_UNQUOTE(JSON_EXTRACT(`title`, '$.$lang_code')))
        FROM `tbl_facility`
        WHERE FIND_IN_SET(tbl_facility.id, tbl_property.facility)
       ) AS facility_select
FROM tbl_property
where is_approved = 1 
");
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
                            <?php $type = $rstate->query("select * from tbl_category where id=" . $row['ptype'] . "")->fetch_assoc();
                            $type = json_decode($type['title'], true);

                            echo $type[$lang_code]; ?>
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
                            <?php echo $row['price'] . " EGP" ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['beds']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['bathroom']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['sqrft']; ?>
                          </td>
                          <td class="align-middle">
                            <?php echo '<span class="badge badge-dark tag-pills-sm-mb">' . str_replace(',', '</span><span class="badge badge-dark tag-pills-sm-mb">', $row['facility_select']); ?>
                          </td>
                          <?php if ($row['pbuysell'] == 1) { ?>

                            <td><span class="badge badge-success">Rent</span></td>
                          <?php } else { ?>

                            <td>
                              <span class="badge badge-danger">Buy</span>
                              <?php if ($row['is_sell'] == 1) { ?><span class="badge badge-info">Property Selled</span>
                              <?php }
                              ?>
                            </td>
                          <?php } ?>

                          <td class="align-middle">
                            <?php echo $row['plimit']; ?>
                          </td>

                          <td>
                            <span class="badge status-toggle <?php echo $row['status'] ?   'badge-danger' : 'badge-success'; ?>"
                              data-id="<?php echo $row['id']; ?>"
                              data-status="<?php echo $row['status']; ?>"
                              style="cursor: pointer;">
                              <?php echo $row['status']  ? "make unpublish" : "make publish"; ?>
                            </span>

                          </td>
                          <td class="align-middle">
                            <?php echo $row['created_at']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['updated_at']; ?>
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
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>