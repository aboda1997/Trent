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
    $sel = $rstate->query("SELECT `id`, `check_in_retry`,`check_in`, `check_out`, `prop_title`, `add_user_id`, `uid`, `prop_img` 
                        FROM `tbl_book` 
                        WHERE `book_status` = 'Confirmed' 
                        AND `check_in_message` != 1  
                        AND `pay_status` = 'Completed'  
                        AND `check_in_retry` != 2  
                        ORDER BY `id` DESC");

    $sel1 = $rstate->query("SELECT `id`, `check_in`, `check_out`, `prop_title`, 
                        `add_user_id`, `uid`, `prop_img` 
                        FROM `tbl_book` 
                        WHERE `book_status` = 'check_in' 
                        AND `check_out_message` != 1  
                        ORDER BY `id` DESC");    // Set Cairo timezone
    $cairoTimezone = new DateTimeZone('Africa/Cairo');

    // Get current time in Cairo
    $currentDateTime = new DateTime('now', $cairoTimezone);

    while ($row = $sel->fetch_assoc()) {
        $add_user_id = $row['add_user_id'];
        $host = $rstate->query("select name , mobile	, ccode from tbl_user where  id= $add_user_id  ")->fetch_assoc();
        $uid = $row['uid'];
        $guest = $rstate->query("select name , mobile	, ccode from tbl_user where  id= $uid  ")->fetch_assoc();
        $host_mobile = $host["mobile"];
        $host_ccode = $host["ccode"];
        $host_name = $host["name"];
        $guest_mobile = $guest["mobile"];
        $guest_ccode = $guest["ccode"];
        $guest_name = $guest["name"];
        $check_in_str = $row['check_in'];
        $check_out_str = $row['check_out'];
        $prop_img = $row['prop_img'];
        $propertytitle = json_decode($row['prop_title'] ?? '', true)["ar"] ?? '';
        $book_id = $row['id'];
        $check_in_retry = $row['check_in_retry'];
        // Add default time if only date is provided
        if (strlen(trim($check_in_str)) <= 10) {
            $check_in_str .= ' 12:00:00';
        }

        // Add default time if only date is provided
        if (strlen(trim($check_out_str)) <= 10) {
            $check_out_str .= ' 12:00:00';
        }
        $guest_message = "أهلاً بيك 👋
        حابين نفكرك بتفاصيل حجزك على Trent ✅

        📍 الوحدة: $propertytitle
        📅 تاريخ الاستلام: $check_in_str
        📅 تاريخ المغادرة: $check_out_str

        📞 تواصل مع المالك علشان تحدد معاه ميعاد الاستلام.
        [$host_name]
        [$host_ccode$host_mobile]

        نتمنى لك تجربة إيجار مريحة وممتعة ✨
        فريق Trent دايمًا معاك!";

        $host_message = "أهلاً وسهلا 👋
        حابين نفكرك بوحدتك المحجوزة على Trent ✅

        📍 الوحدة: $propertytitle
        📅 مدة الحجز: من $check_in_str لـ $check_out_str

        📞 بيانات المستأجر:
        الاسم: [$guest_name ]
        رقم الموبايل: $guest_ccode$guest_mobile

        يُرجى التواصل مع المستأجر لتحديد موعد التسليم واستلام الوحدة.

        شكرًا لتعاونك،
        فريق Trent دايمًا معاك!";

        // Title for Guest Reminder Message (Message 1)
        $guest_Title = "تذكير بتفاصيل حجزك";

        // Title for Host Notification Message (Message 2) 
        $host_Title = "تنبيه بحجز جديد";
        // Create DateTime object for check-in with Cairo timezone
        $check_in = new DateTime($check_in_str, $cairoTimezone);

        // Calculate the difference between current time and check-in time
        $interval = $currentDateTime->diff($check_in);

        // Calculate total hours difference
        $hoursDifference = ($interval->days * 24) + $interval->h;

        // Check if check-in is in the future and within 48 hours
        if ($interval->invert == 0 && $hoursDifference <= 48) {
            $guest_whatsapp = sendMessage([$guest_ccode . $guest_mobile], $guest_message);
            $guest_firebase_notification = sendFirebaseNotification($guest_Title, $guest_message, $uid, 'booking_id', $book_id, $prop_img);
            $host_whatsapp = sendMessage([$host_ccode . $host_mobile], $host_message);
            $host_firebase_notification = sendFirebaseNotification($host_Title, $host_message, $add_user_id, 'booking_id', $book_id, $prop_img);
            if ($host_whatsapp &&  $guest_whatsapp) {
                $updateSql = "UPDATE tbl_book 
                      SET check_in_message = 1
                      WHERE id = $book_id";
                $GLOBALS['rstate']->query($updateSql);
            } else if ((!$host_whatsapp &&  $guest_whatsapp) || ($host_whatsapp &&  !$guest_whatsapp)) {
                $updateSql = "UPDATE tbl_book 
                      SET check_in_retry = $check_in_retry+1
                      WHERE id = $book_id";
                $GLOBALS['rstate']->query($updateSql);
            }
        }
    }



    while ($row = $sel1->fetch_assoc()) {
        $add_user_id = $row['add_user_id'];
        $host = $rstate->query("select name , mobile	, ccode from tbl_user where  id= $add_user_id  ")->fetch_assoc();
        $uid = $row['uid'];
        $guest = $rstate->query("select name , mobile	, ccode from tbl_user where  id= $uid  ")->fetch_assoc();
        $host_mobile = $host["mobile"];
        $host_ccode = $host["ccode"];
        $host_name = $host["name"];
        $guest_mobile = $guest["mobile"];
        $guest_ccode = $guest["ccode"];
        $guest_name = $guest["name"];
        $check_in_str = $row['check_in'];
        $check_out_str = $row['check_out'];
        $prop_img = $row['prop_img'];
        $propertytitle = json_decode($row['prop_title'] ?? '', true)["ar"] ?? '';
        $book_id = $row['id'];
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

        // Calculate the difference between current time and check-in time
        $interval = $currentDateTime->diff($check_out);

        // Calculate total hours difference
        $hoursDifference = ($interval->days * 24) + $interval->h;

        // Check if check-in is in the future and within 48 hours
        $checkout_Message = "أهلاً بيك 👋
        بنذكّرك إن تاريخ مغادرتك هو $check_out_str 🗓️

        نتمنى تكون استمتعت بتجربتك مع Trent 🙏
        حابين نعرف رأيك! 🌟
        ادخل على الأبلكيشن وقيم الوحدة اللي أجّرتها، وابعتلنا تعليق عن تجربتك، سواء في الإقامة أو استخدامك لتطبيق Trent
        Trent.com.eg | Cairo 
        رأيك بيساعدنا نطور دايمًا 💙

        شكرًا لاستخدامك Trent، ونشوفك في حجز جديد قريب إن شاء الله!
        فريق Trent دايمًا معاك ✨";
        $checkout_title = "تذكير بمغادرة الوحدة - Trent";
        if ($interval->invert == 0 && $hoursDifference <= 24) {
            $whatsapp = sendMessage([$guest_ccode . $guest_mobile], $checkout_Message);
            $firebase_notification = sendFirebaseNotification($checkout_title, $checkout_Message, $uid, 'booking_id', $book_id, $prop_img);
            
            if($whatsapp){
                 $updateSql = "UPDATE tbl_book 
                      SET check_out_message = 1
                      WHERE id = $book_id";
                $GLOBALS['rstate']->query($updateSql);
            }
        }
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
