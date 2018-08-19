<?php

	set_time_limit(1800);
	date_default_timezone_set('America/Los_Angeles');
	include_once ('mailer\PHPMailerAutoload.php ');
	include_once '../includes/db.php'; 
	$sql_query = "select * from user_people where (client_phone <> '' or  client_phone is not null) and 
	check_fields in (10, 100) order by id desc ";
	$members = $link->query($sql_query); 
	$i =0;  
	
	while($item = $members->fetch_array()): 
		$source_id = $item['id'] ;
		$source_phone = $item['client_phone'] ;
		$source_name = $item['client_name'] ;
		$source_email = $item['client_email'] ;
		$source_cr_tag = $item['tags'] ; 
		$source_cr_location = $item['client_location'] ;
		 
		//update source row is copied
		$link->query( "update user_people set check_fields='1000' where id='$source_id'" );  
		$inner_query = "select * from user_people where client_email='$source_email' and 
		( client_phone='' or client_zip = '' or client_location = '' or 
		client_phone is NULL  or client_zip is NULL or client_location is NULL )  ";
		
		$inner_rst = $link->query($inner_query);
		if($inner_rst->num_rows  > 0)
		{
			while($target_row = $inner_rst->fetch_array())
			{
				$targetid = $target_row['id'];
				$target_zip = $target_row['client_zip'];
				$target_city = $target_row['client_location'];
				$target_tags = $target_row['tags'];
				$target_src_name = $target_row['client_name'] ; 
				
				if($source_name == $target_src_name)
				{ 
					//start copying
					if($target_city !='')
					{
						$city = $source_cr_location . ","  . $target_city;
						$allcity = explode(',', $city); 
						$allcity = array_filter( array_unique($allcity) );
						$city = implode(',', $allcity);
					}
					else 
					{
						$city = $source_cr_location;
					}
					
					if($target_tags !='')
					{
						$tags = $source_cr_tag ."," . $target_tags;
						$alltags = explode(',', $tags); 
						$alltags = array_filter( array_unique($alltags) ); 
						$tags = implode(',', $alltags); 
					}
					else 
					{
						$tags = $source_cr_tag ;
					}
					 
					$update_query = "update user_people  
					set  client_phone='$source_phone', client_zip='$target_zip', 
					client_location='$city',  check_fields='1000' 
					where id='$targetid'";
					$link->query($update_query);	 
				}
			}
		}  
		$i++;  
	endwhile;
	
	
	
?>
