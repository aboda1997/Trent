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
    $profile_id = isset($_POST['profile_id']) ? $_POST['profile_id'] : '';
    $booking_list = isset($_POST['booking_list']) ? $_POST['booking_list'] : '';

    $lang = $rstate->real_escape_string(isset($_POST['lang']) ? $_POST['lang'] : 'en');
    $decodedIds = json_decode($booking_list, true);
    $ids = array_filter(array_map('trim', $decodedIds));
    $idList = implode(',', $ids);

    $lang_ = load_specific_langauage($lang);

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if (validateIdAndDatabaseExistance($profile_id, 'tbl_payout_profiles' ,  ' uid =' . $uid. '' ) === false) {
        $returnArr = generateResponse('false', $lang_["payout_profile_not_exist"], 404);
    } else if (validateFacilityIds($booking_list,  'tbl_book' , $uid) === false) {
        $returnArr = generateResponse('false', $lang_["invalid_booking_ids"], 400);
    } else {

        $created_at = date('Y-m-d H:i:s');



        if (!isset($returnArr)) {
            $GLOBALS['rstate']->begin_transaction();
            $h = new Estate();
            $table1 = "tbl_payout";
            $table2 = "tbl_payout_list";
            $field_values = ["requested_at"];
            $data_values = [$created_at];
            $Payout_id = $h->restateinsertdata_Api($field_values, $data_values, $table1);
            foreach ($ids as $value) {
                $field_values = ["payout_id", "book_id", "requested_at", "profile_id"];
                $data_values = [$Payout_id, $value, $created_at, $profile_id];

                $Payout_list_id = $h->restateinsertdata_Api($field_values, $data_values, $table2);
            }
            $GLOBALS['rstate']->commit();
        }
    }


    if (isset($returnArr)) {
        echo $returnArr;
    } else {
        $returnArr    = generateResponse('true', $lang_["payout_request_added"], 201, array(
            "payout_id" => $Payout_id,
            "profile_id" => $profile_id
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
