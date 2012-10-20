<?php
$getCountryUrl="http://api.hostip.info/get_json.php";
$getCountry=file_get_contents($getCountryUrl);
$getCountry=json_decode($getCountry);
$countryName=$getCountry->country_name;
$cityName=$getCountry->city;
$locationName=urlencode($cityName." ".$countryName);
//$getCoordinateUrl="https://maps.googleapis.com/maps/api/place/details/json?address=".$countryName."&sensor=true&key=AIzaSyDY3rtzKQi4Mah78JzOfzJD9dRK-MjaYoE";
$getCoordinateUrl="http://maps.googleapis.com/maps/api/geocode/json?address=".$locationName."&sensor=true";

$getCoordinate=file_get_contents($getCoordinateUrl);

$getCoordinate=json_decode($getCoordinate);


foreach($getCoordinate->results as $ac){
	$location['lat']=$ac->geometry->location->lat;
	$location['lng']=$ac->geometry->location->lng;
}
echo json_encode($location);
?>