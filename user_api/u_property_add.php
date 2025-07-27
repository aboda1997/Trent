<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/user_api/estate.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';
require dirname(dirname(__FILE__)) . '/user_api/notifications/Send_mail.php';

header('Content-Type: application/json');
try {
	$status = 1;
	$is_approved = 0;
	$facility = isset($_POST['facilities']) ? $_POST['facilities'] : '';
	$ptype = isset($_POST['category_id']) ? $_POST['category_id'] : '';
	$beds = isset($_POST['beds_count']) ? $_POST['beds_count'] : '';
	$bathroom = isset($_POST['bathrooms_count']) ? $_POST['bathrooms_count'] : '';
	$sqft = isset($_POST['sqft']) ? $_POST['sqft'] : '';
	$price = isset($_POST['price']) ? $_POST['price'] : '';
	$plimit = isset($_POST['guest_count']) ? $_POST['guest_count'] : '';
	$pbuysell = 1;
	$user_id = isset($_POST['uid']) ? $_POST['uid'] : '';
	$government = isset($_POST['government_id']) ? $_POST['government_id'] : '';
	$security_deposit = isset($_POST['security_deposit']) ? $_POST['security_deposit'] : '';
	$max_days = isset($_POST['max_days']) ? $_POST['max_days'] : '';
	$min_days = isset($_POST['min_days']) ? $_POST['min_days'] : '';
	$google_maps_url = isset($_POST['maps_url']) ? $_POST['maps_url'] : '';
	$period = isset($_POST['period']) ? $_POST['period'] : 'd';
	$is_featured = isset($_POST['is_featured']) ? intval($_POST['is_featured']) : 0;
	$cancellation_policy_id = isset($_POST['cancellation_policy_id']) ? intval($_POST['cancellation_policy_id']) : '';
	$date_ranges = isset($_POST['date_ranges']) ? json_decode($_POST['date_ranges'], true) : null;
	$inc_value_ranges = isset($_POST['inc_value_ranges']) ? json_decode($_POST['inc_value_ranges'], true) : null;

	$decodedIds = json_decode($facility, true);
	$ids = array_filter(array_map('trim', $decodedIds));
	$idList = implode(',', $ids);


	$title_en = $rstate->real_escape_string(isset($_POST['title_en']) ? $_POST['title_en'] : '');
	$address_en = $rstate->real_escape_string(isset($_POST['address_en']) ? $_POST['address_en'] : '');
	$description_en = $rstate->real_escape_string(isset($_POST['description_en']) ? $_POST['description_en'] : '');
	$ccount_en = $rstate->real_escape_string(isset($_POST['city_en']) ? $_POST['city_en'] : '');
	$floor_en = $rstate->real_escape_string(isset($_POST['floor_en']) ? $_POST['floor_en'] : '');
	$guest_rules_en = $rstate->real_escape_string(isset($_POST['guest_rules_en']) ? $_POST['guest_rules_en'] : '');
	$compound_en = $rstate->real_escape_string(isset($_POST['compound_en']) ? $_POST['compound_en'] : '');

	$title_ar = $rstate->real_escape_string(isset($_POST['title_ar']) ? $_POST['title_ar'] : '');
	$address_ar = $rstate->real_escape_string(isset($_POST['address_ar']) ? $_POST['address_ar'] : '');
	$description_ar = $rstate->real_escape_string(isset($_POST['description_ar']) ? $_POST['description_ar'] : '');
	$ccount_ar = $rstate->real_escape_string(isset($_POST['city_ar']) ? $_POST['city_ar'] : '');
	$floor_ar = $rstate->real_escape_string(isset($_POST['floor_ar']) ? $_POST['floor_ar'] : '');
	$guest_rules_ar = $rstate->real_escape_string(isset($_POST['guest_rules_ar']) ? $_POST['guest_rules_ar'] : '');
	$compound_ar = $rstate->real_escape_string(isset($_POST['compound_en']) ? $_POST['compound_ar'] : '');



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


	if (
		$user_id == '' or $cancellation_policy_id == '' or $period == '' or $government == ''  or $google_maps_url == '' or  $floor_ar == '' or $floor_en == '' or $guest_rules_ar  == '' or $guest_rules_en == ''  or $pbuysell == '' or  $plimit == '' or $status == '' or $title_ar == '' or $title_en == '' or $address_ar == '' or $address_en == '' or $description_ar == '' or $description_en == '' or $ccount_ar == '' or $ccount_en == '' or $facility == '' or $ptype == '' or $beds == '' or $bathroom == '' or $sqft == ''
		or $price == ''
	) {
		$returnArr = generateResponse('false', "Something Went Wrong!", 400);
	} else if (validateFacilityIds($facility) === false) {
		$returnArr = generateResponse('false', "Facilities Ids must be valid!", 400);
	} else if (validateIdAndDatabaseExistance($ptype, 'tbl_category') === false) {
		$returnArr = generateResponse('false', "Category Id must be valid!", 400);
	} else if (validateIdAndDatabaseExistance($government, 'tbl_government') === false) {
		$returnArr = generateResponse('false', "Government Id must be valid!", 400);
	} else if (validateIdAndDatabaseExistance($cancellation_policy_id, 'tbl_cancellation_policy') === false) {
		$returnArr = generateResponse('false', "Cancellation Policy  Id must be valid!", 400);
	} else if (!in_array($period, ['d', 'm'])) {
		$returnArr    = generateResponse('false', "Period Id not valid!", 400);
	} else if (validateIdAndDatabaseExistance($user_id, 'tbl_user', ' status = 1 and verified =1 ') === false) {
		$returnArr    = generateResponse('false', "User id is not exists", 400);
	} else {


		// Allowed file types for images and videos
		$allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
		$allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'];

		// Initialize arrays for image and video URLs
		$imageUrls = [];
		$videoUrls = [];

		// Directories for storing images and videos
		$uploadDirImages = dirname(dirname(__FILE__)) . "/images/property/";
		$uploadDirVideos = dirname(dirname(__FILE__)) . "/videos/property/";

		// Location Cordinations
		$latitude = null;
		$longitude = null;
		$res = expandShortUrl($google_maps_url);


		if ($res['status']) {
			$cordinates = validateAndExtractCoordinates($res['response']);
			if ($cordinates['status']) {
				// Location Cordinations
				$latitude = $cordinates['latitude'];
				$longitude = $cordinates['longitude'];
			} else {
				$returnArr = generateResponse('false', $cordinates['response'],  400);
			}
		} else {
			$returnArr = generateResponse('false', $res['response'], 400);
		}

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
				$returnArr =  generateResponse("false", "Please upload more than two images.", 400);
			}
		} else {
			// No images uploaded
			$returnArr =  generateResponse("false", "No images uploaded.", 400);
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

		// Convert arrays to comma-separated strings
		$imageUrlsString = implode(',', $imageUrls);
		$videoUrlsString = implode(',', $videoUrls);



		if (!isset($returnArr)) {

			$table = "tbl_property";
			$date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
			$updated_at = $date->format('Y-m-d H:i:s');
			$up_at = $date->format('Y-m-d');
			$field_values = ["created_at", "updated_at", "image",  "cancellation_policy_id", "period", "is_featured", "security_deposit", "government", "map_url", "latitude", "longitude",  "video", "guest_rules", "compound_name", "floor", "status", "is_approved", "title", "price", "address", "facility", "description", "beds", "bathroom", "sqrft",  "ptype",  "city",  "add_user_id", "pbuysell",  "plimit", "max_days", "min_days"];
			$data_values = ["$updated_at", "$updated_at", "$imageUrlsString", $cancellation_policy_id, "$period", "$is_featured", "$security_deposit", "$government", "$google_maps_url", "$latitude", "$longitude", "$videoUrlsString", "$guest_rules_json", "$compound_json", "$floor_json", "$status", "$is_approved", "$title_json", "$price", "$address_json", "$idList", "$description_json", "$beds", "$bathroom", "$sqft",  "$ptype", "$ccount_json",  "$user_id", "$pbuysell", "$plimit", "$max_days", "$min_days"];

			$h = new Estate();
			$check = $h->restateinsertdata_Api($field_values, $data_values, $table);
			if ($check) {
				if (is_array($date_ranges)) {
					$jsonResponse    =  exclude_ranges('en', $user_id, $check, $date_ranges);
					$response = json_decode($jsonResponse, true); // true for associative array
					$result = $response['result']; // "true" or "false"
					if ($result == 'false') {
						$rstate->query("Delete from  tbl_property  WHERE id=" . $check);
						$returnArr  = $jsonResponse;
					}
				}
				if (is_array($inc_value_ranges)) {
					$jsonResponse    =  add_specific_ranges_increased_value('en', $user_id, $check, $inc_value_ranges);
					$response = json_decode($jsonResponse, true); // true for associative array
					$result = $response['result']; // "true" or "false"
					if ($result == 'false') {
						$rstate->query("Delete from  tbl_property  WHERE id=" . $check);
						$returnArr  = $jsonResponse;
					}
				}
			}
		}
		$check_owner = $rstate->query("select * from tbl_property where  is_approved =1 and  add_user_id=" . $user_id . " and is_deleted =0")->num_rows;

		if ($check_owner  >= AppConstants::Property_Count) {
			$rstate->query("UPDATE tbl_user SET is_owner = 0 WHERE id=" . $user_id);
		}
	}

	if (isset($returnArr)) {
		echo $returnArr;
	} else {
		if ($check) {
		$owner = $rstate->query("select name from tbl_user where  id=" . $user_id . "")->fetch_assoc();
        $government = $rstate->query("select name from tbl_government where  id=" . $government . "")->fetch_assoc();
        $gov_name = json_decode($government['name'], true)['ar'];
		$owner_name = $owner['name'];

			// Subject with placeholder
			$subject = '🏠 اضافة عقار جديد : ' . $title_ar;

			// Body with placeholders (using HEREDOC for clean multi-line text)
			$body = <<<EMAIL
			السلام عليكم
			تم إضافة عقار جديد ويحتاج إلى مراجعة أو تفعيل.
			إليك تفاصيل العقار:

			🏷️ عنوان العقار:  $address_ar
			🆔 رقم العقار: $check
			📍 الموقع: $ccount_ar.،. $gov_name
			👤 اسم المُعلن: $owner_name
			📅 تاريخ الإضافة: $up_at

			لمراجعة العقار، تفضل بزيارة الرابط التالي:
			👉 https://trent.com.eg/trent/add_properties.php?id=$check

			مع فائق التحية
			TRENT
			EMAIL;

			$table = "tbl_property";
			$field = ["visibility" => $check];
			$where = "where id=" . '?' . "";
			$h = new Estate();
			$where_conditions = [$check];
			$data = $h->restateupdateData_Api($field, $table, $where, $where_conditions);
			sendPlainTextEmail( $subject, $body ,'abdelrahmanalgendy97@gmail.com' , 'abdelrahman');

			$returnArr    = generateResponse('true', "Property Added Successfully", 201, array("id" => $check, "title" => json_decode($title_json, true)));
		} else {
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
