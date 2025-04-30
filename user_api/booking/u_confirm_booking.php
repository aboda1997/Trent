<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';

    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    $prop_id = isset($_POST['prop_id']) ? $_POST['prop_id'] : null;
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : null;

    $is_confirmed = isset($_POST['is_confirmed']) ? $_POST['is_confirmed'] : 'false';
    $deny_id = isset($_POST['deny_id']) ? $_POST['deny_id'] : null;
    $lang_ = load_specific_langauage($lang);
    if ($uid == '') {
		$returnArr    = generateResponse('false', "User id is required", 400);
	} else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
		$returnArr    = generateResponse('false', "User id is not exists", 400);
	} 
    else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if ($is_confirmed == 'false' && $deny_id == null) {
        $returnArr = generateResponse('false', $lang_["cancel_id_required"], 400);
    } else if ($is_confirmed == 'false' && validateIdAndDatabaseExistance($deny_id, 'tbl_cancel_reason') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_cancel_id"], 400);
    } else  if ($booking_id  == null) {
        $returnArr    = generateResponse('false', $lang_["booking_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($booking_id, 'tbl_book' , ' add_user_id  ='. $uid. '') === false) {
        $returnArr    = generateResponse('false', $lang_["booking_not_available"], 400);
    } else {

        $table = "tbl_book";
        $fp = array();
        $field = array('book_status' => 'Confirmed');
        $field_cancel = array('book_status' => 'Cancelled', 'cancle_reason' => $deny_id , "cancel_by" => 'H');
        $where = "where id=" . '?' . "";
        $where_conditions = [$booking_id];
        $booking_data = $rstate->query("select add_user_id , prop_id , book_date ,prop_title	, uid from tbl_book where  id= $booking_id  ")->fetch_assoc();
        $uid = $booking_data['uid'];
        $user = $rstate->query("select  mobile	 from tbl_user where  id= $uid ")->fetch_assoc();
        $title = json_decode($booking_data['prop_title'], true)['ar'];
        $h = new Estate();
        if ($is_confirmed == 'true') {
            $message = $lang_["property_booking_confirmed_success"];
            $mobile = $user["mobile"];
            //$whatsapp = sendMessage([$mobile], $message);
            //$firebase_notification = sendFirebaseNotification($message, $message, $uid);
            $check = $h->restateupdateData_Api($field, $table, $where, $where_conditions);


            $returnArr    = generateResponse('true', $lang_["property_booking_confirmed_success"], 200, array(
                "booking_details" => [
                    'prop_id' =>  $booking_data['prop_id'],
                    'book_date' =>  $booking_data['book_date'],
                    'prop_title' => json_decode($booking_data['prop_title'], true)[$lang]
                ],
            ));
        } else {
            $cancel_data = $rstate->query("select  reason	 from tbl_cancel_reason where  id= $deny_id ")->fetch_assoc();
            $cancel_text =  json_decode($cancel_data['reason'], true)[$lang];
           
            $message = "عذراً، تم إلغاء حجز العقار ($title) للأسباب التالية:\n\n($cancel_text)\n\nيمكنك مراجعة بيانات العقار وحجزه مرة أخرى عبر موقع أو تطبيق ت-رينت\n\nمع تحيات فريق ت-رينت";            $mobile = $user["mobile"];
            $whatsapp = sendMessage([$mobile], $message);
            $firebase_notification = sendFirebaseNotification($message, $message, $uid);
            $check = $h->restateupdateData_Api($field_cancel, $table, $where, $where_conditions);


            $returnArr    = generateResponse('true', $lang_["property_booking_canceled_success"], 200, array(
                "booking_details" => [
                    'prop_id' =>  $booking_data['prop_id'],
                    'book_date' =>  $booking_data['book_date'],
                    'prop_title' => json_decode($booking_data['prop_title'], true)[$lang]
                ],
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
