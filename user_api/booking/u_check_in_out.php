<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');
try {

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  http_response_code(200);
  exit();
}
  $booking_id  =  isset($_POST['booking_id']) ? $_POST['booking_id'] : null;
  $uid  =  isset($_POST['uid']) ? $_POST['uid'] : '';
  $is_check_in = isset($_POST['is_check_in']) ? $_POST['is_check_in'] : 'true';
  $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';
  $lang_ = load_specific_langauage($lang);

  $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
  $timestamp =$date->format('Y-m-d H:i:s');
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
  } else if ($is_check_in == 'true' && getBookingStatus($booking_id)['book_status'] != 'Confirmed') {
    $returnArr    = generateResponse('false', $lang_["not_allow_to_do"], 400);
  } else if ($is_check_in == 'false' && getBookingStatus($booking_id)['book_status'] != 'Check_in') {
    $returnArr    = generateResponse('false', $lang_["not_allow_to_do"], 400);
  } else if ($is_check_in == 'true' && !validateCheckInDate($booking_id , $timestamp)) {
    $returnArr    = generateResponse('false', $lang_["not_allow_to_do"], 400);
  }
  else {
    $table = "tbl_book";
    $fp = array();
    $field_check_in = array('book_status' => 'Check_in', 'check_intime' => $timestamp);
    $where = "where uid=" . '?' . " and id=" . '?' . "";
    $h = new Estate();
    $where_conditions = [$uid, $booking_id];
    $field_check_out = array('book_status' => 'Completed', 'check_outtime' => $timestamp);
    $booking_data = $rstate->query("select uid , prop_id , book_date ,prop_title	, uid from tbl_book where  id= $booking_id  ")->fetch_assoc();

  if($is_check_in == "true"){
    $check = $h->restateupdateData_Api($field_check_in, $table, $where, $where_conditions);
    $returnArr    = generateResponse('true', $lang_["property_booking_check_in_success"], 200, array(
      "booking_details" => [
          'prop_id' =>  $booking_data['prop_id'],
          'book_date' =>  $booking_data['book_date'],
          'prop_title' => json_decode($booking_data['prop_title'], true)[$lang]
      ],
  ));
  }else{
    $check = $h->restateupdateData_Api($field_check_out, $table, $where, $where_conditions);
    $returnArr    = generateResponse('true', $lang_["property_booking_check_out_success"], 200, array(
      "booking_details" => [
          'prop_id' =>  $booking_data['prop_id'],
          'book_date' =>  $booking_data['book_date'],
          'prop_title' => json_decode($booking_data['prop_title'], true)[$lang]
      ],
  ));
  }
  }
  echo $returnArr;
} catch (Exception $e) {
  // Handle exceptions and return an error response
  $returnArr = generateResponse('false', "An error occurred!", 500, array(
    "error_message" => $e->getMessage()
  ), $e->getFile(),  $e->getLine());
  echo $returnArr;
}
