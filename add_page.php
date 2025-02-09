<?php
require 'include/main_head.php';
$page_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_ar = load_specific_langauage('ar');
$lang_en = load_specific_langauage('en');
if (isset($_GET['id'])) {
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Update', $page_per)) {



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
} else {
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Write', $page_per)) {



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
                <?= $lang['Page_Management'] ?>
              </h3>
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
              <div class="card-header card-header-tabs-line d-flex justify-content-between align-items-center">
                <div></div>
                <div class="card-toolbar">
                  <!-- Add any toolbar buttons or icons here -->
                  <ul class="nav nav-tabs nav-bold nav-tabs-line">
                    <li class="nav-item">
                      <a class="nav-link " data-toggle="tab" href="#ar" onclick="changeLanguage('ar')">العربية</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" href="#en" onclick="changeLanguage('en')">English</a>
                    </li>
                  </ul>
                </div>
              </div>
              <?php
              if (isset($_GET['id'])) {
                $data = $rstate->query("select * from tbl_page where id=" . $_GET['id'] . "")->fetch_assoc();
                $title = json_decode($data['title'], true);
                $description = json_decode($data['description'], true);

              ?>
                <form method="POST" enctype="multipart/form-data">
                  <div class="card-body">
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Enter_Page_Title'] ?></label>
                          <input type="text" class="form-control"
                            placeholder="<?= $lang_en['Enter_Page_Title'] ?>"
                            name="title_en"
                            value="<?php echo $title['en']; ?>"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>

                        <div class="form-group mb-3">
                          <label for="description_en"><?= $lang_en['Page_Description'] ?></label>
                          <textarea class="form-control" rows="5" id="description_en" name="description_en" style="resize: none;"><?php echo $description['en']; ?></textarea>
                        </div>
                      </div>
                      <!-- Arabic Tab -->
                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Enter_Page_Title'] ?></label>
                          <input type="text" class="form-control"
                            placeholder="<?= $lang_ar['Enter_Page_Title'] ?>"
                            name="title_ar"
                            value="<?php echo $title['ar']; ?>"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>

                        <div class="form-group mb-3">
                          <label for="description_ar"><?= $lang_ar['Page_Description'] ?></label>
                          <textarea class="form-control" rows="5" id="description_ar" name="description_ar" style="resize: none;"><?php echo $description['ar']; ?></textarea>
                        </div>
                      </div>
                    </div>

                    <div class="form-group mb-3">
                      <label for="inputGroupSelect01"><?= $lang_en['Select_Page_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>
                        <option value=""><?= $lang_en['Choose'] ?>...</option>
                        <option value="1" <?php if ($data['status'] == 1) echo 'selected'; ?>><?= $lang_en['Publish'] ?></option>
                        <option value="0" <?php if ($data['status'] == 0) echo 'selected'; ?>><?= $lang_en['Unpublish'] ?></option>
                      </select>
                    </div>

                    <input type="hidden" name="type" value="edit_page" />
                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                  </div>
                  <div class="card-footer text-left">
                    <button id="edit_page"  type="submit" class="btn btn-primary"><?= $lang_en['Edit_Page'] ?></button>
                  </div>
                </form> <?php
                      } else {
                        ?>
                <form method="POST" enctype="multipart/form-data">
                  <div class="card-body">
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Enter_Page_Title'] ?></label>
                          <input type="text" class="form-control"
                            placeholder="<?= $lang_en['Enter_Page_Title'] ?>"
                            name="title_en"                                                        required=""

                            aria-describedby="basic-addon1" />
                        </div>

                        <div class="form-group mb-3">
                          <label for="description_en"><?= $lang_en['Page_Description'] ?></label>
                          <textarea class="form-control" rows="5" id="description_en" name="description_en" style="resize: none;"></textarea>
                        </div>
                      </div>
                      <!-- Arabic Tab -->
                      <div class="tab-pane fade show " id="ar">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Enter_Page_Title'] ?></label>
                          <input type="text" class="form-control"
                            placeholder="<?= $lang_ar['Enter_Page_Title'] ?>"
                            name="title_ar"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>

                        <div class="form-group mb-3">
                          <label  for="description_ar"><?= $lang_ar['Page_Description'] ?></label>
                          <textarea class="form-control" rows="5" id="description_ar" name="description_ar" style="resize: none;"></textarea>
                        </div>
                      </div>
                    </div>

                    <div class="form-group mb-3">
                      <label id="Select-Page-Status" for="inputGroupSelect01"><?= $lang_en['Select_Page_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>
                        <option value=""><?= $lang_en['Choose'] ?>...</option>
                        <option value="1" ><?= $lang_en['Publish'] ?></option>
                        <option value="0" ><?= $lang_en['Unpublish'] ?></option>
                      </select>
                    </div>

                    <input type="hidden" name="type" value="add_page" />
                  </div>
                  <div class="card-footer text-left">
                    <button id="add_page" type="submit" class="btn btn-primary"><?= $lang_en['Add_Page'] ?></button>
                  </div>
                </form>
              <?php } ?>


            </div>





          </div>
        </div>
        <!-- Container-fluid Ends-->
      </div>
      <!-- footer start-->

    </div>
  </div>
  <!-- latest jquery-->
  <script>
  function changeLanguage(lang) {
    var langData = (lang === "ar") ? langDataAR : langDataEN;

    document.getElementById('Select-Page-Status').textContent = langData.Select_Page_Status;

    if (document.getElementById('add_page')) {
      document.querySelector('button[type="submit"]').textContent = langData.Add_Page;

    } else {
      document.querySelector('button[type="submit"]').textContent = langData.Edit_Page;

    }

    const statusSelect = document.getElementById('inputGroupSelect01');
    statusSelect.querySelector('option[value=""]').textContent = langData.Choose;
    statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
    statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

  }
</script>

  <?php
  require 'include/footer.php';
  ?>
  </body>

  </html>