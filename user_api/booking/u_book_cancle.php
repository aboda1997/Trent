<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {


  $booking_id  =  isset($_POST['booking_id']) ? $_POST['booking_id'] : null;
  $uid  =  isset($_POST['uid']) ? $_POST['uid'] : '';
  $cancel_id = isset($_POST['cancel_id']) ? $_POST['cancel_id'] : null;
  $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';
  $lang_ = load_specific_langauage($lang);

  if ($uid == '') {
    $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
  } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
    $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
  } else if (checkTableStatus($uid, 'tbl_user') === false) {
    $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
  } else if (!in_array($lang, ['en', 'ar'])) {
    $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
  } else  if ($booking_id  == null) {
    $returnArr    = generateResponse('false', $lang_["booking_id_required"], 400);
  } else if (validateIdAndDatabaseExistance($booking_id, 'tbl_book', ' uid  =' . $uid . '') === false) {
    $returnArr    = generateResponse('false', $lang_["booking_not_available"], 400);
  } else if ($cancel_id == null) {
    $returnArr = generateResponse('false', $lang_["cancel_id_required"], 400);
  }else if ( validateIdAndDatabaseExistance($cancel_id, 'tbl_user_cancel_reason') === false) {
    $returnArr = generateResponse('false', $lang_["invalid_cancel_id"], 400);
} else {

    $table = "tbl_book";
    $field_cancel = array('book_status' => 'Cancelled', 'cancle_reason' => $cancel_id, "cancel_by" => 'G');
    $where = "where uid=" . $uid . " and id=" . $booking_id . " and book_status='Booked'";
    $h = new Estate();
    $check = $h->restateupdateData_Api($field, $table, $where);
    $returnArr = array("ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Booking  Cancelled Successfully!");
  }
  echo $returnArr;
} catch (Exception $e) {
  // Handle exceptions and return an error response
  $returnArr = generateResponse('false', "An error occurred!", 500, array(
    "error_message" => $e->getMessage()
  ), $e->getFile(),  $e->getLine());
  echo $returnArr;
}
