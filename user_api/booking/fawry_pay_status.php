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
    $orderAmount = $inputData['orderAmount'] ?? 0.00;
    $orderStatus = $inputData['orderStatus'] ?? '';
    $paymentMethod = $inputData['paymentMethod'] ?? '';

    // Prepare values in exact same order as field names
    $field_values = ["fawryRefNumber", "merchantRefNumber", "orderAmount", "orderStatus", "paymentMethod"];
    $data_values = [
        $fawryRefNumber,
        $merchantRefNumber,
        $orderAmount,
        $orderStatus,
        $paymentMethod
    ];

    $h = new Estate();
    $returnArr    = generateResponse('true', "Success", 200);
    $check = $h->restateinsertdata_Api($field_values, $data_values, 'payment');
    echo $returnArr;
} catch (Exception $e) {

    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
