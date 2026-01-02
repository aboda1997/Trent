<?php
require_once dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require_once dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
	$pol = array();
	$c = array();

	$pro_id  = $_GET['prop_id'] ?? '';
	$uid  = $_GET['uid'] ?? 0;
	if ($pro_id == '') {
		$returnArr = generateResponse('false', "Something Went Wrong!", 400);
	} else {
		$sql = "SELECT check_in, check_out FROM tbl_book where prop_id=" . $pro_id . " and book_status != 'Cancelled'";
		$result = $rstate->query($sql);
		$date_list = [];


		// Output data of each row
		while ($row = $result->fetch_assoc()) {
			$date_list = array_merge($date_list, getDatesFromRange($row['check_in'], $row['check_out']));
		}

		// Remove duplicate dates
		 [$date_hold,$new_check_list] = get_holding_property_dates($pro_id, $uid, $rstate);
		// Remove duplicate dates
		$combined_dates = array_unique(array_merge($date_hold, $date_list));
		// Sort the dates
		sort($combined_dates);
		$returnArr    = generateResponse('true', "Book Date List  Founded!", 200, array(
			"date_list" => $combined_dates,
		));
	}
	echo $returnArr;
} catch (Exception $e) {
	// Handle exceptions and return an error response
	$returnArr = generateResponse('false', "An error occurred!", 500, array(
		"error_message" => $e->getMessage()
	), $e->getFile(),  $e->getLine());
	echo $returnArr;
}
