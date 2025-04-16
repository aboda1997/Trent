<?php
require dirname(dirname(__FILE__),2) . '/include/reconfig.php';
require dirname(dirname(__FILE__),2) . '/include/validation.php';
require dirname(dirname(__FILE__),2) . '/include/helper.php';
require dirname(dirname(__FILE__),2) . '/include/estate.php';
require_once dirname(dirname(__FILE__) , 2) . '/include/load_language.php';

header('Content-Type: application/json');
try{

	 // Handle preflight request
	 if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

$data = json_decode(file_get_contents('php://input'), true);
$profile_id  = isset($data['profile_id']) ? $data['profile_id'] : '';
$uid  = isset($data['uid']) ? $data['uid'] : '';
$lang  = isset($data['lang']) ? $data['lang'] : 'en';
$lang_ = load_specific_langauage($lang);

if ($profile_id == '' or $uid == '') {
	$returnArr = generateResponse('false', $lang_["general_error"], 401);
} else if (validateIdAndDatabaseExistance($profile_id, 'tbl_payout_profiles') === false) {
	$returnArr = generateResponse('false', $lang_["payout_profile_not_exist"], 404);
}
else {

	$table = "tbl_payout_profiles";
	$field = "status=0";
	$where = "where id=" . $profile_id . "";
	$h = new Estate();
	$check = $h->restateupdateData_single($field, $table, $where);
	$data = [["id" => $profile_id , 'id_deleted'=> true ]  ]; 

	$returnArr = generateResponse('true', $lang_["payout_profile_deleted"], 200 , $data);

}
echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
}
?>
