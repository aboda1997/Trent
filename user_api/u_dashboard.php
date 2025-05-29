<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';
require_once dirname(dirname(__FILE__)) . '/include/load_language.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
	// Handle preflight request
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		http_response_code(200);
		exit();
	}

	$uid = isset($_GET['uid']) ? intval($_GET['uid']) : '';
	$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
	$lang_ = load_specific_langauage($lang);

	if ($uid == null) {
		$returnArr    = generateResponse('false', "User id is required", 400);
	} else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
		$returnArr    = generateResponse('false', "User id is not exists", 400);
	} else {

		$total_property = $rstate->query("select * from tbl_property where  add_user_id=" . $uid . " and is_deleted = 0")->num_rows;
		$total_Booking = $rstate->query("select * from tbl_book where add_user_id=" . $uid . "")->num_rows;
		$total_earn = $rstate->query("select sum(total) as total_amt from tbl_book where add_user_id=" . $uid . " and book_status='Completed' ")->fetch_assoc();
		$earn = empty($total_earn['total_amt']) ? "0" : $total_earn['total_amt'];
		$total_payout = $rstate->query("SELECT  ROUND(SUM(CAST(b.total AS DECIMAL(65,30))) , 2)AS total_payout
		FROM tbl_payout_list pl
		INNER JOIN tbl_book b ON pl.book_id = b.id
		WHERE pl.uid = " . $uid
			. " AND pl.payout_status = 'Completed' ")->fetch_assoc();
		$payout = empty($total_payout['total_payout']) ? "0" : $total_payout['total_payout'];
		$count_payout_profiles = $rstate->query("select count(id) as count_payout from tbl_payout_profiles where status =1 and uid=" . $uid . "")->fetch_assoc();
		$count_payout = empty($count_payout_profiles['count_payout']) ? "0" : $count_payout_profiles['count_payout'];
		$is_gallery_enabled = (bool)$set["gallery_mode"];
		$check_plan = $rstate->query("select * from tbl_user where id=" . $uid . "")->fetch_assoc();

		

		$papi = array(
			array("title" => $lang_["Dashboard_My_Property"], "report_data" => $total_property, "url" => 'images/dashboard/property.png'),
			array("title" => $lang_["Dashboard_My_Booking"], "report_data" => intval($total_Booking), "url" => 'images/dashboard/my-booking.png'),
			array("title" => $lang_["Dashboard_My_Payout"], "report_data" => $payout, "url" => 'images/dashboard/my-payout.png'),
			array("title" => $lang_["Dashboard_My_Payout_Profiles"], "report_data" => intval($count_payout), "url" => 'images/dashboard/my-payout.png')
		);
		$returnArr = generateResponse('true', "Report List Get Successfully!!!", 200, array(
			"report_data" => $papi,
			"is_gallery_enabled" => $is_gallery_enabled,
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
