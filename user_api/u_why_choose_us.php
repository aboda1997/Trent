<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

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
   
		$pol['description'] = $row['why_choose_us_description'];
		$pol['title'] = $row['why_choose_us_title'];
		$pol['img'] = $row['why_choose_us_img'];
		$pol['bg'] = $row['why_choose_us_bg'];
		
		
		
		
		$c[] = $pol;
	
	
}
if(empty($c))
{
	$returnArr    = generateResponse('false', "Why Choose Us Data Not Founded!", 404, array(
		"why_choose_us"=>$c
	));
}
else 
{
	$returnArr    = generateResponse('true', "Why Choose Us Data Founded!", 200, array(
		"why_choose_us"=>$c
	));
}
echo $returnArr;
?>