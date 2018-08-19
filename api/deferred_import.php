<?php
	 
	ini_set('memory_limit', '1024M');
	date_default_timezone_set('America/Los_Angeles'); 
	include_once '../includes/db.php'; 
	include_once ('../application/lib/Classes/PHPExcel/IOFactory.php' );
	 
	$ds = DIRECTORY_SEPARATOR;  
	$upload_path  =  'assets' . $ds . 'uploads' . $ds . 'excel' . $ds; 
	 
	
	while(true)
	{ 
		
	$sql_query = "select * from mc_upload_log where status=0 order by id desc";
	$files = $link->query($sql_query); 
	
	if($files->num_rows == 0 ) 
			break;
		
	$i =0;  
	while($item = $files->fetch_array()):  
		$fileid = $item['id'] ;
		$user_id = $item['user_id'] ;
		$current_file = $item['filepath'] ;
		$start_from = $item['last_row_processed'] ;
		$voc = ""; 
		$last_stop_position =  $item['total_imported'] ;
		$new =  0 ; 
		$file_path = realpath(dirname(__FILE__).'/../') . $ds. $upload_path .  $current_file  ; 
		 
		if( file_exists($file_path)):
		 
			$objPHPExcel = PHPExcel_IOFactory::load($file_path);
			$sheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$arrayCount = count($sheet);    
			  
			$com_vocs = array();
			$where_mem_voc = '';
			$user_voc = $link->query("select * from user_details where user_id='$user_id'  " );
			if($user_voc->num_rows > 0)
			{
				$mem_voc = array_filter( explode(',', $user_voc->fetch_array()['vocations'] ) );  
				$where_mem_voc = "(FIND_IN_SET('".implode("',  member_voc ) OR FIND_IN_SET('", $mem_voc)."',  member_voc ))";  
				
				if(sizeof($mem_voc) > 0)
				{
					$comvocrs = $link->query(" select * from mc_common_vocation where   "  . $where_mem_voc);
					if($comvocrs->num_rows  > 0)
					{
						while( $com_voc_row = $comvocrs->fetch_array() ) 
						{
							$com_vocs[] = $com_voc_row['know_common_voc'] ;
						} 
					} 
				}
			}
			
			$voc = implode(',', $com_vocs); 
			$imported_knows=array();    
			for($x =  $start_from ; $x < $start_from + 100 && $x <= $arrayCount;$x++)
			{
				$cname =  $sheet[$x]["A"] . " " . $sheet[$x]["B"]; 
				$cname =  preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $cname);
				$cname =   html_entity_decode(  utf8_decode(   $cname ) ); 
				 
				
				$email = $sheet[$x]["C"];
				$company = $sheet[$x]["D"];
				$profession = ($sheet[$x]["E"] !='' ? $sheet[$x]["E"] : " ")  ;
				$livestyle = ( isset($sheet[$x]["F"] ) ? $sheet[$x]["F"] : "") ; 
				if( trim($cname) == ""   ) break;    	 
				$duplicate_check_query = "select count(*) as cnt from user_people where user_id='$user_id' and client_email='$email' ";
				$existing_knows = $link->query($duplicate_check_query);   
				if( $existing_knows->fetch_array()['cnt'] == 0 )  
				{
					$insnewknow = "insert into user_people (user_id, client_name, client_email, client_profession, company , isimport, entrydate  ) 
					values ('$user_id','$cname','$email', '$profession', '$company', '1', NOW() )";
					$insQ = $link->query($insnewknow); 
					$knowid = $link->insert_id; 
					$link->query("insert into user_answers ( question_id,  user_id, answer  ) values ('9', '$knowid',  '$voc' )"); 
					$new++; 
				}
			} 
			
			//update excel log
			$status=0;			
			if( $x >= $arrayCount || $sheet[$x]['A'] == null )
			{
				$status =1;
				$arrayCount = $x;
			}  
			$new += $last_stop_position; 
			$link->query("update mc_upload_log set total_imported= '$new' , last_row_processed='$x', status='$status', total_row= '$arrayCount' where id = '$fileid'  "); 
				 
		endif;   
	endwhile; 
	
	}
	
?>
