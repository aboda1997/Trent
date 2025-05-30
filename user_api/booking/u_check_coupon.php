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
	$cid  =  isset($_POST['cid']) ? $_POST['cid'] : null;
	$uid  =  isset($_POST['uid']) ? $_POST['uid'] : '';
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
	  } else  if ($cid  == null) {
		$returnArr    = generateResponse('false', $lang_["coupon_id_required"], 400);
	  } else if (validateCoupon($cid) === false) {
		$returnArr    = generateResponse('false', $lang_["booking_not_available"], 400);
	  }
	echo $returnArr;
} catch (Exception $e) {
	// Handle exceptions and return an error response
	$returnArr = generateResponse('false', "An error occurred!", 500, array(
		"error_message" => $e->getMessage()
	), $e->getFile(),  $e->getLine());
	echo $returnArr;
}
