<?php
header('Content-Type: image/png');
include_once 'db.php';
 
//processing the encoded mail reading patterns
//encoded data pattern.
//i=e2c420d928d4bf8ce0ff2ec19b371514&c=e2c420d928d4bf8ce0ff2ec19b37151471
$hash = $_GET['i'];
$hashwithcode = $_GET['c'];
$idlen =  strlen($hashwithcode) - strlen($hash);  
if( $idlen > 0 )
{
	$mailid = substr($hashwithcode,  strlen($hash), $idlen);
	//if mail id is found then update the mail that it has been read
	$link->query("update mailbox set feedbackmailread='1' where id='$mailid' ");
}


readfile( $_SERVER['DOCUMENT_ROOT'] .DIRECTORY_SEPARATOR .'mycity' .DIRECTORY_SEPARATOR .'images/logo.png');



?>