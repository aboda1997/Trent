<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
try{
$pol = array();
$c = array();
$lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : null;
$only_featured = isset($_GET['only_featured']) && strtolower($_GET['only_featured']) === 'true' ? true : false;
$only_favorites = isset($_GET['only_favorites']) && strtolower($_GET['only_favorites']) === 'true' ? true : false;
$period = isset($_GET['period']) ? $_GET['period'] : null;
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;
$government_id = isset($_GET['government_id']) ? intval($_GET['government_id']) : null;
$compound_name = isset($_GET['compound_name']) ? $_GET['compound_name'] : null;
$facilities = isset($_GET['facilities']) ? $_GET['facilities'] : null;
$beds_count = isset($_GET['beds_count']) ? intval($_GET['beds_count']) : null;
$bathrooms_count = isset($_GET['bathrooms_count']) ? intval($_GET['bathrooms_count']) : null;
$rate = isset($_GET['rate']) ? intval($_GET['rate']) : null;
$guest_count = isset($_GET['guest_count']) ? intval($_GET['guest_count']) : null;
$facilitiesArray = json_decode($facilities, true);

// Get pagination parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
$itemsPerPage = isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 10; // Items per page

// Calculate offset
$offset = ($page - 1) * $itemsPerPage;

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
    COUNT(b.id) AS rate_count
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
$query .= " WHERE p.status = 1 and (p.is_approved  = 1";


// Apply filters dynamically
if ($uid !== null) {

	if(!$only_favorites && !isset($rate) && !$only_featured && !$category_id && !$period   && !$min_price && !$max_price 
	&& !$government_id && !$compound_name 	&& !$facilities && !$beds_count && !$bathrooms_count && !$guest_count 
	){
		$query .= "  or  p.is_approved  = 0) ";

		$query .= " AND p.add_user_id = " . $uid;
	}else{
		$query .= " ) ";

	}
	if ($only_favorites) {
		$query .= " AND f.uid = " . $uid;
	}
	if (isset($rate) && $rate > 0) {
		$query .= "  AND b.uid = " . $uid;
	
	}
	 
}else{
	$query .= " ) ";

}

if ($government_id !== null) {
	$query .= " AND p.government = " . $government_id;
}
if ($compound_name !== null) {
    $compound_name = $rstate->real_escape_string($compound_name);
    $query .= " AND (
        JSON_UNQUOTE(JSON_EXTRACT(p.compound_name, '$.en'))   LIKE '%$compound_name%' 
        OR JSON_UNQUOTE(JSON_EXTRACT(p.compound_name, '$.ar'))    LIKE '%$compound_name%'
		and JSON_UNQUOTE(JSON_EXTRACT(compound_name, '$.en')) IS NOT NULL
		and JSON_UNQUOTE(JSON_EXTRACT(compound_name, '$.ar')) IS NOT NULL

    )";
}
if ($facilities!== null) {
	foreach ($facilitiesArray as $facility) {
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
	$query .= ' AND p.period = "' . $period . '"';
}

if ($guest_count !== null) {
	$query .= " AND p.plimit >= " . intval($guest_count);
}
if ($only_featured) {
	$query .= " AND p.is_featured = 1 " ;
}
// Add minimum rate condition
if (isset($rate) && $rate > 0) {
	$query .= "  and book_status='Completed' and total_rate !=0 ";
	$query .= " GROUP BY p.id HAVING rate_count > 1 AND total_avg_rate >= " . intval($rate);
}else{
	$query .= " GROUP BY p.id";
}

$sel_length  = $rstate->query($query)->num_rows;
$query .= " LIMIT " . $itemsPerPage . " OFFSET " . $offset;
// Execute the query

$sel = $rstate->query($query);
while ($row = $sel->fetch_assoc()) {
	$vr = array();
	$f = array();
	$imageArray = explode(',', $row['image']);

	// Loop through each image URL and push to $vr array
	foreach ($imageArray as $image) {
		// 'is_panorama' => 0
		$vr[] = array('img' => trim($image));
	}

	$get_extra = $rstate->query("select img,pano from tbl_extra where pid=" . $row['id'] . "");
	while ($rk = $get_extra->fetch_assoc()) {
		//'is_panorama' => intval($rk['pano'])
		array_push($vr, array('img' => $rk['img'], ));
	}
	$pol['id'] = $row['id'];

	//$pol['user_id'] = $row['add_user_id'];
	$titleData = json_decode($row['title'], true)[$lang];
	$pol['title'] = $titleData;

	$prop = $rstate->query("select title from tbl_category where id=" . $row['ptype'] . "");
	if ($prop->num_rows > 0) {
		$propData = $prop->fetch_assoc();
		$pol['category_type'] = json_decode($propData['title'], true)[$lang];
	} else {
		$pol['category_type'] = null;
	}

	$pol['image_list'] = $vr;
	$pol['price'] = $row['price'];
	$pol['beds_count'] = $row['beds'];
	$pol['guest_count'] = $row['plimit'];
	$pol['bathrooms_count'] = $row['bathroom'];
	$pol['sqrft'] = $row['sqrft'];
	$periods = [
		 "d" => ["ar" => "يومي", "en" => "daily"] ,
		  "m" => ["ar" => "شهري", "en" => "monthly"] 
	 ];
	$pol['period_name'] = $periods[$row['period']][$lang];

	$pol['compound_name'] = json_decode($row['compound_name'], true)[$lang];

	
	if (is_null($row['government'])) {
		$pol['government_name'] = null;
		
	} else {
		$gov = $rstate->query("
		SELECT name 
		FROM tbl_government 
		WHERE id=" . $row['government'] . "
	");

    if ($gov->num_rows>0) {
        $tit = $gov->fetch_assoc();
        $pol['government_name'] = json_decode($tit['name'], true)[$lang];
    } else {
        // Handle case when the query fails
        $pol['government_name'] = null;
    }
	}
	$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $row['id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
	if ($checkrate != 0) {
		$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $row['id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
		$pol['rate'] = number_format((float)$rdata_rest['rate_rest'], 0, '.', '');
	} else {
		$pol['rate'] = null;
	}

	$pol['latitude'] = $row['latitude'];
	$pol['longitude'] = $row['longitude'];
	//$pol['address'] = json_decode($row['address'], true);
	$pol['city_name'] = json_decode($row['city'], true)[$lang];
    if ($uid){
		$pol['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where uid= $uid and property_id=" . $row['id'] . "")->num_rows; 
	}else{
		$pol['IS_FAVOURITE'] = 0 ;
	}  
	$c[] = $pol;
}
if (empty($c)) {
	$returnArr    = generateResponse('true', "Home Data Not Founded", 200 ,array("property_list" => $c,"length" => 0,  ));

} else {
	$returnArr    = generateResponse('true', "Home Data Get Successfully!", 200,array("property_list" => $c,"length" => $sel_length,  ));

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
