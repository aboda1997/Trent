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
                        WHERE `book_status` = 'Confirmed' 
                        and `pay_status`   = 'Completed'
                        ORDER BY `id` DESC");
    $cairoTimezone = new DateTimeZone('Africa/Cairo');

    // Get current time in Cairo
    $currentDateTime = new DateTime('now', $cairoTimezone);
    $timestamp = $currentDateTime->format('Y-m-d H:i:s');


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
        $check_in = new DateTime($check_in_str, $cairoTimezone);
        // رسالة ترحيب بعد دخول العقار
        $checkin_Message = "أهلاً وسهلاً بيك في وحدة Trent 👋
        بنباركلك وبنتمنى لك إقامة مريحة وممتعة في الوحدة 🏡

        لو احتجت أي مساعدة أو عندك أي استفسار، فريق الدعم متاح ليك على مدار الساعة 📞

        كمان تقدروا تتابعوا تفاصيل الحجز وتشوفوا خدمات إضافية متاحة ليكم من خلال تطبيق Trent 📱
        Trent.com.eg | Cairo 

        فريق Trent دايمًا معاك علشان تجربتك تكون الأفضل 💙

        نتمنى ليك إقامة سعيدة ونشوفك دايمًا معانا!
        فريق Trent ✨";
        $book_id = $row['id'];
        $prop_img = $row['prop_img'];

        $checkin_title = "ترحيب بدخول الوحدة - Trent";
        if ($currentDateTime >= $check_in) {
            $field_check_in = array('book_status' => 'Check_in', 'check_intime' => $timestamp);
            $where = "where uid=" . '?' . " and id=" . '?' . "";
            $where_conditions = [$uid, $book_id];
            $_id = $h->restateupdateData_Api($field_check_in, 'tbl_book', $where, $where_conditions);
            //$whatsapp = sendMessage([$guest_ccode . $guest_mobile], $checkin_Message);
            //$firebase_notification = sendFirebaseNotification($checkin_title, $checkin_Message, $uid, 'booking_id', $book_id, $prop_img);
        }
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
