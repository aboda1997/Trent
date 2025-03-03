<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$pol = array();
$c = array();

$lang = isset($data['lang']) ? $data['lang'] : 'en';
$category_id = isset($data['category_id']) ? intval($data['category_id']) : null;
$uid = isset($data['uid']) ? intval($data['uid']) : null;
$only_featured = isset($data['only_featured']) ? intval($data['only_featured']) : 0;
$only_favorites = isset($data['only_favorites']) ? intval($data['only_favorites']) : 0;
$period = isset($data['period']) ? $data['period'] : null;
$min_price = isset($data['min_price']) ? floatval($data['min_price']) : null;
$max_price = isset($data['max_price']) ? floatval($data['max_price']) : null;
$government_id = isset($data['government_id']) ? intval($data['government_id']) : null;
$facilities = isset($data['facilities']) ? $data['facilities'] : null;
$beds_count = isset($data['beds_count']) ? intval($data['beds_count']) : null;
$bathrooms_count = isset($data['bathrooms_count']) ? intval($data['bathrooms_count']) : null;
$rate = isset($data['rate']) ? intval($data['rate']) : null;
$guest_count = isset($data['guest_count']) ? intval($data['guest_count']) : null;

// Start the base query
$query = "
    SELECT 
	p.*
    FROM tbl_property p
";

if (isset($rate) && $rate > 0) {
	$query = "
    SELECT 
	p.*, 
    IFNULL(SUM(b.total_rate) / COUNT(b.id), 0) AS total_avg_rate, 
    b.book_status, 
    b.total_rate, 
    COUNT(b.id) AS rate_count,
    FROM tbl_property p
";
}


if ($only_favorites) {
	$query = " SELECT
	 p.* , f.property_id
    FROM tbl_property p
";
}
if (isset($rate) && $rate > 0 && $only_favorites) {
	$query = "
    SELECT 
	p.*, 
    IFNULL(SUM(b.total_rate) / COUNT(b.id), 0) AS total_avg_rate, 
    b.book_status, 
    b.total_rate, 
    COUNT(b.id) AS rate_count,f.property_id
    FROM tbl_property p
";
}
// Add favorites condition
if ($only_favorites) {
	$query .= " inner JOIN tbl_fav f ON p.id = f.property_id ";
}
if (isset($rate) && $rate > 0) {
	$query .= " inner JOIN tbl_book b ON p.id = b.prop_id
	";
}

// Filter active properties
$query .= " WHERE p.status = 0 ";


// Apply filters dynamically
if ($uid !== null) {
	$query .= " AND p.add_user_id = " . $uid;
}

if ($government_id !== null) {
	$query .= " AND p.government = " . $government_id;
}

if ($facilities !== null) {
	foreach ($facilities as $facility) {
		$facilityConditions[] = "FIND_IN_SET(" . intval($facility) . ", p.facility)";
	}
	$query .= " AND (" . implode(' OR ', $facilityConditions) . ")";
}

if ($category_id !== null) {
	$query .= " AND p.ptype = " . $category_id;
}

if ($min_price !== null) {
	$query .= " AND p.price >= " . $min_price;
}

if ($max_price !== null) {
	$query .= " AND p.price <= " . $max_price;
}

if ($beds_count !== null) {
	$query .= " AND p.beds = " . $beds_count;
}

if ($bathrooms_count !== null) {
	$query .= " AND p.bathroom = " . $bathrooms_count;
}
if ($period !== null) {
	$query .= " AND p.period = " . $period;
}

if ($guest_count !== null) {
	$query .= " AND p.plimit >= " . intval($guest_count);
}
if ($only_featured) {
	$query .= " AND p.is_featured = " . intval($only_featured);
}
// Add minimum rate condition
if (isset($rate) && $rate > 0) {
	$query .= "  and book_status='Completed' and total_rate !=0 ";
	$query .= " GROUP BY p.id HAVING rate_count > 1 AND total_avg_rate >= " . intval($rate);
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
		array_push($vr, array('image' => $rk['img'], 'is_panorama' => intval($rk['pano'])));
	}
	$pol['id'] = $row['id'];

	//$pol['user_id'] = $row['add_user_id'];
	$titleData = json_decode($row['title'], true);
	$pol['title'] = $titleData;

	$pol['property_type'] = $row['ptype'];
	//$prop = $rstate->query("select title from tbl_category where id=" . $row['ptype'] . "");
	//if ($prop->num_rows > 0) {
	//	$propData = $prop->fetch_assoc();
	//	$pol['property_title'] = json_decode($propData['title'], true);
	//} else {
	//	$pol['property_title'] = null;
	//}
	//$pol['security_deposit'] = $row['security_deposit'];

	$pol['image'] = $vr;
	$pol['price'] = $row['price'];
	$pol['beds'] = $row['beds'];
	$pol['plimit'] = $row['plimit'];
	$pol['bathroom'] = $row['bathroom'];
	$pol['sqrft'] = $row['sqrft'];
	//$pol['is_sell'] = $row['is_sell'];
	$pol['period'] = $row['period'];
	$title  =  $rstate->query("SELECT name  FROM tbl_compound where id="   . $row['compound_id'] . "")->fetch_assoc();
	$pol['compound_name'] = json_decode($title["name"], true);
	//$fac = $rstate->query("select img, id,
	// JSON_UNQUOTE(title) as title 
	// from tbl_facility where id IN(" . $row['facility'] . ")");

	//while ($ro = $fac->fetch_assoc()) {
	//$ro['title'] = json_decode($ro['title'], true);
	//
	//$f[] = $ro;
	//}
	//$pol['facility_select'] = $f;

	//$pol['status'] = $row['status'];
	//$pol['buyorrent'] = $row['pbuysell'];
	$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $row['id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
	if ($checkrate != 0) {
		$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $row['id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
		$pol['rate'] = number_format((float)$rdata_rest['rate_rest'], 0, '.', '');
	} else {
		$pol['rate'] = null;
	}

	//$pol['security_deposit'] = $row['security_deposit'];
	$pol['google_maps_url'] = $row['google_maps_url'];
	//$pol['video'] = $row['video'];
	//$pol['max_days'] = $row['max_days'];
	//$pol['min_days'] = $row['min_days'];

	//$pol['floor'] = json_decode($row['floor'], true);
	//$pol['guest_rules'] = json_decode($row['guest_rules'], true);
	//$pol['description'] = json_decode($row['description'], true);
	//$pol['address'] = json_decode($row['address'], true);
	$pol['city'] = json_decode($row['city'], true);
	$pol['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where  property_id=" . $row['id'] . "")->num_rows;

	$c[] = $pol;
}
if (empty($c)) {
	$returnArr = json_encode(array("Properties" => $c, "ResponseCode" => "200", "Result" => "false", "ResponseMsg" => "Home Data Not Founded"));
} else {
	$returnArr = json_encode(array("Properties" => $c, "ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Home Data Get Successfully!"));
}

echo $returnArr;
