<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);

$status = 0;
$facility = $data['facility'];
$ptype = $data['ptype'];
$beds = $data['beds'];
$bathroom = $data['bathroom'];
$sqft = $data['sqft'];
$listing_date = date("Y-m-d H:i:s");
$price = $data['price'];
$plimit = $data['plimit'];
$pbuysell = $data['pbuysell'];
$user_id = $data['uid'];
$government = $data['government'];
$security_deposit = $data['security_deposit'];
$max_days = $data['max_days'];
$min_days = $data['min_days'];
$google_maps_url = $data['google_maps_url'];
$video = $data['video'];

$title_en = $rstate->real_escape_string($data["title_en"]);
$address_en = $rstate->real_escape_string($data["address_en"]);
$description_en = $rstate->real_escape_string($data["description_en"]);
$ccount_en = $rstate->real_escape_string($data["city_en"]);
$compound_name_en = $rstate->real_escape_string($data["compound_name_en"]);
$floor_en = $rstate->real_escape_string($data["floor_en"]);
$guest_rules_en = $rstate->real_escape_string($data["guest_rules_en"]);

$title_ar = $rstate->real_escape_string($data["title_ar"]);
$address_ar = $rstate->real_escape_string($data["address_ar"]);
$description_ar = $rstate->real_escape_string($data["description_ar"]);
$ccount_ar = $rstate->real_escape_string($data["city_ar"]);

$compound_name_ar = $rstate->real_escape_string($data["compound_name_ar"]);
$floor_ar = $rstate->real_escape_string($data["floor_ar"]);
$guest_rules_ar = $rstate->real_escape_string($data["guest_rules_ar"]);


$compound_name_json = json_encode([
	"en" => $compound_name_en,
	"ar" => $compound_name_ar
], JSON_UNESCAPED_UNICODE);


$floor_json = json_encode([
	"en" => $floor_en,
	"ar" => $floor_ar
], JSON_UNESCAPED_UNICODE);


$guest_rules_json = json_encode([
	"en" => $guest_rules_en,
	"ar" => $guest_rules_ar
], JSON_UNESCAPED_UNICODE);

$ccount_json = json_encode([
	"en" => $ccount_en,
	"ar" => $ccount_ar
], JSON_UNESCAPED_UNICODE);
$description_json = json_encode([
	"en" => $description_en,
	"ar" => $description_ar
], JSON_UNESCAPED_UNICODE);
$address_json = json_encode([
	"en" => $address_en,
	"ar" => $address_ar
], JSON_UNESCAPED_UNICODE);
$title_json = json_encode([
	"en" => $title_en,
	"ar" => $title_ar
], JSON_UNESCAPED_UNICODE);


if ($user_id == '' or $government =='' or $video == '' or $security_deposit == ''  or $google_maps_url == '' or  $floor_ar == '' or $floor_en == '' or $compound_name_ar == '' or $compound_name_en == '' or $guest_rules_ar  == '' or $guest_rules_en == ''  or $pbuysell == '' or  $plimit == '' or $status == '' or $title_ar == '' or $title_en == '' or $address_ar == '' or $address_en == '' or $description_ar == '' or $description_en == '' or $ccount_ar == '' or $ccount_en == '' or $facility == '' or $ptype == '' or $beds == '' or $bathroom == '' or $sqft == '' or $listing_date == '' or $price == '') {
	$returnArr = array(
		"ResponseCode" => "401",
		"Result" => "false",
		"ResponseMsg" => "Something Went Wrong!"
	);
}else if (validateFacilityIds($facility) === false) {
	$returnArr = array(
		"ResponseCode" => "401",
		"Result" => "false",
		"ResponseMsg" => "Facilities Ids must be valid!"
	);}else if (validateIdAndDatabaseExistance($ptype , 'tbl_category') === false){
		$returnArr = array(
			"ResponseCode" => "401",
			"Result" => "false",
			"ResponseMsg" => "ptype Id must be valid!"
		);
	}  else if (validateIdAndDatabaseExistance($government , 'tbl_government') === false){
		$returnArr = array(
			"ResponseCode" => "401",
			"Result" => "false",
			"ResponseMsg" => "government Id must be valid!"
		);
	}  

else {

	$img = $data['img'];
	$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$data = base64_decode($img);
	$path = 'images/property/' . uniqid() . '.png';
	$fname = dirname(dirname(__FILE__)) . '/' . $path;

	file_put_contents($fname, $data);

	$table = "tbl_property";
	$field_values = ["image", "security_deposit" , "government"  , "google_maps_url", "video" ,"guest_rules", "compound_name" , "floor" , "status", "title", "price", "address", "facility", "description", "beds", "bathroom", "sqrft",  "ptype",  "city", "listing_date", "add_user_id", "pbuysell",  "plimit", "max_days" , "min_days"];
	$data_values = ["$path", "$security_deposit","$government" , "$google_maps_url", "$video", "$guest_rules_json" , "$compound_name_json", "$floor_json" ,"$status", "$title_json", "$price", "$address_json", "$facility", "$description_json", "$beds", "$bathroom", "$sqft",  "$ptype", "$ccount_json", "$listing_date", "$user_id", "$pbuysell", "$plimit" , "$max_days" , "$min_days"];

	$h = new Estate();
	$check = $h->restateinsertdata_Api($field_values, $data_values, $table);
	$returnArr    = array(
		"ResponseCode" => "200",
		"Result" => "true",
		"ResponseMsg" => "Property Add Successfully"
	);
}

echo json_encode($returnArr);
