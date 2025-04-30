<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {

	$pol = array();
	$c = array();
	$book_id  =  isset($_GET['book_id']) ? $_GET['book_id'] : '';
	$uid  =  isset($_GET['uid']) ? $_GET['uid'] : '';
    $lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
	if ($uid == '') {
		$returnArr    = generateResponse('false', "User id is required", 400);
	} else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
		$returnArr    = generateResponse('false', "User id is not exists", 400);
	} else if ($book_id == '') {
		$returnArr    = generateResponse('false', "Booking id is required", 400);
	} else if (validateIdAndDatabaseExistance($book_id, 'tbl_book' , "  (uid = ". $uid. " or add_user_id=".$uid.")") === false) {
		$returnArr    = generateResponse('false', "Booking id is not exists", 400);
	} else {
		$fp = array();
		$f = array();
		$v = array();
		$vr = array();
		$po = array();
		$sel = $rstate->query("select * from tbl_book where id=" . $book_id . " and (uid = ". $uid. " or add_user_id=".$uid.")" )->fetch_assoc();

		$fp['book_id'] = $book_id;
		$fp['prop_id'] = $sel['prop_id'];
		$fp['book_date'] = $sel['book_date'];
		$fp['check_in'] = $sel['check_in'];
		$fp['check_out'] = $sel['check_out'];
		$fp['subtotal'] = $sel['subtotal'];
		$fp['total'] = $sel['total'];
		$fp['prop_title'] = json_decode($sel['prop_title'], true)[$lang];
		$fp['p_method_id'] = $sel['p_method_id'];
		$fp['check_intime'] = $sel['check_intime'];
		$fp['check_outtime'] = $sel['check_outtime'];
		$fp['total_day'] = $sel['total_day'];
		$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $sel['prop_id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
		if ($checkrate != 0) {
			$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $row['prop_id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
			$fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 0, '.', '');
		} else {
			$fp['rate'] = number_format(0, 1, '.', '');
		}
		$fp['book_status'] = $sel['book_status'];
		$fp['noguest'] = $sel['noguest'];
		$fp['prop_price'] = $sel['prop_price'];
		$fp['cancle_reason'] = empty($sel['cancle_reason']) ? '' : $sel['cancle_reason'];
		$returnArr    = generateResponse('true', "My Book details Founded!", 200, array("Book_details" => $fp));

	}
	echo $returnArr;

} catch (Exception $e) {
	// Handle exceptions and return an error response
	$returnArr = generateResponse('false', "An error occurred!", 500, array(
		"error_message" => $e->getMessage()
	), $e->getFile(),  $e->getLine());
	echo $returnArr;
}
