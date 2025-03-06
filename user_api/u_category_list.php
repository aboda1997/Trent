<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
$lang_code = 'en';
 
 if($_GET['lang']){
$lang_code = $_GET['lang'];
 }
$pol = array();
$c = array();
$sel = $rstate->query("select JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang_code')) as title , id , img from tbl_category where status=1");
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['title'] = $row['title'];
		$pol['img'] = $row['img'];
		
		$c[] = $pol;
	
	
}
if(empty($c))
{
	$returnArr    = generateResponse('false', "Category List Not Founded!", 200, array(
		"category_list"=>$c
		,"length" => count($c),
	));
}
else 
{

$returnArr    = generateResponse('true', "Category List  Founded!", 200, array(
	"category_list"=>$c
	,"length" => count($c),
));
}
echo $returnArr;
?>