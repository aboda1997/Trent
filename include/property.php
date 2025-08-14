<?php
require "reconfig.php";
require "estate.php";
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';

use Shuchkin\SimpleXLSX;
use PhpOffice\PhpSpreadsheet\IOFactory;

require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/user_api/notifications/send_notification.php';
require dirname(dirname(__FILE__)) . '/user_api/notifications/Send_mail.php';

try {
    if (isset($_POST["type"]) && ((!isset($_SESSION['restatename'])  && $_POST['type'] == 'login') || (isset($_SESSION['restatename'])  && $_POST['type'] != 'login'))) {

        if ($_POST['type'] == 'login') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $type = $_POST['stype'];


            $h = new Estate();

            $data = $h->restatelogin($username, $password, $type)->fetch_assoc();
            if (isset($data['id'])) {
                $_SESSION['restatename'] = $username;
                $_SESSION['permissions'] = get_user_permissions($data['id'], $rstate);
                $_SESSION['id'] = $data['id'];
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Login Successfully!", "message" => "welcome admin!!", "action" => "dashboard.php");
            } else {
                $returnArr = array("ResponseCode" => "200", "Result" => "false", "title" => "Please Use Valid Data!!", "message" => "welcome admin!!", "action" => "index.php");
            }
        } else if ($_POST["type"] == "add_code") {
            $okey = $_POST["status"];
            $title = $rstate->real_escape_string($_POST["title"]);

            $table = "tbl_code";
            $field_values = ["ccode", "status"];
            $data_values = ["$title", "$okey"];

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Country Code Add Successfully!!",
                    "message" => "Country Code section!",
                    "action" => "list_code.php",
                ];
            }
        } else if ($_POST['type'] == 'edit_code') {
            $okey = $_POST['status'];
            $title = $rstate->real_escape_string($_POST['title']);
            $id = $_POST['id'];
            $table = "tbl_code";
            $field = array('status' => $okey, 'ccode' => $title);
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Country Code Update Successfully!!", "message" => "Country Code section!", "action" => "list_code.php");
            }
        } else if ($_POST['type'] == 'add_coupon') {
            $ccode = $rstate->real_escape_string($_POST['coupon_code']);

            $cdate = $_POST['expire_date'];
            $minamt = $_POST['min_amt'];
            $maxamt = $_POST['max_amt'];
            $cstatus = $_POST['status'];
            $cvalue = $_POST['coupon_val'];
            $cdesc_ar = $rstate->real_escape_string($_POST['description_ar']);
            $cdesc_en = $rstate->real_escape_string($_POST['description_en']);

            $ctitle_ar = $rstate->real_escape_string($_POST['title_ar']);
            $ctitle_en = $rstate->real_escape_string($_POST['title_en']);
            $subtitle_ar = $rstate->real_escape_string($_POST['subtitle_ar']);
            $subtitle_en = $rstate->real_escape_string($_POST['subtitle_en']);

            $ctitle_json = json_encode([
                "en" => $ctitle_en,
                "ar" => $ctitle_ar
            ], JSON_UNESCAPED_UNICODE);


            $subtitle_json = json_encode([
                "en" => $subtitle_en,
                "ar" => $subtitle_ar
            ], JSON_UNESCAPED_UNICODE);


            $cdesc_json = json_encode([
                "en" => $cdesc_en,
                "ar" => $cdesc_ar
            ], JSON_UNESCAPED_UNICODE);
            $target_dir = dirname(dirname(__FILE__)) . "/images/offer/";
            $url = "images/offer/";
            $temp = explode(".", $_FILES["coupon_img"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);

            move_uploaded_file($_FILES["coupon_img"]["tmp_name"], $target_file);
            $table = "tbl_coupon";
            $field_values = array("c_img", "c_desc", "c_value", "c_title", "status", "cdate", "ctitle", "min_amt", "max_amt", "subtitle");
            $data_values = array("$url", "$cdesc_json", "$cvalue", "$ccode", "$cstatus", "$cdate", "$ctitle_json", "$minamt", "$maxamt", "$subtitle_json");

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Coupon Add Successfully!!", "message" => "Coupon section!", "action" => "list_coupon.php");
            }
        } else if ($_POST['type'] == 'edit_coupon') {
            $ccode = $rstate->real_escape_string($_POST['coupon_code']);
            $id = $_POST['id'];
            $cdate = $_POST['expire_date'];
            $minamt = $_POST['min_amt'];
            $maxamt = $_POST['max_amt'];
            $cstatus = $_POST['status'];
            $cvalue = $_POST['coupon_val'];
            $cdesc_ar = $rstate->real_escape_string($_POST['description_ar']);
            $cdesc_en = $rstate->real_escape_string($_POST['description_en']);

            $ctitle_ar = $rstate->real_escape_string($_POST['title_ar']);
            $ctitle_en = $rstate->real_escape_string($_POST['title_en']);
            $subtitle_ar = $rstate->real_escape_string($_POST['subtitle_ar']);
            $subtitle_en = $rstate->real_escape_string($_POST['subtitle_en']);

            $ctitle_json = json_encode([
                "en" => $ctitle_en,
                "ar" => $ctitle_ar
            ], JSON_UNESCAPED_UNICODE);


            $subtitle_json = json_encode([
                "en" => $subtitle_en,
                "ar" => $subtitle_ar
            ], JSON_UNESCAPED_UNICODE);


            $cdesc_json = json_encode([
                "en" => $cdesc_en,
                "ar" => $cdesc_ar
            ], JSON_UNESCAPED_UNICODE);
            $target_dir = dirname(dirname(__FILE__)) . "/images/offer/";
            $url = "images/offer/";
            $temp = explode(".", $_FILES["coupon_img"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["coupon_img"]["name"] != '') {

                move_uploaded_file($_FILES["coupon_img"]["tmp_name"], $target_file);
                $table = "tbl_coupon";
                $field = array('c_img' => $url, 'c_desc' => $cdesc_json, 'c_value' => $cvalue, 'c_title' => $ccode, 'status' => $cstatus, 'cdate' => $cdate, 'ctitle' => $ctitle_json, 'max_amt' => $maxamt, 'min_amt' => $minamt, 'subtitle' => $subtitle_json);
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Offer Update Successfully!!", "message" => "Offer section!", "action" => "list_coupon.php");
                }
            } else {
                $table = "tbl_coupon";
                $field = array('c_desc' => $cdesc_json, 'c_value' => $cvalue, 'c_title' => $ccode, 'status' => $cstatus, 'cdate' => $cdate, 'ctitle' => $ctitle_json, 'max_amt' => $maxamt, 'min_amt' => $minamt, 'subtitle' => $subtitle_json);
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Offer Update Successfully!!", "message" => "Offer section!", "action" => "list_coupon.php");
                }
            }
        } else if ($_POST['type'] == 'add_admin_user') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $check = $rstate->query("select id from admin   WHERE username = " .  "'$username'" . "")->fetch_assoc();
            if (isset($check['id'])) {
                throw new Exception("THis Username is already used ");
            }

            $type = $_POST['user_type'];
            $table = "admin";
            $table1 = "role_permissions";
            $field_values = array("username", "password", "type", 'status');
            $field_values1 = array("role_id", "permissions");
            $h = new Estate();
            $permission_ids = [];
            $GLOBALS['rstate']->begin_transaction();

            if ($type == '1') {
                $data_values = array("$username", "$password", "Staff", 1);
                $result = $rstate->query("select id from permissions   WHERE name IN ('Create_Property', 'Update_Property',
                 'Read_Property','Create_Slider','Update_Slider','Read_Slider',
                'Delete_Slider','Create_Booking','Update_Booking','Read_Booking',
                'Create_Cancellation_Policy','Update_Cancellation_Policy','Read_Cancellation_Policy','Delete_Cancellation_Policy',
                'Create_Chat','Update_Chat','Read_Chat','Delete_Chat',
                'Create_Cancel_Reason','Update_Cancel_Reason','Read_Cancel_Reason','Delete_Cancel_Reason',
                'Read_Setting' , 'Update_Setting' ,'Read_Wallet')  ");
            } else {
                $data_values = array("$username", "$password", "Admin", 1);
                $result = $rstate->query("select id from permissions ");
            }
            while ($row = $result->fetch_assoc()) {
                $permission_ids[] = $row['id'];
            }
            $permissions = implode(',', $permission_ids);



            $id = $h->restateinsertdata_id($field_values, $data_values, $table);
            $data_values1 = array("$id", "$permissions");

            $check = $h->restateinsertdata_id($field_values1, $data_values1, $table1);

            $GLOBALS['rstate']->commit();

            $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "User Add Successfully!!", "message" => "User section!", "action" => "list_admin_user.php");
        } else if ($_POST['type'] == 'edit_admin_user') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $check = $rstate->query("select id from admin   WHERE username = " .  "$username")->fetch_assoc();
            if (isset($check['id'])) {
                throw new Exception("THis Username is already used ");
            }

            $type = $_POST['user_type'];
            $id = $_POST['id'];
            $table1 = "role_permissions";
            $table = "admin";
            $field = array('username' => $username, 'password' => $password, 'type' => $type);
            $where = "where id=" . $id . "";
            $where1 = "where role_id=" . $id . "";
            $h = new Estate();
            $permission_ids = [];
            $GLOBALS['rstate']->begin_transaction();

            if ($type == '1') {
                $data_values = array("$username", "$password", "Staff");
                $result = $rstate->query("select id from permissions   WHERE name IN ('Create_Property', 'Update_Property',
                 'Read_Property','Create_Slider','Update_Slider','Read_Slider',
                'Delete_Slider','Create_Booking','Update_Booking','Read_Booking',
                'Create_Cancellation_Policy','Update_Cancellation_Policy','Read_Cancellation_Policy','Delete_Cancellation_Policy',
                'Create_Chat','Update_Chat','Read_Chat','Delete_Chat',
                'Create_Reason','Update_Cancel_Reason','Read_Cancel_Reason','Delete_Cancel_Reason',
                'Read_Setting' , 'Update_Setting' ,'Read_Wallet')  ");
            } else {
                $data_values = array("$username", "$password", "Admin");
                $result = $rstate->query("select id from permissions ");
            }
            while ($row = $result->fetch_assoc()) {
                $permission_ids[] = $row['id'];
            }
            $permissions = implode(',', $permission_ids);
            $field1 = array('role_id' => $id, 'permissions' => $permissions);



            $update = $h->restateupdateData($field,  $table, $where);

            $check = $h->restateupdateData($field1, $table1, $where1);

            $GLOBALS['rstate']->commit();

            $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "User Updated Successfully!!", "message" => "User section!", "action" => "list_admin_user.php");
        } else if ($_POST['type'] == 'delete_admin_user') {
            $id = $_POST['id'];
            $status = (int)$_POST['status'];


            $table = "admin";
            $where = "where id=" . $id . "";

            $h = new Estate();
            $check = $h->restateupdateData(["status" => "!$status"], $table, $where);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "User Deleted Successfully!!", "message" => "User  section!", "action" => "list_admin_user.php");
            }
        } else if ($_POST['type'] == 'add_gal_category') {
            $property = mysqli_real_escape_string($rstate, $_POST['property']);
            $title_ar = mysqli_real_escape_string($rstate, $_POST['title_ar']);
            $title_en = mysqli_real_escape_string($rstate, $_POST['title_en']);
            $status = $_POST['status'];


            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);


            $table = "tbl_gal_cat";
            $field_values = array("pid", "title", "status");
            $data_values = array("$property", "$title_json", "$status");

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Category Add Successfully!!", "message" => "Gallery Category section!", "action" => "list_gal_cat.php");
            }
        } else if ($_POST['type'] == 'edit_gal_category') {
            $property = mysqli_real_escape_string($rstate, $_POST['property']);
            $title_ar = mysqli_real_escape_string($rstate, $_POST['title_ar']);
            $title_en = mysqli_real_escape_string($rstate, $_POST['title_en']);
            $status = $_POST['status'];


            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);

            $id = $_POST['id'];


            $table = "tbl_gal_cat";
            $field = array('pid' => $property, 'status' => $status, 'title' => $title_json);
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Gallery Category Update Successfully!!", "message" => "Gallery Category section!", "action" => "list_gal_cat.php");
            }
        } else if ($_POST['type'] == 'add_faq') {
            $question_en = mysqli_real_escape_string($rstate, $_POST['question_en']);
            $answer_en = mysqli_real_escape_string($rstate, $_POST['answer_en']);
            $question_ar = mysqli_real_escape_string($rstate, $_POST['question_ar']);
            $answer_ar = mysqli_real_escape_string($rstate, $_POST['answer_ar']);
            $okey = $_POST['status'];
            $question_json = json_encode([
                "en" => $question_en,
                "ar" => $question_ar
            ], JSON_UNESCAPED_UNICODE);

            $answer_json = json_encode([
                "en" => $answer_en,
                "ar" => $answer_ar
            ], JSON_UNESCAPED_UNICODE);

            $table = "tbl_faq";
            $field_values = array("question", "answer", "status");
            $data_values = array("$question_json", "$answer_json", "$okey");
            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Faq Add Successfully!!", "message" => "Faq section!", "action" => "list_faq.php");
            }
        } else if ($_POST['type'] == 'edit_faq') {
            $question_en = mysqli_real_escape_string($rstate, $_POST['question_en']);
            $answer_en = mysqli_real_escape_string($rstate, $_POST['answer_en']);
            $question_ar = mysqli_real_escape_string($rstate, $_POST['question_ar']);
            $answer_ar = mysqli_real_escape_string($rstate, $_POST['answer_ar']);
            $okey = $_POST['status'];
            $id = $_POST['id'];
            $question_json = json_encode([
                "en" => $question_en,
                "ar" => $question_ar
            ], JSON_UNESCAPED_UNICODE);

            $answer_json = json_encode([
                "en" => $answer_en,
                "ar" => $answer_ar
            ], JSON_UNESCAPED_UNICODE);

            $table = "tbl_faq";
            $field = array('question' => $question_json, 'status' => $okey, 'answer' => $answer_json);
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Faq Update Successfully!!", "message" => "Faq section!", "action" => "list_faq.php");
            }
        } else if ($_POST['type'] == 'update_reason') {
            $reason = mysqli_real_escape_string($rstate, $_POST['reason']);

            $id = $_POST['id'];

            $table = "tbl_book";
            $field = array('cancle_reason' => $reason, 'book_status' => 'Cancelled');
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Booking Cancelled Successfully!!", "message" => "Booking section!", "action" => "pending.php");
            }
        } else if ($_POST['type'] == 'edit_commission') {
            $commission = $_POST['commission'];
            $id = $_POST['id'];
            $table = "tbl_rider";
            $field = "commission=" . $commission . "";
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData_single($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Commission Update Successfully!!", "message" => "Commission section!", "action" => "riderlist.php");
            }
        } else if ($_POST['type'] == 'edit_profile') {
            $dname = $_POST['username'];
            $dsname = $_POST['password'];
            $id = $_POST['id'];
            $table = "admin";
            $field = array('username' => $dname, 'password' => $dsname);
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Profile Update Successfully!!", "message" => "Profile  section!", "action" => "profile.php");
            }
        } else if ($_POST['type'] == 'edit_setting') {
            $webname = mysqli_real_escape_string($rstate, $_POST['webname']);
            $id = $_POST['id'];

            $nemail = $_POST['nemail'];
            $tax = $_POST['tax'];
            $ofees = $_POST['ofees'];
            $pfees = $_POST['pfees'];
            $gmode = $_POST['gmode'];
            $show_property = $_POST['show_property'];
            $cmobile = $_POST['cmobile'];
            $cemail = $_POST['cemail'];
            if (decryptData($_POST['skey'], dirname(dirname(__FILE__)) . '/keys/private.pem')['status']) {
                $skey = $_POST['skey'];
            } else {
                $skey = encryptData($_POST['skey'], dirname(dirname(__FILE__)) . '/keys/public.pem');
            }
            if (decryptData($_POST['mcode'], dirname(dirname(__FILE__)) . '/keys/private.pem')['status']) {
                $mcode = $_POST['mcode'];
            } else {
                $mcode = encryptData($_POST['mcode'], dirname(dirname(__FILE__)) . '/keys/public.pem');
            }
            $alert_en = mysqli_real_escape_string($rstate, $_POST['ealert']);
            $alert_ar = mysqli_real_escape_string($rstate, $_POST['aalert']);

            $mfees = $_POST['mfees'];
            $perfees = $_POST['perfees'];
            $alert_json = json_encode([
                "en" => $alert_en,
                "ar" => $alert_ar
            ], JSON_UNESCAPED_UNICODE);

            $target_dir = dirname(dirname(__FILE__)) . "/images/website/";
            $url = "images/website/";
            $temp = explode(".", $_FILES["weblogo"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["weblogo"]["name"] != '') {

                move_uploaded_file($_FILES["weblogo"]["tmp_name"], $target_file);
                $table = "tbl_setting";
                $field = array(
                    'notification_email' => $nemail,
                    'owner_fees' => $ofees,
                    'property_manager_fees' => $pfees,
                    'gallery_mode' => $gmode,
                    'weblogo' => $url,
                    'webname' => $webname,
                    'show_property' => $show_property,
                    'gateway_percent_fees' => $perfees,
                    'gateway_money_fees' => $mfees,
                    'contact_us_email' => $cemail,
                    'contact_us_mobile' => $cmobile,
                    'alert_text' => $alert_json,
                    'merchant_code' => $mcode,
                    'secure_key' => $skey,
                    'tax' => $tax,
                );
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Setting Update Successfully!!", "message" => "Setting section!", "action" => "setting.php");
                }
            } else {
                $table = "tbl_setting";
                $field = array(
                    'notification_email' => $nemail,
                    'owner_fees' => $ofees,
                    'property_manager_fees' => $pfees,
                    'gallery_mode' => $gmode,
                    'webname' => $webname,
                    'show_property' => $show_property,
                    'gateway_percent_fees' => $perfees,
                    'gateway_money_fees' => $mfees,
                    'contact_us_email' => $cemail,
                    'contact_us_mobile' => $cmobile,
                    'alert_text' => $alert_json,
                    'merchant_code' => $mcode,
                    'secure_key' => $skey,
                    'tax' => $tax,

                );
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Setting Update Successfully!!", "message" => "Offer section!", "action" => "setting.php");
                }
            }
        } else if ($_POST['type'] == 'add_why_choose_us') {
            $why_choose_us_bg = $_POST['why_choose_us_bg'];
            $why_choose_us_title_en = $rstate->real_escape_string($_POST['why_choose_us_title_en']);
            $why_choose_us_title_ar = $rstate->real_escape_string($_POST['why_choose_us_title_ar']);
            $why_choose_us_description_ar = $rstate->real_escape_string($_POST['why_choose_us_description_ar']);
            $why_choose_us_description_en = $rstate->real_escape_string($_POST['why_choose_us_description_en']);
            $why_choose_us_description_json = json_encode([
                "en" => $why_choose_us_description_en,
                "ar" => $why_choose_us_description_ar
            ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);

            $why_choose_us_title_json = json_encode([
                "en" => $why_choose_us_title_en,
                "ar" => $why_choose_us_title_ar
            ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);

            $target_dir = dirname(dirname(__FILE__)) . "/images/website/";
            $url = "images/website/";
            $temp = explode(".", $_FILES["why_choose_us_img"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);

            move_uploaded_file($_FILES["why_choose_us_img"]["tmp_name"], $target_file);
            $table = "tbl_why_choose_us";
            $field = array(
                'title',
                'description',
                'img',
                'background_color'

            );

            $table = "tbl_why_choose_us";
            $data_values = array("$why_choose_us_title_json", "$why_choose_us_description_json", "$url", "$why_choose_us_bg");

            $h = new Estate();
            $check = $h->restateinsertdata($field, $data_values,  $table);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Why choose Us Data Added Successfully!!", "message" => "Why choose Us  section!", "action" => "list_why_choose_us.php");
            }
        } else if ($_POST['type'] == 'edit_why_choose_us') {
            $id = $_POST['id'];

            $why_choose_us_bg = $_POST['why_choose_us_bg'];
            $why_choose_us_title_en = $rstate->real_escape_string($_POST['why_choose_us_title_en']);
            $why_choose_us_title_ar = $rstate->real_escape_string($_POST['why_choose_us_title_ar']);
            $why_choose_us_description_ar = $rstate->real_escape_string($_POST['why_choose_us_description_ar']);
            $why_choose_us_description_en = $rstate->real_escape_string($_POST['why_choose_us_description_en']);

            $why_choose_us_description_json = json_encode([
                "en" => $why_choose_us_description_en,
                "ar" => $why_choose_us_description_ar
            ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);

            $why_choose_us_title_json = json_encode([
                "en" => $why_choose_us_title_en,
                "ar" => $why_choose_us_title_ar
            ], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS);

            $target_dir = dirname(dirname(__FILE__)) . "/images/website/";
            $url = "images/website/";
            $temp = explode(".", $_FILES["why_choose_us_img"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["why_choose_us_img"]["name"] != '') {

                move_uploaded_file($_FILES["why_choose_us_img"]["tmp_name"], $target_file);
                $table = "tbl_why_choose_us";
                $field = array(
                    'title' => $why_choose_us_title_json,
                    'description' => $why_choose_us_description_json,
                    'img' => $url,
                    'background_color' => $why_choose_us_bg,

                );
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Why choose Us Data Update Successfully!!", "message" => "Why choose Us  section!", "action" => "list_why_choose_us.php");
                }
            } else {
                $table = "tbl_why_choose_us";
                $field = array(

                    'title' => $why_choose_us_title_json,
                    'description' => $why_choose_us_description_json,
                    'background_color' => $why_choose_us_bg,

                );
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Why choose Us Data Update Successfully!!", "message" => "Why choose Us Data section!", "action" => "list_why_choose_us.php");
                }
            }
        } else if ($_POST['type'] == 'delete_why_choose_us') {
            $id = $_POST['id'];


            $table = "tbl_why_choose_us";
            $where = "where id=" . $id . "";

            $h = new Estate();
            $check = $h->restaterestateDeleteData($where,  $table);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Why choose Us Data Deleted Successfully!!", "message" => "Why choose Us  section!", "action" => "list_why_choose_us.php");
            }
        } else if ($_POST['type'] == 'delete_property') {
            $id = $_POST['id'];
            $status = (int)$_POST['status'];

            $table = "tbl_property";
            $where = "where id=" . $id . "";

            $h = new Estate();
            $check = $h->restateupdateData(["is_deleted" => "$status"],  $table, $where);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Property Deleted Successfully!!", "message" => "Property  section!", "action" => "list_properties.php");
            }
        } else if ($_POST['type'] == 'delete_user') {
            $id = $_POST['id'];
            $status = (int)$_POST['status'];


            $table = "tbl_user";
            $where = "where id=" . $id . "";

            $h = new Estate();
            $check = $h->restateupdateData(["status" => "!$status"], $table, $where);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "User Deleted Successfully!!", "message" => "User  section!", "action" => "userlist.php");
            }
        } else if ($_POST['type'] == 'add_cancallation_policy') {

            $status = $_POST['status'];
            $is_recommended = $_POST['is_recommended'];
            $title_en = $_POST['policy_title_en'];
            $title_ar = $_POST['policy_title_ar'];
            $description_ar = htmlspecialchars(trim($_POST['policy_description_ar']));
            $description_en = htmlspecialchars(trim($_POST['policy_description_en']));

            $description_json = json_encode([
                "en" => $description_en,
                "ar" => $description_ar
            ], JSON_UNESCAPED_UNICODE);

            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);

            $field = array(
                'title',
                'description',
                'is_recommended',
                'status'
            );

            $table = "tbl_cancellation_policy";
            $data_values = array("$title_json", "$description_json", "$is_recommended", "$status");

            $h = new Estate();
            $check = $h->restateinsertdata($field, $data_values,  $table);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Cancellation Policy Added Successfully!!", "message" => "Cancellation Policy section!", "action" => "list_policies.php");
            }
        } else if ($_POST['type'] == 'edit_cancallation_policy') {
            $id = $_POST['id'];

            $status = $_POST['status'];
            $is_recommended = $_POST['is_recommended'];
            $title_en = $_POST['policy_title_en'];
            $title_ar = $_POST['policy_title_ar'];
            $description_ar = htmlspecialchars(trim($_POST['policy_description_ar']));
            $description_en = htmlspecialchars(trim($_POST['policy_description_en']));

            $description_json = json_encode([
                "en" => $description_en,
                "ar" => $description_ar
            ], JSON_UNESCAPED_UNICODE);

            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);

            $table = "tbl_cancellation_policy";
            $field = array(

                'title' => $title_json,
                'description' => $description_json,
                'is_recommended' => $is_recommended,
                'status' => $status,

            );
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Cancellation Policy Updated Successfully!!", "message" => "Cancellation Policy section!", "action" => "list_policies.php");
            }
        } else if ($_POST['type'] == 'add_cancel_reason') {

            $status = $_POST['status'];
            $reason_en = $_POST['reason_en'];
            $reason_ar = $_POST['reason_ar'];

            $reason_json = json_encode([
                "en" => $reason_en,
                "ar" => $reason_ar
            ], JSON_UNESCAPED_UNICODE);

            $field = array(
                'reason',

                'status'
            );

            $table = "tbl_cancel_reason";
            $data_values = array("$reason_json",  "$status");

            $h = new Estate();
            $check = $h->restateinsertdata($field, $data_values,  $table);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => " Booking Cancel Reason Added Successfully!!", "message" => "'Booking Cancel Reason section!", "action" => "list_cancel_reason.php");
            }
        } else if ($_POST['type'] == 'edit_cancel_reason') {
            $id = $_POST['id'];

            $status = $_POST['status'];
            $reason_en = $_POST['reason_en'];
            $reason_ar = $_POST['reason_ar'];

            $reason_json = json_encode([
                "en" => $reason_en,
                "ar" => $reason_ar
            ], JSON_UNESCAPED_UNICODE);

            $table = "tbl_cancel_reason";
            $field = array(

                'reason' => $reason_json,

                'status' => $status,

            );
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Booking Cancel Reason Updated Successfully!!", "message" => "'Booking Cancel Reason section!", "action" => "list_cancel_reason.php");
            }
        } else if ($_POST['type'] == 'add_user_cancel_reason') {

            $status = $_POST['status'];
            $reason_en = $_POST['reason_en'];
            $reason_ar = $_POST['reason_ar'];

            $reason_json = json_encode([
                "en" => $reason_en,
                "ar" => $reason_ar
            ], JSON_UNESCAPED_UNICODE);

            $field = array(
                'reason',

                'status'
            );

            $table = "tbl_user_cancel_reason";
            $data_values = array("$reason_json",  "$status");

            $h = new Estate();
            $check = $h->restateinsertdata($field, $data_values,  $table);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => " Booking Cancel Reason Added Successfully!!", "message" => "'Booking Cancel Reason section!", "action" => "list_user_cancel_reason.php");
            }
        } else if ($_POST['type'] == 'edit_user_cancel_reason') {
            $id = $_POST['id'];

            $status = $_POST['status'];
            $reason_en = $_POST['reason_en'];
            $reason_ar = $_POST['reason_ar'];

            $reason_json = json_encode([
                "en" => $reason_en,
                "ar" => $reason_ar
            ], JSON_UNESCAPED_UNICODE);

            $table = "tbl_user_cancel_reason";
            $field = array(

                'reason' => $reason_json,

                'status' => $status,

            );
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);
            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Booking Cancel Reason Updated Successfully!!", "message" => "'Booking Cancel Reason section!", "action" => "list_user_cancel_reason.php");
            }
        } else if ($_POST['type'] == 'add_payout_method') {

            $status = $_POST['status'];
            $name_en = $_POST['name_en'];
            $name_ar = $_POST['name_ar'];

            $name_json = json_encode([
                "en" => $name_en,
                "ar" => $name_ar
            ], JSON_UNESCAPED_UNICODE);

            $field = array(
                'name',

                'status',
                'img'
            );
            $target_dir = dirname(dirname(__FILE__)) . "/images/website/";
            $url = "images/website/";
            $temp = explode(".", $_FILES["slider_img"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);

            move_uploaded_file($_FILES["slider_img"]["tmp_name"], $target_file);
            $table = "tbl_payout_methods";
            $data_values = array("$name_json",  "$status", "$url");

            $h = new Estate();
            $check = $h->restateinsertdata($field, $data_values,  $table);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => " Payout Method  Added Successfully!!", "message" => "Payout Method  section!", "action" => "list_payout_method.php");
            }
        } else if ($_POST['type'] == 'edit_payout_method') {
            $id = $_POST['id'];

            $status = $_POST['status'];
            $name_en = $_POST['name_en'];
            $name_ar = $_POST['name_ar'];

            $name_json = json_encode([
                "en" => $name_en,
                "ar" => $name_ar
            ], JSON_UNESCAPED_UNICODE);
            $target_dir = dirname(dirname(__FILE__)) . "/images/website/";
            $url = "images/website/";
            $temp = explode(".", $_FILES["slider_img"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["slider_img"]["name"] != '') {

                move_uploaded_file($_FILES["slider_img"]["tmp_name"], $target_file);
                $table = "tbl_payout_methods";
                $field = array(

                    'name' => $name_json,

                    'status' => $status,
                    'img' => $url,

                );
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Payout Method  Updated Successfully!!", "message" => "Payout Method section!", "action" => "list_payout_method.php");
                }
            } else {
                $table = "tbl_payout_methods";
                $field = array(

                    'name' => $name_json,

                    'status' => $status,

                );
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Payout Method  Updated Successfully!!", "message" => "Payout Method section!", "action" => "list_payout_method.php");
                }
            }
        } elseif ($_POST["type"] == "add_category") {
            $okey = $_POST["status"];

            $target_dir = dirname(dirname(__FILE__)) . "/images/category/";
            $url = "images/category/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            $title_en = $rstate->real_escape_string($_POST["title_en"]);
            $title_ar = $rstate->real_escape_string($_POST["title_ar"]);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);
            move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
            $table = "tbl_category";
            $field_values = ["img", "status", "title"];
            $data_values = ["$url", "$okey", "$title_json"];

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Category Add Successfully!!",
                    "message" => "Category section!",
                    "action" => "list_category.php",
                ];
            }
        } elseif ($_POST["type"] == "add_country") {
            $okey = $_POST["status"];
            $title = $rstate->real_escape_string($_POST["title"]);
            $target_dir = dirname(dirname(__FILE__)) . "/images/country/";
            $url = "images/country/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);

            move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
            $table = "tbl_country";
            $field_values = ["img", "status", "title"];
            $data_values = ["$url", "$okey", "$title"];

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Country Add Successfully!!",
                    "message" => "Country section!",
                    "action" => "list_country.php",
                ];
            }
        } elseif ($_POST["type"] == "add_extra") {
            $status = $_POST["status"];
            $pid = $_POST['property'];
            $pano = $_POST['pano'];
            $target_dir = dirname(dirname(__FILE__)) . "/images/property/";
            $url = "images/property/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = uniqid() . round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);

            move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
            $table = "tbl_extra";
            $field_values = ["img", "status", "pid", "pano"];
            $data_values = ["$url", "$status", "$pid", "$pano"];

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Extra Image Add Successfully!!",
                    "message" => "Extra Image section!",
                    "action" => "list_extra.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_extra") {
            $status = $_POST["status"];
            $pid = $_POST['property'];
            $id = $_POST['id'];
            $pano = $_POST['pano'];
            $target_dir = dirname(dirname(__FILE__)) . "/images/property/";
            $url = "images/property/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = uniqid() . round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["cat_img"]["name"] != "") {

                move_uploaded_file(
                    $_FILES["cat_img"]["tmp_name"],
                    $target_file
                );
                $table = "tbl_extra";
                $field = ["status" => $status, "img" => $url, "pid" => $pid, "pano" => $pano];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Extra Image Update Successfully!!",
                        "message" => "Extra Image section!",
                        "action" => "list_extra.php",
                    ];
                }
            } else {
                $table = "tbl_extra";
                $field = ["status" => $status, "pid" => $pid, "pano" => $pano];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Extra Image Update Successfully!!",
                        "message" => "Extra Image section!",
                        "action" => "list_extra.php",
                    ];
                }
            }
        } elseif ($_POST["type"] == "add_gal") {
            $status = $_POST["status"];
            $pid = $_POST['property'];
            $cid = $_POST['galcat'];
            $title = $rstate->real_escape_string($_POST["title"]);
            $target_dir = dirname(dirname(__FILE__)) . "/images/gallery/";
            $url = "images/gallery/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);

            move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
            $table = "tbl_gallery";
            $field_values = ["img", "status", "cat_id", "pid"];
            $data_values = ["$url", "$status", "$cid", "$pid"];

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Gallery Add Successfully!!",
                    "message" => "Gallery section!",
                    "action" => "list_gal.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_gal") {
            $status = $_POST["status"];
            $pid = $_POST['property'];
            $cid = $_POST['galcat'];
            $id = $_POST['id'];
            $target_dir = dirname(dirname(__FILE__)) . "/images/gallery/";
            $url = "images/gallery/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["cat_img"]["name"] != "") {

                move_uploaded_file(
                    $_FILES["cat_img"]["tmp_name"],
                    $target_file
                );
                $table = "tbl_gallery";
                $field = ["status" => $status, "img" => $url, "cat_id" => $cid, "pid" => $pid];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Gallery Update Successfully!!",
                        "message" => "Gallery section!",
                        "action" => "list_gal.php",
                    ];
                }
            } else {
                $table = "tbl_gallery";
                $field = ["status" => $status, "cat_id" => $cid, "pid" => $pid];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Gallery Update Successfully!!",
                        "message" => "Gallery section!",
                        "action" => "list_gal.php",
                    ];
                }
            }
        } elseif ($_POST["type"] == "add_property") {
            $status = $_POST["status"];
            $plimit = $_POST['plimit'];
            $pbuysell = 1;
            $facility = implode(',', $_POST['facility']);
            $ptype = $_POST['ptype'];
            $beds = $_POST['beds'];
            $bathroom = $_POST['bathroom'];
            $sqft = $_POST['sqft'];
            $user_id = '0';
            $policy = $_POST['propPrivacy'];
            $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
            $updated_at = $date->format('Y-m-d H:i:s');
            $up_at = $date->format('Y-m-d ');


            $price = $_POST['prop_price'];
            $government = $_POST['pgov'];
            $security_deposit = $_POST['prop_security'];
            $max_days = $_POST['max_day'] == '' ? 0 : $_POST['max_day'];
            $min_days = $_POST['min_day'] == '' ? 0 : $_POST['min_day'];
            $google_maps_url = $_POST['mapurl'];
            $propowner = $_POST['propowner'];
            $period = $_POST['period'];
            $featured = $_POST['featured'];

            $title_en = $rstate->real_escape_string($_POST["title_en"]);
            $address_en = $rstate->real_escape_string($_POST["address_en"]);
            $description_en = $rstate->real_escape_string($_POST["description_en"]);
            $guest_rules_en  = $rstate->real_escape_string($_POST["guest_rules_en"]);
            $compound_name_en = $rstate->real_escape_string($_POST["compound_name_en"]);
            $floor_en = $rstate->real_escape_string($_POST["floor_en"]);
            $city_en = $rstate->real_escape_string($_POST["city_en"]);


            $title_ar = $rstate->real_escape_string($_POST["title_ar"]);
            $address_ar = $rstate->real_escape_string($_POST["address_ar"]);
            $description_ar = $rstate->real_escape_string($_POST["description_ar"]);
            $guest_rules_ar = $rstate->real_escape_string($_POST["guest_rules_ar"]);
            $compound_name_ar = $rstate->real_escape_string($_POST["compound_name_ar"]);
            $floor_ar = $rstate->real_escape_string($_POST["floor_ar"]);
            $city_ar = $rstate->real_escape_string($_POST["city_ar"]);


            $guest_rules_json = json_encode([
                "en" => $guest_rules_en,
                "ar" => $guest_rules_ar
            ], JSON_UNESCAPED_UNICODE);



            $compound_name_json = json_encode([
                "en" => $compound_name_en,
                "ar" => $compound_name_ar
            ], JSON_UNESCAPED_UNICODE);



            $floor_json = json_encode([
                "en" => $floor_en,
                "ar" => $floor_ar
            ], JSON_UNESCAPED_UNICODE);


            $city_json = json_encode([
                "en" => $city_en,
                "ar" => $city_ar
            ], JSON_UNESCAPED_UNICODE);
            $description_json = json_encode([
                "en" => $description_en,
                "ar" => $description_ar
            ], JSON_UNESCAPED_UNICODE);
            $address_json = json_encode([
                "en" => $address_en,
                "ar" => $address_ar
            ], JSON_UNESCAPED_UNICODE);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);

            $date_ranges = isset($_POST['excluded_dates']) ? json_decode($_POST['excluded_dates'], true) : null;
            $inc_value_ranges = isset($_POST['priced_ranges']) ? json_decode($_POST['priced_ranges'], true) : null;

            // Allowed file types for images and videos
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'];

            // Initialize arrays for image and video URLs
            $imageUrls = [];
            $videoUrls = [];

            // Directories for storing images and videos
            $uploadDirImages = dirname(dirname(__FILE__)) . "/images/property/";
            $uploadDirVideos = dirname(dirname(__FILE__)) . "/videos/property/";
            $latitude = null;
            $longitude = null;
            $res = expandShortUrl($google_maps_url);

            if ($res['status']) {
                $cordinates = validateAndExtractCoordinates($res['response']);
                if ($cordinates['status']) {
                    // Location Cordinations
                    $latitude = $cordinates['latitude'];
                    $longitude = $cordinates['longitude'];
                } else {
                    $returnArr = generateResponse('false', $cordinates['response'],  400);
                }
            } else {
                $returnArr = generateResponse('false', $res['response'], 400);
            }


            // Handle image upload
            if (isset($_FILES['prop_img'])) {
                // Check if it's multiple images or a single image
                if (is_array($_FILES['prop_img']['name']) && count($_FILES['prop_img']['name']) >= 3) {
                    // Multiple images
                    foreach ($_FILES['prop_img']['tmp_name'] as $key => $tmpName) {
                        if ($_FILES['prop_img']['error'][$key] === UPLOAD_ERR_OK) {
                            $imageName = time() . '_' . $_FILES['prop_img']['name'][$key];
                            $destination = $uploadDirImages . $imageName;

                            // Validate image type
                            if (in_array($_FILES['prop_img']['type'][$key], $allowedImageTypes)) {
                                if (move_uploaded_file($tmpName, $destination)) {
                                    $imageUrls[] = 'images/property/' . $imageName;
                                } else {
                                    // Handle error if file couldn't be moved
                                    $returnArr = generateDashboardResponse(500, "false", "Failed to upload image: " . $_FILES['prop_img']['name'][$key], "", "list_properties.php");
                                }
                            } else {
                                // Handle invalid image type
                                $returnArr = generateDashboardResponse(400, "false", "Invalid image type: " . $_FILES['prop_img']['name'][$key], "", "list_properties.php");
                            }
                        } else {
                            // Handle error during file upload
                            $returnArr = generateDashboardResponse(400, "false", "Error uploading image: " . $_FILES['prop_img']['name'][$key], "", "list_properties.php");
                        }
                    }
                } else {
                    $returnArr = generateDashboardResponse(400, "false", "Please upload more than two images.", "", "list_properties.php");
                }
            } else {
                // No images uploaded
                $returnArr = generateDashboardResponse(400, "false", "No images uploaded.", "", "list_properties.php");
            }

            // Handle video upload
            if (isset($_FILES['prop_video'])) {
                $video = $_FILES['prop_video'];
                // Check for upload errors
                if ($video['error'] === UPLOAD_ERR_OK) {
                    // Validate video type
                    if (in_array($video['type'], $allowedVideoTypes)) {
                        // Generate a unique file name for the uploaded video
                        $videoName = time() . '_' . $video['name'];
                        $destination = $uploadDirVideos . $videoName;

                        // Move the uploaded video to the destination folder
                        if (move_uploaded_file($video['tmp_name'], $destination)) {
                            $videoUrls[] = 'videos/property/' . $videoName;
                        }
                    }
                }
            }

            // Convert arrays to comma-separated strings
            $imageUrlsString = implode(',', $imageUrls);
            $videoUrlsString = implode(',', $videoUrls);
            if (!isset($returnArr)) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Property Add Successfully!!",
                    "message" => "Property section!",
                    "action" => "list_properties.php",
                ];
                $table = "tbl_property";
                $field_values = ["created_at", "is_need_review", "updated_at", "image", "cancel_reason", "cancellation_policy_id", "period", "is_featured", "security_deposit", "government", "map_url", "is_approved",  "latitude", "longitude", "video", "guest_rules", "compound_name", "floor", "status", "title", "price", "address", "facility", "description", "beds", "bathroom", "sqrft",  "ptype",  "city",  "add_user_id", "pbuysell",  "plimit", "max_days", "min_days"];
                $data_values = ["$updated_at", "0", "$updated_at", "$imageUrlsString", "",  "$policy",  "$period", "$featured", "$security_deposit", "$government", "$google_maps_url", "0", "$latitude", "$longitude", "$videoUrlsString", "$guest_rules_json", "$compound_name_json", "$floor_json", "$status", "$title_json", "$price", "$address_json", "$facility", "$description_json", "$beds", "$bathroom", "$sqft",  "$ptype",  "$city_json",  "$propowner", "$pbuysell", "$plimit", "$max_days", "$min_days"];

                $h = new Estate();
                $check = $h->restateinsertdata_Api($field_values, $data_values, $table);
                $check_owner = $rstate->query("select * from tbl_property where  is_approved =1 and  add_user_id=" . $propowner . " and is_deleted =0")->num_rows;

                if ($check_owner  >= AppConstants::Property_Count) {
                    $rstate->query("UPDATE tbl_user SET is_owner = 0 WHERE id=" . $propowner);
                }
                if ($check) {
                    $table = "tbl_property";
                    $field = ["visibility" => $check];
                    $where = "where id=" . '?' . "";
                    $h = new Estate();
                    $where_conditions = [$check];
                    $new_res = $h->restateupdateData_Api($field, $table, $where, $where_conditions);
                    $result = true;
                    if (is_array($date_ranges)) {
                        $jsonResponse    =  exclude_ranges('en', $propowner, $check, $date_ranges);
                        $response = json_decode($jsonResponse, true); // true for associative array
                        $result = $response['result']; // "true" or "false"
                        if ($result == 'false') {
                            $rstate->query("Delete from  tbl_property  WHERE id=" . $check);
                            http_response_code(200);

                            $returnArr = generateDashboardResponse(200, "false", $response['response_message'], "", "list_properties.php");
                        }
                    }
                    if (is_array($inc_value_ranges)) {
                        $jsonResponse    =  add_specific_ranges_increased_value('en', $propowner, $check, $inc_value_ranges);
                        $response = json_decode($jsonResponse, true); // true for associative array
                        $result = $response['result']; // "true" or "false"
                        if ($result == 'false') {
                            $rstate->query("Delete from  tbl_property  WHERE id=" . $check);
                            http_response_code(200);

                            $returnArr = generateDashboardResponse(200, "false", $response['response_message'], "", "list_properties.php");
                        }
                    }
                    $owner = $rstate->query("select name from tbl_user where  id=" . $propowner . "")->fetch_assoc();
                    $government = $rstate->query("select name from tbl_government where  id=" . $government . "")->fetch_assoc();
                    $gov_name = json_decode($government['name'], true)['ar'];
                    $owner_name = $owner['name'];

                    // Subject with placeholder
                    $subject = '🏠 اضافة عقار جديد : ' . $title_ar;

                    // Body with placeholders (using HEREDOC for clean multi-line text)
                    $body = <<<EMAIL
                        السلام عليكم
                        تم إضافة عقار جديد ويحتاج إلى مراجعة أو تفعيل.
                        إليك تفاصيل العقار:

                        🏷️ عنوان العقار:  $address_ar
                        🆔 رقم العقار: $check
                        📍 الموقع: $city_ar.،. $gov_name
                        👤 اسم المُعلن: $owner_name
                        📅 تاريخ الإضافة: $up_at

                        لمراجعة العقار، تفضل بزيارة الرابط التالي:
                        👉 https://trent.com.eg/trent/add_properties.php?id=$check

                        مع فائق التحية
                        TRENT
                        EMAIL;
                    if ($result == true) {
                        sendPlainTextEmail($subject, $body);
                    }
                } else {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "false",
                        "title" => "Something went wrong!!",
                        "message" => "Property section!",
                        "action" => "list_properties.php",
                    ];
                }
            }
        } elseif ($_POST["type"] == "edit_property") {

            $id = $_POST['id'];
            $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
            $updated_at = $date->format('Y-m-d H:i:s');
            $status = $_POST["status"];
            $plimit = $_POST['plimit'];
            $pbuysell = 1;
            $facility = implode(',', $_POST['facility']);
            $ptype = $_POST['ptype'];
            $beds = $_POST['beds'];
            $bathroom = $_POST['bathroom'];
            $sqft = $_POST['sqft'];
            $policy = $_POST['propPrivacy'];
            $user_id = '0';
            $is_approved = $_POST['approved'];
            $existing_images = $_POST['existing_images'];

            $price = $_POST['prop_price'];
            $government = $_POST['pgov'];
            $security_deposit = $_POST['prop_security'];
            $max_days = $_POST['max_day'] == '' ? 0 : $_POST['max_day'];
            $min_days = $_POST['min_day'] == '' ? 0 : $_POST['min_day'];
            $google_maps_url = $_POST['mapurl'];
            $propowner = $_POST['propowner'];

            $period = $_POST['period'];
            $visibility = $_POST['visibility'] ?? 0;
            $featured = $_POST['featured'];
            $title_en = $rstate->real_escape_string($_POST["title_en"]);
            $address_en = $rstate->real_escape_string($_POST["address_en"]);
            $description_en = $rstate->real_escape_string(trim($_POST["description_en"]));
            $guest_rules_en  = $rstate->real_escape_string(trim($_POST["guest_rules_en"]));
            $compound_name_en = $rstate->real_escape_string($_POST["compound_name_en"]);
            $floor_en = $rstate->real_escape_string($_POST["floor_en"]);
            $city_en = $rstate->real_escape_string($_POST["city_en"]);


            $title_ar = $rstate->real_escape_string($_POST["title_ar"]);
            $address_ar = $rstate->real_escape_string($_POST["address_ar"]);
            $description_ar = $rstate->real_escape_string(trim($_POST["description_ar"]));
            $guest_rules_ar = $rstate->real_escape_string(trim($_POST["guest_rules_ar"]));
            $compound_name_ar = $rstate->real_escape_string($_POST["compound_name_ar"]);
            $floor_ar = $rstate->real_escape_string($_POST["floor_ar"]);
            $city_ar = $rstate->real_escape_string($_POST["city_ar"]);
            $cancel_reason = $rstate->real_escape_string($_POST["cancel_reason"]) ?? '';
            $need_review = 0;
            $date_ranges = isset($_POST['excluded_dates']) ? json_decode($_POST['excluded_dates'], true) : null;
            $inc_value_ranges = isset($_POST['priced_ranges']) ? json_decode($_POST['priced_ranges'], true) : null;

            if ($is_approved == '0' && $cancel_reason != '') {
                deny_property($cancel_reason,  $id, $propowner, $address_ar, $rstate);
                $need_review = 1;
            }
            if ($is_approved == '1') {
                approve_property($rstate, $propowner, $title_ar, $id);
            }
            if ($is_approved == '') {
                $result =  $rstate->query("select is_approved , is_need_review , cancel_reason from tbl_property where id = $id ")->fetch_assoc();
                $is_approved = $result['is_approved'];
                $cancel_reason = $result['cancel_reason'];
                $need_review = $result['is_need_review'];
            }
            $guest_rules_json = json_encode([
                "en" => $guest_rules_en,
                "ar" => $guest_rules_ar
            ], JSON_UNESCAPED_UNICODE);



            $compound_name_json = json_encode([
                "en" => $compound_name_en,
                "ar" => $compound_name_ar
            ], JSON_UNESCAPED_UNICODE);



            $floor_json = json_encode([
                "en" => $floor_en,
                "ar" => $floor_ar
            ], JSON_UNESCAPED_UNICODE);


            $city_json = json_encode([
                "en" => $city_en,
                "ar" => $city_ar
            ], JSON_UNESCAPED_UNICODE);
            $description_json = json_encode([
                "en" => $description_en,
                "ar" => $description_ar
            ], JSON_UNESCAPED_UNICODE);
            $address_json = json_encode([
                "en" => $address_en,
                "ar" => $address_ar
            ], JSON_UNESCAPED_UNICODE);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);


            // Allowed file types for images and videos
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/mkv'];

            // Initialize arrays for image and video URLs
            $imageUrls = [];
            $videoUrls = [];

            // Directories for storing images and videos
            $uploadDirImages = dirname(dirname(__FILE__)) . "/images/property/";
            $uploadDirVideos = dirname(dirname(__FILE__)) . "/videos/property/";
            $latitude = null;
            $longitude = null;
            $res = expandShortUrl($google_maps_url);

            if ($res['status']) {
                $cordinates = validateAndExtractCoordinates($res['response']);
                if ($cordinates['status']) {
                    // Location Cordinations
                    $latitude = $cordinates['latitude'];
                    $longitude = $cordinates['longitude'];
                } else {
                    $returnArr = generateResponse('false', $cordinates['response'],  400);
                }
            } else {
                $returnArr = generateResponse('false', $res['response'], 400);
            }


            // Handle image upload
            if (isset($_FILES['prop_img'])) {
                // Check if it's multiple images or a single image
                if (is_array($_FILES['prop_img']['name']) && count($_FILES['prop_img']['name']) >= 1) {
                    // Multiple images
                    foreach ($_FILES['prop_img']['tmp_name'] as $key => $tmpName) {
                        if ($_FILES['prop_img']['error'][$key] === UPLOAD_ERR_OK) {
                            $imageName = time() . '_' . $_FILES['prop_img']['name'][$key];
                            $destination = $uploadDirImages . $imageName;

                            // Validate image type
                            if (in_array($_FILES['prop_img']['type'][$key], $allowedImageTypes)) {
                                if (move_uploaded_file($tmpName, $destination)) {
                                    $imageUrls[] = 'images/property/' . $imageName;
                                } else {
                                    // Handle error if file couldn't be moved
                                    $returnArr = generateDashboardResponse(500, "false", "Failed to upload image: " . $_FILES['prop_img']['name'][$key], "", "list_properties.php");
                                }
                            } else {
                                // Handle invalid image type
                                $returnArr = generateDashboardResponse(400, "false", "Invalid image type: " . $_FILES['prop_img']['name'][$key], "", "list_properties.php");
                            }
                        }
                    }
                }
            }


            // Handle video upload
            if (isset($_FILES['prop_video'])) {
                $video = $_FILES['prop_video'];
                // Check for upload errors
                if ($video['error'] === UPLOAD_ERR_OK) {
                    // Validate video type
                    if (in_array($video['type'], $allowedVideoTypes)) {
                        // Generate a unique file name for the uploaded video
                        $videoName = time() . '_' . $video['name'];
                        $destination = $uploadDirVideos . $videoName;
                        // Move the uploaded video to the destination folder
                        if (move_uploaded_file($video['tmp_name'], $destination)) {
                            $videoUrls[] = 'videos/property/' . $videoName;
                        } else {
                            // Handle error if video couldn't be moved
                            $returnArr = generateDashboardResponse(500, "false", "Failed to upload video.", "", "list_properties.php");
                        }
                    } else {
                        // Handle invalid video type
                        $returnArr = generateDashboardResponse(400, "false", "Invalid video type.", "", "list_properties.php");
                    }
                }
            }

            // Convert arrays to comma-separated strings
            $imageUrlsString = implode(',', $imageUrls);
            $videoUrlsString = implode(',', $videoUrls);
            $table = "tbl_property";


            $field_values = [
                "security_deposit" => "$security_deposit",
                "cancellation_policy_id" => "$policy",

                "period" => "$period",
                "is_featured" => "$featured",
                "is_approved" => "$is_approved",
                "government" => "$government",
                "map_url" => "$google_maps_url",
                "latitude" => "$latitude",
                "longitude" => "$longitude",
                "guest_rules" => "$guest_rules_json",
                "compound_name" => "$compound_name_json",
                "floor" => "$floor_json",
                "status" => "$status",
                "title" => "$title_json",
                "price" => "$price",
                "address" => "$address_json",
                "facility" => "$facility",
                "description" => "$description_json",
                "beds" => "$beds",
                "bathroom" => "$bathroom",
                "sqrft" => "$sqft",
                "ptype" => "$ptype",
                "city" => "$city_json",
                "add_user_id" => "$propowner",
                "pbuysell" => "$pbuysell",
                "plimit" => "$plimit",
                "max_days" => "$max_days",
                "min_days" => "$min_days",
                "cancel_reason" => "$cancel_reason",
                'updated_at' => $updated_at,
                'is_need_review' => $need_review,
                'visibility' => $visibility
            ];
            if (!empty($imageUrls)) {
                $field_values["image"] =  $imageUrlsString . ',' . $existing_images;
            } else {
                $field_values["image"] =   $existing_images;
            }

            if (!empty($videoUrls)) {
                $field_values["video"] =  $videoUrlsString;
            } 
            if (is_array($inc_value_ranges) &&  !isset($returnArr)) {
                $jsonResponse    =  add_specific_ranges_increased_value('en', $propowner, $id, $inc_value_ranges);
                $response = json_decode($jsonResponse, true); // true for associative array
                $result = $response['result']; // "true" or "false"
                if ($result == 'false') {
                    http_response_code(200);
                    $returnArr = generateDashboardResponse(200, "false", $response['response_message'], "", "list_properties.php");
                }
            }
            if (is_array($date_ranges) && !isset($returnArr)) {
                $jsonResponse   =  exclude_ranges('en', $propowner, $id, $date_ranges);
                $response = json_decode($jsonResponse, true); // true for associative array
                $result = $response['result']; // "true" or "false"
                if ($result == 'false') {
                    http_response_code(200);
                    $returnArr = generateDashboardResponse(200, "false", $response['response_message'], "", "list_properties.php");
                }
            }
            if (!isset($returnArr)) {
                $where = "where id=" . '?' . "";
                $where_conditions  = [$id];
                $h = new Estate();
                $check = $h->restateupdateData_Api($field_values, $table, $where, $where_conditions);

                if ($check) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Property Update Successfully!!",
                        "message" => "Property section!",
                        "action" => "list_properties.php",
                    ];
                } else {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "false",
                        "title" => "Something went wrong!!",
                        "message" => "Property section!",
                        "action" => "list_properties.php",
                    ];
                }
            }
        } elseif ($_POST["type"] == "toggle_status") {
            $okey = $_POST["status"];
            $id = $_POST["id"];

            $table = "tbl_property";
            $field = ["status" => $okey];
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Status Updated Successfully!!",
                    "message" => "Status section!",
                    "action" => "list_properties.php",
                ];
            }
        } elseif ($_POST["type"] == "toggle_approval") {
            $okey = $_POST["status"];
            $id = $_POST["id"];
            $uid = $_POST["uid"];
            $title = $rstate->real_escape_string($_POST["property_title"]);

            $table = "tbl_property";
            $field = ["is_approved" => $okey, "is_need_review" => 0];
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);

            if ($check == 1) {
                approve_property($rstate, $uid, $title, $id);

                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Property Approved Successfully!!",
                    "message" => "APProval section!",
                    "action" => "pending_properties.php",
                ];
            }
        } elseif ($_POST["type"] == "toggle_message_approval") {
            $okey = $_POST["status"];
            $id = $_POST["id"];

            $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
            $updated_at = $date->format('Y-m-d H:i:s');
            $chat_data = $rstate->query("select * from tbl_messages where   id=" . $id .  "")->fetch_assoc();

            $uid  = $chat_data['receiver_id'];
            $sel = $rstate->query("select * from tbl_user where   id=" . $uid .  "")->fetch_assoc();

            $receiver_mobile   = $sel['mobile'] ?? '';
            $receiver_ccode   = $sel['ccode'] ?? '';

            $message = 'لديك رساله جديده';
            $title_ = 'لديك رساله جديده';
            $table = "tbl_messages";
            $field = ["is_approved" => $okey, 'updated_at' => $updated_at];
            $where = "where id=" . $id . "";
            $h = new Estate();

            $check = $h->restateupdateData($field, $table, $where);
            if ($okey ==  '1') {
                $result = sendMessage([$receiver_ccode . $receiver_mobile], $message);
                $firebase_notification = sendFirebaseNotification($title_, $message,  $uid, "chat_id", $chat_data['chat_id']);

                $title =  "Messages Approved Successfully!!";
            } else {
                $title =  "Messages rejected Successfully!!";
            }
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => $title,
                    "message" => "APProval section!",
                    "action" => "pending_chat.php",
                ];
            }
        } elseif ($_POST["type"] == "toggle_all_message_approval") {
            $okey = $_POST["status"];
            $id = $_POST["id"];

            $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
            $updated_at = $date->format('Y-m-d H:i:s');
            $query = "
            SELECT DISTINCT m.receiver_id, u.*  
            FROM tbl_messages m
            JOIN tbl_user u ON m.receiver_id = u.id
            WHERE m.chat_id = " . $id . "
        ";
            $message = 'لديك رساله جديده';
            $title_ = 'لديك رساله جديده';
            $db = $rstate->query($query);

            $table = "tbl_messages";
            $field = ["is_approved" => $okey, 'updated_at' => $updated_at];
            $where = "where chat_id=" . $id . "";
            $h = new Estate();

            $check = $h->restateupdateData($field, $table, $where);
            if ($okey ==  '1') {
                if ($db) {
                    while ($row = $db->fetch_assoc()) {
                        $result = sendMessage([$row['ccode'] . $row['mobile']], $message);
                        $firebase_notification = sendFirebaseNotification($title_, $message, $row['id'], "chat_id", $id);
                    }
                }
                $title =  "Message Approved Successfully!!";
            } else {
                $title =  "Message rejected Successfully!!";
            }
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => $title,
                    "message" => "APProval section!",
                    "action" => "pending_chat.php",
                ];
            }
        } elseif ($_POST["type"] == "approve_payout") {
            $id = $_POST["id"];
            $table = "tbl_payout_list";
            $field = ["payout_status" => "Completed"];
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);

            if ($check == 1) {

                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Payout Approved Successfully!!",
                    "message" => "APProval section!",
                    "action" => "pending_payout.php",
                ];
            }
        } elseif ($_POST["type"] == "approve_payout_and_generate_payout") {
            $selected_ids = $_POST["selected_ids"];
            $id_list = implode(",", array_map('intval', $selected_ids));

            $GLOBALS['rstate']->begin_transaction();

            $table = "tbl_payout_list";
            $field = ["payout_status" => "Completed"];
            foreach ($selected_ids as $id) {

                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
            }
            $GLOBALS['rstate']->commit();
            $sel = $rstate->query("
            SELECT 
    pl.requested_at,
    b.add_user_id AS owner_id,
    owner_user.name AS owner_name,
    b.id AS book_id,
    b.total,
    b.uid AS client_id,
    client_user.name AS client_name,
    pp.wallet_number,
    pp.bank_account_number,
    pp.bank_name,
    JSON_UNQUOTE(JSON_EXTRACT(pm.name, '$.ar')) AS method_name
FROM 
    tbl_payout_list pl
INNER JOIN tbl_book b ON pl.book_id = b.id
INNER JOIN tbl_user owner_user ON b.add_user_id = owner_user.id
INNER JOIN tbl_user client_user ON b.uid = client_user.id
INNER JOIN tbl_payout_profiles pp ON pl.profile_id = pp.id
INNER JOIN tbl_payout_methods pm ON pp.method_id = pm.id
WHERE 
    pl.id IN (" . $id_list . ")");
            if ($check == 1) {
                $data = [];
                $created_at = date('Y-m-d H:i:s');

                while ($row = $sel->fetch_assoc()) {
                    $data[] =   [
                        $row['client_name'],
                        $row['book_id'],
                        $row['wallet_number'],
                        $row['method_name'],
                        $row['bank_account_number'],
                        $row['bank_name'],
                        $row['owner_name'],
                        $row['total'],
                        $row['requested_at'],
                        $created_at
                    ];
                }
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Payout Approved Successfully!!",
                    "message" => "APProval section!",
                    "action" => "pending_payout.php",
                ];
                downloadCSV(
                    $arabicHeaders = [
                        'اسم العميل',
                        'رقم الحجز',
                        'رقم الواليت',
                        'طريقة السحب (واليت او بنك)',
                        'رقم حساب البنك',
                        'اسم البنك',
                        'اسم مالك العقار بشكل كامل',
                        'القيمة',
                        'تاريخ طلب السحب',
                        'تاريخ اليوم'
                    ],
                    $data
                );
            }
        } elseif ($_POST["type"] == "deny_reason") {
            $id = $_POST["id"];
            $uid = $_POST["uid"];
            $reason = $_POST["reason"];
            $title = $rstate->real_escape_string($_POST["property_title"]);

            $check =  deny_property($reason,  $id, $uid, $title, $rstate);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Deny Reason Added Successfully!!",
                    "message" => "Deny section!",
                    "action" => "pending_properties.php",
                ];
            }
        } elseif ($_POST["type"] == "delete_rating") {
            $id = $_POST["id"];
            $table = "tbl_rating";
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restaterestateDeleteData($where,  $table);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Rating Deleted Successfully!!",
                    "message" => "Rating section!",
                    "action" => "rating_list.php",
                ];
            }
        } elseif ($_POST["type"] == "deny_payout_reason") {
            $id = $_POST["id"];
            $reason = $_POST["reason"];
            $title = $rstate->real_escape_string($_POST["property_title"]);
            $table = "tbl_payout_list";
            $field = ["cancel_reason" => $reason, "payout_status" => "Rejected"];
            $where = "where id=" . $id . "";
            $uid = $_POST["uid"];
            $sel = $rstate->query("select * from tbl_user where   id=" . $uid .  "")->fetch_assoc();

            $new_mobile   = $sel['mobile'];
            $ccode   = $sel['ccode'];
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);

            // Create the message
            $message = "عذراً، تم رفض طلب سحب أموال العقار ($title) للسبب التالي:  ($reason)  
يمكنك مراجعة بيانات الطلب والمحاولة مرة أخرى من خلال حسابك في موقع أو تطبيق ت-رينت  
مع خالص تحيات فريق ت-رينت";
            $result = sendMessage([$ccode . $new_mobile], $message);



            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Deny Reason Added Successfully!!",
                    "message" => "Deny section!",
                    "action" => "pending_payout.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_privacy") {
            $id = $_POST["id"];
            $privacy_ar = $_POST["privacy_ar"];
            $privacy_en = $_POST["privacy_en"];
            $privacy_json = json_encode([
                "en" => $privacy_en,
                "ar" => $privacy_ar
            ], JSON_UNESCAPED_UNICODE);


            $table = "tbl_setting";
            $field = ["privacy_policy" => $privacy_json];
            $where = "where id=" . '?' . "";
            $h = new Estate();
            $check = $h->restateupdateData_Api($field, $table, $where, [$id]);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Privacy Updated Successfully!!",
                    "message" => "Privacy section!",
                    "action" => "add_privacy_policy.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_confidence_booking") {
            $id = $_POST["id"];
            $confidence_booking_ar = $_POST["confidence_booking_ar"];
            $confidence_booking_en = $_POST["confidence_booking_en"];
            $confidence_booking_json = json_encode([
                "en" => $confidence_booking_en,
                "ar" => $confidence_booking_ar
            ], JSON_UNESCAPED_UNICODE);


            $table = "tbl_setting";
            $field = ["confidence_booking" => $confidence_booking_json];
            $where = "where id=" . '?' . "";
            $h = new Estate();
            $check = $h->restateupdateData_Api($field, $table, $where, [$id]);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Confidence Booking Updated Successfully!!",
                    "message" => "Confidence Booking section!",
                    "action" => "add_confidence_booking.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_terms") {
            $id = $_POST["id"];
            $terms_ar = $_POST["terms_ar"];
            $terms_en = $_POST["terms_en"];
            $terms_json = json_encode([
                "en" => $terms_en,
                "ar" => $terms_ar
            ], JSON_UNESCAPED_UNICODE);

            $table = "tbl_setting";
            $field = ["terms_and_conditions" => $terms_json];

            $where = "where id=" . '?' . "";
            $h = new Estate();
            $check = $h->restateupdateData_Api($field, $table, $where, [$id]);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Terms And Conditions  Updated Successfully!!",
                    "message" => "Terms And Conditions  section!",
                    "action" => "add_terms_and_conditions.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_guidelines") {
            $id = $_POST["id"];
            $guidelines_ar = $_POST["guidelines_ar"];
            $guidelines_en = $_POST["guidelines_en"];
            $guidelines_json = json_encode([
                "en" => $guidelines_en,
                "ar" => $guidelines_ar
            ], JSON_UNESCAPED_UNICODE);


            $table = "tbl_setting";
            $field = ["guidelines" => $guidelines_json];
            $where = "where id=" . '?' . "";
            $h = new Estate();
            $check = $h->restateupdateData_Api($field, $table, $where, [$id]);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Guidelines Updated Successfully!!",
                    "message" => "guidelines section!",
                    "action" => "add_guidelines.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_host_terms") {
            $id = $_POST["id"];
            $terms_ar = $_POST["host_terms_ar"];
            $terms_en = $_POST["host_terms_en"];
            $terms_json = json_encode([
                "en" => $terms_en,
                "ar" => $terms_ar
            ], JSON_UNESCAPED_UNICODE);

            $table = "tbl_setting";
            $field = ["host_terms_and_conditions" => $terms_json];

            $where = "where id=" . '?' . "";
            $h = new Estate();
            $check = $h->restateupdateData_Api($field, $table, $where, [$id]);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Host Terms And Conditions  Updated Successfully!!",
                    "message" => "Host Terms And Conditions  section!",
                    "action" => "add_host_terms_and_conditions.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_listing_guidelines") {
            $id = $_POST["id"];
            $guidelines_ar = $_POST["listing_guidelines_ar"];
            $guidelines_en = $_POST["listing_guidelines_en"];
            $guidelines_json = json_encode([
                "en" => $guidelines_en,
                "ar" => $guidelines_ar
            ], JSON_UNESCAPED_UNICODE);


            $table = "tbl_setting";
            $field = ["listing_guidelines" => $guidelines_json];
            $where = "where id=" . '?' . "";
            $h = new Estate();
            $check = $h->restateupdateData_Api($field, $table, $where, [$id]);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Listing Guidelines Updated Successfully!!",
                    "message" => "listing guidelines section!",
                    "action" => "add_listing_guidelines.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_host_cancellation_policies") {
            $id = $_POST["id"];
            $terms_ar = $_POST["host_cancellation_policies_ar"];
            $terms_en = $_POST["host_cancellation_policies_en"];
            $terms_json = json_encode([
                "en" => $terms_en,
                "ar" => $terms_ar
            ], JSON_UNESCAPED_UNICODE);

            $table = "tbl_setting";
            $field = ["host_cancellation_policies" => $terms_json];

            $where = "where id=" . '?' . "";
            $h = new Estate();
            $check = $h->restateupdateData_Api($field, $table, $where, [$id]);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Host Cancellation Policies  Updated Successfully!!",
                    "message" => "Host Cancellation Policies  section!",
                    "action" => "add_host_cancellation_policies.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_guest_cancellation_policies") {
            $id = $_POST["id"];
            $guidelines_ar = $_POST["guest_cancellation_policies_ar"];
            $guidelines_en = $_POST["guest_cancellation_policies_en"];
            $guidelines_json = json_encode([
                "en" => $guidelines_en,
                "ar" => $guidelines_ar
            ], JSON_UNESCAPED_UNICODE);


            $table = "tbl_setting";
            $field = ["cancellation_policies" => $guidelines_json];
            $where = "where id=" . '?' . "";
            $h = new Estate();
            $check = $h->restateupdateData_Api($field, $table, $where, [$id]);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Guest Cancellation Policies Updated Successfully!!",
                    "message" => "Guest Cancellation Policies section!",
                    "action" => "add_guest_cancellation_policies.php",
                ];
            }
        } elseif ($_POST["type"] == "add_facility") {
            $okey = $_POST["status"];
            $title_ar = $rstate->real_escape_string($_POST["title_ar"]);
            $title_en = $rstate->real_escape_string($_POST["title_en"]);
            $target_dir = dirname(dirname(__FILE__)) . "/images/facility/";
            $url = "images/facility/";
            $temp = explode(".", $_FILES["facility_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);

            move_uploaded_file($_FILES["facility_img"]["tmp_name"], $target_file);
            $table = "tbl_facility";
            $field_values = ["img", "status", "title"];
            $data_values = ["$url", "$okey", "$title_json"];

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Facility Add Successfully!!",
                    "message" => "Facility section!",
                    "action" => "list_facility.php",
                ];
            }
        } elseif ($_POST["type"] == "add_slider") {
            $okey = $_POST["status"];
            $title_ar = $rstate->real_escape_string($_POST["title_ar"]);
            $title_en = $rstate->real_escape_string($_POST["title_en"]);
            $target_dir = dirname(dirname(__FILE__)) . "/images/slider/";
            $propowner = implode(',', $_POST['propowner'] ?? []);
            $ptype = $_POST['ptype'] ?? null;
            $pgov = $_POST['pgov'] ?? null;
            $city_name = isset($_POST["pcity"]) ? implode('\x1F', $_POST["pcity"] ?? []) : '';
            $compound_name = isset($_POST["pcompound"]) ? implode('\x1F', $_POST["pcompound"] ?? []) : '';
            $url = "images/slider/";
            $temp = explode(".", $_FILES["slider_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);

            move_uploaded_file($_FILES["slider_img"]["tmp_name"], $target_file);

            $url_ar = "images/slider/";
            $temp_ar = explode(".", $_FILES["slider_ar_img"]["name"]);
            $newfilename_ar = 'ar_' . round(microtime(true)) . "." . end($temp_ar);
            $target_file_ar = $target_dir . basename($newfilename_ar);
            $url_ar = $url_ar . basename($newfilename_ar);

            move_uploaded_file($_FILES["slider_ar_img"]["tmp_name"], $target_file_ar);


            $table = "tbl_slider";
            $field_values = ["img", "img_ar", "status", "title", "uid", "government_id", "cat_id", "compound_name", "city_name"];
            $data_values = ["$url", "$url_ar", "$okey", "$title_json", $propowner, $pgov, $ptype, $city_name,  $compound_name];

            $h = new Estate();
            $check = $h->restateinsertdata($field_values, $data_values, $table);
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Slider Add Successfully!!",
                    "message" => "Slider section!",
                    "action" => "list_slider.php",
                ];
            }
        } elseif ($_POST["type"] == "edit_category") {
            $okey = $_POST["status"];
            $id = $_POST["id"];
            $title_en = $rstate->real_escape_string($_POST["title_en"]);
            $title_ar = $rstate->real_escape_string($_POST["title_ar"]);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);
            $target_dir = dirname(dirname(__FILE__)) . "/images/category/";
            $url = "images/category/";

            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["cat_img"]["name"] != "") {

                move_uploaded_file(
                    $_FILES["cat_img"]["tmp_name"],
                    $target_file
                );
                $table = "tbl_category";
                $field = ["status" => $okey, "img" => $url, "title" => $title_json];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Category Update Successfully!!",
                        "message" => "Category section!",
                        "action" => "list_category.php",
                    ];
                }
            } else {
                $table = "tbl_category";
                $field = ["status" => $okey, "title" => $title_json];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Category Update Successfully!!",
                        "message" => "Category section!",
                        "action" => "list_category.php",
                    ];
                }
            }
        } elseif ($_POST["type"] == "edit_country") {
            $okey = $_POST["status"];
            $id = $_POST["id"];
            $title = $rstate->real_escape_string($_POST["title"]);
            $target_dir = dirname(dirname(__FILE__)) . "/images/country/";
            $url = "images/country/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["cat_img"]["name"] != "") {

                move_uploaded_file(
                    $_FILES["cat_img"]["tmp_name"],
                    $target_file
                );
                $table = "tbl_country";
                $field = ["status" => $okey, "img" => $url, "title" => $title];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Country Update Successfully!!",
                        "message" => "Country section!",
                        "action" => "list_country.php",
                    ];
                }
            } else {
                $table = "tbl_country";
                $field = ["status" => $okey, "title" => $title];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Country Update Successfully!!",
                        "message" => "Country section!",
                        "action" => "list_country.php",
                    ];
                }
            }
        } elseif ($_POST["type"] == "edit_facility") {
            $okey = $_POST["status"];
            $id = $_POST["id"];
            $title_ar = $rstate->real_escape_string($_POST["title_ar"]);
            $title_en = $rstate->real_escape_string($_POST["title_en"]);
            $target_dir = dirname(dirname(__FILE__)) . "/images/facility/";
            $url = "images/facility/";
            $temp = explode(".", $_FILES["facility_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);

            if ($_FILES["facility_img"]["name"] != "") {

                move_uploaded_file(
                    $_FILES["facility_img"]["tmp_name"],
                    $target_file
                );
                $table = "tbl_facility";
                $field = ["status" => $okey, "img" => $url, "title" => $title_json];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Facility Update Successfully!!",
                        "message" => "Facility section!",
                        "action" => "list_facility.php",
                    ];
                }
            } else {
                $table = "tbl_facility";
                $field = ["status" => $okey, "title" => $title_json];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Facility Update Successfully!!",
                        "message" => "Facility section!",
                        "action" => "list_facility.php",
                    ];
                }
            }
        } elseif ($_POST["type"] == "edit_slider") {
            $okey = $_POST["status"];
            $id = $_POST["id"];
            $title_ar = $rstate->real_escape_string($_POST["title_ar"]);
            $title_en = $rstate->real_escape_string($_POST["title_en"]);
            $target_dir = dirname(dirname(__FILE__)) . "/images/slider/";
            $url = "images/slider/";
            $temp = explode(".", $_FILES["slider_img"]["name"]);
            $propowner = implode(',', $_POST['propowner'] ?? []);
            $ptype = $_POST['ptype'] ?? Null;
            $pgov = $_POST['pgov'] ?? Null;
            $city_name = isset($_POST["pcity"]) ? implode('\x1F', $_POST["pcity"] ?? []) : '';
            $compound_name = isset($_POST["pcompound"]) ? implode('\x1F', $_POST["pcompound"] ?? []) : '';
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);
            $url_ar = "images/slider/";
            $temp_ar = explode(".", $_FILES["slider_ar_img"]["name"]);
            $newfilename_ar = 'ar_' . round(microtime(true)) . "." . end($temp_ar);
            $target_file_ar = $target_dir . basename($newfilename_ar);
            $url_ar = $url_ar . basename($newfilename_ar);


            if ($_FILES["slider_ar_img"]["name"] != "" && $_FILES["slider_ar_img"]["name"] != "") {

                move_uploaded_file($_FILES["slider_ar_img"]["tmp_name"], $target_file_ar);
                move_uploaded_file(
                    $_FILES["slider_img"]["tmp_name"],
                    $target_file
                );
                $table = "tbl_slider";
                $field = [
                    "status" => $okey,
                    "img" => $url,

                    "img_ar" => $url_ar,
                    "title" => $title_json,
                    "compound_name" => $compound_name,
                    "city_name" => $city_name,
                    "cat_id" => $ptype,
                    "government_id" => $pgov,
                    "uid" => "$propowner"
                ];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateDatanull_Api($field, $table, $where);

                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Slider Update Successfully!!",
                        "message" => "Slider section!",
                        "action" => "list_slider.php",
                    ];
                }
            } else if ($_FILES["slider_img"]["name"] != "") {

                move_uploaded_file(
                    $_FILES["slider_img"]["tmp_name"],
                    $target_file
                );
                $table = "tbl_slider";
                $field = [
                    "status" => $okey,
                    "img" => $url,
                    "title" => $title_json,
                    "compound_name" => $compound_name,
                    "city_name" => $city_name,
                    "cat_id" => $ptype,
                    "government_id" => $pgov,
                    "uid" => "$propowner"
                ];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateDatanull_Api($field, $table, $where);

                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Slider Update Successfully!!",
                        "message" => "Slider section!",
                        "action" => "list_slider.php",
                    ];
                }
            } else if ($_FILES["slider_ar_img"]["name"] != "") {

                move_uploaded_file($_FILES["slider_ar_img"]["tmp_name"], $target_file_ar);

                $table = "tbl_slider";
                $field = [
                    "status" => $okey,
                    "img_ar" => $url_ar,
                    "title" => $title_json,
                    "compound_name" => $compound_name,
                    "city_name" => $city_name,
                    "cat_id" => $ptype,
                    "government_id" => $pgov,
                    "uid" => "$propowner"
                ];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateDatanull_Api($field, $table, $where);

                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Slider Update Successfully!!",
                        "message" => "Slider section!",
                        "action" => "list_slider.php",
                    ];
                }
            } else {
                $table = "tbl_slider";
                $field = [
                    "status" => $okey,
                    "title" => $title_json,
                    "compound_name" => $compound_name,
                    "city_name" => $city_name,
                    "cat_id" => $ptype,
                    "government_id" => $pgov,
                    "uid" => "$propowner"
                ];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateDatanull_Api($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Slider Update Successfully!!",
                        "message" => "Slider section!",
                        "action" => "list_slider.php",
                    ];
                }
            }
        } elseif ($_POST["type"] == "Earning_report") {
            $table = "tbl_book";
            $status = $_POST["from_date"];
            $status = $_POST["to_date"];
            $query = "SELECT * FROM tbl_book WHERE book_status IN ('Check_in', 'Confirmed')";

            // Add date filter if provided
            if (isset($_POST['from_date']) && !empty($_POST['from_date'])) {
                $from_date = $rstate->real_escape_string($_POST['from_date']);
                $query .= " AND book_date >= '$from_date'";
            }

            if (isset($_POST['to_date']) && !empty($_POST['to_date'])) {
                $to_date = $rstate->real_escape_string($_POST['to_date']);
                $query .= " AND book_date <= '$to_date'";
            }
            $sel = $rstate->query($query);

            $data = [];

            while ($row = $sel->fetch_assoc()) {
                $client_id = $row['uid'];
                $client_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$client_id)->fetch_assoc();

                $owner_id = $row['add_user_id'];
                $owner_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$owner_id)->fetch_assoc();

                $data[] = [
                    $row['id'],
                    $row['prop_id'],
                    $row['trent_fees'],
                    $row['service_fees'],
                    $row['subtotal'],
                    $row['total'],
                    $row['book_date'],
                    $client_data['name'] ?? '',
                    ($client_data['ccode'] ?? '') . ($client_data['mobile'] ?? ''),
                    $owner_data['name'] ?? '',
                    ($owner_data['ccode'] ?? '') . ($owner_data['mobile'] ?? ''),
                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Report Exported Successfully!!",
                "message" => "APProval section!",
                "action" => "pending_payout.php",
            ];

            downloadXLS(
                // Updated headers to match all data fields
                $arabicHeaders = [
                    'رقم الحجز',         // Booking ID
                    'رقم التعريفي للعقار',         // Booking ID
                    'رسوم الإيجار',      // Rent fees
                    'رسوم الخدمة',       // Service fees
                    'المجموع الفرعي',    // Subtotal
                    'المجموع الكلي',    // Total
                    'تاريخ الحجز',       // Booking date
                    'اسم العميل',        // Client name
                    'اتصال العميل',      // Client contact
                    'اسم المالك',       // Owner name
                    'اتصال المالك',      // Owner contact
                ],
                $data
            );
        } elseif ($_POST["type"] == "Active_User_report") {
            $query = "SELECT 
                u.*,
                COUNT(b.id) AS booking_count,
                sum(b.total) AS booking_total

            FROM 
                tbl_user u
            LEFT JOIN 
                tbl_book b ON u.id = b.uid AND b.book_status IN ('Check_in', 'Confirmed')
            GROUP BY 
                u.id
            ORDER BY 
                booking_count DESC;";

            $sel = $rstate->query($query);

            $data = [];

            while ($row = $sel->fetch_assoc()) {
                $data[] =   [
                    $row['id'],
                    $row['name'],
                    $row['ccode'] . $row['mobile'],
                    $row['booking_count'],
                    number_format($row['booking_total'] ?? 0, 2, '.', '')
                ];
            }
            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Report Exported Successfully!!",
                "message" => "APProval section!",
                "action" => "pending_payout.php",
            ];
            downloadXLS(
                $arabicHeaders = [
                    'معرف المستخدم',           // User ID
                    'اسم المستخدم الكامل',     // User full name
                    'جوال المستخدم',           // User mobile
                    'عدد العقارات المحجوزة',   // Booking count (number of properties booked)
                    'المجموع الكلي'

                ],
                $data
            );
        } elseif ($_POST["type"] == "Active_Prop_report") {
            $query = "SELECT 
            u.*,
            COUNT(b.id) AS booking_count
            ,b.prop_title AS title,
            sum(b.total_day) AS days

        FROM 
            tbl_user u
        LEFT JOIN 
            tbl_book b ON u.id = b.add_user_id 
        where  
             b.book_status IN ('Check_in', 'Confirmed')
        GROUP BY 
             b.add_user_id
        ORDER BY 
            booking_count DESC;";
            $sel = $rstate->query($query);

            $data = [];

            while ($row = $sel->fetch_assoc()) {
                $data[] =   [
                    $row['id'],
                    json_decode($row['title'], true)['en'] ?? '',
                    $row['name'],
                    $row['ccode'] . $row['mobile'],
                    $row['booking_count'],
                    $row['days'],
                ];
            }
            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Report Exported Successfully!!",
                "message" => "APProval section!",
                "action" => "pending_payout.php",
            ];
            downloadXLS(
                $arabicHeaders = [
                    'معرف العقار',     // Property ID
                    'اسم العقار',      // Property name
                    'اسم المالك',      // Owner name
                    'جوال المالك',     // Owner mobile
                    'عدد الحجوزات',    // Booking count
                    'مجموع الايام'
                ],
                $data
            );
        } elseif ($_POST["type"] == "download_excel-template") {
            $query = "SELECT 
            u.name,u.ccode,u.mobile
        FROM 
            tbl_user u
            where u.status = 1 and u.verified = 1 
            ";
            $sel = $rstate->query($query);

            $data = [];

            while ($row = $sel->fetch_assoc()) {
                $data[] =   [

                    $row['ccode'] . $row['mobile'],
                    ''
                ];
            }
            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "template Exported Successfully!!",
                "message" => "APProval section!",
                "action" => "campings.php",
            ];
            downloadXLS(
                $arabicHeaders = [
                    'رقم الجوال',
                    'الرساله'
                ],
                $data
            );
        } elseif ($_POST["type"] == "export_properties_data") {
            $references = [];
            $approved = $_POST["approved"];
            // 1. Get all categories
            $categoryQuery = "SELECT id, title FROM tbl_category";
            $categoryResult = $rstate->query($categoryQuery);
            while ($cat = $categoryResult->fetch_assoc()) {
                $references['categories'][$cat['id']] = $cat['title']; // JSON string
            }

            // 2. Get all governments
            $governmentQuery = "SELECT id, name FROM tbl_government";
            $governmentResult = $rstate->query($governmentQuery);
            while ($gov = $governmentResult->fetch_assoc()) {
                $references['governments'][$gov['id']] = $gov['name']; // JSON string
            }

            // 3. Get all facilities
            $facilityQuery = "SELECT id, title FROM tbl_facility";
            $facilityResult = $rstate->query($facilityQuery);
            while ($fac = $facilityResult->fetch_assoc()) {
                $references['facilities'][$fac['id']] = $fac['title']; // JSON string
            }
            $query = "SELECT 
            id , title , image , price ,status,
            address ,description , beds , bathroom , sqrft , city	, created_at , plimit	, floor,
            security_deposit ,min_days	 ,max_days , guest_rules, video ,period ,is_featured,is_approved,
            compound_name,longitude ,latitude,map_url, cancellation_policy_id,cancel_reason , is_need_review , updated_at,is_deleted,
            ptype,facility,government,add_user_id
        FROM 
            tbl_property 
        where 
            is_approved = $approved
            ";
            $sel = $rstate->query($query);

            $data = [];

            while ($row = $sel->fetch_assoc()) {

                $owner_id = $row['add_user_id'];
                $owner_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$owner_id)->fetch_assoc();

                $data[] =   [

                    'id' => $row['id'] ?? '',
                    'owner_name' => $owner_data['name'] ?? '',
                    'owner_contact' => ($owner_data['ccode'] ?? '') . ($owner_data['mobile'] ?? ''),
                    'title_en' => getMultilingualValue($row['title'], 'en'),
                    'title_ar' => getMultilingualValue($row['title'], 'ar'),
                    'image' => $row['image'] ?? '',
                    'price' => $row['price'] ?? '',
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',
                    'address_en' => getMultilingualValue($row['address'], 'en'),
                    'address_ar' => getMultilingualValue($row['address'], 'ar'),
                    'description_en' => getMultilingualValue($row['description'], 'en'),
                    'description_ar' => getMultilingualValue($row['description'], 'ar'),
                    'beds' => $row['beds'] ?? '',
                    'bathroom' => $row['bathroom'] ?? '',
                    'sqrft' => $row['sqrft'] ?? '',
                    'city_en' => getMultilingualValue($row['city'], 'en'),
                    'city_ar' => getMultilingualValue($row['city'], 'ar'),
                    'created_at' => $row['created_at'] ?? '',
                    'plimit' => $row['plimit'] ?? '',
                    'floor_en' => getMultilingualValue($row['floor'], 'en'),
                    'floor_ar' => getMultilingualValue($row['floor'], 'ar'),
                    'security_deposit' => $row['security_deposit'] ?? '',
                    'min_days' => $row['min_days'] ?? '',
                    'max_days' => $row['max_days'] ?? '',
                    'guest_rules_en' => getMultilingualValue($row['guest_rules'], 'en'),
                    'guest_rules_ar' => getMultilingualValue($row['guest_rules'], 'ar'),
                    'video' => $row['video'] ?? '',
                    'period' => ($row['period'] ?? '') === 'm' ? 'monthly' : 'daily',
                    'is_featured' => $row['is_featured'] ?? '',
                    'is_approved' => $row['is_approved'] ?? '',
                    'compound_name_en' => getMultilingualValue($row['compound_name'], 'en'),
                    'compound_name_ar' => getMultilingualValue($row['compound_name'], 'ar'),
                    'longitude' => $row['longitude'] ?? '',
                    'latitude' => $row['latitude'] ?? '',
                    'map_url' => $row['map_url'] ?? '',
                    'cancellation_policy_id' => $row['cancellation_policy_id'] ?? '',
                    'cancel_reason' => $row['cancel_reason'] ?? '',
                    'is_need_review' => $row['is_need_review'] ?? '',
                    'updated_at' => $row['updated_at'] ?? '',
                    'is_deleted' => $row['is_deleted'] ?? '',
                    'facilities' => getFacilityNames($row['facility'] ?? '', $references),
                    'property_type_en' => getMultilingualReference($row['ptype'], 'categories', $references, 'en'),
                    'property_type_ar' => getMultilingualReference($row['ptype'], 'categories', $references, 'ar'),
                    'government_en' => getMultilingualReference($row['government'], 'governments', $references, 'en'),
                    'government_ar' => getMultilingualReference($row['government'], 'governments', $references, 'ar')
                ];
            }
            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "template Exported Successfully!!",
                "message" => "APProval section!",
                "action" => "campings.php",
            ];
            downloadXLS(

                // Updated headers
                $headers = [
                    'ID',
                    'Owner Name',
                    'Owner Mobile',
                    'Title (English)',
                    'Title (Arabic)',
                    'Image URL',
                    'Price',
                    'Status',
                    'Address (English)',
                    'Address (Arabic)',
                    'Description (English)',
                    'Description (Arabic)',
                    'Number of Beds',
                    'Number of Bathrooms',
                    'Square Footage',
                    'City (English)',
                    'City (Arabic)',
                    'Created Date',
                    'Price Limit',
                    'Floor (English)',
                    'Floor (Arabic)',
                    'Security Deposit',
                    'Minimum Stay (Days)',
                    'Maximum Stay (Days)',
                    'Guest Rules (English)',
                    'Guest Rules (Arabic)',
                    'Video URL',
                    'Rental Period',
                    'Is Featured?',
                    'Is Approved?',
                    'Compound Name (English)',
                    'Compound Name (Arabic)',
                    'Longitude',
                    'Latitude',
                    'Map URL',
                    'Cancellation Policy ID',
                    'Cancellation Reason',
                    'Needs Review?',
                    'Updated Date',
                    'Is Deleted?',
                    'Facilities',
                    'Property Type (English)',
                    'Property Type (Arabic)',
                    'Government (English)',
                    'Government (Arabic)'
                ],
                $data
            );
        } else if ($_POST["type"] == "export_booking_data") {
            $book_status = $_POST["book_status"];
            $query = "SELECT 
            id, book_date, prop_id, check_in, check_out, method_key, book_status, prop_price, total_day, prop_title,
            noguest, pay_status,total,confirmed_at, uid	 ,add_user_id ,cancel_by, cancle_reason  
        FROM 
            tbl_book
        WHERE 
            book_status = '$book_status'";
            $sel = $rstate->query($query);
            $data = [];

            while ($row = $sel->fetch_assoc()) {
                $client_id = $row['uid'];
                $client_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$client_id)->fetch_assoc();

                $owner_id = $row['add_user_id'];
                $owner_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$owner_id)->fetch_assoc();
                $cancel_by = $row['cancel_by'];
                $cancel_id = $row['cancle_reason '] ?? 0;
                $cancel_reason = ($cancel_by == "H")
                    ? $rstate->query("SELECT reason FROM tbl_cancel_reason WHERE id = $cancel_id")->fetch_assoc()
                    : $rstate->query("SELECT reason FROM tbl_user_cancel_reason WHERE id = $cancel_id")->fetch_assoc();

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'book_date' => $row['book_date'] ?? '',
                    'client_name' => $client_data['name'] ?? '',
                    'client_contact' => ($client_data['ccode'] ?? '') . ($client_data['mobile'] ?? ''),
                    'owner_name' => $owner_data['name'] ?? '',
                    'owner_contact' => ($owner_data['ccode'] ?? '') . ($owner_data['mobile'] ?? ''),
                    'book_date' => $row['book_date'] ?? '',
                    'prop_id' => $row['prop_id'] ?? '',
                    'check_in' => $row['check_in'] ?? '',
                    'check_out' => $row['check_out'] ?? '',
                    'method_key' => $row['method_key'] ?? '',
                    'book_status' => $row['book_status'] ?? '',
                    'prop_price' => $row['prop_price'] ?? '',
                    'total_day' => $row['total_day'] ?? '',
                    'noguest' => $row['noguest'] ?? '',
                    'pay_status' => $row['pay_status'] ?? '',
                    ...($book_status == 'Cancelled' ? [
                        'cancel_by' => $cancel_by === 'G' ? 'guest' : 'host',
                        'cancel_reason' => getMultilingualValue($cancel_reason ?? '', 'en'),
                    ] : []),
                    'title_en' => getMultilingualValue($row['prop_title'] ?? '', 'en'),
                    'title_ar' => getMultilingualValue($row['prop_title'] ?? '', 'ar'),
                    'total' => $row['total'] ?? '',
                    'confirmed_at' => $row['confirmed_at'] ?? '',
                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Bookings Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Updated headers with new booking fields
                $headers = [
                    'ID',
                    'Booking Date',
                    'Guest Name',
                    'Guest Mobile',
                    'Host Name',
                    'Host Mobile',
                    'Property ID',
                    'Check-In Date',
                    'Check-Out Date',
                    'Payment Method',
                    'Booking Status',
                    'Property Price',
                    'Total Days',
                    'Number of Guests',
                    'Payment Status',
                    // Conditionally add cancel headers
                    ...($book_status == 'Cancelled' ? [
                        'Cancelled By',
                        'Cancellation Reason',
                    ] : []),
                    'Title (English)',
                    'Title (Arabic)',
                    'Final Total',
                    'Confirmed At'
                ],
                $data
            );
        } else if ($_POST["type"] == "export_category_data") {
            $query = "SELECT 
            *
        FROM 
            tbl_category
        ";
            $sel = $rstate->query($query);
            $data = [];

            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'title_en' => getMultilingualValue($row['title'] ?? '', 'en'),
                    'title_ar' => getMultilingualValue($row['title'] ?? '', 'ar'),
                    'status' => $row['status'] ?? '',
                    'img' => $row['img'] ?? '',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Updated headers with new booking fields
                $headers = [
                    'ID',
                    'Title (English)',
                    'Title (Arabic)',
                    'Status',
                    'Image'
                ],
                $data
            );
        } else if ($_POST["type"] == "export_coupon_data") {
            $query = "SELECT 
            *
        FROM 
            tbl_coupon
        ";
            $sel = $rstate->query($query);
            $data = [];

            while ($row = $sel->fetch_assoc()) {
                $data[] = [
                    'id' => $row['id'] ?? '',
                    'title_en' => getMultilingualValue($row['ctitle'] ?? '', 'en'),
                    'title_ar' => getMultilingualValue($row['ctitle'] ?? '', 'ar'),
                    'des_en' => getMultilingualValue($row['c_desc'] ?? '', 'en'),
                    'des_ar' => getMultilingualValue($row['c_desc'] ?? '', 'ar'),
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',
                    'date' => $row['cdate'] ?? '',
                    'value' => $row['status'] ?? '',
                    'min' => $row['min_amt'] ?? '',
                    'max' => $row['max_amt'] ?? '',
                    'img' => $row['c_img'] ?? '',
                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Updated headers to match all fields in the data array
                $headers = [
                    'ID',
                    'Title (English)',
                    'Title (Arabic)',
                    'Description (English)',
                    'Description (Arabic)',
                    'Status',
                    'Date',
                    'Value',
                    'Minimum Amount',
                    'Maximum Amount',
                    'Image'
                ],
                $data
            );
        } else if ($_POST["type"] == "export_payout_data") {
            $payout_status = $_POST["payout_status"];

            $query = "SELECT  p.id as pid,p.requested_at,p.profile_id,b.id, b.total, b.prop_title, b.uid ,b.add_user_id FROM 
            tbl_payout_list p INNER JOIN tbl_book b ON FIND_IN_SET(b.id, p.book_id) > 0 WHERE p.payout_status = '$payout_status'";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {
                $client_id = $row['uid'];
                $client_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$client_id)->fetch_assoc();

                $owner_id = $row['add_user_id'];
                $owner_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$owner_id)->fetch_assoc();

                $profile_id = $row['profile_id'];
                $payment_data = $rstate->query("select pf.uid ,pf.bank_name , pf.bank_account_number , pf.wallet_number , pm.name  from tbl_payout_profiles pf LEFT JOIN tbl_payout_methods pm  on pf.method_id = pm.id   where pf.id= $profile_id")->fetch_assoc();

                $data[] = [
                    'id' => $row['pid'] ?? '',
                    'client_name' => $client_data['name'] ?? '',
                    'client_contact' => ($client_data['ccode'] ?? '') . ($client_data['mobile'] ?? ''),
                    'owner_name' => $owner_data['name'] ?? '',
                    'owner_contact' => ($owner_data['ccode'] ?? '') . ($owner_data['mobile'] ?? ''),
                    'prop_title_en' => getMultilingualValue($row['prop_title'] ?? '', 'en'),
                    'prop_title_ar' => getMultilingualValue($row['prop_title'] ?? '', 'ar'),
                    'total' => $row['total'] ?? '',
                    'date' => $row['requested_at'] ?? '',
                    'book_id' => $row['id'] ?? '',
                    'bank_name' => $payment_data['bank_name'] ?? '',
                    'bank_account_number' => $payment_data['bank_account_number'] ?? '',
                    'wallet_number' => $payment_data['wallet_number'] ?? '',
                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Guest Name',
                    'Guest Mobile',
                    'Host Name',
                    'Host Mobile',
                    'Property Title (English)',
                    'Property Title (Arabic)',
                    'Total Amount',
                    'Requested  at',
                    'Booking ID',
                    'Bank Name',
                    'Bank Account Number',
                    'Wallet Number'
                ],
                $data
            );
        } else if ($_POST["type"] == "export_rating_data") {

            $query = "SELECT r.*, b.prop_title, b.prop_id as property_id FROM tbl_rating r 
                          INNER JOIN tbl_book b ON FIND_IN_SET(b.id, r.book_id) > 0";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {
                $client_id = $row['uid'];
                $client_data = $rstate->query("SELECT * FROM tbl_user WHERE id=" . (int)$client_id)->fetch_assoc();

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'client_name' => $client_data['name'] ?? '',
                    'client_contact' => ($client_data['ccode'] ?? '') . ($client_data['mobile'] ?? ''),
                    'prop_title_en' => getMultilingualValue($row['prop_title'] ?? '', 'en'),
                    'prop_title_ar' => getMultilingualValue($row['prop_title'] ?? '', 'ar'),
                    'book_id' => $row['book_id'] ?? '',
                    'rating' => $row['rating'] ?? '',
                    'comment' => $row['comment'] ?? '',
                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Guest Name',
                    'Guest Mobile',
                    'Property Title (English)',
                    'Property Title (Arabic)',
                    'Booking ID',
                    'Rating',
                    'Comment'
                ],
                $data
            );
        } else if ($_POST["type"] == "export_user_data") {

            $query = "SELECT * FROM `tbl_user`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {
                $check_owner = $rstate->query("SELECT * FROM tbl_property WHERE add_user_id=" . (int)$row['id'] . " AND is_deleted = 0")->num_rows;
                $balance = '0.00';
                $sell = $rstate->query("select id ,message,status,amt,tdate from wallet_report where uid=" . (int)$row['id']  . " order by id desc");
                while ($dat = $sell->fetch_assoc()) {

                    if ($dat['status'] == 'Adding') {
                        $balance = bcadd($balance, $dat['amt'], 2);
                    } else if ($dat['status'] == 'Withdraw') {
                        $balance = bcsub($balance, $dat['amt'], 2);
                    }
                }
                $data[] = [
                    'id' => $row['id'] ?? '',
                    'user_name' => $row['name'] ?? '',
                    'user_contact' => ($row['ccode'] ?? '') . ($row['mobile'] ?? ''),
                    'join_date' => $row['reg_date'] ?? '',
                    'Property Count' => $check_owner,
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',
                    'IS_Owner' => ($row['is_owner'] ?? '') == 1 ? 'Owner' : 'Property Manager',

                    'Wallet Balance' => $balance


                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'User Name',
                    'User Mobile',
                    'Join Date',
                    'Property Count',
                    'Status',
                    'Is Owner',
                    'Wallet Balance',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_admin_data") {

            $query = "SELECT * FROM `admin`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'username' => $row['username'] ?? '',
                    'type' => $row['type'] ?? '',
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'UserName',
                    'Type',
                    'Status',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_payout_method_data") {

            $query = "SELECT * FROM `tbl_payout_methods`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'payout_title_en' => getMultilingualValue($row['name'] ?? '', 'en'),
                    'payout_title_ar' => getMultilingualValue($row['name'] ?? '', 'ar'),
                    'img' => $row['img'] ?? '',
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Payout title (English)',
                    'Payout title (Arabic)',
                    'Image',
                    'Status',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_facility_data") {

            $query = "SELECT * FROM `tbl_facility`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'title_en' => getMultilingualValue($row['title'] ?? '', 'en'),
                    'title_ar' => getMultilingualValue($row['title'] ?? '', 'ar'),
                    'img' => $row['img'] ?? '',
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    ' Title (English)',
                    ' Title (Arabic)',
                    'Image',
                    'Status',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_faq_data") {

            $query = "SELECT * FROM `tbl_faq`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'question_en' => getMultilingualValue($row['question'] ?? '', 'en'),
                    'question_ar' => getMultilingualValue($row['question'] ?? '', 'ar'),
                    'answer_en' => getMultilingualValue($row['answer'] ?? '', 'en'),
                    'answer_ar' => getMultilingualValue($row['answer'] ?? '', 'ar'),
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Question (English)',
                    'Question (Arabic)',
                    'Answer (English)',
                    'Answer (Arabic)',
                    'Status',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_cancellation_policy_data") {

            $query = "SELECT * FROM `tbl_cancellation_policy`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'title_en' => getMultilingualValue($row['title'] ?? '', 'en'),
                    'title_ar' => getMultilingualValue($row['title'] ?? '', 'ar'),
                    'des_en' => getMultilingualValue($row['description'] ?? '', 'en'),
                    'des_ar' => getMultilingualValue($row['description'] ?? '', 'ar'),
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',
                    'is_recommended' => ($row['is_recommended'] ?? '') == 1 ? 'Yes' : 'No',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Title (English)',
                    'Title (Arabic)',
                    'Description (English)',
                    'Description (Arabic)',
                    'Status',
                    'Is Recommended',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_wallet_data") {

            $query = "SELECT 
                                                wr.*,
                                                u.mobile, u.ccode, u.name,
                                                a.username AS admin_username
                                            FROM 
                                                wallet_report wr
                                            LEFT JOIN 
                                                tbl_user u ON wr.uid = u.id
                                            LEFT JOIN 
                                                admin a ON wr.EmployeeId = a.id";;
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'message' => $row['message'] ?? '',
                    'status' => $row['status'] ?? '',
                    'amount' => $row['amt'] ?? '',
                    'admin_name' => $row['admin_username'] ?? '',
                    'user_name' => $row['name'] ?? '',
                    'user_contact' => ($row['ccode'] ?? '') . ($row['mobile'] ?? ''),

                    'created_at' => $row['tdate'] ?? '',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Message',
                    'Status',
                    'Amount',
                    'Admin name',
                    'User name',
                    'User Contact',
                    'Created At'

                ],
                $data
            );
        } else if ($_POST["type"] == "export_chat_data") {

            $query = "SELECT p.*, sender.name AS sender_name, receiver.name AS receiver_name ,
                                            sender.ccode as sender_ccode , sender.mobile  as sender_mobile , receiver.ccode as receiver_ccode , receiver.mobile as receiver_mobile
                          FROM tbl_messages p
                          INNER JOIN tbl_user sender ON p.sender_id = sender.id
                          INNER JOIN tbl_user receiver ON p.receiver_id = receiver.id
                          ";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'chat_id' => $row['chat_id'] ?? '',
                    'message' => getMultilingualValue($row['message'] ?? '', 'message'),
                    'img' => $row['img'] ?? '',
                    'sender_name' => $row['name'] ?? '',
                    'sender_contact' => ($row['ccode'] ?? '') . ($row['mobile'] ?? ''),
                    'receiver_name' => $row['name'] ?? '',
                    'receiver_contact' => ($row['ccode'] ?? '') . ($row['mobile'] ?? ''),

                    'approved' => ($row['is_approved'] ?? '') == 1 ? 'Approved' : 'Not Approved',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'Chat ID',
                    'Message',
                    'Image',
                    'Sender name',
                    'Sender Contact',
                    'Receiver name',
                    'Receiver Contact',
                    'Is Approved',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_cancel_reason_data") {

            $query = "SELECT * FROM `tbl_cancel_reason`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'reason_en' => getMultilingualValue($row['reason'] ?? '', 'en'),
                    'reason_ar' => getMultilingualValue($row['reason'] ?? '', 'ar'),
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Reason (English)',
                    'Reason (Arabic)',
                    'Status',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_user_cancel_reason_data") {

            $query = "SELECT * FROM `tbl_user_cancel_reason`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'reason_en' => getMultilingualValue($row['reason'] ?? '', 'en'),
                    'reason_ar' => getMultilingualValue($row['reason'] ?? '', 'ar'),
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Reason (English)',
                    'Reason (Arabic)',
                    'Status',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_slider_data") {

            $query = "SELECT s.* , c.title as c_title , g.name as g_name FROM `tbl_slider`  s
            inner join tbl_category c on s.cat_id = c.id 
            inner join tbl_government g on s.government_id = g.id 
            ";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'title_en' => getMultilingualValue($row['title'] ?? '', 'en'),
                    'title_ar' => getMultilingualValue($row['title'] ?? '', 'ar'),

                    'category_title_en' => getMultilingualValue($row['c_title'] ?? '', 'en'),
                    'category_title_ar' => getMultilingualValue($row['c_title'] ?? '', 'ar'),

                    'gov_title_en' => getMultilingualValue($row['g_name'] ?? '', 'en'),
                    'gov_title_ar' => getMultilingualValue($row['g_name'] ?? '', 'ar'),
                    'img' => $row['img'] ?? '',
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Title (English)',
                    'Title (Arabic)',
                    'Category Title (English)',
                    'Category  Title (Arabic)',
                    'Government Title (English)',
                    'Government  Title (Arabic)',
                    'Image',
                    'Status',

                ],
                $data
            );
        } else if ($_POST["type"] == "export_why_choose_data") {

            $query = "SELECT * FROM `tbl_why_choose_us`";
            $sel = $rstate->query($query);
            $data = [];
            while ($row = $sel->fetch_assoc()) {

                $data[] = [
                    'id' => $row['id'] ?? '',
                    'title_en' => getMultilingualValue($row['title'] ?? '', 'en'),
                    'title_ar' => getMultilingualValue($row['title'] ?? '', 'ar'),
                    'des_en' => getMultilingualValue($row['description'] ?? '', 'en'),
                    'des_ar' => getMultilingualValue($row['description'] ?? '', 'ar'),
                    'background_color' => $row['background_color'] ?? '',
                    'img' => $row['img'] ?? '',
                    'status' => ($row['status'] ?? '') == 1 ? 'Active' : 'Not Active',
                    'header' => ($row['is_header'] ?? '') == 1 ? 'Yes' : 'No',

                ];
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Catgories Exported Successfully!!",
                "message" => "Booking data exported!",
                "action" => "campings.php",
            ];

            downloadXLS(
                // Complete headers matching all data fields
                $headers = [
                    'ID',
                    'Title (English)',
                    'Title (Arabic)',
                    'Description (English)',
                    'Description (Arabic)',
                    'Background Color',
                    'Image',
                    'Status',
                    'Is Header'

                ],
                $data
            );
        } else if ($_POST["type"] == "upload_whats-up-campings") {
            $h = new Estate();
            $h->restateDeleteData_Api('', 'tbl_uploaded_excel_data');
            $rows = parseExcelFile();
            // Get all rows and remove header (first row)
            $header = array_shift($rows); // Remove and discard header row

            foreach ($rows as $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Process each row (adjust according to your Excel structure)
                $phone = $row[0] ?? null;
                $message = $row[1] ?? null;

                $table = "tbl_uploaded_excel_data";
                $field_values = array("f1", "f2", "item_id");
                $data_values = array("$phone", "$message", '65665');

                $h = new Estate();
                $h->restateinsertdata_Api($field_values, $data_values, $table);
            }
            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "file Uploaded successfully!!",
                "message" => "Whatsup section!",
                "action" => "campings.php",
            ];
        } elseif ($_POST["type"] == "Send_whatsup_message") {
            $query = "SELECT 
            u.f1,u.f2  , u.id
        FROM 
            tbl_uploaded_excel_data u
            where u.item_id = 65665 
            ";
            $sel = $rstate->query($query);
            while ($row = $sel->fetch_assoc()) {

                $message = $row['f2'];
                $mobile = $row['f1'];
                $id = $row['id'];
                $result = sendMessage([$mobile], $message);
                if ($result) {
                    $query2 = "Delete 
                      FROM 
            tbl_uploaded_excel_data 
            where id = $id 
            ";
                    $rstate->query($query2);
                    sleep(5);
                }
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Messages sent successfully!!",
                "message" => "Whatsup section!",
                "action" => "campings.php",
            ];
        } elseif ($_POST["type"] == "send_user_whatsup_message") {
            $ids = implode(',', $_POST['user_ids']);
            $message =  $_POST['message'];

            $query = "SELECT 
                u.ccode, u.mobile
            FROM 
                tbl_user u
            WHERE 
             u.id IN ($ids)";
            $sel = $rstate->query($query);
            while ($row = $sel->fetch_assoc()) {

                $mobile = $row['ccode'] . $row['mobile'];
                $result = sendMessage([$mobile], $message);
                if ($result) {
                    sleep(5);
                }
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Messages sent successfully!!",
                "message" => "Whatsup section!",
                "action" => "users.php",
            ];
        } elseif ($_POST["type"] == "delete_on_hold_booking") {
            $ids = implode(',', $_POST['user_ids']);

            $query = "SELECT 
                u.id
            FROM 
                tbl_non_completed u
            WHERE 
             u.id IN ($ids)";
            $sel = $rstate->query($query);
            while ($row = $sel->fetch_assoc()) {


                $table = "tbl_non_completed";
                $field = array('status' => '0');
                $where = "where id=" . '?' . "";
                $where_conditions = [$row['id']];
                $h = new Estate();
                $check = $h->restateupdateData_Api($field, $table, $where, $where_conditions);
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Temporal booking Deleted successfully!!",
                "message" => "Whatsup section!",
                "action" => "temporal_booking.php",
            ];
        } elseif ($_POST["type"] == "send_owner_whatsup_message") {
            $ids = implode(',', $_POST['user_ids']);
            $message =  $_POST['message'];

            $query = "SELECT 
                u.ccode, u.mobile
            FROM 
                tbl_user u
            WHERE 
             u.id IN ($ids)";
            $sel = $rstate->query($query);
            while ($row = $sel->fetch_assoc()) {

                $mobile = $row['ccode'] . $row['mobile'];
                $result = sendMessage([$mobile], $message);
                if ($result) {
                    sleep(5);
                }
            }

            $returnArr = [
                "ResponseCode" => "200",
                "Result" => "true",
                "title" => "Messages sent successfully!!",
                "message" => "Whatsup section!",
                "action" => "owners.php",
            ];
        } elseif ($_POST["type"] == "add_money") {
            $table = 'wallet_report';
            $owner = $_POST["propowner"];
            $notes = $_POST["notes"];
            $money = $_POST["money"];
            $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
            $updated_at = $date->format('Y-m-d H:i:s');

            $balance = '0.00';
            $sell = $rstate->query("select id ,message,status,amt,tdate from wallet_report where uid=" . (int)$owner  . " order by id desc");
            while ($dat = $sell->fetch_assoc()) {

                if ($dat['status'] == 'Adding') {
                    $balance = bcadd($balance, $dat['amt'], 2);
                } else if ($dat['status'] == 'Withdraw') {
                    $balance = bcsub($balance, $dat['amt'], 2);
                }
            }
            $title = "Money Added successfully!!";
            $status = 'Adding';
            if ($money < 0) {
                $title =  "Money withdrawed successfully!!";
                $status = 'Withdraw';
                $money = -1 * $money;
            }
            $field_values = array("uid", "EmployeeId", "message", "status", "amt", "tdate");

            $added_by = $_SESSION['id'];
            if (!$added_by) {
                throw new Exception("unauthorized Operation");
            }
            if ($status== 'Withdraw' && $balance < $money) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => 'no sufficient balance',
                    "message" => "Whatsup section!",
                    "action" => "add_money.php",
                ];
            } else {
                $h = new Estate();
                $data_values = array("$owner", "$added_by", "$notes", "$status", "$money", "$updated_at");
                $h->restateinsertdata_Api($field_values, $data_values, $table);
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => $title,
                    "message" => "Whatsup section!",
                    "action" => "add_money.php",
                ];
            }
        } elseif ($_POST["type"] == "cancel_book") {
            $id = $_POST["id"];
            $uid = $_POST["uid"];
            $guest_uid = $_POST["guest_uid"];
            $deny_id =  $_POST["reason"];
            $title = $_POST["property_title"];
            $table = "tbl_book";

            $field_cancel = array('book_status' => 'Cancelled', 'cancle_reason' => $deny_id, "cancel_by" => 'A');
            $where = "where id=" . '?' . "";
            $where_conditions = [$id];
            $cancel_data = $rstate->query("select  reason	 from tbl_cancel_reason where  id= $deny_id ")->fetch_assoc();
            $cancel_text =  json_decode($cancel_data['reason'] ?? "", true)['ar'] ?? "";

            $message = "عزيزي العميل،
نأسف لإبلاغك بأنه تم إلغاء حجز العقار [$title]للسبب التالي: [$cancel_text]
يمكنك: • مراجعة شروط الحجز والتأكد من استيفائها • إعادة حجز العقار مرة أخرى عبر تطبيق ت-رينت • التواصل معنا للحصول على المساعدة
شكراً لثقتكم بنا فريق ت-رينت 🏠";
            $title_ = ' تم إلغاء حجز العقار';
            $user = $rstate->query("select  mobile, ccode	 from tbl_user where  id= $guest_uid ")->fetch_assoc();

            $mobile = $user["mobile"];
            $ccode = $user["ccode"];
            $h = new Estate();


            $check = $h->restateupdateData_Api($field_cancel, $table, $where, $where_conditions);

            if ($check) {
                refundMoney($guest_uid, $id, 'A', $deny_id);
                $whatsapp = sendMessage([$ccode . $mobile], $message);
                $firebase_notification = sendFirebaseNotification($title_, $message, $guest_uid,  "booking_id", $id);

                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Booking Cancelled Successfully!!",
                    "message" => "APProval section!",
                    "action" => "pending.php",
                ];
            }
        } else if ($_POST["type"] == "reset_book") {
            $id = $_POST["id"];
            $table = "tbl_book";
            $where = "where id=" . '?' . "";
            $where_conditions = [$id];
            $query = "SELECT 
                refunded,confirmed_at , pay_status , total ,reminder_value ,uid 
            FROM 
                tbl_book 
            WHERE 
             id = $id ";
            $data = $rstate->query($query)->fetch_assoc();
            $partial_value = ($data['pay_status'] == 'Completed') ? number_format($data['total'], 2, '.', '') : number_format($data['total'] - $data['reminder_value'], 2, '.', '');

            $field = array('book_status' => 'Booked', 'refunded' => 0);

            $uid = $data['uid'];
            $balance = '0.00';
            $sell = $rstate->query("select id ,message,status,amt,tdate from wallet_report where uid=" . (int)$uid  . " order by id desc");
            while ($dat = $sell->fetch_assoc()) {

                if ($dat['status'] == 'Adding') {
                    $balance = bcadd($balance, $dat['amt'], 2);
                } else if ($dat['status'] == 'Withdraw') {
                    $balance = bcsub($balance, $dat['amt'], 2);
                }
            }
            if ($data['refunded'] && $balance < $partial_value) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Wallet balance not sufficient to revert booking!!",
                    "message" => "APProval section!",
                    "action" => "cancelled.php",
                ];
            } else if ($data['refunded']  && $balance >= $partial_value) {
                $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
                $updated_at = $date->format('Y-m-d H:i:s');
                $notes = "Refund Withdrawed successfully!!";
                $status = 'Withdraw';
                $field_values = array("uid", "EmployeeId", "message", "status", "amt", "tdate");
                $h = new Estate();
                $added_by = $_SESSION['id'];
                if (!$added_by) {
                    throw new Exception("unauthorized Operation");
                }
                $data_values = array("$uid", $added_by, "$notes", "$status", "$partial_value", "$updated_at");
                $GLOBALS['rstate']->begin_transaction();
                $check = $h->restateupdateData_Api($field, 'tbl_book', $where, $where_conditions);
                if (!$check) {
                    throw new Exception("update failed");
                }
                $wallet_id = $h->restateinsertdata_Api($field_values, $data_values, 'wallet_report');
                if (!$wallet_id) {
                    throw new Exception("Insert failed");
                }
                $GLOBALS['rstate']->commit();

                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Booking reverted Successfully!!",
                    "message" => "APProval section!",
                    "action" => "cancelled.php",
                ];
            } else {
                $h = new Estate();

                $GLOBALS['rstate']->begin_transaction();
                $check = $h->restateupdateData_Api($field, 'tbl_book', $where, $where_conditions);

                $GLOBALS['rstate']->commit();

                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Booking reverted Successfully!!",
                    "message" => "APProval section!",
                    "action" => "cancelled.php",
                ];
            }
        } elseif ($_POST["type"] == "confirm_book") {
            $id = $_POST["id"];
            $uid = $_POST["uid"];
            $guest_uid = $_POST["guest_uid"];
            $table = "tbl_book";
            $title = $_POST["property_title"];
            $where = "where id=" . '?' . "";
            $where_conditions = [$id];
            $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
            $created_at = $date->format('Y-m-d H:i:s');
            $field = array('book_status' => 'Confirmed', 'confirmed_at' => $created_at, 'confirmed_by' => 'A');

            $message = "مبروك!
تم تأكيد حجز العقار  [$title] بنجاح.
التفاصيل: • يمكنك مراجعة تفاصيل الحجز من خلال التطبيق • ستتلقى تفاصيل الاتصال بالمالك قريباً • في حالة وجود أي استفسار، لا تتردد في التواصل معنا
نتمنى لك إقامة مريحة فريق ت-رينت 🎉";
            $title_ = 'تهانينا! تم تأكيد حجز العقار ✅';
            $user = $rstate->query("select  mobile, ccode	 from tbl_user where  id= $guest_uid ")->fetch_assoc();

            $mobile = $user["mobile"];
            $ccode = $user["ccode"];
            $h = new Estate();


            $check = $h->restateupdateData_Api($field, $table, $where, $where_conditions);

            if ($check) {
                $whatsapp = sendMessage([$ccode . $mobile], $message);
                $firebase_notification = sendFirebaseNotification($title_, $message, $guest_uid,  "booking_id", $id);

                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Booking Confirmed Successfully!!",
                    "message" => "APProval section!",
                    "action" => "pending.php",
                ];
            }
        } elseif ($_POST["type"] == "update_status") {
            $id = $_POST["id"];
            $status = $_POST["status"];
            $coll_type = $_POST["coll_type"];
            $page_name = $_POST["page_name"];
            if ($coll_type == "userstatus") {
                $table = "tbl_user";
                $field = "status=" . $status . "";
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData_single($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "User Status Change Successfully!!",
                        "message" => "User section!",
                        "action" => "userlist.php",
                    ];
                }
            } elseif ($coll_type == "dark_mode") {
                $table = "tbl_setting";
                $field = "show_dark=" . $status . "";
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData_single($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Dark Mode Status Change Successfully!!",
                        "message" => "Dark Mode section!",
                        "action" => $page_name,
                    ];
                }
            } else {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "false",
                    "title" => "Option Not There!!",
                    "message" => "Error!!",
                    "action" => "dashboard.php",
                ];
            }
        } else {
            $returnArr = array("ResponseCode" => "200", "Result" => "false", "title" => "Don't Try New Function!", "message" => "welcome admin!!", "action" => "dashboard.php");
        }
    } else {
        $returnArr = array("ResponseCode" => "200", "Result" => "false", "title" => "Don't Try New Function!", "message" => "welcome admin!!", "action" => "dashboard.php");
    }
    echo json_encode($returnArr);
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = array("ResponseCode" => "200", "Result" => "false", "title" => "Ar Error occured ! $e", "message" => "welcome admin!!", "action" => "dashboard.php");
    echo json_encode($returnArr);
}

function get_user_permissions($id, $rstate)
{

    $permissions = array();
    $sel = $rstate->query("SELECT p.name
FROM permissions p
JOIN role_permissions rp ON FIND_IN_SET(p.id, rp.permissions) > 0
 where   rp.id=" . $id .  "");
    while ($row = $sel->fetch_assoc()) {
        $permissions[] = $row['name'];
    }

    return $permissions;
}

function deny_property(string $reason,  $id, $uid, $title, $rstate)
{

    $table = "tbl_property";

    $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
    $updated_at = $date->format('Y-m-d H:i:s');

    $field = ["cancel_reason" => $reason, 'is_need_review' => 1, 'updated_at' => $updated_at];
    $where = "where id=" . $id . "";

    $sel = $rstate->query("select * from tbl_user where   id=" . $uid .  "")->fetch_assoc();

    $new_mobile   = $sel['mobile'];
    $ccode   = $sel['ccode'];

    $h = new Estate();
    $check = $h->restateupdateData($field, $table, $where);

    // Create the message
    $message = "عزيزي المالك، لم نتمكن من نشر العقار  [$title] للسبب التالي[$reason]";
    $title_ = 'يتطلب عقارك بعض التعديلات ';

    $result = sendMessage([$ccode . $new_mobile], $message);
    $firebase_notification = sendFirebaseNotification($title_, $message, $uid, "property_id", $id);

    return $check;
}

function approve_property($rstate, $uid, $title_ar, $id)
{
    $sel = $rstate->query("select * from tbl_user where   id=" . $uid .  "")->fetch_assoc();

    $new_mobile   = $sel['mobile'];
    $ccode   = $sel['ccode'];
    $message = "اهلاً وسهلاً!\n\n"
        . "تم نشر عقارك [$title_ar] بنجاح على منصة Trent\n\n"
        . "* شارك رابط العقار مع الأصدقاء\n"
        . "* تحديث بيانات العقار عند الحاجة\n\n"
        . "نتمنى لك تأجيراً سريعاً ومكسب مستمر\n"
        . "فريق Trent 📈\n"
        . "https://www.trent.com.eg/properties/$id";
    $title_ = 'تم نشر عقارك بنجاح! ';
    $result = sendMessage([$ccode . $new_mobile], $message);
    $firebase_notification = sendFirebaseNotification($title_, $message, $uid, "property_id", $id);
}
function downloadCSV($headers, $data)
{
    // 1. Start output buffering
    ob_start();

    // 2. Set headers FIRST
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="export_' . date('Y-m-d') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // 3. Create CSV content
    $output = fopen('php://output', 'w');

    // Optional: Add UTF-8 BOM for Excel compatibility
    fputs($output, "\xEF\xBB\xBF");

    // Add headers
    fputcsv($output, $headers);


    // Add data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }

    // 4. Close and exit
    fclose($output);
    ob_end_flush();
    exit; // Critical - prevents any additional output
}
function downloadXLS($headers, $data)
{
    // Start output buffering
    ob_start();

    // Set headers for Excel download with UTF-8 encoding
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="export_' . date('Y-m-d') . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility (must be first characters in file)
    fputs($output, "\xEF\xBB\xBF");

    // Start HTML table with XML declaration and proper meta tags
    fputs($output, "<!DOCTYPE html>\n");
    fputs($output, "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel'>\n");
    fputs($output, "<head>\n");
    fputs($output, "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>\n");
    fputs($output, "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Export</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->\n");
    fputs($output, "</head>\n");
    fputs($output, "<body>\n");
    fputs($output, "<table border='1'>\n");

    // Add headers
    fputs($output, "<tr>");
    foreach ($headers as $header) {
        fputs($output, "<th>" . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . "</th>");
    }
    fputs($output, "</tr>\n");

    // Add data rows
    foreach ($data as $row) {
        fputs($output, "<tr>");
        foreach ($row as $cell) {
            // Preserve Arabic characters without htmlspecialchars if it's causing issues
            // Or use htmlspecialchars with UTF-8 encoding if needed for security
            $cellValue = (is_string($cell)) ? $cell : strval($cell);
            fputs($output, "<td>" . $cellValue . "</td>");
        }
        fputs($output, "</tr>\n");
    }

    // Close table and document
    fputs($output, "</table>\n");
    fputs($output, "</body>\n");
    fputs($output, "</html>");

    // Flush and exit
    fclose($output);
    ob_end_flush();
    exit;
}
function parseExcelFile()
{
    $name = $_FILES['excelFile']['name'];
    $filePath = $_FILES['excelFile']['tmp_name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    try {
        // Identify file type and create appropriate reader
        $reader = IOFactory::createReaderForFile($filePath);
        $spreadsheet = $reader->load($filePath);

        return $spreadsheet->getActiveSheet()->toArray();
    } catch (Exception $e) {
        throw new Exception("Error reading file: " . $e->getMessage());
    }
}
// Function to decode JSON multilingual fields
function getMultilingualValue($jsonString, $lang)
{
    if (empty($jsonString)) return '';
    if (is_array($jsonString)) return $jsonString[$lang] ?? reset($jsonString);
    $decoded = json_decode($jsonString, true);
    return $decoded[$lang] ?? (is_array($decoded) ? reset($decoded) : $jsonString);
}

// Function to get multilingual names from reference IDs
function getMultilingualReference($id, $referenceType, $references, $lang)
{
    if (empty($id)) return '';
    $refData = $references[$referenceType][$id] ?? null;
    return $refData ? getMultilingualValue($refData, $lang) : '';
}

// Function to get facility names from comma-separated IDs
function getFacilityNames($facilityIds, $references)
{
    if (empty($facilityIds)) return '';
    $ids = explode(',', $facilityIds);
    $names = [];
    foreach ($ids as $id) {
        $id = trim($id);
        if (isset($references['facilities'][$id])) {
            $names[] = getMultilingualValue($references['facilities'][$id], 'en');
        }
    }
    return implode(', ', $names);
}
