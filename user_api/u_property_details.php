<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang  = "en";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
	$lang = $_GET['lang'];
}
$pol = array();
$c = array();
$pro_id  =  isset($_GET['prop_id']) ? $_GET['prop_id'] : '';
if ($pro_id == '' ) {
	$returnArr = generateResponse('false', "Something Went Wrong!", 401);
} else if (validateIdAndDatabaseExistance($pro_id, 'tbl_property' ) === false) {
	$returnArr = generateResponse('false', "this property not exist!", 401);
} else {
	$fp = array();
	$f = array();
	$v = array();
	$vr = array();
	$po = array();
	$sel = $rstate->query("select * from tbl_property where    id=" . $pro_id . "")->fetch_assoc();
	$imageArray = explode(',', $sel['image']);

	// Loop through each image URL and push to $vr array
	foreach ($imageArray as $image) {
		$vr[] = array('image' => trim($image), 'is_panorama' => 0);
	}

	$get_extra = $rstate->query("select img,pano from tbl_extra where pid=" . $sel['id'] . "");
	while ($rk = $get_extra->fetch_assoc()) {
		array_push($vr, array('image' => $rk['img'], 'is_panorama' => intval($rk['pano'])));
	}
	$fp['id'] = $sel['id'];
	$fp['user_id'] = $sel['add_user_id'];
	$titleData = json_decode($sel['title'], true);
	$fp['title'] = $titleData;
	$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $sel['id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
	if ($checkrate != 0) {
		$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $sel['id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
		$fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 2, '.', '');
	} else {
		$fp['rate'] = null;
	}

	$fp['images'] = $vr;
	$prop = $rstate->query("select title from tbl_category where id=" . $sel['ptype'] . "");
	if ($prop->num_rows > 0) {
		$propData = $prop->fetch_assoc();
		$fp['property_type'] = json_decode($propData['title']);
	} else {
		$fp['property_type'] = null;
	}
	$fp['price'] = $sel['price'];
	$fp['buyorrent'] = $sel['pbuysell'];
	$fp['is_enquiry'] = $rstate->query("select * from tbl_enquiry where prop_id=" . $sel['id'] .  "")->num_rows;

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
	$fp['maps_url'] = $sel['google_maps_url'];
	$fp['video'] = $sel['video'];
	$fp['max_days'] = $sel['max_days'];
	$fp['min_days'] = $sel['min_days'];
	
	$periods = [
		"d" => ["ar" => "يومي", "en" => "daily"] ,
		 "m" => ["ar" => "شهري", "en" => "monthly"] 
	];
   $pol['period'] = $periods[$sel['period']][$lang];

	$fp['floor'] = json_decode($sel['floor'], true);
	$fp['guest_rules'] = json_decode($sel['guest_rules'], true);
	if (is_null($sel['compound_id'])) {
		$fp['compound_name'] = null;
	} else {
		$title = $rstate->query("SELECT name FROM tbl_compound WHERE id=" . $sel['compound_id']);

    if ($title->num_rows>0) {
        $tit = $title->fetch_assoc();
        $fp['compound_name'] = json_decode($tit['name'], true);
    } else {
        // Handle case when the query fails
        $fp['compound_name'] = null;
    }
	}
	$fp['description'] = json_decode($sel['description'], true);
	$fp['address'] = json_decode($sel['address'], true);
	$fp['city'] = json_decode($sel['city'], true);

	$fp['guest_count'] = $sel['plimit'];

	$fp['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where  property_id=" . $sel['id'] . "")->num_rows;


	if (is_null($sel['government'])) {
		$fp['government_name'] = null;
		
	} else {
		$gov = $rstate->query("
		SELECT name 
		FROM tbl_government 
		WHERE id=" . $sel['government'] . "
	");

    if ($gov->num_rows>0) {
        $tit = $gov->fetch_assoc();
        $fp['government_name'] = json_decode($tit['name'], true);
    } else {
        // Handle case when the query fails
        $fp['government_name'] = null;
    }
	}
	$fac = $rstate->query("select img, id,
	 JSON_UNQUOTE(title) as title 
	 from tbl_facility where id IN(" . $sel['facility'] . ")");

	while ($row = $fac->fetch_assoc()) {
		$row['title'] = json_decode($row['title'], true);
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
	$returnArr = json_encode(array("property_details" => $fp,   "facilities" => $f, "gallery" => $v,  "ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Property Details Founded!"));
}
echo $returnArr;
