<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require  'get_pay_status.php';

$date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
$currentDate = $date->format('Y-m-d H:i:s');

// Update database
try {
    $h = new Estate();

    $sel2 = $rstate->query("SELECT *
                        FROM `tbl_non_completed` 
                        WHERE `completed` = '0' 
                        and `status`   = '1'
                        and `ref_number`   != ''
                        ORDER BY `id` DESC");
    $cairoTimezone = new DateTimeZone('Africa/Cairo');


    // Get current time in Cairo
    $currentDateTime = new DateTime('now', $cairoTimezone);
    $fourHoursAgo = (clone $currentDateTime)->modify('-1 hours');


    while ($row = $sel2->fetch_assoc()) {
        $created_at = $row['created_at'];
        $createdDateTime = new DateTime($created_at, $cairoTimezone);
        $merchant_ref_number = $row['ref_number'];
        $item_id = $row['id'];
        $method = $row['method'];
        $final_total = $row['partial_value'];
        if ($createdDateTime > $fourHoursAgo) {
            $pay_status = getPaymentStatus($merchant_ref_number, $item_id, (int)$final_total);
            if ($pay_status['status']) {
                save_booking($item_id, $merchant_ref_number, $method);
            }
        }
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}



function save_booking($item_code,  $merchantRefNumber, $method)
{

    // Get booking details from database
    $booking_query = $GLOBALS['rstate']->query("SELECT * FROM tbl_non_completed WHERE id = '" . $GLOBALS['rstate']->real_escape_string($item_code) . "'");
    if ($booking_query->num_rows) {
        $booking_data = $booking_query->fetch_assoc();

        // Prepare form data for POST request
        $post_fields = http_build_query([
            'uid' => $booking_data['uid'],
            'prop_id' => $booking_data['prop_id'],
            'merchant_ref_number' => $merchantRefNumber,
            'item_id' => $booking_data['id'],
            'from_date' => $booking_data['f1'],
            'to_date' => $booking_data['f2'],
            'confirm_guest_rules' => 'true',
            'method_key' => $method,
            'guest_counts' =>  $booking_data['guest_count'],
            'coupon_code' =>  $booking_data['c_code']
        ]);

        // Set the base URL
        $base_url = 'https://www.trent.com.eg/trent';
        $save_booking_url = $base_url . '/user_api/booking/u_save_booking.php';

        // Initialize cURL
        $ch = curl_init($save_booking_url);

        // Set cURL options for form POST
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post_fields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_SSL_VERIFYPEER => true, // Enable SSL verification
            CURLOPT_TIMEOUT => 10 // Set timeout to 30 seconds
        ]);

        // Execute the request
        $response = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for errors
        if (curl_errno($ch)) {

            curl_close($ch);
            return false;
        }

        // Close cURL session
        curl_close($ch);

        // Check if the request was successful (2xx status codes)
        if ($http_code >= 200 && $http_code < 300) {
            return true;
        } else {

            return false;
        }
    }

    return false;
}


function complete_paying($item_code, $merchantRefNumber)
{

    // Get booking details from database
    $booking_query = $GLOBALS['rstate']->query("SELECT * FROM tbl_book WHERE item_id = '" . $GLOBALS['rstate']->real_escape_string($item_code) . "'");

    if ($booking_query->num_rows) {
        $booking_data = $booking_query->fetch_assoc();

        // Prepare form data for POST request
        $post_fields = http_build_query([
            'uid' => $booking_data['uid'],
            'booking_id' => $booking_data['id'],
            'merchant_ref_number' => $merchantRefNumber,
            'item_id' => $item_code,
            'method_key' => 'CARD',
        ]);

        // Set the base URL
        $base_url = 'https://www.trent.com.eg/trent';
        $save_booking_url = $base_url . '/user_api/booking/u_complete_paying.php';

        // Initialize cURL
        $ch = curl_init($save_booking_url);

        // Set cURL options for form POST
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post_fields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_SSL_VERIFYPEER => true, // Enable SSL verification
            CURLOPT_TIMEOUT => 10 // Set timeout to 30 seconds
        ]);

        // Execute the request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for errors
        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }

        // Close cURL session
        curl_close($ch);

        // Check if the request was successful (2xx status codes)
        if ($http_code >= 200 && $http_code < 300) {
            return true;
        } else {

            return false;
        }
    }

    return false;
}
