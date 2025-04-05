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
                <?php
                if (isset($_GET['cn_id'])) {
                ?>
                  <form method="POST" enctype="multipart/form-data">

                    <div class="form-group mb-3">

                      <label id="basic-addon1">Enter Reason </label>

                      <textarea type="text" class="form-control" placeholder="Enter Reason" name="reason" aria-label="Username" aria-describedby="basic-addon1" style="resize:none;"></textarea>
                      <input type="hidden" name="type" value="update_approval_reason" />
                      <input type="hidden" name="id" value="<?php echo $_GET['cn_id']; ?>" />

                    </div>


                    <button type="submit" class="btn btn-primary">Cancle Approval</button>
                  </form>
                <?php
                } else {
                ?>
                  <div class="table-responsive">
                    <table class="display" id="basic-1">
                      <thead>
                        <tr>
                          <th>Sr No.</th>
                          <th>Property Title</th>
                          <th>Property Type</th>
                          <th>Property Image</th>

                          <?php
                          if ($_SESSION['restatename'] == 'Staff') {
                            if (in_array('Update', $property_per)) {
                          ?>
                              <th>
                                Property Approval</th>

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
                        $query = "
SELECT 
    p.*
FROM 
    tbl_property p
WHERE 
    p.is_approved = 0 
    AND (p.cancel_reason IS  NULL 
    or p.cancel_reason = '')
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


                            <?php
                            if ($_SESSION['restatename'] == 'Staff') {
                              if (in_array('Update', $property_per)) {
                            ?>
                                <td style="white-space: nowrap; width: 15%;">
                                  <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                    <div class="btn-group btn-group-sm" style="float: none;">
                                      <button class="btn btn-success drop" style="float: none; margin: 5px;" data-id="<?php echo $row['id']; ?>" data-status="1" data-type="toggle_approval">Approved</button>
                                      <a href="?cn_id=<?php echo $row['id']; ?>" class="btn btn-danger" style="float: none; margin: 5px;">Cancelled</a>

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
                                    <button class="btn btn-success drop" style="float: none; margin: 5px;" data-id="<?php echo $row['id']; ?>" data-status="1" data-type="toggle_approval">Approved</button>
                                    <a href="?cn_id=<?php echo $row['id']; ?>" class="btn btn-danger" style="float: none; margin: 5px;">Cancelled</a>

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
<!-- latest jquery-->

<?php
require 'include/footer.php';
?>
</body>

</html>