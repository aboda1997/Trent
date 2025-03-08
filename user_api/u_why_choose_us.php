<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
try{
$lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

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
	$returnArr    = generateResponse('true', "Why Choose Us Data Not Founded!", 200, array(
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

} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
}
?>