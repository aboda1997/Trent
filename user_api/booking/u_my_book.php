<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__) , 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__) , 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
$pol = array();
$c = array();

$status  =  isset($_GET['status']) ? $_GET['status'] : '';
$uid  =  isset($_GET['uid']) ? $_GET['uid'] : '';
$lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
if ($uid == '' or $status == '') {
	$returnArr = generateResponse('false', "Something Went Wrong!", 400);
} else {
	$fp = array();
	$wow = array();
	if ($status == 'active') {
		$bd = $rstate->query("select * from tbl_book where uid=" . $uid . " and book_status!='Completed' and book_status!='Cancelled' order by id desc");
	} else {
		$bd = $rstate->query("select * from tbl_book where uid=" . $uid . " and (book_status='Completed' or book_status='Cancelled') order by id desc");
	}
	while ($row = $bd->fetch_assoc()) {
		$fp['book_id'] = $row['id'];
		$fp['prop_id'] = $row['prop_id'];
		$fp['prop_img'] = $row['prop_img'];
		$fp['prop_title'] = json_decode($row['prop_title'], true)[$lang];

		$fp['p_method_id'] = $row['p_method_id'];
		$fp['prop_price'] = $row['prop_price'];
		$fp['total_day'] = $row['total_day'];
		$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $row['prop_id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
		if ($checkrate != 0) {
			$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $row['prop_id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
			$fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 0, '.', '');
		} else {
			$fp['rate'] = number_format(0, 1, '.', '');
		}
		$fp['book_status'] = $row['book_status'];


		$wow[] = $fp;
	}
	$returnArr    = generateResponse('true', "My Booking Founded!", 200, array("My_Booking" => $wow));

}
echo $returnArr;
