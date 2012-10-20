<?php
function getAPI(){
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$TheIp=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else {
		$TheIp=$_SERVER['REMOTE_ADDR'];
	}
}
?>