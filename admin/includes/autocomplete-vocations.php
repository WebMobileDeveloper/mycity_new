<?php
header('Content-Type: application/json');
	session_start();
	include_once 'db.php'; 
   
	$searchTerm = $_GET['phrase'];
  
	$mainQry = "SELECT * FROM vocations where voc_name like '$searchTerm%' ORDER by voc_name LIMIT 50" ;
	$connections = $link->query( $mainQry );
	
	$users = array();
	if( $connections->num_rows > 0)
	{ 
		while($row = $connections->fetch_array() )
		{ 
			$users[] = array( 'code' => $row['id'] , 'name' => $row['voc_name']); 
		} 
	}		
		echo  json_encode(  $users );
	
	?>