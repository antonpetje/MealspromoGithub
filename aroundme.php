<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
	<meta content="width=device-width,minimum-scale=1,maximum-scale=1" name="viewport" />
    <title>Find meals promotion around you, Anywhere Anytime </title>
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<link href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
     <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY3rtzKQi4Mah78JzOfzJD9dRK-MjaYoE&sensor=true&libraries=places"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
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
        var pyrmont = new google.maps.LatLng(1.283333,103.833333);

		map = new google.maps.Map(document.getElementById('map'), {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			center: pyrmont,
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
		
		
		if(navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude); 
			pertama(pos);
          }, function() {

          });
        } else {			
			// Browser doesn't support Geolocation
			alert("This website require geolocation,and your browser not support geolocation. Please update your browser or try using another browser.");
        }               
      }
	function pertama(pos){		
		var marker = new google.maps.Marker({
				position: pos,
				map: map,
				title: 'I am here!!',
				draggable:true,
				icon: 'http://google-maps-icons.googlecode.com/files/cluster.png'
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
			Welcome,<br/>
			Click restaurant marker to see the restaurant promotion!<br/>
			Move your current location to see another restaurant.
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
  </body>
  <script>
	$(document).ready(function() {		
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
	  });
	</script>
</html>
