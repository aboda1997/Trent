<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';


    $data = array();

    $setting = $rstate->query("select terms_and_conditions 	from tbl_setting")->fetch_assoc();

    $terms_and_conditions = json_decode($setting['terms_and_conditions'], true);

    // Check if decoding was successful and if the expected key exists
    if (json_last_error() === JSON_ERROR_NONE && is_array($terms_and_conditions) && isset($terms_and_conditions[$lang_code])) {
        $data['terms_and_conditions'] = $terms_and_conditions[$lang_code];
    } else {
        // Provide a default fallback value
        $data['terms_and_conditions'] = "";
    }
    
    $returnArr = generateResponse(
        'true',
        "Terms And Conditions Exist!",
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
