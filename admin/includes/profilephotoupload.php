<?php
if(!isset($_SESSION))session_start();
	$ds = DIRECTORY_SEPARATOR; 
	$apppath = '';
	$storeFolder =  'images'; 
	if (!empty($_FILES))
	{
		$ext = pathinfo( $_FILES['file']['name'] , PATHINFO_EXTENSION);

	  
			$tempFile = $_FILES['file']['tmp_name'];  
			$userid = $_POST['hidmid']; 
			$targetPath = $_SERVER['DOCUMENT_ROOT'] . $ds.  $storeFolder . $ds;
			$newfilename =  'amemprofile_' . md5( $userid) . "." .   $ext ; 
			$targetFile =  $targetPath .  $newfilename; 
			move_uploaded_file($tempFile,$targetFile); 
			 
			//location 2
			$secondtarget = $_SERVER['DOCUMENT_ROOT'] . $ds  . 
			$ds. "assets" .  $ds. "uploads" .  $ds . "profiles" .  $ds . $newfilename; 
			copy( $targetFile , $secondtarget);
			$_SESSION['tempmemprofile'] = $newfilename ;
			
			//update photo
			//$param = array('id' => $userid, 'path' => $newfilename );
			//json_decode(   curlexecute($param, $siteurl . 'api/api.php/members/updatephoto/'), true); 
		 
	} 
?>