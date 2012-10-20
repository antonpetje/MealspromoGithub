<?php
$firsttime=0;
if (isset($_COOKIE["vstmealspromofirst"])){
	$firsttime=1;
}else{
	$firsttime=0;
}

$expire=time()+60*60*24*30;
setcookie("vstmealspromofirst", "1", $expire);

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
	<meta content="width=device-width,minimum-scale=1,maximum-scale=1" name="viewport" />
    <title>Find meals promotion around you, Anywhere Anytime </title>
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<link href="css/jquery-ui-1.9.0.custom.css" rel="stylesheet">
     <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY3rtzKQi4Mah78JzOfzJD9dRK-MjaYoE&sensor=true&libraries=places"></script>

	
	<script src="js/jquery-1.8.2.js"></script>
	<script src="js/jquery-ui-1.9.0.custom.js"></script>
	<script src="js/modernizr.custom.01297.js"></script>
    <style>
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map { height: 100%;width:70%;float:left;}
	  #rightpanel { height: 100%;width:29%;border:1px solid black;float:left; }
    </style>

    <script>
      var map;
      var infowindow;

      function initialize() {	
		if (Modernizr.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude); 				
				pertama(pos);
			}, function() {
				alert("We can't detect your location. We will use default location.");
				defaultLocation();
			});	
		} else {
			alert("This website require geolocation,and your browser not support geolocation. Please update your browser or try using another browser.");
			defaultLocation();
		}		              
      }	
	function defaultLocation(){
		var sentData={mode:"geolocation"};	
		$.post("getLocDefault.php",sentData,function(data){
			var deflocation = jQuery.parseJSON(data);
			var pos = new google.maps.LatLng(deflocation.lat,deflocation.lng); 
			pertama(pos);
		});	
	}
	function pertama(pos){
		map = new google.maps.Map(document.getElementById('map'), {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: pos,
			zoom: 18,
			styles:
			[
				{
					featureType: "poi",
					elementType: "labels",
					stylers:
					[
						{
							visibility: "off"
						}
					]
				}
			]
        });
		var marker = new google.maps.Marker({
				position: pos,
				map: map,
				title: 'I am here!!',
				draggable:true
		});
		google.maps.event.addDomListener(marker, 'dragend', function(){
			var newLatlng=this.getPosition();
			googleMapMarker(newLatlng);
		});	
		map.setCenter(pos);		
		googleMapMarker(pos);
	}
	function googleMapMarker(pos){
		var request = {
			  location: pos,
			  radius: 600,
			  types: ['restaurant']
			};
			var service = new google.maps.places.PlacesService(map);
			service.search(request, callback);
	}
	 function rightPanel(lat,lng,gid,ref) {
		var sentData={Latlng:lat+","+lng,gid:gid,ref:ref};		
		$.post("rightpanel.php",sentData,function(data){
			$("#rightpanel .rp_resto").html(data);
		});	
		
	 }

      function callback(results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            createMarker(results[i]);
          }			
        }else{
			alert("Seems no restaurant around your marker, please move to another preferred location.");
		}
      }

      function createMarker(place) {
        var placeLoc = place.geometry.location;
		var ref = place.reference;
		var gid = place.id;
        var markerresto = new google.maps.Marker({
			map: map,
			position: place.geometry.location,
			icon:'http://google-maps-icons.googlecode.com/files/restaurant.png'
        });

        google.maps.event.addListener(markerresto, 'click', function() {
			newLatlng=markerresto.getPosition();
			rightPanel(newLatlng.lat(),newLatlng.lng(),gid,ref);
        });
      }

      google.maps.event.addDomListener(window, 'load', initialize);	  
    </script>
  </head>
  <body>
    <div id="map" style="width:70%; height:100%"></div>
	<div id="rightpanel" style="width:29%; height:100%">
		<div class="rp_welcome">
			<span id="menuAbout">About Us</span>
			<span id="menuHelp">Help</span>
			<span id="menuFAQ">FAQ</span>
			<span id="menuDisclaimer">Disclaimer</span>
			<span id="menuContactUs">Contact Us</span>
		</div>		
		<div class="rp_resto">
		
		</div>
	</div>	
	<div id="dialogAddPromotion">
		<input type="hidden" name="start" id="addPromo_restoID">
		<label>Promotion Title</label>
		<input type="text" name="title" id="promo_title" class="text">
		<label>Start Date*</label>
		<input type="text" name="start" id="promo_start" class="text" readonly>
		<label>End Date</label>
		<input type="text" name="end" id="promo_end" class="text" readonly>
		<label>Website</label>
		<input type="text" name="url" id="promo_url" class="text" placeholder="www.example.com">
		<label>Description/Summary*</label>
		<textarea name="description" rows="" cols="" id="promo_description"></textarea>		 
	</div>

	<div id="dialogAbout"></div>
	<div id="dialogHelp">
		Welcome to mealspromo.com,
		<label>How to use:</label>
		Make sure you enable the location feature on your browser.<br/>
		Click restaurant marker around your location to see the restaurant promotion<br/>
		Drag and Drop your location marker to see another restaurant around your marker.
		
	</div>
	<div id="dialogDisclaimer">
		<p>
		All information posted by user. 
		Mealspromo.com not responsible for any messages posted by user.
		</p>
		<p>
		Mealspromo never keep or save any information from user such as user indetity, user location.
		</p>
	</div>
	<div id="dialogContactUs">
		<a href="mailto:mealspromo@gmail.com">mailto:mealspromo@gmail.com</a>
	</div>
	<div id="dialogFAQ">
		
	</div>
  </body>
  <script>
	$(document).ready(function() {
		if("<?php echo $firsttime;?>"==0){
			helpDialog();
		}
		$("#menuAbout").click(function(){
			$( "#dialogAbout" ).dialog({
				position:'center',
				modal: true,
				title: 'About Us',
				closeOnEscape: true,
				resizable:false,
				width:400
			});
		});
		$("#menuFAQ").click(function(){
			$( "#dialogFAQ" ).dialog({
				position:'center',
				modal: true,
				title: 'FAQ',
				closeOnEscape: true,
				resizable:false,
				width:400
			});
		});
		$("#menuHelp").click(function(){
			helpDialog()
		});
		$("#menuDisclaimer").click(function(){
			$( "#dialogDisclaimer" ).dialog({
				position:'center',
				modal: true,
				title: 'Disclaimer',
				closeOnEscape: true,
				resizable:false,
				width:400
			});
		});
		$("#menuContactUs").click(function(){
			$( "#dialogContactUs" ).dialog({
				position:'center',
				modal: true,
				title: 'Contact Us',
				closeOnEscape: true,
				resizable:false,
				width:400
			});
		});
		$( "#promo_start" ).datepicker({
			minDate: 0,
            changeMonth: true,
			dateFormat:'DD, d MM yy',
            onSelect: function( selectedDate ) {
                $( "#promo_end" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $( "#promo_end" ).datepicker({
            changeMonth: true,
			dateFormat:'DD, d MM yy',
            onSelect: function( selectedDate ) {
                $( "#promo_start" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
		function helpDialog(){
			$( "#dialogHelp" ).dialog({
				position:'center',
				modal: true,
				title: 'Help',
				closeOnEscape: true,
				resizable:false,
				width:400
			});
		}
	  });
	</script>
</html>
