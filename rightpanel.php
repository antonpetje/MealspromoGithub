<?php
require 'dbConnect.php';

function checkResto($gid){
	$queryFind=mysql_query("select * from resto_googleplaces where resto_googleplacesid='$gid' && resto_googleplacesid!='' LIMIT 1");
	while($lineFind=mysql_fetch_array($queryFind)){
		$rgp_id=$lineFind['rgp_id'];
		$resto_id=$lineFind['resto_id'];
		$resto_googleplacesid=$lineFind['resto_googleplacesid'];
		$resto_googlereference=$lineFind['resto_googlereference'];
		$rgp_updatedate=$lineFind['rgp_updatedate'];
	}
	return	$resto_id;
}
//echo $_POST['ref'];
if(isset($_POST['Latlng']) && isset($_POST['gid']) && isset($_POST['ref']) &&  $_POST['gid']!=""){
	$Latlng=$_POST['Latlng'];
	$gid=$_POST['gid'];
	$ref=$_POST['ref'];
	$promotion=0;
	//check in database any restaurant with this referenceID
	//if found get the information
	//if not found get the information from google map API
	//save the data into database
	
	$resto_id=checkResto($gid); //output will be restaurant ID
	if($resto_id!="" && $resto_id!=0){ 
		//echo "existing restaurant";
		//get data from database
		$query=mysql_query("select * from resto where resto_id='$resto_id' LIMIT 1");
		while($line=mysql_fetch_array($query)){
			$name=$line['resto_name'];
			$address=$line['resto_address'];
			$phone=$line['resto_phone'];
			$longitude=$line['resto_longitude'];
			$latitude=$line['resto_latitude'];
		}
	}else{
		//echo "new restaurant";
		//get data from googlemap API
		//insert data into database
		$url="https://maps.googleapis.com/maps/api/place/details/json?reference=".$ref."&sensor=true&key=AIzaSyDY3rtzKQi4Mah78JzOfzJD9dRK-MjaYoE";
		$json=file_get_contents($url);
		$jsonA=json_decode($json);
		$googleID=$jsonA->result->id;
		$formatted_address=$jsonA->result->formatted_address;
		$formatted_phone_number=$jsonA->result->formatted_phone_number;
		$longitude=$jsonA->result->geometry->location->lng;
		$latitude=$jsonA->result->geometry->location->lat;
		$international_phone_number=$jsonA->result->international_phone_number;
		$name=$jsonA->result->name;
		$url=$jsonA->result->url;
		$vicinity=$jsonA->result->vicinity;
		$website=$jsonA->result->website;

		if(is_array($jsonA->result->address_components)){
			foreach($jsonA->result->address_components as $ac){
				if (in_array("country", $ac->types)) {
					$country=$ac->long_name;
				}
				if (in_array("postal_code", $ac->types)) {
					$postal_code=$ac->long_name;
				}
			}
		}
		if($vicinity==""){
			$address=$formatted_address;
		}else{
			$address=$vicinity;
		}
		if($international_phone_number==""){
			$phone=$formatted_phone_number;
		}else{
			$phone=$international_phone_number;
		}

		if(($jsonA->status)=="OK"){
			//insert into resto
			//insert into resto_googleplaces
			$queryInsert=mysql_query("insert into resto (resto_name,resto_address,resto_longitude,resto_latitude,country,resto_phone,resto_website,resto_postalcode,resto_googleplus) values ('$name','$address','$longitude','$latitude','$country','$phone','$website','$postal_code','$url')");
			$lastresto=mysql_query("select max(resto_id) as resto_id from resto");
			while($lineLastresto=mysql_fetch_array ($lastresto)){
				$resto_id=$lineLastresto['resto_id'];
			}
			$queryInsert2=mysql_query("insert into resto_googleplaces (resto_id,resto_googleplacesid,resto_googlereference,rgp_updatedate) values ('$resto_id','$googleID','$ref',NOW())");
		}
	}
	echo "<div class='rp_restoname'>".$name."</div>";
	if($address!=""){
		echo  "<div class='rp_restoaddress'>Address: ".$address."</div>";
	}
	if($phone!=""){
		echo  "<div class='rp_restophone'>Phone: ".$phone."</div>";
	}
	?>
	<div class="rp_promotion">
	<?php
	$promotion=0;
	$todayDate=date('Y-m-d');
	$query=mysql_query("select * from promotion where resto_id='$resto_id' and DATE(promo_enddate)>='$todayDate' and promo_status='1' order by promo_startdate asc");
	while($line=mysql_fetch_array($query)){
		$promo[$promotion]['title']=$line['promo_name'];
		$promo[$promotion]['id']=$line['promo_id'];
		$promo[$promotion]['start']=$line['promo_startdate'];
		$promo[$promotion]['end']=$line['promo_enddate'];
		$promo[$promotion]['description']=$line['promo_description'];
		$promo[$promotion]['url']=$line['promo_url'];
		$promotion++;
	}
	if($promotion==0){
		echo "Seems no promotion in this restaurant, <div class='btn1 addpromotionhome' restoID='".$resto_id."'>click here</div> if you know.";
	}else{
		echo "We found promotion(s)/deals in this restaurant";
		foreach($promo as $p){
				$title=$p['title'];
				$startDate=$p['start'];
				$endDate=$p['end'];
				$description=$p['description'];
				$url=$p['url'];
			echo "<div class='detailPromo'>";
				if($title!=""){
					echo "<div class='detailPromo_title'>".$title."</div>";
				}
				if($startDate!=""){
					if($startDate==$endDate){
						$dateOfEvent=date("l, j F Y",strtotime($startDate));
					}else{
						$dateOfEvent=date("l, j F Y",strtotime($startDate))." - ".date("l, j F Y",strtotime($endDate));
					}
					echo "<div class='detailPromo_date'>".$dateOfEvent."</div>";
				}
				if($description!=""){
					echo "<div class='detailPromo_description'>".$description."</div>";
				}
				if($url!=""){
					echo "<div class='detailPromo_url'><a href='http://".$url."'>".$url."</a></div>";
				}
			echo "</div>";
		
		}
		echo "<br/><div class='btn1 addpromotionhome' restoID='".$resto_id."'>click here</div> if you know another promotion";
	}
	?>
	</div>
	<script>
	$(document).ready(function() {
		$(".addpromotionhome").click(function(){
			var restoID = $( "#addPromo_restoID" ),
				promoTitle = $( "#promo_title" ),
				promoStart = $( "#promo_start" ),
				promoEnd = $( "#promo_end" ),
				promoUrl = $( "#promo_url" ),
				promoDesc = $( "#promo_description" ),
				allFields = $( [] ).add(restoID).add(promoTitle).add(promoStart).add(promoEnd).add(promoUrl).add(promoDesc);
				var message="";
			$("#addPromo_restoID").val("<?php echo $resto_id;?>");
			$( "#dialogAddPromotion" ).dialog({
				position:'center',
				modal: true,
				title: 'Share the promotion that you know',
				closeOnEscape: true,
				resizable:false,
				draggable:true,
				width: 400,
				open: function(event, ui){

				},
				close: function(event, ui) {
					allFields.val( "" ).removeClass( "ui-state-error" );
					$( "#promo_end" ).datepicker( "option", "minDate", 0 );
					$( "#promo_start" ).datepicker( "option", "maxDate", null );
				},
				buttons: {
					"Submit": function() {
						if(promoStart.val()!="" && promoDesc.val()!="" && restoID.val()!=""){
							//store to database
							var sentData={restoID:restoID.val(),promoTitle:promoTitle.val(),promoStart:promoStart.val(),promoEnd:promoEnd.val(),promoUrl:promoUrl.val(),promoDesc:promoDesc.val()};		
							$.post("savepromotion.php",sentData,function(data){
								if(data==1){
									rightPanel("<?php echo $latitude;?>","<?php echo $longitude;?>","<?php echo $gid;?>","<?php echo $ref;?>");
									$( "#dialogAddPromotion" ).dialog( "close" );
								}else{
									alert("Please try again.");
								}
							});	
													
						}else{
							alert("Invalid input,please try again.");
						}						
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});	
		});
	  });
	</script>
	<?php

}else{
	echo "Restaurant not found";
}

?>


