<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang  = "en";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
	$lang = $_GET['lang'];
}
$pol = array();
$c = array();
$pro_id  = $data['prop_id'];
$uid  = $data['uid'];
if ($pro_id == '' or $uid == '') {
	$returnArr = array("ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Something Went Wrong!");
}  else if (validateIdAndDatabaseExistance($pro_id , 'tbl_property') === false){
	$returnArr = array(
		"ResponseCode" => "401",
		"Result" => "false",
		"ResponseMsg" => "this property not exist!"
	);
}
else if (checkTableStatus($pro_id , 'tbl_property') === false){
	$returnArr = array(
		"ResponseCode" => "401",
		"Result" => "false",
		"ResponseMsg" => "Not allow to show this property"
	);
}else {
	$fp = array();
	$f = array();
	$v = array();
	$vr = array();
	$po = array();
	$sel = $rstate->query("select * from tbl_property where id=" . $pro_id . "")->fetch_assoc();
	

	$vr[] = array('image' => $sel['image'], 'is_panorama' => "0");
	$get_extra = $rstate->query("select img,pano from tbl_extra where pid=" . $sel['id'] . "");
	while ($rk = $get_extra->fetch_assoc()) {
		array_push($vr, array('image' => $rk['img'], 'is_panorama' => $rk['pano']));
	}
	$fp['id'] = $sel['id'];
	$fp['user_id'] = $sel['add_user_id'];
	$titleData = json_decode($sel['title'], true);
	$fp['title'] = isset($titleData[$lang]) ? $titleData[$lang] : '';
	$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $sel['id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
	if ($checkrate != 0) {
		$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $sel['id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
		$fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 2, '.', '');
	} else {
		$fp['rate'] = '';
	}

	$fp['image'] = $vr;
	$fp['property_type'] = $sel['ptype'];
	$prop = $rstate->query("select title from tbl_category where id=" . $sel['ptype'] . "");
	if ($prop->num_rows > 0) {
		$propData = $prop->fetch_assoc();
		$fp['property_title'] = json_decode($propData['title'], true)[$lang] ?? '';
	} else {
		$fp['property_title'] = null;
	}
	$fp['price'] = $sel['price'];
	$fp['buyorrent'] = $sel['pbuysell'];
	$fp['is_enquiry'] = $rstate->query("select * from tbl_enquiry where prop_id=" . $sel['id'] . " and uid=" . $uid . "")->num_rows;

	$fp['beds'] = $sel['beds'];
	if ($sel['add_user_id'] == 0) {
		$fp['owner_image'] = 'images/property/owner.jpg';
		$fp['owner_name'] = 'Host';
	} else {
		$udata = $rstate->query("select pro_pic,name from tbl_user where id=" . $sel['add_user_id'] . "")->fetch_assoc();
		$fp['owner_image'] = (empty($udata['pro_pic'])) ? 'images/property/owner.jpg' : $udata['pro_pic'];
		$fp['owner_name'] = $udata['name'];
	}

	$fp['bathroom'] = $sel['bathroom'];
	$fp['sqrft'] = $sel['sqrft'];

	$fp['security_deposit'] = $sel['security_deposit'];
	$fp['google_maps_url'] = $sel['google_maps_url'];
	$fp['video'] = $sel['video'];
	$fp['max_days'] = $sel['max_days'];
	$fp['min_days'] = $sel['min_days'];

	$fp['floor'] = json_decode($sel['floor'], true)[$lang] ?? '';
	$fp['guest_rules'] = json_decode($sel['guest_rules'], true)[$lang] ?? '';
	$fp['compound_name'] = json_decode($sel['compound_name'], true)[$lang] ?? '';
	$fp['description'] = json_decode($sel['description'], true)[$lang] ?? '';
	$fp['address'] = json_decode($sel['address'], true)[$lang] ?? '';
	$fp['city'] = json_decode($sel['city'], true)[$lang] ?? '';

	$fp['plimit'] = $sel['plimit'];

	$fp['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where uid=" . $uid . " and property_id=" . $sel['id'] . "")->num_rows;


	$gov = $rstate->query("
		SELECT id, JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang')) as name 
		FROM tbl_government 
		WHERE id=" . $sel['government'] . "
	");
	if ($gov->num_rows > 0) {
		while ($row = $gov->fetch_assoc()) {

			$government[] = $row;
		}
	} else {
		$government = [];
	}

	$fac = $rstate->query("select img,
	 JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang')) as title 
	 from tbl_facility where id IN(" . $sel['facility'] . ")");

	while ($row = $fac->fetch_assoc()) {

		$f[] = $row;
	}

	$gal = $rstate->query("select img from tbl_gallery where pid=" . $sel['id'] . " limit 5");

	while ($rows = $gal->fetch_assoc()) {

		$v[] = $rows['img'];
	}
	$count_review =  $rstate->query("select * from tbl_book where prop_id=" . $pro_id . " and book_status='Completed' and is_rate=1 order by id desc")->num_rows;
	$bov = array();
	$kol = array();
	$rev = $rstate->query("select * from tbl_book where prop_id=" . $pro_id . " and book_status='Completed' and is_rate=1 order by id desc limit 3");
	while ($k = $rev->fetch_assoc()) {
		$udata = $rstate->query("select * from tbl_user where id=" . $k['uid'] . "")->fetch_assoc();
		$bov['user_img'] = $udata['pro_pic'];
		$bov['user_title'] = $udata['name'];
		$bov['user_rate'] = $k['total_rate'];
		$bov['user_desc'] = $k['rate_text'];
		$kol[] = $bov;
	}
	$returnArr = array("propetydetails" => $fp, "government" => $government,  "facility" => $f, "gallery" => $v, "reviewlist" => $kol, "total_review" => $count_review, "ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Property Details Founded!");
}
echo json_encode($returnArr);
