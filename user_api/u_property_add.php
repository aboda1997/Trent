<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/user_api/estate.php';

header('Content-type: text/json');
try{
$status = 1;
$is_approved = 0;
$facility = isset($_POST['facilities']) ? $_POST['facilities'] : '';
$ptype = isset($_POST['category_id']) ? $_POST['category_id'] : ''; 
$beds = isset($_POST['beds_count']) ? $_POST['beds_count'] : '';  
$bathroom = isset($_POST['bathrooms_count']) ? $_POST['bathrooms_count'] : '';  
$sqft = isset($_POST['sqft']) ? $_POST['sqft'] : ''; 
$listing_date = date("Y-m-d H:i:s");
$price = isset($_POST['price']) ? $_POST['price'] : ''; 
$plimit = isset($_POST['guest_count']) ? $_POST['guest_count'] : '';  
$pbuysell = 1;
$user_id = isset($_POST['uid']) ? $_POST['uid'] : '';  
$government = isset($_POST['government_id']) ? $_POST['government_id'] : '';   
$security_deposit =isset($_POST['security_deposit']) ? $_POST['security_deposit'] : '';  
$max_days = isset($_POST['max_days']) ? $_POST['max_days'] : ''; 
$min_days = isset($_POST['min_days']) ? $_POST['min_days'] : ''; 
$google_maps_url =isset($_POST['maps_url']) ? $_POST['maps_url'] : ''; 
$period =isset($_POST['period']) ? $_POST['period'] : 'd';
$is_featured =isset($_POST['is_featured']) ? intval($_POST['is_featured']) : 0;

$decodedIds = json_decode($facility, true);
$ids = array_filter(array_map('trim', $decodedIds));
$idList = implode(',', $ids);

$title_en = $rstate->real_escape_string(isset($_POST['title_en']) ? $_POST['title_en'] : '');
$address_en = $rstate->real_escape_string(isset($_POST['address_en']) ? $_POST['address_en'] : '');
$description_en = $rstate->real_escape_string(isset($_POST['description_en']) ? $_POST['description_en'] : '');
$ccount_en = $rstate->real_escape_string(isset($_POST['city_en']) ? $_POST['city_en'] : '');
$floor_en = $rstate->real_escape_string( isset($_POST['floor_en']) ? $_POST['floor_en'] : '');
$guest_rules_en = $rstate->real_escape_string( isset($_POST['guest_rules_en']) ? $_POST['guest_rules_en'] : '');
$compound_en = $rstate->real_escape_string( isset($_POST['compound_en']) ? $_POST['compound_en'] : '');

$title_ar = $rstate->real_escape_string(isset($_POST['title_ar']) ? $_POST['title_ar'] : '');
$address_ar = $rstate->real_escape_string(isset($_POST['address_ar']) ? $_POST['address_ar'] : '');
$description_ar = $rstate->real_escape_string(isset($_POST['description_ar']) ? $_POST['description_ar'] : '');
$ccount_ar = $rstate->real_escape_string(isset($_POST['city_ar']) ? $_POST['city_ar'] : '');
$floor_ar = $rstate->real_escape_string( isset($_POST['floor_ar']) ? $_POST['floor_ar'] : '');
$guest_rules_ar = $rstate->real_escape_string( isset($_POST['guest_rules_ar']) ? $_POST['guest_rules_ar'] : '');
$compound_ar = $rstate->real_escape_string( isset($_POST['compound_en']) ? $_POST['compound_ar'] : '');



$floor_json = json_encode([
	"en" => $floor_en,
	"ar" => $floor_ar
], JSON_UNESCAPED_UNICODE);

$compound_json = json_encode([
	"en" => $compound_en,
	"ar" => $compound_ar
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


if ($user_id == '' or $period == '' or $government == '' or $security_deposit == ''  or $google_maps_url == '' or  $floor_ar == '' or $floor_en == '' or $compound_en == '' or $compound_ar == '' or $guest_rules_ar  == '' or $guest_rules_en == ''  or $pbuysell == '' or  $plimit == '' or $status == '' or $title_ar == '' or $title_en == '' or $address_ar == '' or $address_en == '' or $description_ar == '' or $description_en == '' or $ccount_ar == '' or $ccount_en == '' or $facility == '' or $ptype == '' or $beds == '' or $bathroom == '' or $sqft == '' or $listing_date == '' or $price == '') {
	$returnArr = generateResponse('false', "Something Went Wrong!",400);
} else if (validateFacilityIds($facility) === false) {
	$returnArr = generateResponse('false', "Facilities Ids must be valid!", 400);
} else if (validateIdAndDatabaseExistance($ptype, 'tbl_category') === false) {
	$returnArr = generateResponse('false', "Category Id must be valid!", 400);
} else if (validateIdAndDatabaseExistance($government, 'tbl_government') === false) {
	$returnArr = generateResponse('false', "Government Id must be valid!", 400);
}
else if (!in_array($period, ['d', 'm'])) {
	$returnArr    = generateResponse('false', "Period Id not valid!", 400);
}
else {


	// Allowed file types for images and videos
	$allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
	$allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'];
	
	// Initialize arrays for image and video URLs
	$imageUrls = [];
	$videoUrls = [];

	// Directories for storing images and videos
	$uploadDirImages = dirname(dirname(__FILE__)) . "/images/property/";
	$uploadDirVideos = dirname(dirname(__FILE__)) . "/videos/property/";


	// Handle image upload
	if (isset($_FILES['images'])) {
		// Check if it's multiple images or a single image
		if (is_array($_FILES['images']['name']) && count($_FILES['images']['name']) >= 3) {
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
							$returnArr=  generateResponse("false", "Failed to upload image: " . $_FILES['images']['name'][$key], 500);
						}
					} else {
						// Handle invalid image type
						$returnArr=  generateResponse("false", "Invalid image type: " . $_FILES['images']['name'][$key], 400);
					}
				} else {
					// Handle error during file upload
					$returnArr=  generateResponse("false", "Error uploading image: " . $_FILES['images']['name'][$key], 400);
				}
			}
		} else {
			$returnArr=  generateResponse("false", "Please upload more than two images.", 400);

		}
	} else {
		// No images uploaded
		$returnArr=  generateResponse("false", "No images uploaded.", 400);
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
					$returnArr=  generateResponse("false", "Failed to upload video.", 500);
				}
			} else {
				// Handle invalid video type
				$returnArr=  generateResponse("false", "Invalid video type.", 400);
			}
		} else {
			// Handle error during video upload
			$returnArr=  generateResponse("false", "Error uploading video.", 400);
		}
	}

	// Convert arrays to comma-separated strings
	$imageUrlsString = implode(',', $imageUrls);
	$videoUrlsString = implode(',', $videoUrls);



	$table = "tbl_property"; 

	$field_values = ["image", "period", "is_featured", "security_deposit", "government", "google_maps_url", "video", "guest_rules", "compound_name", "floor", "status", "is_approved","title", "price", "address", "facility", "description", "beds", "bathroom", "sqrft",  "ptype",  "city", "listing_date", "add_user_id", "pbuysell",  "plimit", "max_days", "min_days"];
	$data_values = ["$imageUrlsString", "$period", "$is_featured", "$security_deposit", "$government", "$google_maps_url", "$videoUrlsString", "$guest_rules_json", "$compound_json", "$floor_json", "$status", "$is_approved" , "$title_json", "$price", "$address_json", "$idList", "$description_json", "$beds", "$bathroom", "$sqft",  "$ptype", "$ccount_json", "$listing_date", "$user_id", "$pbuysell", "$plimit", "$max_days", "$min_days"];

	$h = new Estate();
	$check = $h->restateinsertdata_Api($field_values, $data_values, $table);
	$check_owner = $rstate->query("select * from tbl_property where  add_user_id=" . $user_id . "")->num_rows;

	if ($check_owner  >= 6) {
		$rstate->query("UPDATE tbl_user SET is_owner = 0 WHERE id=" . $user_id);
	}

	
}

if (isset($returnArr)) {
	echo $returnArr;
}else{
	if($check){
	$returnArr    = generateResponse('true', "Property Added Successfully", 201 , array("id"=> $check, "title" => json_decode($title_json, true) ));
	
	}else{
		$returnArr    = generateResponse('false', "Database error", 500);

	}
	echo $returnArr;

}
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
}
?>