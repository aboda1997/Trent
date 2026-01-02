<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

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
  } else if (validateIdAndDatabaseExistance($cancel_id, 'tbl_user_cancel_reason') === false) {
    $returnArr = generateResponse('false', $lang_["invalid_cancel_id"], 400);
  } else if (!in_array(getBookingStatus($booking_id)['book_status'], ['Booked', 'Confirmed'])){
    $returnArr    = generateResponse('false', $lang_["not_allow_to_do"], 400);
  } else {
 $query = "SELECT b.method_key
    FROM tbl_book b
    WHERE b.id = $booking_id";
    $result = $GLOBALS['rstate']->query($query)->fetch_assoc();
    $table = "tbl_book";
    $field_cancel = array('book_status' => 'Cancelled', 'cancle_reason' => $cancel_id, "cancel_by" => 'G');
    $where = "where uid=" . '?' . " and id=" . '?' . "";
    $table = "tbl_book";
    $h = new Estate();
    $where_conditions = [$uid, $booking_id];
    $check = $h->restateupdateData_Api($field_cancel, $table, $where, $where_conditions);
    if ($result['method_key'] == 'TRENT_BALANCE'){
    $refund = refundMoney($uid , $booking_id);

    }
    $returnArr = generateResponse("true",  "Booking  Cancelled Successfully!", 200);
  }
  echo $returnArr;
} catch (Exception $e) {
  // Handle exceptions and return an error response
  $returnArr = generateResponse('false', "An error occurred!", 500, array(
    "error_message" => $e->getMessage()
  ), $e->getFile(),  $e->getLine());
  echo $returnArr;
}
