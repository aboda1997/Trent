<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try{

$pol = array();
$c = array();
$pro_id  =  isset($_GET['prop_id']) ? $_GET['prop_id'] : '';
$lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
if ($pro_id == ''  ) {
	$returnArr = generateResponse('false', "Something Went Wrong!", 400);
} else if (validateIdAndDatabaseExistance($pro_id, 'tbl_property' , ' status = 1 and is_approved =1 and is_deleted =0') === false) {
	$returnArr = generateResponse('false', "this property not exist!", 400);
}else if (checkTableStatus($pro_id, 'tbl_property') == false) {
	$returnArr = generateResponse('false', "This Property already deleted", 410);
} else {
	$fp = array();

	$sel = $rstate->query("select * from tbl_property where status = 1 and  id=" . $pro_id .  "")->fetch_assoc();
	$fp['id'] = $sel['id'];
	$fp['owner_id'] = $sel['add_user_id'];
	$fp['max_days'] = $sel['max_days'];
	$fp['min_days'] = $sel['min_days'];

	$fp['guest_rules'] = json_decode($sel['guest_rules'], true)[$lang] ?? '';
	$fp['guest_count'] = $sel['plimit'];
	$titleData = json_decode($sel['title'], true);
	$fp['title'] = $titleData[$lang]??'';

	$check_date_query = $rstate->query("SELECT check_in, check_out FROM tbl_book WHERE prop_id = $pro_id AND book_status != 'Cancelled' ORDER BY check_out ASC");

	$booked_dates = [];
	while ($row = $check_date_query->fetch_assoc()) {
		$booked_dates[] = [
			'check_in'  => strtotime($row['check_in']),
			'check_out' => strtotime($row['check_out'])
		];
	}
	
	// Find the next available date
	$next_available_date = date('Y-m-d'); // Default to today if no bookings exist
	
	if (!empty($booked_dates)) {
		$latest_check_out = 0;
		
		foreach ($booked_dates as $booking) {
			if ($booking['check_out'] > $latest_check_out) {
				$latest_check_out = $booking['check_out'];
			}
		}
	
		// The next available date is the day after the latest check_out date
		$next_available_date = date('Y-m-d', strtotime('+1 day', $latest_check_out));
	}
	
	// Assign the next available date to the response
	$returnArr    = generateResponse('true', "Property Booking Details Founded!", 200, array("property_booking_details" => $fp ));

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
