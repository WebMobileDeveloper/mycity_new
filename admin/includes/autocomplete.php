<?php
header('Content-Type: application/json');
if(!isset($_SESSION))session_start();
	include_once 'db.php';
	$user_id = @$_SESSION['user_id'];
	  $user_role =  $_SESSION['user_role'];
 
	$searchTerm = $_GET['phrase'];

	if($user_role == 'admin')
	{
			 $mainQry = "SELECT * FROM user_people where  client_name like '$searchTerm%' ORDER by client_name LIMIT 10" ;

			 $connections = $link->query( $mainQry );
	
			$users = array();
			if( $connections->num_rows > 0)
			{ 
				while($row = $connections->fetch_array() )
				{ 
					$users[] = array( 'code' => $row['id'] , 'name' => $row['client_name']); 
				} 
			}
	 }
	 else 
	 {
		
			//read active user group and create where clause for group id search
			$user = $link->query("SELECT * FROM user_details where user_id = '$user_id'");
			$user = $user->fetch_array();
			$groups = explode(",", $user['groups']);  
			$where_in_set = " (  " ;
			for($i=0; $i < sizeof($groups); $i++ )
			{
				$groupid = $groups[$i];
				$where_in_set .= " FIND_IN_SET('$groupid', groups) "; 
				if( $i < sizeof($groups)-1 )
				{
					$where_in_set .= " OR "; 
				}
			}
			$where_in_set .=" ) " ;
			$qryInner = " SELECT a.user_id FROM user_details as a inner join mc_user as b on b.id = a.user_id 
			WHERE $where_in_set  AND  b.id != '1' and user_pkg='Gold'  " ;
	
			//create main query to insert any new referrals
			$mainQry = "SELECT  id, client_name  FROM user_people  WHERE client_name LIKE '$searchTerm%' and   user_id IN  ( $qryInner )  ORDER BY client_name LIMIT 10" ;
	  
			$connections = $link->query( $mainQry );
	
			$users = array();
			if( $connections->num_rows > 0)
			{ 
				while($row = $connections->fetch_array() )
				{ 
					$users[] = array( 'code' => $row['id'] , 'name' => $row['client_name']); 
				} 
			}	 
	}

	echo  json_encode(  $users );
	
	?>