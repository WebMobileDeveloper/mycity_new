<?php 

	date_default_timezone_set('America/Los_Angeles');
	include_once ('mailer\PHPMailerAutoload.php ');
	include_once '../includes/db.php';
	  
	$sql_query = "select * from user_people where client_phone <> '' and 
	 client_zip <> '' and client_location <>  ''  and check_fields in ( 1 , 0) order by check_fields desc ";
	$members = $link->query($sql_query);
	 
	 
	$i =0;
	while($item = $members->fetch_array()):
		$id = $item['id'] ;
		$phone = $item['client_phone'] ;
		$name = $item['client_name'] ;
		$email = $item['client_email'] ;
		$cr_tag = $item['tags'] ; 
		$cr_location = $item['client_location'] ; 
		 
		$inner_query = "select * from user_people where client_email='$email' and 
		( client_phone='' or client_zip = '' or client_location = '' or  
		  client_phone is NULL  or client_zip is NULL or client_location is NULL )  ";
		$inner_rst = $link->query($inner_query);
		 
		
		if($inner_rst->num_rows  > 0)
		{
			while($source_row = $inner_rst->fetch_array())
			{
				$targetid = $source_row['id'];
				$zip = $source_row['client_zip'];
				$city = $source_row['client_location'];
				$tags = $source_row['tags'];
				$src_name = $source_row['client_name'] ; 
				
				if($src_name == $name)
				{ 
					//start copying
					if($city !='')
					{
						$city = $cr_location . ","  . $city;
						$allcity = explode(',', $city); 
						$allcity = array_filter( array_unique($allcity) ); 
						$city = implode(',', $allcity);
					}
					else 
					{
						$city = $cr_location;
					}
				
					
					if($tags !='')
					{
						$tags = $cr_tag ."," . $tags;
						$alltags = explode(',', $tags); 
						$alltags = array_filter( array_unique($alltags) ); 
						$tags = implode(',', $alltags); 
					}
					else 
					{
						$tags = $cr_tag ;
					}
					 
					$update_query = "update user_people  
					set  client_phone='$phone', client_zip='$zip', 
					client_location='$city', 
					tags ='$tags' 
					where id='$targetid'"; 
					$link->query($update_query); 
					//update target rows 
					$mark_row_process = "update user_people set check_fields='100' where id='$targetid' <br/>";
					$link->query($mark_row_process); 
					  
				}
				
			}
			 	   
		} 
		
		//update source row is copied
		$link->query( "update user_people set check_fields='10' where id='$id'" );
		
		if($i > 60 )
			break; 
		$i++;  
	endwhile;
				
?>
