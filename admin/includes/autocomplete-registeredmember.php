<?php
header('Content-Type: application/json');
	session_start();
	include_once 'db.php';
	$user_id = @$_SESSION['user_id'];
    $user_role =  $_SESSION['user_role'];


	$searchTerm = $_GET['phrase'];
  
  if($user_role == 'admin')
	$mainQry = "SELECT * FROM mc_user where  username like '%$searchTerm%' ORDER by username LIMIT 50" ;
  else  
	$mainQry = "SELECT * FROM mc_user where id = '$user_id' and username like '%$searchTerm%' ORDER by username LIMIT 50" ;
	
	$connections = $link->query( $mainQry );
	
	$users = array();
	if( $connections->num_rows > 0)
	{ 
		while($row = $connections->fetch_array() )
		{ 
			$users[] = array( 'code' => $row['id'] , 'name' => $row['username']); 
		} 
	}		
		echo  json_encode(  $users );
	
	?>