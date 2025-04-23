<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';

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
	if ($uid == null) {
		$returnArr    = generateResponse('false', "User id is required", 400);
	} else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
		$returnArr    = generateResponse('false', "User id is not exists", 400);
	} else {

		$total_property = $rstate->query("select * from tbl_property where  add_user_id=" . $uid . "")->num_rows;
		$total_extra_image = $rstate->query("select * from tbl_extra where add_user_id=" . $uid . "")->num_rows;
		$total_gallery_image = $rstate->query("select * from tbl_gallery where add_user_id=" . $uid . "")->num_rows;
		$total_gallery_category = $rstate->query("select * from tbl_gal_cat where add_user_id=" . $uid . "")->num_rows;
		$total_Booking = $rstate->query("select * from tbl_book where add_user_id=" . $uid . "")->num_rows;
		$total_enquiry = $rstate->query("select * from tbl_enquiry where add_user_id=" . $uid . "")->num_rows;
		$total_review = $rstate->query("select * from tbl_book where add_user_id=" . $uid . " and is_rate=1")->num_rows;
		$total_earn = $rstate->query("select sum(total) as total_amt from tbl_book where add_user_id=" . $uid . " and book_status='Completed' and p_method_id!=2")->fetch_assoc();
		$earn = empty($total_earn['total_amt']) ? "0" : $total_earn['total_amt'];
		$total_payout = $rstate->query("select sum(amt) as total_payout from payout_setting where owner_id=" . $uid . "")->fetch_assoc();
		$payout = empty($total_payout['total_payout']) ? "0" : $total_payout['total_payout'];
		$count_payout_profiles = $rstate->query("select count(id) as count_payout from tbl_payout_profiles where uid=" . $uid . "")->fetch_assoc();
		$count_payout = empty($count_payout_profiles['count_payout']) ? "0" : $count_payout_profiles['count_payout'];
		$finalearn = floatval($earn) - floatval($payout);
		$is_gallery_enabled = (bool)$set["gallery_mode"];
		$check_plan = $rstate->query("select * from tbl_user where id=" . $uid . "")->fetch_assoc();
		if ($check_plan['pack_id'] == 0) {
			$current_membership = 'no subscribed';
			$valid_till = '';
		} else {

			$pack = $rstate->query("select * from tbl_package where id=" . $check_plan['pack_id'] . "")->fetch_assoc();
			$udata = $rstate->query("select * from tbl_user where id=" . $uid . "")->fetch_assoc();
			$current_membership = $pack['title'];
			$valid_till = $udata['end_date'];
		}
		$udata = $rstate->query("select * from tbl_user where id=" . $uid . "")->fetch_assoc();
		$timestamp = date("Y-m-d");
		if ($udata['end_date'] < $timestamp) {
			$table = "tbl_user";
			$field = ["start_date" => NULL, "end_date" => NULL, "pack_id" => "0", "is_subscribe" => "0"];
			$where = "where id=" . $uid . "";
			$h = new Estate();
			$check = $h->restateupdateDatanull_Api($field, $table, $where);
			$table = "plan_purchase_history";
			$where = "where uid=" . $uid . "";
			$h = new Estate();
			$check = $h->restateDeleteData_Api($where, $table);
		}

		$getstatus = $rstate->query("select * from tbl_user where id=" . $uid . " and is_subscribe=1")->num_rows;
		$papi = array(array("title" => "My Property", "report_data" => $total_property, "url" => 'images/dashboard/property.png'), array("title" => "My Extra Images", "report_data" => $total_extra_image, "url" => 'images/dashboard/extra_images.png'), array("title" => "My Gallery Category", "report_data" => $total_gallery_category, "url" => 'images/dashboard/category.png'), array("title" => "My Gallery Images", "report_data" => $total_gallery_image, "url" => 'images/dashboard/gallery_image.png'), array("title" => "My Booking", "report_data" => intval($total_Booking), "url" => 'images/dashboard/my-booking.png'), array("title" => "My Earning", "report_data" => $finalearn, "url" => 'images/dashboard/my-earning.png'), array("title" => "My Enquiry", "report_data" => intval($total_enquiry), "url" => 'images/dashboard/my-inquiry.png'), array("title" => "Total Review", "report_data" => $total_review, "url" => 'images/dashboard/review.png'), array("title" => "My Payout", "report_data" => floatval($payout), "url" => 'images/dashboard/my-payout.png'),
		array("title" => "My Payout Profiles", "report_data" => $count_payout, "url" => 'images/dashboard/my-payout.png')
	);
		$member = array(array("title" => "Current Membership", "report_data" => $current_membership), array("title" => "Memerbship Expired Date", "report_data" => $valid_till));
		$returnArr = generateResponse('true', "Report List Get Successfully!!!", 200, array(
			"report_data" => $papi,
			"is_subscribe" => $getstatus,
			"is_gallery_enabled" => $is_gallery_enabled,
			"member_data" => $member
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
