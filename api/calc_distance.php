<?php


header('Access-Control-Allow-Origin: *');


function calculate_distance($source, $target)
{

 	$details = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins='.$source .
	'&destinations='.$target .
	'&key=AIzaSyATxvU0zarl51s5k4Vql-hfztyhMqekzp4' ; 
	
	 
 	$json = file_get_contents($details);
	
 
 	$details = json_decode($json, TRUE);
 	$distanceinmiles  =  ( $details['rows'][0]['elements'][0]['distance']['value'] * 0.000621371); 
 	$distancemat = array('distance'=> $distanceinmiles);
 	return json_encode( $distancemat ); 
}



$sourcezip = $_POST['source'];
$targetzip = $_POST['target'];
 

if($sourcezip > 0 && $targetzip > 0)
{
	echo calculate_distance($sourcezip, $targetzip);
}
else
{
	$distancemat = array('distance'=> -1);
 	echo  json_encode( $distancemat ); 
}



?>