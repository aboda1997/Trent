<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $data = json_decode(file_get_contents('php://input'), true);

    $uid = isset($data['uid']) ? $data['uid'] : '';
    $rating_id = isset($data['rating_id']) ? $data['rating_id'] : '';
    $lang = $rstate->real_escape_string(isset($data['lang']) ? $data['lang'] : 'en');

    $lang_ = load_specific_langauage($lang);
   
    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if ($rating_id == '') {
        $returnArr = generateResponse('false', $lang_["rating_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($rating_id, 'tbl_rating' , ' uid = ' .$uid. '') === false) {
        $returnArr = generateResponse('false', $lang_["rating_not_available"], 400);
    } else {

            $GLOBALS['rstate']->begin_transaction();
            $h = new Estate();
            $table = "tbl_rating";
            $where = "where id=" . $rating_id ;
            $res = $h->restateDeleteData_Api_fav($where , $table);
            
            $GLOBALS['rstate']->commit();
            $res_text = $lang_["rating_deleted"];


    }


    if (isset($returnArr)) {
        echo $returnArr;
    } else {
        $returnArr    = generateResponse('true', $res_text, 200, array(
            "rating_id" => $rating_id,
        ));

        echo $returnArr;
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
