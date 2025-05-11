<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {


    $prop_id = isset($_GET['prop_id']) ? $_GET['prop_id'] : '';

    $lang = $rstate->real_escape_string(isset($_GET['lang']) ? $_GET['lang'] : 'en');

    $lang_ = load_specific_langauage($lang);

    if ($prop_id == '') {
        $returnArr = generateResponse('false', $lang_["property_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', ' status = 1 and is_approved =1') === false) {
        $returnArr = generateResponse('false', $lang_["property_not_available"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else {
        $fp = array();
        $wow = array();

        $data = $rstate->query("select * from tbl_rating where prop_id=" . $prop_id . " ");
        while ($row = $data->fetch_assoc()) {
            $fp['id'] = $row['id'];
            $fp['booking_id'] = $row['book_id'];
            $fp['prop_id'] = $row['prop_id'];
            $fp['rating'] = $row['rating'];
            $fp['comment'] = $row['comment'];
            $fp['created_at'] = $row['created_at'];
            $uid = $row['uid'];
            $user_data = $rstate->query("select * from tbl_user where id=" . $uid . " and status = 1")->fetch_assoc();
            $fp['user_name'] = $user_data['name'];
            $fp['user_img'] = $user_data['pro_pic'];
            $fp['user_reg_date'] = $user_data['reg_date'];


            $wow[] = $fp;
        }
        $returnArr    = generateResponse('true', "Property Ratings Founded!", 200, array("Ratings" => $wow));
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
