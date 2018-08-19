<?php
 
	ini_set('memory_limit', '1024M');
	date_default_timezone_set('America/Los_Angeles'); 
	include_once '../includes/db.php';   
	 
	//field copy
	while(true)
	{
		$outer_loop_query = "SELECT count(*) as cnt, client_email FROM user_people where check_fields not in(-1, -10, 999 ) 
	   group by client_email order by cnt desc limit 0,50" ;
	  $rs_outer = $link->query($outer_loop_query);
	  if($rs_outer->num_rows == 0 ) 
			break;
	  if($rs_outer->num_rows  > 0)
	  {
		  
		  while($current_row = $rs_outer->fetch_array())
		  {
			  
			  $current_email = $current_row['client_email'];
			  $current_repeat_cnt  = $current_row['cnt'];
			  if($current_repeat_cnt ==1) break;
		   
		  //update source row is copied
		  $inner_query = "select * from user_people where client_email='$current_email' and 
		  (client_phone <> '' and client_phone is not null) and 
		  (client_location <> '' and client_location is not null) and 
		  (client_zip <> '' and client_zip is not null) order by id desc"; 
		   
		  $rs_records = $link->query($inner_query);
		  if($rs_records->num_rows  > 0)
		  {
			  $item = $rs_records->fetch_array();
			  $source_id = $item['id'] ;
			  $source_phone = $item['client_phone'] ;
			  $source_name = $item['client_name'] ;
			  $source_email = $item['client_email'] ;
			  $source_zip = $item['client_zip'] ;
			  $source_cr_tag = $item['tags'] ; 
			  $source_cr_location = $item['client_location'] ;
			  
			  //update source row is copied
			  $link->query( "update user_people set check_fields='999' where id='$source_id'" );  
			  $qr_copy_target = "select * from user_people where client_email='$current_email' and id <> '$source_id' "; 
			   
			  $rs_target = $link->query($qr_copy_target);
			  if($rs_target->num_rows  > 0)
			  {
				 while($target_row = $rs_target->fetch_array())
				 {
					  $targetid = $target_row['id'];
					  $target_zip = $target_row['client_zip'];
					  $target_city = $target_row['client_location'];
					  $target_tags = $target_row['tags'];
					  $target_src_name = $target_row['client_name'] ;
					  $check_fields = $target_row['check_fields'] ;
					  
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
						  
						if($target_zip == ''   )
						{
							$target_zip = $source_zip;							
						}
						
						//mark processed
						if($check_fields != 999)
						{
							$update_query = "update user_people 
							set  client_phone='$source_phone', client_zip='$target_zip', 
							client_location='$city',  check_fields='999' 
							where id='$targetid'"; 
						} 
					  }
					  else 
					  {
						//mark processed
						$update_query = "update user_people 
						set check_fields='-1' where id='$targetid'";   
					  }
					  $link->query($update_query); 
					}
			  }
			}
			else 
			{
				$update_query = "update user_people 
						set check_fields='-10'  where client_email='$current_email'" ; 
				$link->query($update_query);
			}
		  }
	  }		 
	}
	  
?>
