<?php
require_once dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require_once dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';
require_once  'fawry_pull_status.php';

function getPaymentStatus( $merchant_ref_number , $item_id ,$total_as_int) {
    $get_secure_key = $GLOBALS['rstate']->query("select merchant_code ,secure_key from tbl_setting ")->fetch_assoc();
    $secureKey = $get_secure_key['secure_key'];
    $merchantCode = $get_secure_key['merchant_code'];
    $decrypted_secure_key =  decryptData($secureKey,  dirname(dirname(__FILE__), 2) . '/keys/private.pem')['data'];
    $decrypted_code = decryptData($merchantCode,  dirname(dirname(__FILE__), 2) . '/keys/private.pem')['data'];

    $check_push_pay = $GLOBALS['rstate']->query("SELECT orderStatus, orderAmount, itemId AS itemCode FROM payment WHERE itemId = $item_id and merchantRefNumber = '" . $merchant_ref_number . "'");
    // Trim all fields to avoid hidden characters
    $concatenatedString =
        trim($decrypted_code) .
        trim($merchant_ref_number) .
        trim($decrypted_secure_key);
    $expectedSignature = hash('sha256', $concatenatedString);
    $check_pull_pay = getFawryPaymentStatus($decrypted_code, $merchant_ref_number, $expectedSignature);
    // Check if push payment has rows and pull payment status exists
    if ($check_push_pay->num_rows && $check_pull_pay["status"]) {
        $check_push_pay->data_seek(0);
        $check_push_pay_data = $check_push_pay->fetch_assoc();
        // Convert amounts to integers for strict comparison
        $push_amount = (int)$check_push_pay_data['orderAmount'];
        $pull_amount = (int)$check_pull_pay['orderAmount'];

        // Verify all conditions with integer comparison
        return (
            $check_push_pay_data['orderStatus'] == 'PAID' &&
            $check_push_pay_data['itemCode'] == $item_id &&
            $check_pull_pay['orderStatus'] == 'PAID' &&
            $check_pull_pay['itemCode'] == $item_id &&
            abs($push_amount - $total_as_int) < 2 &&
            abs($pull_amount - $total_as_int) < 2 
        );
    }

    return false;
   
}

?>