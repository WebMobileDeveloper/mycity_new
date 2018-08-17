<?php
header('Content-Type: application/json');
	session_start();
	include_once 'db.php';
	$user_id = @$_SESSION['user_id'];
    $user_role =  $_SESSION['user_role'];
	 
	$searchTerm = $_GET['phrase'];
  

  if($user_role == 'admin')
	$mainQry = "SELECT * FROM user_people where  client_name like '$searchTerm%' ORDER by client_name LIMIT 10" ;
  else 
	$mainQry = "SELECT * FROM user_people where user_id = '$user_id' and client_name like '$searchTerm%' ORDER by client_name LIMIT 10" ;
	
	
	$connections = $link->query( $mainQry );
	
	$users = array();
	if( $connections->num_rows > 0)
	{ 
		while($row = $connections->fetch_array() )
		{ 
			$users[] = array( 'code' => $row['id'] , 'name' => $row['client_name']); 
		} 
	}		
		echo  json_encode(  $users );
	
	?>