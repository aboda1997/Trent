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

$query = "SELECT cat_id , id, img, JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang_code')) AS title 
          FROM tbl_slider 
          WHERE status=1
        ";

if (isset($_GET['category_id'])) {
    $cat_id = $_GET['category_id']; 
    $query .= " AND cat_id = " . $cat_id;

}
$query .=    " ORDER BY id DESC";

$sel = $rstate->query($query);
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['title'] = $row['title'];
		$pol['img'] = $row['img'];
        $pol['category_id'] = $row['cat_id'];
        
		
		
		
		
		$c[] = $pol;
	
	
}
if(empty($c))
{
    $returnArr    = generateResponse('false', "Slider List Not Founded!", 200, array(
        "slider_list"=>$c
        ,"length" => count($c),
    ));
}
else 
{
    
    $returnArr    = generateResponse('true', "Slider List Founded!", 200, array(
        "slider_list"=>$c
        ,"length" => count($c),
    ));
}
echo $returnArr;
?>