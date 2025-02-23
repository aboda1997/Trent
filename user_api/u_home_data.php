<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$uid = isset($data['uid']) ? intval($data['uid']) : null;
if ($uid == null) {
    $returnArr = array(
        "ResponseCode" => "401",
        "Result" => "false",
        "ResponseMsg" => "Something Went Wrong!"
    );
	http_response_code(401);

} else {
	
	
	$f = array();
	$fp = array();
	$vop =array();
	$fpv = array();
	$fps = array();
	$cat  = array();
	
	$wo = array();
	$wo['id'] = "0";
		$wo['title'] = "All";
		$wo['img'] = "images/category/grid-circle.png";
		$wo['status'] = "1";
	$sql = $rstate->query("select * from tbl_category where status=1");
	while($rp = $sql->fetch_assoc())
	{
		$vop['id'] = $rp['id'];
		$vop['title'] = json_decode($rp['title'], true);
		$vop['img'] = $rp['img'];
		$vop['status'] = $rp['status'];
		$cat[] = $vop;
	}
	array_unshift($cat , $wo);
	if($uid == 0)
{
	$prop = $rstate->query("select * from tbl_property where  status=1 and is_sell=0  order by id desc limit 5 ");
}
else 
{
	$prop = $rstate->query("select * from tbl_property where  status=1 and is_sell=0 and add_user_id =".$uid." order by id desc ");
}
	while($row = $prop->fetch_assoc())
	{

		$vr = array();
		$imageArray = explode(',', $row['image']);
	
		// Loop through each image URL and push to $vr array
		foreach ($imageArray as $image) {
			$vr[] = array('image' => trim($image), 'is_panorama' => 0);
		}
	
		$get_extra = $rstate->query("select img,pano from tbl_extra where pid=" . $row['id'] . "");
		while ($rk = $get_extra->fetch_assoc()) {
			array_push($vr, array('image' => $rk['img'], 'is_panorama' => intval($rk['pano'])));
		}
		$fp['id'] = $row['id'];
		$fp['title'] =  json_decode($row['title'], true);
		$fp['plimit'] = $row['plimit'];
		$checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=".$row['id']." and book_status='Completed' and total_rate !=0")->num_rows;
		if($checkrate !=0)
		{
			$rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=".$row['id']." and book_status='Completed' and total_rate !=0")->fetch_assoc();
			$fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 0, '.', '');
		}
		else 
		{
		$fp['rate'] = null;
		}
		$fp['security_deposit'] = $row['security_deposit'];
		$fp['google_maps_url'] = $row['google_maps_url'];
		$fp['video'] = $row['video'];
		$fp['max_days'] = $row['max_days'];
		$fp['min_days'] = $row['min_days'];
	
		$fp['floor'] = json_decode($row['floor'], true);
		$fp['guest_rules'] = json_decode($row['guest_rules'], true);
		$fp['compound_name'] = json_decode($row['compound_name'], true);
		$fp['description'] = json_decode($row['description'], true);
		$fp['address'] = json_decode($row['address'], true);
		$fp['city'] = json_decode($row['city'], true);
		$fp['property_type'] = $row['ptype'];
		$fp['beds'] = $row['beds'];
		$fp['bathroom'] = $row['bathroom'];
		$fp['sqrft'] = $row['sqrft'];
		$fp['image'] = $vr;
		$fp['price'] = $row['price'];
		$fp['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where uid=".$uid." and property_id=".$row['id']."")->num_rows;
		$f[] = $fp;
	}
	
	$tbwallet = $rstate->query("select wallet from tbl_user where id=".$uid."")->fetch_assoc();
if($uid == 0)
{
	$wallet = "0";
}
else 
{
	$wallet = $tbwallet['wallet'];
}

	$kp = array('Catlist'=>$cat,"currency"=>$set['currency'],"wallet"=>$wallet,"Featured_Property"=>$f,"cate_wise_property"=>$fpv,"show_add_property"=>$set['show_property']);
	
	
	$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Home Data Get Successfully!","HomeData"=>$kp);

}
echo json_encode($returnArr);
?>