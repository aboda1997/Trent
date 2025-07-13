<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';

header('Content-Type: application/json');
try{
		$lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
	
	$data = json_decode(file_get_contents('php://input'), true);
	$lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
	$lang = load_specific_langauage($lang_code);

	$uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : null;

	if ($uid == null) {
		$returnArr = generateResponse('false', "You must enter User id!", 400);
	} else {
		$data = array();

		$count = $rstate->query("select * from tbl_user where status= 1 and verified =1 and id=" . $uid . "")->num_rows;
		$check_count = $rstate->query("select * from tbl_property where  is_approved = 1 and  add_user_id=" . $uid . " and is_deleted = 0")->num_rows;

		if ($check_count  >= AppConstants::Property_Count) {
			$rstate->query("UPDATE tbl_user SET is_owner = 0 WHERE id=" . $uid);
		} else {
			$rstate->query("UPDATE tbl_user SET is_owner = 1 WHERE id=" . $uid);
		}
		$owner = $lang['Property_Manager'];
		if ($count != 0) {
			$row = $rstate->query("select * from tbl_user where  status= 1 and verified =1 and  id=" . $uid . "")->fetch_assoc();
			$setting = $rstate->query("select * from tbl_setting")->fetch_assoc();


			$data['full_name'] = $row['name'];
			$gender = [
				"f" => ["ar" => "انثى", "en" => "female"] ,
				 "m" => ["ar" => "ذكر", "en" => "male"] 
			];
			$data['gender'] = $row['gender'];
			$data['c_code'] = $row['ccode'];
			$data['email'] = $row['email'];
			$data['language'] = $lang_code;
			$data['phone'] = $row['mobile'];
			$data['pro_img'] = $row['pro_pic'];
			$data['is_deleted'] = 'false';
			if ( $row['status'] === 0) {
				$data['is_deleted'] = 'true';
			}
			$data['user_fees_percent'] = $set['gateway_percent_fees'];
			$data['user_fees_egp'] = $set['gateway_money_fees'];

			if ($row['is_owner']) {
				$owner = $lang['owner'];
				$data['membership'] = $owner;
				$data['owner_fees_percent'] = $set['owner_fees'];


			}else{
				$data['membership'] = $owner;
				$data['owner_fees_percent'] = $set['property_manager_fees'];

			}
			$returnArr = generateResponse(
				'true',
				"User Exist!",
				200,
				array(
					"user_data" => $data
				)
			);
		} else {
			$returnArr = generateResponse('false', "User Not Exist!", 404);
		}
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