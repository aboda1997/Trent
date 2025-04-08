<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

    $data = array();

    $setting = $rstate->query("select confidence_booking from tbl_setting")->fetch_assoc();

    $confidence_booking = json_decode($setting['confidence_booking'], true);

    // Check if decoding was successful and if the expected key exists
    if (json_last_error() === JSON_ERROR_NONE && is_array($confidence_booking) && isset($confidence_booking[$lang_code])) {
        $data['confidence_booking'] = $confidence_booking[$lang_code];
    } else {
        // Provide a default fallback value
        $data['confidence_booking'] = "";
    }
    
    $returnArr = generateResponse(
        'true',
        "Confidence Booking Exist!",
        200,
    
             $data
        
    );

    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
