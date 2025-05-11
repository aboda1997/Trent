<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {

	$pol = array();
	$c = array();

	$status  =  isset($_GET['status']) ? $_GET['status'] : '';
	$uid  =  isset($_GET['uid']) ? $_GET['uid'] : '';
	$lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
	$is_owner = isset($_GET['is_owner']) ? $_GET['is_owner'] : 'false';
	if ($uid == '') {
		$returnArr    = generateResponse('false', "User id is required", 400);
	} else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
		$returnArr    = generateResponse('false', "User id is not exists", 400);
	} else if ($status == '') {
		$returnArr    = generateResponse('false', "Booking Status is required", 400);
	} else {
		$fp = array();
		$wow = array();
		if ($status == 'active') {
			if ($is_owner == 'true') {
				$bd = $rstate->query("select * from tbl_book where add_user_id=" . $uid . " and book_status!='Completed' and book_status!='Cancelled' order by id desc");
			} else {
				$bd = $rstate->query("select * from tbl_book where uid=" . $uid . " and book_status!='Completed' and book_status!='Cancelled' order by id desc");
			}
		} else {
			if ($is_owner == 'true') {
				$bd = $rstate->query("select * from tbl_book where add_user_id=" . $uid . " and (book_status='Completed' or book_status='Cancelled') order by id desc");
			} else {
				$bd = $rstate->query("select * from tbl_book where uid=" . $uid . " and (book_status='Completed' or book_status='Cancelled') order by id desc");
			}
		}
		while ($row = $bd->fetch_assoc()) {
			$fp['book_id'] = $row['id'];
			$fp['prop_id'] = $row['prop_id'];
			$fp['prop_img'] = $row['prop_img'];
			$fp['prop_title'] = json_decode($row['prop_title'], true)[$lang];

			$fp['p_method_id'] = $row['p_method_id'];
			$fp['prop_price'] = $row['prop_price'];
			$fp['total_day'] = $row['total_day'];
			$fp['total_paid'] = $row['total'];
			$fp['check_in'] = $row['check_in'];
			$fp['check_out'] = $row['check_out'];

			$rdata_rest = $rstate->query("SELECT sum(rating)/count(*) as rate_rest FROM tbl_rating where prop_id=" . $row['prop_id'] . "")->fetch_assoc();
			$fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 1, '.', '');
			$book_id = $row['id'];
			$individual_data = $rstate->query("select * from tbl_rating where book_id=" . $book_id . " ")->fetch_assoc();
			if ($status != 'active') {

				$fp['individual_rate'] = [
					'id' => $individual_data['id']?? null,
					'rate' =>  isset($individual_data['rating']) 
					? number_format((float)$individual_data['rating'], 1, '.', '') 
					: null ,
					'comment' => $individual_data['comment'] ?? null ,
					'created_at' => $individual_data['created_at'] ?? null

				];
			}

			$fp['book_status'] = $row['book_status'];


			$wow[] = $fp;
		}
		$returnArr    = generateResponse('true', "My Booking Founded!", 200, array("My_Booking" => $wow));
	}
	echo $returnArr;
} catch (Exception $e) {
	// Handle exceptions and return an error response
	$returnArr = generateResponse('false', "An error occurred!", 500, array(
		"error_message" => $e->getMessage()
	), $e->getFile(),  $e->getLine());
	echo $returnArr;
}
