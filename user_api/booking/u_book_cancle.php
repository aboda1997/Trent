<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {

  $data = json_decode(file_get_contents('php://input'), true);

  $book_id  =  isset($data['book_id']) ? $data['book_id'] : '';
  $uid  =  isset($data['uid']) ? $data['uid'] : '';
  if ($uid == '') {
    $returnArr    = generateResponse('false', "User id is required", 400);
  } else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
    $returnArr    = generateResponse('false', "User id is not exists", 400);
  } else if ($book_id == '') {
    $returnArr    = generateResponse('false', "Book id is required", 400);
  } else if (validateIdAndDatabaseExistance($book_id, 'tbl_book', "  (uid = " . $uid . ")") === false) {
    $returnArr    = generateResponse('false', "Book id is not exists", 400);
  } else {
    $book_id = $rstate->real_escape_string($data['book_id']);
    $uid =  $rstate->real_escape_string($data['uid']);
    $cancle_reason =  $rstate->real_escape_string($data['cancle_reason']);

    $table = "tbl_book";
    $field_cancel = array('book_status' => 'Cancelled', 'cancle_reason' => $deny_id, "cancel_by" => 'G');
    $where = "where uid=" . $uid . " and id=" . $book_id . " and book_status='Booked'";
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
