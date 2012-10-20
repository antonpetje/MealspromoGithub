<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
      html { height: 100% }
      body { height: 100%; margin: 0; padding: 0 }
      #map_canvas { height: 100%;width:70%;float:left;}
	  #rightpanel { height: 100%;width:29%;border:1px solid black;float:left; }
    </style>
    <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY3rtzKQi4Mah78JzOfzJD9dRK-MjaYoE&sensor=true">
    </script>
    <script type="text/javascript">
      function initialize() {
        var mapOptions = {
          zoom: 8,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
		

		if(navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var pos = new google.maps.LatLng(position.coords.latitude,
                                             position.coords.longitude);

            
			var marker = new google.maps.Marker({
				position: pos,
				map: map,
				title: 'I am here!!',
				draggable:true
			});

            map.setCenter(pos);
          }, function() {
            handleNoGeolocation(true);
          });
        } else {
          // Browser doesn't support Geolocation
          handleNoGeolocation(false);
        }
      }

	  function handleNoGeolocation(errorFlag) {
        if (errorFlag) {
          var content = 'Error: The Geolocation service failed.';
        } else {
          var content = 'Error: Your browser doesn\'t support geolocation.';
        }

        var options = {
          map: map,
          position: new google.maps.LatLng(1.283333,103.833333),
          content: content
        };

        var infowindow = new google.maps.InfoWindow(options);
        map.setCenter(options.position);
      }
    </script>
  </head>
  <body onload="initialize()">
    <div id="map_canvas" style="width:70%; height:100%"></div>
	<div id="rightpanel" style="width:29%; height:100%"></div>
  </body>
</html>