<?php 
	ini_set('max_execution_time', 600);
	date_default_timezone_set('America/Los_Angeles');
	include_once ('mailer\PHPMailerAutoload.php ');
	include_once '../includes/db.php';
	  
	$sql_query = "select * from user_people where client_phone <> '' and 
	(client_zip = '' or client_location = '' ) and check_fields in ( 0, 1) order by check_fields desc";
	$members = $link->query($sql_query);
	 
	$i =0;
	while($item = $members->fetch_array()):
		$id = $item['id'] ;
		$phone = $item['client_phone'] ;
		$name = $item['client_name'] ;
		$email = $item['client_email'] ;
		$cr_tag = $item['tags'] ; 
		$cr_location = $item['client_location'] ; 
		$inner_query = "select * from user_people where client_phone='$phone' and 
		client_zip <> '' and client_location <> ''   ";
		$inner_rst = $link->query($inner_query);
		
		echo   $inner_query . '<br/>';
		
		if($inner_rst->num_rows  > 0)
		{
			$source_row = $inner_rst->fetch_array();
			$zip = $source_row['client_zip'];
			$city = $source_row['client_location'];
			$tags = $source_row['tags'];
			$src_name = $source_row['client_name'] ;
			$src_email = $source_row['client_email'] ;
			 
			if($cr_location !='')
				$city = $cr_location ."," . $city;
			
			if($cr_tag !='')
				$tags = $cr_tag ."," . $tags;
			
			if($src_name== $name && $src_email == $email )
			{
				$update_query = "update user_people 
				set client_zip='$zip', 
				client_location='$city', 
				tags ='$tags' 
				where id='$id'"; 
				echo   $update_query . '<br/>';
				 
				/* 
				 
				 $link->query($update_query); 
				 */
			} 
		} 
		
		//update log
		//$link->query( "update user_people set check_fields='10' where id='$id'" );
		
		if($i > 100 )
			break; 
		$i++;  
	endwhile;
				
?>
