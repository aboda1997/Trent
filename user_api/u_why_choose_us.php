<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-Type: application/json');
try{
$lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

$pol = array();
$c = array();
$cc = array();
$sel = $rstate->query("select is_header ,JSON_UNQUOTE(JSON_EXTRACT(description, '$.$lang_code')) as description , JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang_code')) as title ,img , background_color from tbl_why_choose_us ");
while($row = $sel->fetch_assoc())
{
   
		$pol['description'] = $row['description'];
		$pol['title'] = $row['title'];
		$pol['img'] = $row['img'];
		$pol['background_color'] = $row['background_color'];
		
		if ($row['is_header'] == 1){
		
			$cc['description'] = $row['description'];
			$cc['title'] = $row['title'];
			$cc['img'] = $row['img'];
			$cc['background_color'] = $row['background_color'];
			
		}else{
			$c[] = $pol;

		}
		
		
		
	
	
}
if(empty($c))
{
	$returnArr    = generateResponse('true', "Why Choose Us Data Not Founded!", 200, array(
		array("why_choose_us_list"=>$c , "why_choose_us_header" => $cc)
	));
}
else 
{
	$returnArr    = generateResponse('true', "Why Choose Us Data Founded!", 200, array(
		array("why_choose_us_list"=>$c , "why_choose_us_header" => $cc)
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