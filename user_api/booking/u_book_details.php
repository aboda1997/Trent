<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/constants.php';

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
	} else if (validateIdAndDatabaseExistance($book_id, 'tbl_book', "  (uid = " . $uid . " or add_user_id=" . $uid . ")") === false) {
		$returnArr    = generateResponse('false', "Booking id is not exists", 400);
	} else {
		$fp = array();
		$f = array();
		$v = array();
		$vr = array();
		$po = array();
		$sel = $rstate->query("select * from tbl_book where id=" . $book_id . " and (uid = " . $uid . " or add_user_id=" . $uid . ")")->fetch_assoc();
		$prop_data = $rstate->query("select * from tbl_property where id=" . $sel['prop_id'] . "")->fetch_assoc();
		$imageArray = array_filter(explode(',', $prop_data['image'] ?? ''));

		// Loop through each image URL and push to $vr array
		foreach ($imageArray as $image) {
			$vr[] = array('img' => trim($image));
		}
		$fp['book_id'] = $book_id;
		$fp['prop_id'] = $sel['prop_id'];
		$fp['address'] = json_decode($prop_data['address'], true)[$lang] ?? '';
		$fp['city'] = json_decode($prop_data['city'], true)[$lang] ?? '';
		if (is_null($prop_data['government'])) {
			$fp['government'] = null;
		} else {
			$gov = $rstate->query("
			SELECT id, name 
			FROM tbl_government 
			WHERE id=" . intval($prop_data['government']) . "
		");

			if ($gov->num_rows > 0) {
				$fp['government'] = [];

				while ($tit = $gov->fetch_assoc()) {
					// Combine the id and name into a single associative array
					$fp['government'] = [
						'id' => $tit['id'],
						'name' => json_decode($tit['name'], true)[$lang]
					];
				}
			} else {
				// Handle case when the query fails
				$fp['government'] = null;
			}
		}
		$fp['bathrooms_count'] = $prop_data['bathroom'];
		$fp['beds_count'] = $prop_data['beds'];

		if (is_null($prop_data['ptype'])) {
			$fp['category'] = null;
		} else {
			$title = $rstate->query("SELECT id, title FROM tbl_category WHERE id=" . $prop_data['ptype']);

			if ($title->num_rows > 0) {
				while ($tit = $title->fetch_assoc()) {
					// Combine the id and name into a single associative array
					$fp['category'] = [
						'id' => $tit['id'],
						'type' => json_decode($tit['title'], true)[$lang]
					];
				}
			} else {
				// Handle case when the query fails
				$fp['category'] = null;
			}
		}
		$fp['prop_img_list'] = $vr;
		$fp['book_date'] = $sel['book_date'];
		$fp['reminder_value'] = number_format($sel['reminder_value'], 2, '.', '');
		$fp['partial_value'] =  number_format($sel['total'] - $sel['reminder_value'], 2, '.', '');
		$fp['check_in'] = $sel['check_in'];
		$fp['check_out'] = $sel['check_out'];
		$fp['subtotal'] = $sel['subtotal'];
		$fp['total'] = $sel['total'];
		$fp['prop_title'] = json_decode($sel['prop_title'], true)[$lang] ?? '';
		$fp['pay_method'] = AppConstants::getPaymentMethod($sel['method_key'], $lang);
		$fp['check_intime'] = $sel['check_intime'];
		$fp['check_outtime'] = $sel['check_outtime'];
		$fp['total_day'] = $sel['total_day'];

		$rdata_rest = $rstate->query("SELECT sum(rating)/count(*) as rate_rest FROM tbl_rating where prop_id=" . $sel['prop_id'] . "")->fetch_assoc();
		$fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 1, '.', '');
		$fp['is_full_paid'] = ($sel['pay_status'] === 'Completed');
		$fp['item_id'] = $book_id;

		$fp['book_status'] = $sel['book_status'];
		$fp['noguest'] = $sel['noguest'];
		$fp['prop_price'] = $sel['prop_price'];
		$fp['cancle_reason'] = empty($sel['cancle_reason']) ? '' : $sel['cancle_reason'];
		$returnArr    = generateResponse('true', "My Booking details Founded!", 200, array("Booking_details" => $fp));
	}
	echo $returnArr;
} catch (Exception $e) {
	// Handle exceptions and return an error response
	$returnArr = generateResponse('false', "An error occurred!", 500, array(
		"error_message" => $e->getMessage()
	), $e->getFile(),  $e->getLine());
	echo $returnArr;
}
