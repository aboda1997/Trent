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

    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : '';
    $rating = isset($_POST['rating']) ? $_POST['rating'] : '';
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    $lang = $rstate->real_escape_string(isset($_POST['lang']) ? $_POST['lang'] : 'en');

    $lang_ = load_specific_langauage($lang);
    $fieldNames = [
        'comment' => [
            'en' => 'Comment',
            'ar' => 'التعليق'
        ],

    ];
    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if ($booking_id == '') {
        $returnArr = generateResponse('false', $lang_["booking_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($booking_id, 'tbl_book',  ' uid =' . $uid . '') === false) {
        $returnArr = generateResponse('false', $lang_["booking_not_available"], 400);
    } else if (validateIdAndDatabaseExistance($booking_id, 'tbl_book',  ' book_status = "Completed" ') === false) {
        $returnArr = generateResponse('false', $lang_["adding_rating_not_allowed"], 400);
    } else if ($rating < 1  || $rating > 5) {
        $returnArr = generateResponse('false', $lang_["rating_not_valid"], 400);
    } else if (!validateName($comment, $fieldNames['comment'][$lang], 250, $lang, false)['status']) {
        $returnArr    = generateResponse('false', validateName($comment, $fieldNames['comment'][$lang], 250, $lang, false)['response'], 400);
    } else {
        $prop_id = $rstate->query("select prop_id from tbl_book where id=" . $booking_id . "")->fetch_assoc()['prop_id'];
        $rating_exists = $rstate->query("select id from tbl_rating where book_id=" . $booking_id . " and uid = " .$uid. '')->num_rows;

        if ($rating_exists != 0) {
            $GLOBALS['rstate']->begin_transaction();
            $h = new Estate();
            $table = "tbl_rating";
            $field_values = ["rating" => $rating, "comment" => $comment, "status" => 1,  "book_id" => $booking_id, "uid" => $uid, "prop_id" => $prop_id];
            $where = "where book_id=" . '?' . " and uid = ? ";
            $where_conditions = [$booking_id ,$uid];
            $rating_id = $h->restateupdateData_Api($field_values, $table, $where, $where_conditions);
            $GLOBALS['rstate']->commit();
            $res_text = $lang_["rating_updated"];


        } else {
            $GLOBALS['rstate']->begin_transaction();
            $created_at = date('Y-m-d H:i:s');

            $h = new Estate();
            $table = "tbl_rating";
            $field_values = ["rating", "comment", "status",  "book_id", "uid", "prop_id", 'created_at'];
            $data_values = [$rating, $comment, 1, $booking_id, $uid, $prop_id, $created_at];
            $rating_id = $h->restateinsertdata_Api($field_values, $data_values, $table);

            $GLOBALS['rstate']->commit();
            $res_text = $lang_["rating_added"];
        }
    }


    if (isset($returnArr)) {
        echo $returnArr;
    } else {
        $returnArr    = generateResponse('true', $res_text, 201, array(
            "booking_id" => $booking_id,
            "rating" => $rating
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
