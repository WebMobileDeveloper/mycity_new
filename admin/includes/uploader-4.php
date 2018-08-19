<?php
if(!isset($_SESSION))session_start();
	$ds = DIRECTORY_SEPARATOR; 
	$apppath = '';
	$storeFolder =  'assets/uploads'; 
	if (!empty($_FILES))
	{
		$tempFile = $_FILES['file']['tmp_name'];  
		$userid = $_POST['hidliuserid'];
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . $ds. $apppath . $ds. $storeFolder . $ds;
		$newfilename = 'linkeden_' . $userid . "." .  pathinfo( $_FILES['file']['name'] , PATHINFO_EXTENSION); 
		$targetFile =  $targetPath .  $newfilename; 
		move_uploaded_file($tempFile,$targetFile); 
	}
	
?>