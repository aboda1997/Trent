<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');

$status = 0;
$facility = $_POST['facility'];
$ptype = $_POST['ptype'];
$beds = $_POST['beds'];
$bathroom = $_POST['bathroom'];
$sqft = $_POST['sqft'];

$listing_date = date("Y-m-d H:i:s");
$price = $_POST['price'];
$user_id = $_POST['uid'];
$prop_id = $_POST['prop_id'];
$plimit = $_POST['plimit'];
$pbuysell = 1;
$government = $_POST['government'];
$security_deposit = $_POST['security_deposit'];
$max_days = $_POST['max_days'];
$min_days = $_POST['min_days'];

$google_maps_url = $_POST['google_maps_url'];
$title_en = $rstate->real_escape_string($_POST["title_en"]);
$address_en = $rstate->real_escape_string($_POST["address_en"]);
$description_en = $rstate->real_escape_string($_POST["description_en"]);
$ccount_en = $rstate->real_escape_string($_POST["city_en"]);
$compound_name_en = $rstate->real_escape_string($_POST["compound_name_en"]);
$floor_en = $rstate->real_escape_string($_POST["floor_en"]);
$guest_rules_en = $rstate->real_escape_string($_POST["guest_rules_en"]);

$title_ar = $rstate->real_escape_string($_POST["title_ar"]);
$address_ar = $rstate->real_escape_string($_POST["address_ar"]);
$description_ar = $rstate->real_escape_string($_POST["description_ar"]);
$ccount_ar = $rstate->real_escape_string($_POST["city_ar"]);

$compound_name_ar = $rstate->real_escape_string($_POST["compound_name_ar"]);
$floor_ar = $rstate->real_escape_string($_POST["floor_ar"]);
$guest_rules_ar = $rstate->real_escape_string($_POST["guest_rules_ar"]);


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

// Validate the string


if ($user_id == '' or $government == ''  or $security_deposit == ''  or $google_maps_url == '' or  $floor_ar == '' or $floor_en == '' or $compound_name_ar == '' or $compound_name_en == '' or $guest_rules_ar  == '' or $guest_rules_en == ''  or $pbuysell == '' or  $plimit == '' or $status == '' or $title_ar == '' or $title_en == '' or $address_ar == '' or $address_en == '' or $description_ar == '' or $description_en == '' or $ccount_ar == '' or $ccount_en == '' or $facility == '' or $ptype == '' or $beds == '' or $bathroom == '' or $sqft == '' or $listing_date == '' or $price == '') {
	
	$returnArr    = generateResponse('false', "Something Went Wrong!", 401);

} else if (validateFacilityIds($facility) === 0) {
	$returnArr    = generateResponse('false', "Facilities Ids must be valid!", 401);

} else if (validateIdAndDatabaseExistance($ptype, 'tbl_category') === false) {
	$returnArr    = generateResponse('false', "ptype Id must be valid!", 401);

} else if (validateIdAndDatabaseExistance($government, 'tbl_government') === false) {
	$returnArr    = generateResponse('false', "government Id must be valid!", 401);

} else {

	$check_owner = $rstate->query("select * from tbl_property where id=" . $prop_id . " and add_user_id=" . $user_id . "")->num_rows;
	if ($check_owner != 0) {
		$field = [
			"pbuysell" => $pbuysell,
			"security_deposit" => $security_deposit,
			"government" => $government,
			"google_maps_url" => $google_maps_url,
			"guest_rules" => $guest_rules_json,
			"compound_name" => $compound_name_json,
			"floor" => $floor_json,
			"max_days" => "$max_days",
			"min_days" => "$min_days",
			"plimit" => $plimit,
			"status" => $status,
			"title" => $title_json,
			"price" => $price,
			"address" => $address_json,
			"facility" => $facility,
			"description" => $description_json,
			"beds" => $beds,
			"bathroom" => $bathroom,
			"sqrft" => $sqft,
			"ptype" => $ptype,
			"city" => $ccount_json,
			"listing_date" => $listing_date
		];

		// Allowed file types for images and videos
		$allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
		$allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mov'];

		// Initialize arrays for image and video URLs
		$imageUrls = [];
		$videoUrls = [];

		// Directories for storing images and videos
		$uploadDirImages = dirname(dirname(__FILE__)) . "/images/property/";
		$uploadDirVideos = dirname(dirname(__FILE__)) . "/videos/property/";


		// Handle image upload
		if (isset($_FILES['images'])) {
			// Check if it's multiple images or a single image
			if (is_array($_FILES['images']['name'])) {
				// Multiple images
				foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
					if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
						$imageName = time() . '_' . $_FILES['images']['name'][$key];
						$destination = $uploadDirImages . $imageName;

						// Validate image type
						if (in_array($_FILES['images']['type'][$key], $allowedImageTypes)) {
							if (move_uploaded_file($tmpName, $destination)) {
								$imageUrls[] = 'images/property/' . $imageName;
							} else {
								// Handle error if file couldn't be moved
								$returnArr =  generateResponse("false", "Failed to upload image: " . $_FILES['images']['name'][$key], 500);
							}
						} else {
							// Handle invalid image type
							$returnArr =  generateResponse("false", "Invalid image type: " . $_FILES['images']['name'][$key], 400);
						}
					} else {
						// Handle error during file upload
						$returnArr =  generateResponse("false", "Error uploading image: " . $_FILES['images']['name'][$key], 400);
					}
				}
			} else {
				// Single image
				if ($_FILES['images']['error'] === UPLOAD_ERR_OK) {
					$imageName = time() . '_' . $_FILES['images']['name'];
					$destination = $uploadDirImages . $imageName;

					// Validate image type
					if (in_array($_FILES['images']['type'], $allowedImageTypes)) {
						if (move_uploaded_file($_FILES['images']['tmp_name'], $destination)) {
							$imageUrls[] = 'images/property/' . $imageName;
						} else {
							// Handle error if file couldn't be moved
							$returnArr =  generateResponse("false", "Failed to upload image: " . $_FILES['images']['name'], 500);
						}
					} else {
						// Handle invalid image type
						$returnArr =  generateResponse("false", "Invalid image type: " . $_FILES['images']['name'], 400);
					}
				} else {
					// Handle error during file upload
					$returnArr =  generateResponse("false", "Error uploading image: " . $_FILES['images']['name'], 400);
				}
			}
		}

		// Handle video upload
		if (isset($_FILES['video'])) {
			$video = $_FILES['video'];
			// Check for upload errors
			if ($video['error'] === UPLOAD_ERR_OK) {
				// Validate video type
				if (in_array($video['type'], $allowedVideoTypes)) {
					// Generate a unique file name for the uploaded video
					$videoName = time() . '_' . $video['name'];
					$destination = $uploadDirVideos . $videoName;

					// Move the uploaded video to the destination folder
					if (move_uploaded_file($video['tmp_name'], $destination)) {
						$videoUrls[] = 'videos/property/' . $videoName;
						
					} else {
						// Handle error if video couldn't be moved
						$returnArr =  generateResponse("false", "Failed to upload video.", 500);
					}
				} else {
					// Handle invalid video type
					$returnArr =  generateResponse("false", "Invalid video type.", 400);
				}
			} else {
				// Handle error during video upload
				$returnArr =  generateResponse("false", "Error uploading video.", 400);
			}
		}
        
		if(!empty($imageUrls)){
		$imageUrlsString = implode(',', $imageUrls);
		$field["image"] =  $imageUrlsString;

		}


		if(!empty($videoUrls)){
			$videoUrlsString = implode(',', $videoUrls);
			$field["video"] =  $videoUrlsString;
	    }

		$check_owner_ = $rstate->query("select * from tbl_property where  add_user_id=" . $user_id . "")->num_rows;

		if ($check_owner_  >= 6) {
			$rstate->query("UPDATE tbl_user SET is_owner = 0 WHERE id=" . $user_id);
		}


		$table = "tbl_property";
		
		$where = "where id=" . $prop_id . " and add_user_id=" . $user_id . "";
		$h = new Estate();
		$check = $h->restateupdateData($field, $table, $where);
	} else {
		$returnArr    = generateResponse('false', "Edit Your Own Property!", 401);
	}
}
if (isset($returnArr)) {
	echo $returnArr;
} else {
	$returnArr    = generateResponse('true', "Property Update Successfully", 200);
	echo $returnArr;
}
