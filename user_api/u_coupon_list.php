<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {

	$lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

	$pol = array();
	$c = array();

	$sel = $rstate->query("select * from tbl_coupon where status=1");
	while ($row = $sel->fetch_assoc()) {

		$pol['id'] = $row['id'];
		$pol['c_img'] = $row['c_img'];
		$pol['cdate'] = $row['cdate'];
		$pol['c_desc'] = json_decode($row['c_desc'], true)[$lang];
		$pol['c_value'] = $row['c_value'];
		$pol['coupon_code'] = $row['c_title'];
		$pol['coupon_title'] = json_decode($row['ctitle'], true)[$lang];
		$pol['min_amt'] = $row['min_amt'];
		$c[] = $pol;
	}
	if (empty($c)) {

		$returnArr    = generateResponse('true', "Coupon List Not Founded!", 200, array(
			"coupon_list" => $c,
			"length" => count($c),
		));
	} else {

		$returnArr    = generateResponse('true', "Coupon List Founded!", 200, array(
			"coupon_list" => $c,
			"length" => count($c),
		));
	}
	echo $returnArr;
} catch (Exception $e) {
	// Handle exceptions and return an error response
	$returnArr = generateResponse('false', "An error occurred!", 500, array(
		"error_message" => $e->getMessage()
	));
	echo $returnArr;
}
