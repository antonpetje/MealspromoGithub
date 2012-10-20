<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style>
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map { height: 100%;width:70%;float:left;}
	  #rightpanel { height: 100%;width:29%;border:1px solid black;float:left; }
    </style>
	
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY3rtzKQi4Mah78JzOfzJD9dRK-MjaYoE&sensor=true&libraries=places"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script type="text/javascript">
	$(document).ready(function() {
		
		
	});
    function initialize() {
		var myLatlng = new google.maps.LatLng(1.280659,103.838303);
        var mapOptions = {
          center: myLatlng,
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map"),
            mapOptions);
		var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: 'Hello World!',
			draggable:true
        });
		google.maps.event.addDomListener(marker, 'dragend', updateMarker);		
		function updateMarker() {
			var newLatlng=marker.getPosition();
			//update
			var request = {
			  location: newLatlng,
			  radius: 1000,
			  types: ['restaurant']
			};
			var service = new google.maps.places.PlacesService(map);
			service.search(request, callback);			
		 }
     }	
	
	
	function rightPanel(lat,lng,ref) {
		$("#rightpanel").html("aku di klik"+lat+" - "+lng+" - "+ref);
		
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
		var markerresto = new google.maps.Marker({
		  map: map,
		  position: place.geometry.location
		});

		google.maps.event.addListener(markerresto, 'click', function() {
			newLatlng=markerresto.getPosition();
			rightPanel(newLatlng.lat(),newLatlng.lng(),ref);
		});
	  }
    </script>
  </head>
  <body onload="initialize()">
    <div id="map" style="width:70%; height:100%"></div>
	<div id="rightpanel" style="width:29%; height:100%"></div>
  </body>
</html>