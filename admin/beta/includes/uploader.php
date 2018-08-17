<?php
session_start();
	$ds = DIRECTORY_SEPARATOR;  //1
	$apppath = '';
	$storeFolder =  'assets/uploads';   //2
 
	if (!empty($_FILES))
	{
		$tempFile = $_FILES['file']['tmp_name'];          //3
		$targetPath = $_SERVER['DOCUMENT_ROOT'] . $ds. $apppath . $ds. $storeFolder . $ds;  //4
		
		
		$newfilename = 'knowlist_' . $_SESSION['user_id'] . "." .  pathinfo( $_FILES['file']['name'] , PATHINFO_EXTENSION); 
		
		$targetFile =  $targetPath .  $newfilename;  //5
		move_uploaded_file($tempFile,$targetFile); //6
	}
?>    