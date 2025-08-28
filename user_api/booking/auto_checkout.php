<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';

$date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
$currentDate = $date->format('Y-m-d H:i:s');

// Update database
try {
    $h = new Estate();

    $sel2 = $rstate->query("SELECT `id`, `check_in`, `check_out`, `prop_title`, `add_user_id`, `uid`, `prop_img` 
                        FROM `tbl_book` 
                        WHERE `book_status` = 'Check_in' 
                        and `pay_status`   = 'Completed'
                        ORDER BY `id` DESC");
    $cairoTimezone = new DateTimeZone('Africa/Cairo');

    // Get current time in Cairo
    $currentDateTime = new DateTime('now', $cairoTimezone);
    $timestamp = $currentDateTime->format('Y-m-d H:i:s');


    $checkout_title = "تذكير بمغادرة الوحدة - Trent";
    while ($row = $sel2->fetch_assoc()) {
        $uid = $row['uid'];
        $guest = $rstate->query("select name , mobile	, ccode from tbl_user where  id= $uid  ")->fetch_assoc();
        $guest_mobile = $guest["mobile"];
        $guest_ccode = $guest["ccode"];
        $guest_name = $guest["name"];
        $check_in_str = $row['check_in'];
        $check_out_str = $row['check_out'];
        // Add default time if only date is provided
        if (strlen(trim($check_out_str)) <= 10) {
            $check_out_str .= ' 12:00:00';
        }
        // Add default time if only date is provided
        if (strlen(trim($check_in_str)) <= 10) {
            $check_in_str .= ' 12:00:00';
        }
        // Create DateTime object for check-in with Cairo timezone
        $check_out = new DateTime($check_out_str, $cairoTimezone);

        // رسالة تذكير بمغادرة الوحدة
        $checkout_Message = "أهلاً بيك 👋
بنذكّرك إن تاريخ مغادرتك هو $check_out_str 🗓️

نتمنى تكون استمتعت بتجربتك مع Trent 🙏
حابين نعرف رأيك! 🌟
ادخل على الأبلكيشن وقيم الوحدة اللي أجّرتها، وابعتلنا تعليق عن تجربتك، سواء في الإقامة أو استخدامك لتطبيق Trent
Trent.com.eg | Cairo 
رأيك بيساعدنا نطور دايمًا 💙

شكرًا لاستخدامك Trent، ونشوفك في حجز جديد قريب إن شاء الله!
فريق Trent دايمًا معاك ✨";
        $book_id = $row['id'];
        $prop_img = $row['prop_img'];

        if ($currentDateTime >= $check_out) {
            $field_check_out = array('book_status' => 'Completed', 'check_outtime' => $timestamp);
            $where = "where uid=" . '?' . " and id=" . '?' . "";
            $where_conditions = [$uid, $book_id];
            $_id = $h->restateupdateData_Api($field_check_out, 'tbl_book', $where, $where_conditions);
            $whatsapp = sendMessage([$guest_ccode . $guest_mobile], $checkout_Message);
            $firebase_notification = sendFirebaseNotification($checkout_title, $checkout_Message, $uid, 'booking_id', $book_id, $prop_img);
        }
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
