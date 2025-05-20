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
                Booking Rating list Management</h3>
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

                        <th>Property Name </th>
                        <th>rating </th>

                        <th>Comment</th>


                        <th> Full Name </th>
                        <th> action </th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $city = $rstate->query("SELECT  r.* ,b.prop_title FROM tbl_rating r INNER JOIN tbl_book b ON FIND_IN_SET(b.id, r.book_id) > 0 ");
                      $i = 0;
                      while ($row = $city->fetch_assoc()) {
                        $i = $i + 1;
                        $guest_id = $row['uid'];
                        $guest = $rstate->query("select name  , mobile from tbl_user where id= $guest_id")->fetch_assoc();

                      ?>
                        <tr>
                          <td>
                            <?php echo $i; ?>
                          </td>

                          <td class="align-middle">
                            <?php
                            $type = json_decode($row['prop_title'], true);

                            echo $type[$lang_code]; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['rating']; ?>
                          </td>

                          <td class="align-middle">
                            <?php echo $row['comment']; ?>
                          </td>


                          <td class="align-middle">
                            <?php echo $guest['name']; ?>
                          </td>

                          <td style="white-space: nowrap; width: 15%;">
                            <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                              <div class="btn-group btn-group-sm" style="float: none;">

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
        <h5 class="modal-title" id="approveModalLabel">Confirm Approval</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="approveForm">

        <input type="hidden" id="approveId" name="id">
        <input type="hidden" name="type" value="delete_rating" />
      </form>
      <div class="modal-body">
        Are you sure you want to delete this rating?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="confirmApproveBtn">Yes, Delete</button>
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

  });
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>