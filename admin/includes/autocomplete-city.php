<?php
header('Content-Type: application/json');
	session_start();
	include_once 'db.php'; 
   
	$searchTerm = $_GET['phrase']; 
	$mainQry = "( select id, grp_name as pkey from groups where grp_name like '$searchTerm%' and grp_name !='' and islisted='1' order by grp_name limit 30 ) union " .
	" ( select  distinct id, zip as pkey from user_details where zip like '$searchTerm%'  and zip !='' order by zip limit 30 ) " ;
	$connections = $link->query( $mainQry );
	
	$zips = array();
	if( $connections->num_rows > 0)
	{ 
		while($row = $connections->fetch_array() )
		{ 
			$zips[] = array( 'code' => $row['id'] , 'name' => $row['pkey']); 
		} 
	}
	echo  json_encode(  $zips );
	
 ?> 