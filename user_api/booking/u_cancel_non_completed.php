<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';

    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : 0;
    $non_completed_data = $rstate->query("select id from tbl_non_completed where id=" . $item_id. " and uid=". $uid )->num_rows;

    $lang_ = load_specific_langauage($lang);
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
        $non_completed_data_ = $rstate->query("select * from tbl_non_completed where id=" . $item_id)->fetch_assoc();
      
        // Start transaction with proper isolation level
        $GLOBALS['rstate']->query("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
        $GLOBALS['rstate']->begin_transaction();

        try {
          
                $h = new Estate();

                $table = "tbl_non_completed";
                $field = array('status' => '0');
                $where = "where id=" . '?' . "";
                $where_conditions = [$item_id];
                $check = $h->restateupdateData_Api($field, $table, $where, $where_conditions);
                $GLOBALS['rstate']->commit();
                $returnArr    = generateResponse('true', 'Operation Done Successfully', 200);

        } catch (Exception $e) {
            $GLOBALS['rstate']->rollback();
            throw $e; // Re-throw to be caught by the outer try-catch
        }
    }


    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
