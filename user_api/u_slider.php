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

$query = "SELECT cat_id , id, img, JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang_code')) AS title 
          FROM tbl_slider 
          WHERE status=1
        ";

if (isset($data['cat_id'])) {
    $cat_id = $data['cat_id']; 
    $query .= " AND cat_id = " . $cat_id;

}
$query .=    " ORDER BY id DESC";

$sel = $rstate->query($query);
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['title'] = $row['title'];
		$pol['img'] = $row['img'];
        $pol['cat_id'] = $row['cat_id'];
        
		
		
		
		
		$c[] = $pol;
	
	
}
if(empty($c))
{
	$returnArr = array("sliderlist"=>$c,"ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"slider list Not Founded!");
}
else 
{
$returnArr = array("sliderlist"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"slider List Founded!");
}
echo json_encode($returnArr);
?>