<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
    $uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : '';

    $lang_ = load_specific_langauage($lang_code);

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else {
        $pol = array();
        $c = array();
        $sel = $rstate->query("SELECT id, name  , bank_name ,full_name, method_id , bank_account_number , wallet_number FROM tbl_payout_profiles WHERE status=1 and uid = $uid");
        while ($row = $sel->fetch_assoc()) {
            $method_id = $row["method_id"];
            $method = $rstate->query("SELECT id, name  FROM tbl_payout_methods WHERE id=$method_id ")->fetch_assoc();
            $pol['id'] = $row['id'];
            $pol['profile_name'] = $row['name'];
            $pol['full_name'] = $row['full_name'];
            $pol['bank_name'] = $row['bank_name'];
            $pol['method_name'] = $method ? json_decode($method['name'], true)[$lang_code] : '';
            $pol['bank_account_number'] = $row['bank_account_number'];
            $pol['wallet_number'] = $row['wallet_number'];

            $c[] = $pol;
        }
        if (empty($c)) {

            $returnArr    = generateResponse('true', "Payout Method List Not Founded!", 200, array(
                "payout_profiles_list" => $c,
                "length" => count($c),
            ));
        } else {
            $returnArr    = generateResponse('true', "Payout Method List Founded!", 200, array(
                "payout_profiles_list" => $c,
                "length" => count($c),
            ));
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
