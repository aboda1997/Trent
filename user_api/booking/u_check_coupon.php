<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');
try {
	$cid  =  isset($_GET['coupon_code']) ? $_GET['coupon_code'] : null;
	$uid  =  isset($_GET['uid']) ? $_GET['uid'] : '';
	$total  =  isset($_GET['total']) ? $_GET['total'] : 0;
	$lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
	$lang_ = load_specific_langauage($lang);
	if ($uid == '') {
		$returnArr = generateResponse('false', $lang_["user_id_required"], 400);
	} else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
		$returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
	} else if (checkTableStatus($uid, 'tbl_user') === false) {
		$returnArr = generateResponse('false', $lang_["account_deleted"], 400);
	} else if (!in_array($lang, ['en', 'ar'])) {
		$returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
	} else  if ($cid  == null) {
		$returnArr    = generateResponse('false', $lang_["coupon_id_required"], 400);
	} else if (validateCoupon($cid, $total)['status'] === false) {
		$returnArr    = generateResponse('false', $lang_["coupon_not_available"], 400);
	} else {
		$value  = validateCoupon($cid, $total)['value'];
		$final_total =  $total - $value ; 
		$partial_value =  ($final_total*10 )/ 100 ; 
		$reminder_value =  $final_total - $partial_value ; 
		$returnArr    = generateResponse('true', "Valid Coupon", 200, array(
			'coupon_value' => $value ,
			'partial_value' =>number_format($partial_value, 2, '.', '') ,
			'reminder_value' => number_format($reminder_value, 2, '.', ''),
			'final_total' => number_format($final_total, 2, '.', '')
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
