<?php
 
	ini_set('memory_limit', '1024M');
	date_default_timezone_set('America/Los_Angeles'); 
	include_once '../includes/db.php';   
	 
	//field copy
	while(true)
	{
		$outer_loop_query = "SELECT id  FROM mc_user where username <> '' and username is not null limit 0,50" ;
		$rs_outer = $link->query($outer_loop_query);
		if($rs_outer->num_rows == 0 ) 
			break;
	    if($rs_outer->num_rows  > 0)
	    {
			while($current_row = $rs_outer->fetch_array())
			{
				$mid = $current_row['id'];
				$inner_query =  "select * from  referralsuggestions where  knowenteredby = '$mid' and ( source_rank = '0' and  target_rank='0' ) and rank_calc <> '10' " ;
				$rs_rankfiller = $link->query($inner_query );
				if($rs_rankfiller->num_rows  > 0)
				{
				   $rank_update_cnt=0;
				   while($ref_row = $rs_rankfiller->fetch_array())
				   {
					   $refrowid = $ref_row['id'];
					   $source = $ref_row['knowtorefer'];
					   $target =  $ref_row['knowreferedto'];
					   
					   $rank_query  = "select  sum( ranking) as totalscore from   user_rating where user_id in (" . $source  . ","  . $target . " )  group by user_id" ;
					   $trknowtorefer = $link->query( $rank_query ); 
					   $trrowcount = $trknowtorefer->num_rows ;
					   
					   if($trrowcount == 0)
						{ 
							$sourcerank = $targetrank =0;
						}
						else if($trrowcount == 1)
						{
							$rankrow = $trknowtorefer->fetch_assoc();
							$sourcerank = $rankrow['totalscore'] ;  
							$targetrank =0;
						}
						else if($trrowcount >= 2)
						{
							$rankrow = $trknowtorefer->fetch_assoc( );
							$sourcerank = $rankrow['totalscore'] ;  
							$rankrow = $trknowtorefer->fetch_assoc( );
							$targetrank = $rankrow['totalscore'] ;  
						}
						$update_query = "update  referralsuggestions set rank_calc='10', source_rank='$sourcerank', target_rank='$targetrank' where id='$refrowid' " ;
						$link->query( $update_query );
						$rank_update_cnt++;
						if($rank_update_cnt > 100)
						{
							break;
						}
					}
				} 
			}
	  }		 
	}
	  
?>
