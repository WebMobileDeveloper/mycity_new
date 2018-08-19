<?php
 
	ini_set('memory_limit', '1024M');
	date_default_timezone_set('America/Los_Angeles'); 
	include_once '../includes/db.php';    
	//field copy
	while(true)
	{
		$outer_loop_query = " SELECT id,user_email  FROM mc_user where user_email <>'' and user_email is not null and  id<>'1' and  username <> '' and
		username is not null limit 680,100 " ;
		$rs_outer = $link->query($outer_loop_query);
		if($rs_outer->num_rows == 0 )
			break; 
		$tag ='MyCity Member';
	    if($rs_outer->num_rows  > 0)
	    {
			while($current_row = $rs_outer->fetch_array())
			{
				$email= $current_row['user_email'];
				$mid = $current_row['id'];
				$inner_query =  "select * from  user_people where  client_email = '$email' and  !find_in_set('$tag',  tags)  " ;
				 
				$rs_rankfiller = $link->query($inner_query );
				if($rs_rankfiller->num_rows  > 0)
				{
				    $newtags = ''; 
					while(  $item = $rs_rankfiller->fetch_array() )
					{
						$current_tags = array();
						$rowid =$item['id'];
						$current_tags =  array_filter( explode(',', $item['tags']) );
						$current_tags[] = $tag; 
						$newtags = implode(',',  array_unique( $current_tags) ); 
						$link->query("update user_people set tags='$newtags' where id='$rowid'");
					}   
				} 
			}
	  }	 
	}	   
?>
