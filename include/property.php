<?php
require "reconfig.php";
require "estate.php";
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

use Shuchkin\SimpleXLSX;
use PhpOffice\PhpSpreadsheet\IOFactory;

require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/user_api/notifications/send_notification.php';

try {
    if (isset($_POST["type"])) {

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
                'Delete_Slider','Create_Booking','Update_Booking','Read_Booking','Delete_Booking',
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
                'Delete_Slider','Create_Booking','Update_Booking','Read_Booking','Delete_Booking',
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

                'status'
            );

            $table = "tbl_payout_methods";
            $data_values = array("$name_json",  "$status");

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
            if (!isset($returnArr)) {

                $table = "tbl_property";
                $field_values = ["created_at", "is_need_review", "updated_at", "image", "cancel_reason", "cancellation_policy_id", "period", "is_featured", "security_deposit", "government", "map_url", "is_approved",  "latitude", "longitude", "video", "guest_rules", "compound_name", "floor", "status", "title", "price", "address", "facility", "description", "beds", "bathroom", "sqrft",  "ptype",  "city",  "add_user_id", "pbuysell",  "plimit", "max_days", "min_days"];
                $data_values = ["$updated_at", "0", "$updated_at", "$imageUrlsString", "",  "$policy",  "$period", "$featured", "$security_deposit", "$government", "$google_maps_url", "0", "$latitude", "$longitude", "$videoUrlsString", "$guest_rules_json", "$compound_name_json", "$floor_json", "$status", "$title_json", "$price", "$address_json", "$facility", "$description_json", "$beds", "$bathroom", "$sqft",  "$ptype",  "$city_json",  "$propowner", "$pbuysell", "$plimit", "$max_days", "$min_days"];

                $h = new Estate();
                $check = $h->restateinsertdata_Api($field_values, $data_values, $table);
            }
            if ($check) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Property Add Successfully!!",
                    "message" => "Property section!",
                    "action" => "list_properties.php",
                ];
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
                'is_need_review' => $need_review
            ];
            if (!empty($imageUrls)) {
                $field_values["image"] =  $imageUrlsString . ',' . $existing_images;
            } else {
                $field_values["image"] =   $existing_images;
            }

            if (!empty($videoUrls)) {
                $field_values["video"] =  $videoUrlsString;
            } else {

                $field_values["video"] =  "";
            }

            if (!isset($returnArr)) {
                $where = "where id=" . '?' . "";
                $where_conditions  = [$id];
                $h = new Estate();
                $check = $h->restateupdateData_Api($field_values, $table, $where, $where_conditions);
            }
            if ($check) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Property Update Successfully!!",
                    "message" => "Property section!",
                    "action" => "list_properties.php",
                ];
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

            $table = "tbl_messages";
            $field = ["is_approved" => $okey, 'updated_at' => $updated_at];
            $where = "where id=" . $id . "";
            $h = new Estate();

            $check = $h->restateupdateData($field, $table, $where);
            if ($okey ==  '1') {
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
            $ptype = $_POST['ptype'] ?? Null;
            $pgov = $_POST['pgov'] ?? null;
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
            $table = "tbl_slider";
            $field_values = ["img", "status", "title", "uid", "government_id", "cat_id"];
            $data_values = ["$url", "$okey", "$title_json", $propowner, $pgov, $ptype];

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
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            $title_json = json_encode([
                "en" => $title_en,
                "ar" => $title_ar
            ], JSON_UNESCAPED_UNICODE);

            if ($_FILES["slider_img"]["name"] != "") {

                move_uploaded_file(
                    $_FILES["slider_img"]["tmp_name"],
                    $target_file
                );
                $table = "tbl_slider";
                $field = ["status" => $okey, "img" => $url, "title" => $title_json,  "cat_id" => $ptype,  "government_id" => $pgov,  "uid" => "$propowner"];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

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
                $field = ["status" => $okey, "img" => $url, "title" => $title_json,  "cat_id" => $ptype,  "government_id" => $pgov,  "uid" => "$propowner"];
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
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
                $data[] =   [
                    $row['id'],
                    $row['trent_fees'],
                    $row['book_date'],
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
                    'رقم الحجز',
                    'القيمة',
                    'تاريخ  الحجز',
                ],
                $data
            );
        } elseif ($_POST["type"] == "Active_User_report") {
            $query = "SELECT 
                u.*,
                COUNT(b.id) AS booking_count
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
                    $row['booking_count']
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

                ],
                $data
            );
        } elseif ($_POST["type"] == "Active_Prop_report") {
            $query = "SELECT 
            u.*,
            COUNT(b.id) AS booking_count
            ,b.prop_title AS title
        FROM 
            tbl_user u
        LEFT JOIN 
            tbl_book b ON u.id = b.add_user_id AND b.book_status IN ('Check_in', 'Confirmed')
        GROUP BY 
            u.id
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
                    $row['booking_count']
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
        } else if ($_POST["type"] == "upload_whats-up-campings") {
            $h = new Estate();
            $h->restateDeleteData_Api('' , 'tbl_uploaded_excel_data');
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

            $title = "Money Added successfully!!";
            $status = 'Adding';
            if ($money < 0) {
                $title =  "Money withdrawed successfully!!";
                $status = 'Withdraw';
            }
            $field_values = array("uid", "EmployeeId", "message", "status", "amt", "tdate");

            $added_by = $_SESSION['id'];
            if (!$added_by) {
                throw new Exception("unauthorized Operation");
            }
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
            } elseif ($coll_type == "proper_sell") {
                $table = "tbl_property";
                $field = "is_sell=" . $status . "";
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData_single($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Property Selled Successfully!!",
                        "message" => "User section!",
                        "action" => "list_properties.php",
                    ];
                }
            } elseif ($coll_type == "confirmed_book") {
                $table = "tbl_book";
                $field = "book_status='" . $status . "'";
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData_single($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Booking Confirmed  Successfully!!",
                        "message" => "Booking section!",
                        "action" => "approved.php",
                    ];
                }

                $bdata = $rstate->query("select * from tbl_book where id=" . $id . "")->fetch_assoc();
                $udata = $rstate->query("select * from tbl_user where id=" . $bdata['uid'] . "")->fetch_assoc();
                $name = $udata['name'];
                $uid = $bdata['uid'];



                $content = array(
                    "en" => $name . ', Your Booking #' . $id . ' Has Been Confirmed Successfully.'
                );
                $heading = array(
                    "en" => "Confirmed Successfully!!"
                );

                $fields = array(
                    'app_id' => $set['one_key'],
                    'included_segments' =>  array("Active Users"),
                    'data' => array("order_id" => $id, "type" => 'normal'),
                    'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $bdata['uid'])),
                    'contents' => $content,
                    'headings' => $heading
                );
                $fields = json_encode($fields);


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic ' . $set['one_hash']
                    )
                );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);

                $timestamp = date("Y-m-d H:i:s");

                $title_mains = "Confirmed Successfully!!";
                $descriptions = 'Booking #' . $id . ' Has Been Confirmed Successfully.';

                $table = "tbl_notification";
                $field_values = array("uid", "datetime", "title", "description");
                $data_values = array("$uid", "$timestamp", "$title_mains", "$descriptions");

                $h = new Estate();
                $h->restateinsertdata_Api($field_values, $data_values, $table);
            } elseif ($coll_type == "Check_in") {
                $table = "tbl_book";
                $field = "book_status='" . $status . "'";
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData_single($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Booking Check In  Successfully!!",
                        "message" => "Booking section!",
                        "action" => "check_in.php",
                    ];
                }
                $bdata = $rstate->query("select * from tbl_book where id=" . $id . "")->fetch_assoc();
                $udata = $rstate->query("select * from tbl_user where id=" . $bdata['uid'] . "")->fetch_assoc();
                $name = $udata['name'];
                $uid = $bdata['uid'];



                $content = array(
                    "en" => $name . ', Your Booking #' . $id . ' Has Been Check In Successfully.'
                );
                $heading = array(
                    "en" => "Check In Successfully!!"
                );

                $fields = array(
                    'app_id' => $set['one_key'],
                    'included_segments' =>  array("Active Users"),
                    'data' => array("order_id" => $id, "type" => 'normal'),
                    'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $uid)),
                    'contents' => $content,
                    'headings' => $heading
                );
                $fields = json_encode($fields);


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic ' . $set['one_hash']
                    )
                );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);

                $timestamp = date("Y-m-d H:i:s");

                $title_mains = "Check In Successfully!!";
                $descriptions = 'Booking #' . $id . ' Has Been Check In Successfully.';

                $table = "tbl_notification";
                $field_values = array("uid", "datetime", "title", "description");
                $data_values = array("$uid", "$timestamp", "$title_mains", "$descriptions");

                $h = new Estate();
                $h->restateinsertdata_Api($field_values, $data_values, $table);
            } elseif ($coll_type == "Check_out") {
                $table = "tbl_book";
                $field = "book_status='" . $status . "'";
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData_single($field, $table, $where);
                if ($check == 1) {
                    $returnArr = [
                        "ResponseCode" => "200",
                        "Result" => "true",
                        "title" => "Booking Check Out  Successfully!!",
                        "message" => "Booking section!",
                        "action" => "completed.php",
                    ];
                }
                $bdata = $rstate->query("select * from tbl_book where id=" . $id . "")->fetch_assoc();
                $udata = $rstate->query("select * from tbl_user where id=" . $bdata['uid'] . "")->fetch_assoc();
                $name = $udata['name'];
                $uid = $bdata['uid'];



                $content = array(
                    "en" => $name . ', Your Booking #' . $id . ' Has Been Check Out Successfully.'
                );
                $heading = array(
                    "en" => "Check Out Successfully!!"
                );

                $fields = array(
                    'app_id' => $set['one_key'],
                    'included_segments' =>  array("Active Users"),
                    'data' => array("order_id" => $id, "type" => 'normal'),
                    'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $uid)),
                    'contents' => $content,
                    'headings' => $heading
                );
                $fields = json_encode($fields);


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic ' . $set['one_hash']
                    )
                );
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);

                $timestamp = date("Y-m-d H:i:s");

                $title_mains = "Check Out Successfully!!";
                $descriptions = 'Booking #' . $id . ' Has Been Check Out Successfully.';

                $table = "tbl_notification";
                $field_values = array("uid", "datetime", "title", "description");
                $data_values = array("$uid", "$timestamp", "$title_mains", "$descriptions");

                $h = new Estate();
                $h->restateinsertdata_Api($field_values, $data_values, $table);
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

    $message = "هلاً وسهلاً!
تم نشر عقارك  [$title_ar] بنجاح على منصة ت-رينت.
ما يمكنك فعله الآن: • مشاركة رابط العقار مع الأصدقاء • متابعة عدد المشاهدات والاستفسارات • تحديث بيانات العقار عند الحاجة
نتمنى لك تأجيراً سريعاً وموفقاً فريق ت-رينت 📈";
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

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="export_' . date('Y-m-d') . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility
    fputs($output, "\xEF\xBB\xBF");

    // Start HTML table (Excel accepts HTML tables as XLS content)
    fputs($output, "<table border='1'>\n");

    // Add headers
    fputs($output, "<tr>");
    foreach ($headers as $header) {
        fputs($output, "<th>" . htmlspecialchars($header) . "</th>");
    }
    fputs($output, "</tr>\n");

    // Add data rows
    foreach ($data as $row) {
        fputs($output, "<tr>");
        foreach ($row as $cell) {
            fputs($output, "<td>" . htmlspecialchars($cell) . "</td>");
        }
        fputs($output, "</tr>\n");
    }

    // Close table
    fputs($output, "</table>");

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
