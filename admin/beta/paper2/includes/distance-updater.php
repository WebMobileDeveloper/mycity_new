<?php
	//USE THIS FILE TO UPDATE DISTANCE IN BACKEND
	date_default_timezone_set('America/Los_Angeles');
	set_time_limit(60*10);
	
	ini_set('post_max_size', '64M');
	ini_set('upload_max_filesize', '64M');
 
	session_start();
	include_once 'db.php';
	include_once 'functions.php';

	$user_id = @$_SESSION['user_id'];
	$_username = @$_SESSION['username'];
	$_user_email = @$_SESSION['user_email'];
	$_user_phone = @$_SESSION['user_phone'];
	$_user_role = @$_SESSION['user_role'];

	$results = $link->query( " select id, sourcezip, targetzip from referralsuggestions2 
	where sourcezip<> '' and sourcezip <> '0' and targetzip <> '' and targetzip <> '0' and distance = '0' and distancecalculated='0' order by id desc " );
	
	
	$distance =0;
    if($results->num_rows > 0)
    {
		$i=0;
		while($row = $results->fetch_array())
        {
			if($i >  500) break;
			$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins='. $row['sourcezip']  .  
			'&destinations='. $row['targetzip'] . '&key=AIzaSyATxvU0zarl51s5k4Vql-hfztyhMqekzp4' ; 
			$json = file_get_contents($url); 
			$details = json_decode($json, TRUE); 
			$distanceinmiles  =  ( $details['rows'][0]['elements'][0]['distance']['value'] * 0.000621371); 
		    $link->query("UPDATE referralsuggestions2 SET distance='" . $distanceinmiles  . 
			 "', distancecalculated='1' WHERE  id = '" . $row['id'] . "' ");  
			echo    '<p>('.$i . ')  <a target="_blank" href="'. $url .'">'.$url.'</a><br/>Distance updated: '. $distanceinmiles. '</p>'  ; 
			 	 
			$i++; 
		}
    }
	
	
?>