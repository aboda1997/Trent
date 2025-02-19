<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$pro_id  = isset($data['prop_id']) ? $data['prop_id'] : '';
$uid  = isset($data['uid']) ? $data['uid'] : '';

if ($pro_id == '' or $uid == '') {
	$returnArr = generateResponse('false', "Something Went Wrong!", 401);
} else if (validateIdAndDatabaseExistance($pro_id, 'tbl_property',  "  add_user_id = " . $uid . " ") === false) {
	$returnArr = generateResponse('false', "this property not exist!", 401);
} else {
	$sel = $rstate->query("select * from tbl_property where  add_user_id =" .$uid . " and  id=" . $pro_id . "")->fetch_assoc();
    $property_type = $sel['ptype'];
	$table = "tbl_fav";
	$field_values = array("uid", "property_id", "property_type");
	$data_values = array("$uid", "$pro_id", "$property_type");

	$h = new Estate();
	$h->restateinsertdata_Api($field_values, $data_values, $table);
	$returnArr = generateResponse('true', "The property was toggled as favorite successfully!", 201);

}
echo $returnArr;
