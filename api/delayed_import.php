<?php
	date_default_timezone_set('America/Los_Angeles');
	include_once ('mailer\PHPMailerAutoload.php ');
	include_once '../includes/db.php';
	include_once (  '../application/lib/Classes/PHPExcel/IOFactory.php' );
	
	
	$site_path  = 'D:'. DIRECTORY_SEPARATOR . 'ampps'. DIRECTORY_SEPARATOR .
	'www'. DIRECTORY_SEPARATOR . 'edgeci'. DIRECTORY_SEPARATOR ; 
 
 
 
	$ds = DIRECTORY_SEPARATOR; 
	$targetPath = $site_path .  'assets/uploads/' . $ds . "excel" . $ds ;
	
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql_query = "select  * from mc_upload_log where status='0' order by ID desc ";
	$rst = $pdo->query($sql_query);  
	 
	if($rst->rowCount() > 0)
	{
		$files = $rst->fetchAll(PDO::FETCH_ASSOC)  ;
		foreach($files as $row) 
		{
			$file_path =  $targetPath . $row["filepath"];  
		 
		
		
		$inputFileName = $file_path; 
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		$sheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$arrayCount = count($sheet);  // Here get total count of row in that Excel sheet
		$new =0;   
		$comvoc = $this->MyCommonVocations->get_common_vocations( $uid  );
		$voc = $comvoc['common_vocs']; 
		
		}

		
		 				 
	}
	
?>
