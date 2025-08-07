<?php
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require_once dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');

    // Decode JSON input to array
    $inputData = json_decode($input, true);

    // Extract required fields with default values if not present
    $fawryRefNumber = $inputData['fawryRefNumber'] ?? '';
    $merchantRefNumber = $inputData['merchantRefNumber'] ?? '';
    $orderAmount = number_format((float)$inputData['orderAmount'], 2, '.', '');
    $orderStatus = $inputData['orderStatus'] ?? '';
    $paymentMethod = $inputData['paymentMethod'] ?? '';
    $itemCode = $inputData['orderItems'][0]['itemCode'] ?? 0;
    $checkKey = $rstate->query("SELECT id FROM payment WHERE merchantRefNumber = '" . $merchantRefNumber . "'");
    $get_secure_key = $rstate->query("select merchant_code ,secure_key from tbl_setting ");
    $secureKey = $get_secure_key->fetch_assoc()['secure_key'];
    $decrypted_secure_key = decryptData($secureKey,  dirname(dirname(__FILE__), 2) . '/keys/private.pem');
    $h = new Estate();
    $paymentMethods = [
        'CARD' => 'CARD',
        'Mobile Wallet' => 'MWALLET',
        'PAYATFAWRY' => 'PayAtFawry'
    ];
    $method = $paymentMethods[$paymentMethod] ?? '';

    if (!verifyFawrySignature($inputData, $inputData['messageSignature'], $decrypted_secure_key['data'])) {
        $returnArr    = generateResponse('false', "Not valid Data", 400);
    } else if ($checkKey->num_rows) {
        $field_values = ["orderStatus" => $orderStatus];
        $where = "where merchantRefNumber=" . '?' . " and itemId = ? ";
        $where_conditions = [$merchantRefNumber, $itemCode];
        $_id = $h->restateupdateData_Api($field_values, 'payment', $where, $where_conditions);


        if ($orderStatus == "PAID") {
            if (strpos($itemCode, 'item') === 0) {
                complete_paying($itemCode, $merchantRefNumber);
            } else {

                $field = array('ref_number' => $merchantRefNumber,  'active' => '1',  'method' => $method,  'fawry_number' => $fawryRefNumber);
                $where1 = "where  id=" . '?' . "";
                $where_conditions1 = [$itemCode];
                $check = $h->restateupdateData_Api($field, 'tbl_non_completed', $where1, $where_conditions1);

                $t = save_booking($itemCode, $merchantRefNumber, $method);
            }
        } else {

            $field1 = array('ref_number' => $merchantRefNumber,  'method' => $method,   'fawry_number' => $fawryRefNumber);
            $where1 = "where  id=" . '?' . "";
            $where_conditions1 = [$itemCode];

            $check = $h->restateupdateData_Api($field1, 'tbl_non_completed', $where1, $where_conditions1);
        }
        $returnArr    = generateResponse('true', "Success", 200);
    } else {
        // Prepare values in exact same order as field names
        $field_values = ["fawryRefNumber", "merchantRefNumber", "orderAmount", "orderStatus", "paymentMethod", "itemId"];
        $data_values = [
            $fawryRefNumber,
            $merchantRefNumber,
            $orderAmount,
            $orderStatus,
            $paymentMethod,
            $itemCode
        ];

        $returnArr    = generateResponse('true', "Success", 200);
        $check = $h->restateinsertdata_Api($field_values, $data_values, 'payment');
        if ($orderStatus == "PAID") {
            if (strpos($itemCode, 'item') === 0) {
                complete_paying($itemCode, $merchantRefNumber);
            } else {
                $field = array('ref_number' => $merchantRefNumber,  'active' => '1',  'method' => $method, 'fawry_number' => $fawryRefNumber);
                $where1 = "where  id=" . '?' . "";
                $where_conditions1 = [$itemCode];
                $check = $h->restateupdateData_Api($field, 'tbl_non_completed', $where1, $where_conditions1);

                save_booking($itemCode, $merchantRefNumber, $method);
            }
        } else {
            $field1 = array('ref_number' => $merchantRefNumber,  'method' => $method,  'fawry_number' => $fawryRefNumber);
            $where1 = "where  id=" . '?' . "";
            $where_conditions1 = [$itemCode];

            $check = $h->restateupdateData_Api($field1, 'tbl_non_completed', $where1, $where_conditions1);
        }
    }
    echo $returnArr;
} catch (Exception $e) {

    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}

function verifyFawrySignature(array $paymentData, string $receivedSignature, string $secureKey): bool
{
    // Strict formatting
    $paymentAmount = number_format((float)$paymentData['paymentAmount'], 2, '.', '');
    $orderAmount = number_format((float)$paymentData['orderAmount'], 2, '.', '');
    $paymentReference = $paymentData['paymentRefrenceNumber'] ?? "";

    // Trim all fields to avoid hidden characters
    $concatenatedString =
        trim($paymentData['fawryRefNumber']) .
        trim($paymentData['merchantRefNumber']) .
        $paymentAmount .
        $orderAmount .
        trim($paymentData['orderStatus']) .
        trim($paymentData['paymentMethod']) .
        $paymentReference .
        trim($secureKey);
    $expectedSignature = hash('sha256', $concatenatedString);
    // Compare securely (to prevent timing attacks)
    return hash_equals($expectedSignature, $receivedSignature);
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
