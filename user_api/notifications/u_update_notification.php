<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

    $uid = isset($_POST['uid']) ? $_POST['uid'] : null;
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $lang_ = load_specific_langauage($lang_code);


    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } elseif ($id !== '*' && validateIdAndDatabaseExistance($id, 'tbl_notification_head') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_notification_id"], 400);
    } else {

        $table = "tbl_notification_head";
        $field_values = ["is_seen" => 1];
        $h = new Estate();
        if ($id == '*') {
            $check = $h->restateupdateData_Api($field_values, $table,  "where uid=" . '?' . "", [$uid]);
        } else {
            $check = $h->restateupdateData_Api($field_values, $table,  "where id=" . '?' .  " and  uid =  ? ", [$id , $uid]);
        }
        $returnArr    = generateResponse('true', "Notification Updated SuccessFully", 200);
    }

    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
