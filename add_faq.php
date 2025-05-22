<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];

if (isset($_GET['id'])) {
  if (!in_array('Update_FAQ', $per)) {



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
  if ( !in_array('Create_FAQ', $per)) {



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
                $data = $rstate->query("select * from tbl_faq where id=" . $_GET['id'] . "")->fetch_assoc();
                $question = json_decode($data['question'], true);
                $answer = json_decode($data['answer'], true);
              ?>
                <form 
                onsubmit="return submitform(true)" 

                method="POST" enctype="multipart/form-data">
                  <div class="card-body">
                  <div id="alert-container" class="mb-3" style="display: none;">
                      <div class="alert alert-danger" id="alert-message"></div>
                    </div>
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show active" id="en">
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
                            <div class="invalid-feedback" id="question_en_feedback" style="display: none;">
                        <?= $lang_en['faq_question'] ?>
                      </div>
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
                            <div class="invalid-feedback" id="answer_en_feedback" style="display: none;">
                        <?= $lang_en['faq_answer'] ?>
                      </div>
                        </div>
                      </div>
                      <!-- Arabic Tab -->
                      <div class="tab-pane fade show " id="ar">
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
                            <div class="invalid-feedback" id="question_ar_feedback" style="display: none;">
                        <?= $lang_en['faq_question'] ?>
                      </div>
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
                            <div class="invalid-feedback" id="answer_ar_feedback" style="display: none;">
                        <?= $lang_en['faq_answer'] ?>
                      </div>
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
                        <div class="invalid-feedback" id="status_feedback" style="display: none;">
                        <?= $lang_en['faq_status'] ?>
                      </div>
                      </select>
                    </div>
                    <input type="hidden" name="type" value="edit_faq" />
                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
                  </div>
                  <div class="card-footer text-left">

                    <button onclick="return validateForm()" id="edit-faq" type="submit" class="btn btn-primary">
                      <?= $lang_en['Edit_FAQ'] ?>

                    </button>
                  </div>
                </form>
              <?php
              } else {
              ?>
                <form 
                onsubmit="return submitform(true)" 

                method="POST" enctype="multipart/form-data">

                  <div class="card-body">
                  <div id="alert-container" class="mb-3" style="display: none;">
                      <div class="alert alert-danger" id="alert-message"></div>
                    </div>
                    <div class="tab-content">
                      <!-- English Tab -->
                      <div class="tab-pane fade show active" id="en">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_en['Enter_Question'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            placeholder="<?= $lang_en['Enter_Question'] ?>"
                            name="question_en"
                            required=""
                            aria-describedby="basic-addon1" />
                            
                            <div class="invalid-feedback" id="question_en_feedback" style="display: none;">
                        <?= $lang_en['faq_question'] ?>
                      </div>
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
                            <div class="invalid-feedback" id="answer_en_feedback" style="display: none;">
                        <?= $lang_en['faq_answer'] ?>
                      </div>
                        </div>
                      </div>
                      <!-- Arabic Tab -->
                      <div class="tab-pane fade show  " id="ar">
                        <div class="form-group mb-3">
                          <label id="basic-addon1"><?= $lang_ar['Enter_Question'] ?></label>
                          <input
                            type="text"
                            class="form-control"
                            placeholder="<?= $lang_ar['Enter_Question'] ?>"
                            name="question_ar"
                            required=""
                            aria-describedby="basic-addon1" />
                            <div class="invalid-feedback" id="question_ar_feedback" style="display: none;">
                        <?= $lang_ar['faq_question'] ?>
                      </div>
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
                            <div class="invalid-feedback" id="answer_ar_feedback" style="display: none;">
                        <?= $lang_ar['faq_answer'] ?>
                      </div>
                          </div>
                      </div>
                    </div>

                    <div class="form-group mb-3">
                      <label id="status-label" for="inputGroupSelect01"><?= $lang_en['Select_Status'] ?></label>
                      <select class="form-control" name="status" id="inputGroupSelect01" required>
                        <option value=""><?= $lang_en['Choose'] ?>...</option>
                        <option value="1"><?= $lang_en['Publish'] ?></option>
                        <option value="0"><?= $lang_en['Unpublish'] ?></option>
                      </select>
                      <div class="invalid-feedback" id="status_feedback" style="display: none;">
                        <?= $lang_en['faq_status'] ?>
                      </div>
                    </div>
                    <input type="hidden" name="type" value="add_faq" />
                  </div>
                  <div class="card-footer text-left">
                    <button onclick="return validateForm()" id="add-faq" type="submit" class="btn btn-primary"><?= $lang_en['Add_FAQ'] ?></button>
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
  function getCurrentLanguage() {
    // Get the active tab
    const activeTab = document.querySelector('.nav-link.active').getAttribute('href').substring(1);
    return activeTab === 'en' ? 'en' : 'ar';
  }

  function validateForm() {
    // Clear previous feedback
    document.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
      feedback.style.display = 'none';
    });

    const questionEn = document.querySelector('input[name="question_en"]').value;
    const questionAr = document.querySelector('input[name="question_ar"]').value;

    
    const answerEn = document.querySelector('input[name="answer_en"]').value;
    const answerAr = document.querySelector('input[name="answer_ar"]').value;
    const status = document.querySelector('select[name="status"]').value;

    let isValid = true;
    let isArabicValid = true;
    let isEnglishValid = true;
    let alertMessage = '';
    let lang = getCurrentLanguage();

    if (!questionEn) {
      document.getElementById('question_en_feedback').style.display = 'block';
      isEnglishValid = false;

    }
    if (!questionAr) {
      document.getElementById('question_ar_feedback').style.display = 'block';
      isArabicValid = false;

    }
    if (!answerEn) {
      document.getElementById('answer_en_feedback').style.display = 'block';
      isEnglishValid = false;

    }
    if (!answerAr) {
      document.getElementById('answer_ar_feedback').style.display = 'block';
      isArabicValid = false;

    }

    if (!status) {
      document.getElementById('status_feedback').style.display = 'block';
      isValid = false;
    }



    if (!isArabicValid && isEnglishValid) {
      // Show alert if there are required fields missing
      if (lang == "en") {
        alertMessage = langDataEN.alert_en;

      } else {
        alertMessage = langDataAR.alert_en;

      }
      isValid = false;
    }
    if (!isEnglishValid && isArabicValid) {
      // Show alert if there are required fields missing
      if (lang == "ar") {
        alertMessage = langDataAR.alert_ar;

      } else {
        alertMessage = langDataEN.alert_ar;

      }
      isValid = false;
    }
    if (isArabicValid && isEnglishValid) {
      alertMessage = '';
    }
    if (alertMessage) {
      document.getElementById('alert-message').innerHTML = alertMessage;
      document.getElementById('alert-container').style.display = 'block';

    } else {
      document.getElementById('alert-container').style.display = 'none';

    }
    if (!isValid) {
      return false;
    }

    return true; // Allow form submission
  }

  function changeLanguage(lang) {
    var langData = (lang === "ar") ? langDataAR : langDataEN;

    document.getElementById('question_en_feedback').textContent = langData.faq_question;
    document.getElementById('answer_en_feedback').textContent = langData.faq_answer;
    document.getElementById('status_feedback').textContent = langData.faq_status;
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