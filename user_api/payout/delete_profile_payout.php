<?php
require dirname(dirname(__FILE__),2) . '/include/reconfig.php';
require dirname(dirname(__FILE__),2) . '/include/validation.php';
require dirname(dirname(__FILE__),2) . '/include/helper.php';
require dirname(dirname(__FILE__),2) . '/include/estate.php';

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

if ($profile_id == '' or $uid == '') {
	$returnArr = generateResponse('false', "Something Went Wrong!", 401);
} else if (validateIdAndDatabaseExistance($profile_id, 'tbl_payout_profiles') === false) {
	$returnArr = generateResponse('false', "This Payout Profile Not Exist!", 404);
}
else {

	$table = "tbl_payout_profiles";
	$field = "status=0";
	$where = "where id=" . $profile_id . "";
	$h = new Estate();
	$check = $h->restateupdateData_single($field, $table, $where);
	$data = [["id" => $profile_id , 'id_deleted'=> true ]  ]; 

	$returnArr = generateResponse('true', "Payout Profile Deleted Successfully!", 200 , $data);

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
