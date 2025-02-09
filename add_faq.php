<?php
require 'include/main_head.php';
$faq_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_ar = load_specific_langauage('ar');
$lang_en = load_specific_langauage('en');

if (isset($_GET['id'])) {
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Update', $faq_per)) {



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
  if ($_SESSION['restatename'] == 'Staff' && !in_array('Write', $faq_per)) {



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
                <?= $lang['FAQ_Management'] ?>
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
                      <a class="nav-link active" data-toggle="tab" href="#ar" onclick="changeLanguage('ar')">العربية</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" href="#en" onclick="changeLanguage('en')">English</a>
                    </li>
                  </ul>
                </div>
              </div>
              <?php
              if (isset($_GET['id'])) {
                $data = $rstate->query("select * from tbl_faq where id=" . $_GET['id'] . "")->fetch_assoc();
                $question = json_decode($data['question'], true);
                $answer = json_decode($data['answer'], true);
              ?>
                <form method="POST" enctype="multipart/form-data">
                  <div class="card-body">
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show " id="en">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Enter_Question'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            value="<?php echo $question['en']; ?>"
                            placeholder="<?= $lang_en['Enter_Question'] ?>"
                            name="question_en"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>

                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Enter_Answer'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            value="<?php echo $answer['ar']; ?>"
                            placeholder="<?= $lang_en['Enter_Answer'] ?>"
                            name="answer_en"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>
                      </div>
                      <!-- Arabic Tab -->
                      <div class="tab-pane fade show active" id="ar">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Enter_Question'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            value="<?php echo $question['ar']; ?>"
                            placeholder="<?= $lang_ar['Enter_Question'] ?>"
                            name="question_ar"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>

                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Enter_Answer'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            value="<?php echo $answer['ar']; ?>"
                            placeholder="<?= $lang_ar['Enter_Answer'] ?>"
                            name="answer_ar"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>
                      </div>
                    </div>

                    <div class="form-group mb-3">
                      <label id="status-label" for="inputGroupSelect01"><?= $lang_ar['Select_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>

                        <option value="">
                          <?= $lang_ar['Choose'] ?>...</option>
                        <option value="1" <?php if ($data['status'] == 1) {
                                            echo 'selected';
                                          } ?>>
                          <?= $lang_ar['Publish'] ?>
                        </option>
                        <option value="0" <?php if ($data['status'] == 0) {
                                            echo 'selected';
                                          } ?>>
                          <?= $lang_ar['Unpublish'] ?>

                        </option>
                      </select>
                    </div>
                    <input type="hidden" name="type" value="edit_faq" />
                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                  </div>
                  <div class="card-footer text-left">

                    <button id="edit-faq" type="submit" class="btn btn-primary">
                      <?= $lang_ar['Edit_FAQ'] ?>

                    </button>
                  </div>
                </form>
              <?php
              } else {
              ?>
                <form method="POST" enctype="multipart/form-data">

                  <div class="card-body">
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show " id="en">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Enter_Question'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            placeholder="<?= $lang_en['Enter_Question'] ?>"
                            name="question_en"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>

                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Enter_Answer'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            placeholder="<?= $lang_en['Enter_Answer'] ?>"
                            name="answer_en"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>
                      </div>
                      <!-- Arabic Tab -->
                      <div class="tab-pane fade show active " id="ar">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Enter_Question'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            placeholder="<?= $lang_ar['Enter_Question'] ?>"
                            name="question_ar"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>

                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Enter_Answer'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            placeholder="<?= $lang_ar['Enter_Answer'] ?>"
                            name="answer_ar"
                            required=""
                            aria-describedby="basic-addon1" />
                        </div>
                      </div>
                    </div>

                    <div class="form-group mb-3">
                      <label id="status-label" for="inputGroupSelect01"><?= $lang_ar['Select_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>
                        <option value=""><?= $lang_ar['Choose'] ?>...</option>
                        <option value="1"><?= $lang_ar['Publish'] ?></option>
                        <option value="0"><?= $lang_ar['Unpublish'] ?></option>
                      </select>
                    </div>
                    <input type="hidden" name="type" value="add_faq" />
                  </div>
                  <div class="card-footer text-left">
                    <button id="add-faq" type="submit" class="btn btn-primary"><?= $lang_ar['Add_FAQ'] ?></button>
                  </div>
                </form>
              <?php } ?>

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
  function changeLanguage(lang) {
    var langData = (lang === "ar") ? langDataAR : langDataEN;

    document.getElementById('status-label').textContent = langData.Select_Status;

    if (document.getElementById('add-faq')) {
      document.querySelector('button[type="submit"]').textContent = langData.Add_FAQ;

    } else {
      document.querySelector('button[type="submit"]').textContent = langData.Edit_FAQ;

    }

    const statusSelect = document.getElementById('inputGroupSelect01');
    statusSelect.querySelector('option[value=""]').textContent = langData.Choose;
    statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
    statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

  }
</script>

<!-- latest jquery-->
<?php
require 'include/footer.php';
?>
</body>

</html>