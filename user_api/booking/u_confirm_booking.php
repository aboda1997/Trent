<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';

    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    $prop_id = isset($_POST['prop_id']) ? $_POST['prop_id'] : null;

    $is_confirmed = isset($_POST['is_confirmed']) ? $_POST['is_confirmed'] : 'false';
    $deny_id = isset($_POST['deny_id']) ? $_POST['deny_id'] : null;
    $lang_ = load_specific_langauage($lang);

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if (!$is_confirmed  && $deny_id == null) {
        $returnArr = generateResponse('false', $lang_["cancel_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($deny_id, 'tbl_cancel_reason') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_cancel_id"], 400);
    } else  if ($prop_id  == null) {
        $returnArr    = generateResponse('false', $lang_["prop_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', ' status = 1 and is_approved =1') === false) {
        $returnArr    = generateResponse('false', $lang_["prop_not_available"], 400);
    } else {

        $table = "tbl_book";

        $h = new Estate();
        $check = $h->restateinsertdata_Api($field_values, $data_values, $table);
        $returnArr    = generateResponse('true', "Property booking Details", 200, array(
            "booking_details" => $fp,
        ));
    }

    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
