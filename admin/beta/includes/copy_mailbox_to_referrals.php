<?php
	//USE THIS FILE TO UPDATE DISTANCE IN BACKEND
	date_default_timezone_set('America/Los_Angeles');
	set_time_limit(60*10);
	
	ini_set('post_max_size', '64M');
	ini_set('upload_max_filesize', '64M');
 
	session_start();
	include_once 'db.php';
	include_once 'functions.php';

	$user_id = @$_SESSION['user_id'];
	$_username = @$_SESSION['username'];
	$_user_email = @$_SESSION['user_email'];
	$_user_phone = @$_SESSION['user_phone'];
	$_user_role = @$_SESSION['user_role'];

	$results = $link->query( "select id, sender, receipent, suggestedconnectid , senton from   mailbox  order by   id  desc" );
	
	 
    if($results->num_rows > 0)
    {
		$i=0;
		while($row = $results->fetch_array())
        {
			$senderid = $row['sender'];
			$receipent = $row['receipent'];
			$suggestedconnectid = $row['suggestedconnectid'];
			$senton =  $row['senton'];
			$partnerid =0;
			$partnerinfo = $link->query("select user_id from  user_people  where id='$suggestedconnectid'");
	        if($partnerinfo->num_rows  > 0)
	        { 
	            $partnerid = $partnerinfo->fetch_array()['user_id'] ;
	        } 
 

			$link->query( "insert into referralsuggestionsback (partnerid, knowtorefer, knowreferedto, entrydate, emailstatus, knowenteredby) 
			values 
			( '$partnerid', '$suggestedconnectid', '$receipent',  '$senton', '1', '$senderid' )  " );

		echo ( "insert into referralsuggestionsback (partnerid, knowtorefer, knowreferedto, entrydate, emailstatus, knowenteredby) 
			values 
			( '$partnerid', '$suggestedconnectid', '$receipent',  '$senton', '1', '$senderid' )  " );echo  "<br/>";

echo $i . "<br/>";

			$i++; 
		}
    }
	
	
?>