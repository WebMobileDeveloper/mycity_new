<?php
if(!isset($_SESSION))session_start();
	include_once 'db.php';
	
	$ds = DIRECTORY_SEPARATOR; 
	$apppath = '';
	$storeFolder =  'assets/uploads'; 
	if (!empty($_FILES))
	{
		$time = strtotime( date("Y-m-d H:i:s", time() ) );
		$tempFile = $_FILES['file']['tmp_name'];  
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . $ds. $apppath . $ds. $storeFolder . $ds;
		//$newfilename = 'linkeden_' . $_SESSION['user_id'] . "." .  pathinfo( $_FILES['file']['name'] , PATHINFO_EXTENSION); 
		
		$newfilename = 'linkedin_knows_' . $_SESSION['user_id'] . $time . "." .  pathinfo( $_FILES['file']['name'] , PATHINFO_EXTENSION); 
		 
		$targetFile =  $targetPath .  $newfilename;  //5
		move_uploaded_file($tempFile,$targetFile); //6
		
		$_SESSION['excelfileimport'] = $newfilename;
		 
		//location 2
		$secondtarget = $_SERVER['DOCUMENT_ROOT'] .  $ds. "assets" .  $ds. "uploads" .  
		$ds . "excel" .  $ds . $newfilename; 
		copy( $targetFile , $secondtarget);
		
		//saving in DB
		$insQstmnt = "insert into mc_upload_log (user_id, filepath, upload_date ) 
		VALUES ('" .  $_SESSION['user_id'] .  "', '$newfilename', 'NOW()' )";
		$link->query($insQstmnt);  
	}
	
?>    