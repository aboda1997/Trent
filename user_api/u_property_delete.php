<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';

header('Content-Type: application/json');
try{

	 // Handle preflight request
	 if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

$data = json_decode(file_get_contents('php://input'), true);
$pro_id  = isset($data['prop_id']) ? $data['prop_id'] : '';
$uid  = isset($data['uid']) ? $data['uid'] : '';

if ($pro_id == '' or $uid == '') {
	$returnArr = generateResponse('false', "Something Went Wrong!", 401);
} else if (validateIdAndDatabaseExistance($pro_id, 'tbl_property',  "  add_user_id = " . $uid . " ") === false) {
	$returnArr = generateResponse('false', "this property not exist!", 404);
}else if (checkTableStatus($pro_id, 'tbl_property') == false) {
	$returnArr = generateResponse('false', "This Property already deleted", 410);
}  else {

	$table = "tbl_property";
	$field = "status=0";
	$where = "where id=" . $pro_id . "";
	$h = new Estate();
	$check = $h->restateupdateData_single($field, $table, $where);
	$returnArr = generateResponse('true', "Property Deleted Successfully!", 200);

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
