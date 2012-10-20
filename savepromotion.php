<?php
require 'dbConnect.php';

if(isset($_POST['promoStart']) && isset($_POST['promoDesc']) && isset($_POST['restoID'])){
	$restoID=$_POST['restoID'];
	$promoTitle=$_POST['promoTitle'];
	$promoStart=date("Y-m-d",strtotime($_POST['promoStart']));
	$promoEnd=$_POST['promoEnd'];
	$promoDesc=$_POST['promoDesc'];
	$promoUrl=$_POST['promoUrl'];
	if($promoEnd==""){
		$promoEnd=$promoStart;
	}
	$promoEnd=date("Y-m-d",strtotime($promoEnd));
	$query=mysql_query("insert into promotion (resto_id,promo_name,promo_startdate,promo_enddate,promo_description,promo_status,promo_url) values ('$restoID','$promoTitle','$promoStart','$promoEnd','$promoDesc','1','$promoUrl')");
	if (mysql_affected_rows() > 0) {
		$status=1;
	}else{
		$status=0;
	}
	echo $status;

}
?>

