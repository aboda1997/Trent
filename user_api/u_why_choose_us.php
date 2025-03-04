<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang_code = 'en';
 
 if($_GET['lang']){
$lang_code = $_GET['lang'];
 }
$pol = array();
$c = array();
$sel = $rstate->query("select JSON_UNQUOTE(JSON_EXTRACT(why_choose_us_description, '$.$lang_code')) as why_choose_us_description , JSON_UNQUOTE(JSON_EXTRACT(why_choose_us_title, '$.$lang_code')) as why_choose_us_title ,why_choose_us_img , why_choose_us_bg from tbl_setting ");
while($row = $sel->fetch_assoc())
{
   
		$pol['why_choose_us_description'] = $row['why_choose_us_description'];
		$pol['why_choose_us_title'] = $row['why_choose_us_title'];
		$pol['why_choose_us_img'] = $row['why_choose_us_img'];
		$pol['why_choose_us_bg'] = $row['why_choose_us_bg'];
		
		
		
		
		$c[] = $pol;
	
	
}
if(empty($c))
{
	$returnArr = array("why_choose_us_data"=>$c,"ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"why choose us data Not Founded!");
}
else 
{
$returnArr = array("why_choose_us_data"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"why choose us data Founded!");
}
echo json_encode($returnArr);
?>