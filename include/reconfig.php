<?php
require_once  dirname( dirname(__FILE__) ).'/include/load_language.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
  $rstate = new mysqli("localhost", "root", "", "trent_PROPERTY");
  $rstate->set_charset("utf8mb4");
} catch(Exception $e) {
  error_log($e->getMessage());
  //Should be a message a typical user could understand
}
    
	$set = $rstate->query("SELECT * FROM `tbl_setting`")->fetch_assoc();
	
	$main = $rstate->query("SELECT * FROM `tbl_prop`")->fetch_assoc();
	
?>