<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';

header('Content-type: text/json');
// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
try{
$data = json_decode(file_get_contents('php://input'), true);
$pro_id  = isset($data['prop_id']) ? $data['prop_id'] : '';
$uid  = isset($data['uid']) ? $data['uid'] : '';
$sel = $rstate->query("select id from tbl_fav where  uid =" .$uid . " and  property_id=" . $pro_id . "");

if ($pro_id == '' or $uid == '') {
	$returnArr = generateResponse('false', "Something Went Wrong!", 401);
} else if (validateIdAndDatabaseExistance($pro_id, 'tbl_property') === false) {
	$returnArr = generateResponse('false', "this property not exist!", 404);
}else if ($sel->num_rows > 0){
	$row = $sel->fetch_assoc();

	$table = "tbl_fav";
	$where = " where  id = ". $row['id'] . "" ;

	$h = new Estate();
	$h->restateDeleteData_Api($where, $table);
	$data = [["id" => $pro_id , 'status'=> 'false'] ]; 
	$returnArr = generateResponse('true', "The property was removed from favorites successfully!",200,$data);


}else {
	$sel = $rstate->query("select * from tbl_property where  add_user_id =" .$uid . " and  id=" . $pro_id . "")->fetch_assoc();
	$table = "tbl_fav";
	$field_values = array("uid", "property_id", );
	$data_values = array("$uid", "$pro_id");
	$h = new Estate();
	$h->restateinsertdata_Api($field_values, $data_values, $table);
	$data = [["id" => $pro_id , 'status'=> 'true' ]  ]; 
	$returnArr = generateResponse('true', "The property was toggled as favorite successfully!", 201, $data);

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