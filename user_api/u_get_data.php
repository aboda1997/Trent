 <?php
	require dirname(dirname(__FILE__)) . '/include/reconfig.php';
	require dirname(dirname(__FILE__)) . '/include/constants.php';
	header('Content-type: text/json');
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

		$count = $rstate->query("select * from tbl_user where id=" . $uid . "")->num_rows;
		$check_count = $rstate->query("select * from tbl_property where  status = 1 and  add_user_id=" . $uid . "")->num_rows;

		if ($check_count  >= AppConstants::Property_Count) {
			$rstate->query("UPDATE tbl_user SET is_owner = 0 WHERE id=" . $uid);
		} else {
			$rstate->query("UPDATE tbl_user SET is_owner = 1 WHERE id=" . $uid);
		}
		$owner = $lang['Property_Manager'];
		if ($count != 0) {
			$row = $rstate->query("select * from tbl_user where id=" . $uid . "")->fetch_assoc();
			$setting = $rstate->query("select * from tbl_setting")->fetch_assoc();


			$data['first_name'] = $row['first_name'];
			$data['last_name'] = $row['last_name'];

			$data['gender'] = $row['gender'];
			$data['email'] = $row['email'];
			$data['phone'] = $row['phone'];
			$data['img'] = $row['pro_pic'];
			$data['is_deleted'] = $row['status'];

			if ($row['is_owner']) {
				$owner = $lang['owner'];
				$data['membership'] = $owner;

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