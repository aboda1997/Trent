<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang  = "en";
if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
	$lang = $_GET['lang'];
}


$pol = array();
$c = array();
$uid = isset($data['uid']) ? intval($data['uid']) : null;
$government_id = isset($data['government_id']) ? intval($data['government_id']) : null;
$facilities_ids = isset($data['facilities_ids']) ? $data['facilities_ids'] : null;
$prop_type_id = isset($data['prop_type_id']) ? intval($data['prop_type_id']) : null;
$min_price = isset($data['min_price']) ? floatval($data['min_price']) : null;
$max_price = isset($data['max_price']) ? floatval($data['max_price']) : null;
$beds = isset($data['beds']) ? intval($data['beds']) : null;
$bathrooms = isset($data['bathrooms']) ? intval($data['bathrooms']) : null;

// Start the base query
$query = "SELECT * FROM tbl_property WHERE is_deleted = 0  and status = 0";

// Apply filters dynamically
if ($uid !== null) {
    $query .= " AND add_user_id = " . $uid;
}

if ($government_id !== null) {
    $query .= " AND government = " . $government_id;
}

if ($facilities_ids !== null) {
    $facilitiesArray = explode(',', $facilities_ids);
    $facilityConditions = [];
    foreach ($facilitiesArray as $facility) {
        $facilityConditions[] = "FIND_IN_SET(" . intval($facility) . ", facility)";
    }
    $query .= " AND (" . implode(' OR ', $facilityConditions) . ")";
}

if ($prop_type_id !== null) {
    $query .= " AND ptype = " . $prop_type_id;
}

if ($min_price !== null) {
    $query .= " AND price >= " . $min_price;
}

if ($max_price !== null) {
    $query .= " AND price <= " . $max_price;
}

if ($beds !== null) {
    $query .= " AND beds = " . $beds;
}

if ($bathrooms !== null) {
    $query .= " AND bathroom = " . $bathrooms;
}

// Execute the query
$sel = $rstate->query($query);
while ($row = $sel->fetch_assoc()) {
	$vr = array();
	$f = array();
	$imageArray = explode(',', $row['image']);

	// Loop through each image URL and push to $vr array
	foreach ($imageArray as $image) {
		$vr[] = array('image' => trim($image), 'is_panorama' => 0);
	}

	$get_extra = $rstate->query("select img,pano from tbl_extra where pid=" . $row['id'] . "");
	while ($rk = $get_extra->fetch_assoc()) {
		array_push($vr, array('image' => $rk['img'], 'is_panorama' => $rk['pano']));
	}
	$pol['id'] = $row['id'];

	$pol['user_id'] = $row['add_user_id'];
	$titleData = json_decode($row['title'], true);
	$pol['title'] = $titleData;

	$pol['property_type_id'] = $row['ptype'];
	$prop = $rstate->query("select title from tbl_category where id=" . $row['ptype'] . "");
	if ($prop->num_rows > 0) {
		$propData = $prop->fetch_assoc();
		$pol['property_title'] = json_decode($propData['title'], true);
	} else {
		$pol['property_title'] = null;
	}

	$pol['image'] = $vr;
	$pol['price'] = $row['price'];
	$pol['beds'] = $row['beds'];
	$pol['plimit'] = $row['plimit'];
	$pol['bathroom'] = $row['bathroom'];
	$pol['sqrft'] = $row['sqrft'];
	$pol['is_sell'] = $row['is_sell'];

	$fac = $rstate->query("select img, id,
	 JSON_UNQUOTE(title) as title 
	 from tbl_facility where id IN(" . $row['facility'] . ")");

	while ($ro = $fac->fetch_assoc()) {
		$ro['title'] = json_decode($ro['title'], true);

		$f[] = $ro;
	}
	$pol['facility_select'] = $f;

	$pol['status'] = $row['status'];
	$pol['buyorrent'] = $row['pbuysell'];
	$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $row['id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
	if ($checkrate != 0) {
		$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $row['id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
		$pol['rate'] = number_format((float)$rdata_rest['rate_rest'], 0, '.', '');
	} else {
		$pol['rate'] = null;
	}

	$pol['security_deposit'] = $row['security_deposit'];
	$pol['google_maps_url'] = $row['google_maps_url'];
	$pol['video'] = $row['video'];
	$pol['max_days'] = $row['max_days'];
	$pol['min_days'] = $row['min_days'];

	$pol['floor'] = json_decode($row['floor'], true);
	$pol['guest_rules'] = json_decode($row['guest_rules'], true);
	$pol['compound_name'] = json_decode($row['compound_name'], true);
	$pol['description'] = json_decode($row['description'], true);
	$pol['address'] = json_decode($row['address'], true);
	$pol['city'] = json_decode($row['city'], true);

	$c[] = $pol;
}
if (empty($c)) {
	$returnArr = json_encode(array("proplist" => $c, "ResponseCode" => "200", "Result" => "false", "ResponseMsg" => "Property List Not Founded!"));
} else {
	$returnArr = json_encode(array("proplist" => $c, "ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Property  List Founded!"));
}

echo $returnArr;
