<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');
try {

    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $prop_id  =  isset($_POST['prop_id']) ? $_POST['prop_id'] : null;
    $uid  =  isset($_POST['uid']) ? $_POST['uid'] : '';
    $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';
    $lang_ = load_specific_langauage($lang);
    $date_ranges = isset($_POST['date_ranges']) ? json_decode($_POST['date_ranges'], true) : null;
   
    echo exclude_ranges($lang , $uid , $prop_id , $date_ranges );
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
