<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');
try {
    $uid  =  isset($_GET['uid']) ? $_GET['uid'] : '';
    $item_id  =  isset($_GET['item_id']) ? $_GET['item_id'] : 0;
    $lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
    $lang_ = load_specific_langauage($lang);
    $non_completed_data = $rstate->query("select sub_total , total from tbl_non_completed where id=" . $item_id)->fetch_assoc();
    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if ($item_id == 0) {
        $returnArr = generateResponse('false',  $lang_["item_id_required"], 400);
    } else if ($non_completed_data == 0) {
        $returnArr    = generateResponse('false',  $lang_["general_validation_error"], 400);
    } else {
        $where_conditions = [$item_id];
        $h = new Estate();

        $where = "where  id=" . '?' . "";
        $field = array('c_code' => '', );
        $check = $h->restateupdateData_Api($field, 'tbl_non_completed', $where, $where_conditions);

        $returnArr  = generateResponse('true', "Removed Successfully", 200);
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
