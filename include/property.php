<?php
require "reconfig.php";
require "estate.php";
require dirname(dirname(__FILE__)) . '/include/helper.php';
try {
    if (isset($_POST["type"])) {

        if ($_POST['type'] == 'login') {
            $username = $_POST['username'];
            $password = $_POST['password'];


            $h = new Estate();

            $count = $h->restatelogin($username, $password, 'admin');
            if ($count != 0) {
                $_SESSION['restatename'] = $username;
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
        } else if ($_POST['type'] == 'edit_payment') {

            $attributes = mysqli_real_escape_string($rstate, $_POST['p_attr']);
            $ptitle = mysqli_real_escape_string($rstate, $_POST['ptitle']);
            $okey = $_POST['status'];
            $id = $_POST['id'];
            $p_show = $_POST['p_show'];
            $s_show = $_POST['s_show'];
            $target_dir = dirname(dirname(__FILE__)) . "/images/payment/";
            $url = "images/payment/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = round(microtime(true)) . '.' . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);
            if ($_FILES["cat_img"]["name"] != '') {

                move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
                $table = "tbl_payment_list";
                $field = array('status' => $okey, 'img' => $url, 'attributes' => $attributes, 'subtitle' => $ptitle, 'p_show' => $p_show, 's_show' => $s_show);
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Payment Gateway Update Successfully!!", "message" => "Payment Gateway section!", "action" => "paymentlist.php");
                }
            } else {
                $table = "tbl_payment_list";
                $field = array('status' => $okey, 'attributes' => $attributes, 'subtitle' => $ptitle, 'p_show' => $p_show, 's_show' => $s_show);
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Payment Gateway Update Successfully!!", "message" => "Payment Gateway section!", "action" => "paymentlist.php");
                }
            }
        } else if ($_POST['type'] == 'add_coupon') {
            $ccode = $rstate->real_escape_string($_POST['coupon_code']);

            $cdate = $_POST['expire_date'];
            $minamt = $_POST['min_amt'];
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
            $field_values = array("c_img", "c_desc", "c_value", "c_title", "status", "cdate", "ctitle", "min_amt", "subtitle");
            $data_values = array("$url", "$cdesc_json", "$cvalue", "$ccode", "$cstatus", "$cdate", "$ctitle_json", "$minamt", "$subtitle_json");

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
                $field = array('c_img' => $url, 'c_desc' => $cdesc_json, 'c_value' => $cvalue, 'c_title' => $ccode, 'status' => $cstatus, 'cdate' => $cdate, 'ctitle' => $ctitle_json, 'min_amt' => $minamt, 'subtitle' => $subtitle_json);
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);

                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Offer Update Successfully!!", "message" => "Offer section!", "action" => "list_coupon.php");
                }
            } else {
                $table = "tbl_coupon";
                $field = array('c_desc' => $cdesc_json, 'c_value' => $cvalue, 'c_title' => $ccode, 'status' => $cstatus, 'cdate' => $cdate, 'ctitle' => $ctitle_json, 'min_amt' => $minamt, 'subtitle' => $subtitle_json);
                $where = "where id=" . $id . "";
                $h = new Estate();
                $check = $h->restateupdateData($field, $table, $where);
                if ($check == 1) {
                    $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Offer Update Successfully!!", "message" => "Offer section!", "action" => "list_coupon.php");
                }
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
            $ofees = $_POST['ofees'];
            $pfees = $_POST['pfees'];
            $gmode = $_POST['gmode'];
            $show_property = $_POST['show_property'];

            $mfees = $_POST['mfees'];
            $perfees = $_POST['perfees'];

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
            $why_choose_us_title_en = $_POST['why_choose_us_title_en'];
            $why_choose_us_title_ar = $_POST['why_choose_us_title_ar'];
            $why_choose_us_description_ar = htmlspecialchars(trim($_POST['why_choose_us_description_ar']));
            $why_choose_us_description_en = htmlspecialchars(trim($_POST['why_choose_us_description_en']));

            $why_choose_us_description_json = json_encode([
                "en" => $why_choose_us_description_en,
                "ar" => $why_choose_us_description_ar
            ], JSON_UNESCAPED_UNICODE);

            $why_choose_us_title_json = json_encode([
                "en" => $why_choose_us_title_en,
                "ar" => $why_choose_us_title_ar
            ], JSON_UNESCAPED_UNICODE);

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
            $data_values = array("$why_choose_us_title_json", "$why_choose_us_description_json", "$url" , "$why_choose_us_bg");

            $h = new Estate();
            $check = $h->restateinsertdata($field,$data_values ,  $table);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Why choose Us Data Added Successfully!!", "message" => "Why choose Us  section!", "action" => "list_why_choose_us.php");
            }
        } else if ($_POST['type'] == 'edit_why_choose_us') {
            $id = $_POST['id'];

            $why_choose_us_bg = $_POST['why_choose_us_bg'];
            $why_choose_us_title_en = $_POST['why_choose_us_title_en'];
            $why_choose_us_title_ar = $_POST['why_choose_us_title_ar'];
            $why_choose_us_description_ar = htmlspecialchars(trim($_POST['why_choose_us_description_ar']));
            $why_choose_us_description_en = htmlspecialchars(trim($_POST['why_choose_us_description_en']));

            $why_choose_us_description_json = json_encode([
                "en" => $why_choose_us_description_en,
                "ar" => $why_choose_us_description_ar
            ], JSON_UNESCAPED_UNICODE);

            $why_choose_us_title_json = json_encode([
                "en" => $why_choose_us_title_en,
                "ar" => $why_choose_us_title_ar
            ], JSON_UNESCAPED_UNICODE);

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
        }
        else if ($_POST['type'] == 'delete_why_choose_us') {
            $id = $_POST['id'];

            
            $table = "tbl_why_choose_us";
            $where = "where id=" . $id . "";

            $h = new Estate();
            $check = $h->restaterestateDeleteData($where,  $table);

            if ($check == 1) {
                $returnArr = array("ResponseCode" => "200", "Result" => "true", "title" => "Why choose Us Data Deleted Successfully!!", "message" => "Why choose Us  section!", "action" => "list_why_choose_us.php");
            }
        }
        
        elseif ($_POST["type"] == "add_category") {
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

            $listing_date = date("Y-m-d H:i:s");
            $price = $_POST['prop_price'];
            $government = $_POST['pgov'];
            $security_deposit = $_POST['prop_security'];
            $max_days = $_POST['max_day'];
            $min_days = $_POST['min_day'];
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
                                    $returnArr = generateDashboardResponse(500, "false", "Failed to upload image: " . $_FILES['images']['name'][$key], "", "list_properties.php");
                                }
                            } else {
                                // Handle invalid image type
                                $returnArr = generateDashboardResponse(400, "false", "Invalid image type: " . $_FILES['images']['name'][$key], "", "list_properties.php");
                            }
                        } else {
                            // Handle error during file upload
                            $returnArr = generateDashboardResponse(400, "false", "Error uploading image: " . $_FILES['images']['name'][$key], "", "list_properties.php");
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
                $field_values = ["image", "period", "is_featured", "security_deposit", "government", "google_maps_url", "video", "guest_rules", "compound_name", "floor", "status", "title", "price", "address", "facility", "description", "beds", "bathroom", "sqrft",  "ptype",  "city", "listing_date", "add_user_id", "pbuysell",  "plimit", "max_days", "min_days"];
                $data_values = ["$imageUrlsString", "$period", "$featured", "$security_deposit", "$government", "$google_maps_url", "$videoUrlsString", "$guest_rules_json", "$compound_name_json", "$floor_json", "$status", "$title_json", "$price", "$address_json", "$facility", "$description_json", "$beds", "$bathroom", "$sqft",  "$ptype",  "$city_json", "$listing_date", "$propowner", "$pbuysell", "$plimit", "$max_days", "$min_days"];

                $h = new Estate();
                $check = $h->restateinsertdata($field_values, $data_values, $table);
            } else {
                $check = 0;
            }

            if ($check == 1) {
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
            $status = $_POST["status"];
            $plimit = $_POST['plimit'];
            $pbuysell = 1;
            $facility = implode(',', $_POST['facility']);
            $ptype = $_POST['ptype'];
            $beds = $_POST['beds'];
            $bathroom = $_POST['bathroom'];
            $sqft = $_POST['sqft'];
            $user_id = '0';

            $listing_date = date("Y-m-d H:i:s");
            $price = $_POST['prop_price'];
            $government = $_POST['pgov'];
            $security_deposit = $_POST['prop_security'];
            $max_days = $_POST['max_day'];
            $min_days = $_POST['min_day'];
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
                                    $returnArr = generateDashboardResponse(500, "false", "Failed to upload image: " . $_FILES['images']['name'][$key], "", "list_properties.php");
                                }
                            } else {
                                // Handle invalid image type
                                $returnArr = generateDashboardResponse(400, "false", "Invalid image type: " . $_FILES['images']['name'][$key], "", "list_properties.php");
                            }
                        } else {
                            // Handle error during file upload
                            $returnArr = generateDashboardResponse(400, "false", "Error uploading image: " . $_FILES['images']['name'][$key], "", "list_properties.php");
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


            $field_values = ["security_deposit",  "period", "is_featured", "government", "google_maps_url",  "guest_rules", "compound_name", "floor", "status", "title", "price", "address", "facility", "description", "beds", "bathroom", "sqrft",  "ptype",  "city", "listing_date", "add_user_id", "pbuysell",  "plimit", "max_days", "min_days"];
            $data_values = ["$security_deposit", "$period", "$featured", "$government", "$google_maps_url",  "$guest_rules_json", "$compound_name_json", "$floor_json", "$status", "$title_json", "$price", "$address_json", "$facility", "$description_json", "$beds", "$bathroom", "$sqft",  "$ptype",  "$city_json", "$listing_date", "$propowner", "$pbuysell", "$plimit", "$max_days", "$min_days"];

            $combinedArray = array_combine($field_values, $data_values);
            if (!empty($imageUrls)) {
                $combinedArray["image"] =  $imageUrlsString;
            }

            if (!empty($videoUrls)) {
                $combinedArray["video"] =  $videoUrlsString;
            }
            if (!isset($returnArr)) {

                $where = "where id=" . $id . " and add_user_id=" . $propowner . "";
                $h = new Estate();
                $check = $h->restateupdateData($combinedArray, $table, $where);
            } else {
                $check = 0;
            }
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Property Update Successfully!!",
                    "message" => "Property section!",
                    "action" => "list_properties.php",
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
            $field_values = ["img", "status", "title"];
            $data_values = ["$url", "$okey", "$title_json"];

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
                $field = ["status" => $okey, "img" => $url, "title" => $title_json];
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
                $field = ["status" => $okey, "title" => $title_json];
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
        } elseif ($_POST["type"] == "coupon_delete") {
            $id = $_POST["id"];
            $table = "tbl_coupon";
            $where = "where id=" . $id . "";
            $h = new Estate();
            $check = $h->restateDeleteData($where, $table);
            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Coupon Code Delete Successfully!!",
                    "message" => "Coupon Code section!",
                    "action" => "list_coupon.php",
                ];
            }
        } elseif ($_POST["type"] == "com_payout") {
            $payout_id = $_POST["payout_id"];
            $target_dir = dirname(dirname(__FILE__)) . "/images/proof/";
            $url = "images/proof/";
            $temp = explode(".", $_FILES["cat_img"]["name"]);
            $newfilename = round(microtime(true)) . "." . end($temp);
            $target_file = $target_dir . basename($newfilename);
            $url = $url . basename($newfilename);

            move_uploaded_file($_FILES["cat_img"]["tmp_name"], $target_file);
            $table = "payout_setting";
            $field = ["proof" => $url, "status" => "completed"];
            $where = "where id=" . $payout_id . "";
            $h = new Estate();
            $check = $h->restateupdateData($field, $table, $where);

            if ($check == 1) {
                $returnArr = [
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "title" => "Payout Update Successfully!!",
                    "message" => "Payout section!",
                    "action" => "list_payout.php",
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
    $returnArr = generateDashboardResponse(500, "false", "An error occurred!", "$e", "dashboard.php");
    echo $returnArr;
}
