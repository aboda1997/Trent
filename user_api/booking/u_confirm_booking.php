<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';
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
    $prop_id = isset($_POST['prop_id']) ? $_POST['prop_id'] : null;
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : null;

    $is_confirmed = isset($_POST['is_confirmed']) ? $_POST['is_confirmed'] : 'false';
    $deny_id = isset($_POST['deny_id']) ? $_POST['deny_id'] : null;
    $lang_ = load_specific_langauage($lang);
    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if ($is_confirmed == 'false' && $deny_id == null) {
        $returnArr = generateResponse('false', $lang_["cancel_id_required"], 400);
    } else if ($is_confirmed == 'false' && validateIdAndDatabaseExistance($deny_id, 'tbl_cancel_reason') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_cancel_id"], 400);
    } else  if ($booking_id  == null) {
        $returnArr    = generateResponse('false', $lang_["booking_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($booking_id, 'tbl_book', ' add_user_id  =' . $uid . '') === false) {
        $returnArr    = generateResponse('false', $lang_["booking_not_available"], 400);
    } else if (getBookingStatus($booking_id)['book_status'] != 'Booked') {
        $returnArr    = generateResponse('false', $lang_["not_allow_to_do"], 400);
    } else {
        $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
        $created_at = $date->format('Y-m-d H:i:s');
        $table = "tbl_book";
        $fp = array();
        $field = array('book_status' => 'Confirmed', 'confirmed_at' => $created_at, 'confirmed_by' => 'H');
        $field_cancel = array('book_status' => 'Cancelled', 'cancle_reason' => $deny_id, "cancel_by" => 'H');
        $where = "where id=" . '?' . "";
        $where_conditions = [$booking_id];
        $booking_data = $rstate->query("select add_user_id , prop_id , book_date ,prop_title	, uid from tbl_book where  id= $booking_id  ")->fetch_assoc();
        $uid = $booking_data['uid'];
        $user = $rstate->query("select  mobile, ccode	 from tbl_user where  id= $uid ")->fetch_assoc();
        $prop_id = $booking_data['prop_id'];
        $property_data = $rstate->query("select address from tbl_property where  id= $prop_id  ")->fetch_assoc();
        $address = json_decode($property_data['address'] ?? '', true)['ar'] ?? '';
        $title = json_decode($booking_data['prop_title'] ?? '', true)['ar'] ?? "";

        $h = new Estate();
        if ($is_confirmed == 'true') {
            $mobile = $user["mobile"];
            $ccode = $user["ccode"];
            $message = "مبروك!
تم تأكيد حجز العقار  [$title] بنجاح.
التفاصيل: • يمكنك مراجعة تفاصيل الحجز من خلال التطبيق • ستتلقى تفاصيل الاتصال بالمالك قريباً • في حالة وجود أي استفسار، لا تتردد في التواصل معنا
نتمنى لك إقامة مريحة فريق ت-رينت 🎉";
            $title_ = 'تهانينا! تم تأكيد حجز العقار ✅';
            $whatsapp = sendMessage([$ccode . $mobile], $message);
            $firebase_notification = sendFirebaseNotification($title_, $message, $uid, "booking_id", $booking_id);
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
            $cancel_text =  json_decode($cancel_data['reason']??"", true)['ar']??'';

            $message = "عزيزي العميل،
نأسف لإبلاغك بأنه تم إلغاء حجز العقار [$title]للسبب التالي: [$cancel_text]
يمكنك: • مراجعة شروط الحجز والتأكد من استيفائها • إعادة حجز العقار مرة أخرى عبر تطبيق ت-رينت • التواصل معنا للحصول على المساعدة
شكراً لثقتكم بنا فريق ت-رينت 🏠";
            $title_ = ' تم إلغاء حجز العقار';
            $mobile = $user["mobile"];
            $ccode = $user["ccode"];

            $whatsapp = sendMessage([$ccode . $mobile], $message);
            $firebase_notification = sendFirebaseNotification($title_, $message, $uid,  "booking_id", $booking_id);
            $check = $h->restateupdateData_Api($field_cancel, $table, $where, $where_conditions);

            if($check){
                refundMoney($uid , $booking_id , 'H' , $deny_id);
            }

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
