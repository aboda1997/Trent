<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-Type: application/json');
try{
$data = json_decode(file_get_contents('php://input'), true);

$pol = array();
$c = array();
$pro_id  =  isset($_GET['prop_id']) ? $_GET['prop_id'] : '';
$uid  =  isset($_GET['uid']) ? $_GET['uid'] : null;
if ($pro_id == ''  ) {
	$returnArr = generateResponse('false', "Something Went Wrong!", 400);
} else if (validateIdAndDatabaseExistance($pro_id, 'tbl_property') === false) {
	$returnArr = generateResponse('false', "this property not exist!", 400);
}else if (checkTableStatus($pro_id, 'tbl_property') == false) {
	$returnArr = generateResponse('false', "This Property already deleted", 410);
}else {
	$fp = array();
	$f = array();
	$v = array();
	$vr = array();
	$po = array();
	$sel = $rstate->query("select * from tbl_property where status = 1 and  id=" . $pro_id .  "")->fetch_assoc();
	$imageArray = explode(',', $sel['image']);

	// Loop through each image URL and push to $vr array
	foreach ($imageArray as $image) {
		$vr[] = array('img' => trim($image), 'is_panorama' => 0);
	}

	$get_extra = $rstate->query("select img,pano from tbl_extra where pid=" . $sel['id'] . "");
	while ($rk = $get_extra->fetch_assoc()) {
		array_push($vr, array('img' => $rk['img'], 'is_panorama' => intval($rk['pano'])));
	}
	$fp['id'] = $sel['id'];
	$fp['owner_id'] = $sel['add_user_id'];
	$titleData = json_decode($sel['title'], true);
	$fp['title'] = $titleData;
	$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $sel['id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
	if ($checkrate != 0) {
		$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $sel['id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
		$fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 1, '.', '');
	} else {
		$fp['rate'] = number_format(0, 1, '.', '');
	}

	$fp['image_list'] = $vr;
	if (is_null($sel['ptype'])) {
		$fp['category'] = null;
	} else {
		$title = $rstate->query("SELECT id, title FROM tbl_category WHERE id=" . $sel['ptype']);

		if ($title->num_rows > 0) {
			while ($tit = $title->fetch_assoc()) {
				// Combine the id and name into a single associative array
				$fp['category'] = [
					'id' => $tit['id'],
					'type' => json_decode($tit['title'], true)
				];
			}
		} else {
			// Handle case when the query fails
			$fp['category'] = null;
		}
	}


	$fp['price'] = $sel['price'];
	$fp['buy_or_rent'] = $sel['pbuysell'];
	$fp['is_enquiry'] = $rstate->query("select * from tbl_enquiry where prop_id=" . $sel['id'] .  "")->num_rows;

	$fp['beds_count'] = $sel['beds'];
	if ($sel['add_user_id'] == 0) {
		$fp['owner'] = [
			'img' => 'images/property/owner.jpg',
			'name' => 'Host'
		];
	} else {
		$udata = $rstate->query("select pro_pic,name from tbl_user where id=" . $sel['add_user_id'] . "")->fetch_assoc();
		$fp['owner'] = [
			'img' => (empty($udata['pro_pic'])) ? 'images/property/owner.jpg' : $udata['pro_pic'],
			'name' =>  $udata['name']
		];
	}

	$fp['bathrooms_count'] = $sel['bathroom'];
	$fp['sqrft'] = $sel['sqrft'];

	$fp['security_deposit'] = $sel['security_deposit'];
	$fp['maps_url'] = $sel['map_url'];
	
	$fp['latitude'] = $sel['latitude'];
	$fp['longitude'] = $sel['longitude'];
	$fp['video'] = $sel['video'];
	$fp['max_days'] = $sel['max_days'];
	$fp['min_days'] = $sel['min_days'];


	$fp['floor'] = json_decode($sel['floor'], true);
	$fp['guest_rules'] = json_decode($sel['guest_rules'], true);


	$fp['description'] = json_decode($sel['description'], true);
	$fp['address'] = json_decode($sel['address'], true);
	$fp['city'] = json_decode($sel['city'], true);

	$fp['guest_count'] = $sel['plimit'];

	if ($uid){
		$fp['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where  uid= $uid and property_id=" . $sel['id'] . "")->num_rows;
	}else{
		$pol['IS_FAVOURITE'] = 0 ;
	}  
	$periods = [
		"d" => ["ar" => "يومي", "en" => "daily"],
		"m" => ["ar" => "شهري", "en" => "monthly"]
	];

	$fp['period'] = [
		'id' => $sel['period'],
		'name' => $periods[$sel['period']]
	];

	$fp['compound'] = json_decode($sel['compound_name'], true);


	if (is_null($sel['government'])) {
		$fp['government'] = null;
	} else {
		$gov = $rstate->query("
        SELECT id, name 
        FROM tbl_government 
        WHERE id=" . intval($sel['government']) . "
    ");

		if ($gov->num_rows > 0) {
			$fp['government'] = [];

			while ($tit = $gov->fetch_assoc()) {
				// Combine the id and name into a single associative array
				$fp['government'] = [
					'id' => $tit['id'],
					'name' => json_decode($tit['name'], true)
				];
			}
		} else {
			// Handle case when the query fails
			$fp['government'] = null;
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
	$returnArr    = generateResponse('true', "Property Details Founded!", 200, array("property_details" => $fp,   "facility_list" => $f, "gallery" => $v  ));

}
echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile() ,  $e->getLine());
    echo $returnArr;
}
?>
