<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {


    $uid = isset($_GET['uid']) ? $_GET['uid'] : null;

    $lang = $rstate->real_escape_string(isset($_GET['lang']) ? $_GET['lang'] : 'en');

    $lang_ = load_specific_langauage($lang);

    if ($uid == null) {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else {
        $fp = array();
        $wow = array();

        date_default_timezone_set('Africa/Cairo');

        // Calculate the timestamp 3 hours ago in Cairo time
        $thirty_minutes_ago = date('Y-m-d H:i:s', strtotime('-30 minutes'));

        // Build the SQL query
        $sql = "SELECT *
        FROM tbl_non_completed 
        WHERE 
        completed = 0 
        AND (
            (status = 1 AND created_at > '" . $GLOBALS['rstate']->real_escape_string($thirty_minutes_ago) . "')
            OR 
            (active = 1)
        )
        and uid = " . (int)$uid;
        $result = $rstate->query($sql);
        while ($row = $result->fetch_assoc()) {

            $fp['id'] = $row['id'];
            $set = $rstate->query("SELECT title FROM tbl_property")->fetch_assoc();
            $fp['prop_title'] =json_decode($set['title'], true)[$lang] ?? '';
            $fp['from_date'] = $row['f1'];
            $fp['to_date'] = $row['f2'];
            $fp['prop_id'] = $row['prop_id'];
            $fp['uid'] = $row['uid'];
            $fp['guest_count'] = $row['guest_count'];
            $fp['confirm_guest_rules'] = 'true';
            $skey = encryptData($row['ref_number'], dirname(dirname(__FILE__),2) . '/keys/public.pem');
            $fp['ref_number'] = $skey;
            $uid = $row['uid'];
            $fp['coupon'] = $row['c_code'];
            $fp['method_key'] = $row['method'];
            $fp['fawry_number'] = $row['fawry_number'];

            $wow[] = $fp;
        }
        $returnArr    = generateResponse('true', "Non Completed Booking Founded!", 200, array("Booking" => $wow));
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
