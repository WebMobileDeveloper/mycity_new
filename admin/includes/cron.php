<?php
date_default_timezone_set('America/Los_Angeles');
set_time_limit(60*10);

ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');


session_start();
include_once 'db.php';
include_once 'functions.php';


function reminderCronTrigger(){
    global $link;
    $row = $link->query(" SELECT mailbox.subject,mailbox.mailbody,mailbox.sender,mailbox.receipent,mailbox.senton,mc_user.user_email,user_people.client_email FROM `mailbox`
						left join mc_user on mc_user.id	= mailbox.sender
						left join user_details on user_details.user_id	= mailbox.sender
						left join user_people on user_people.id = mailbox.receipent	
						WHERE user_details.upd_reminder_email='yes' AND mailbox.email_type = 'trigger-mail' AND mailbox.reminder_status = 0 AND mailbox.senton > DATE(NOW()) - INTERVAL 7 DAY ");
    while($data = $row->fetch_array()){
		$mailLogId = $data['id'];
		$subject = "Just a reminder email again:- ".$data['subject'];
		$body = $data['mailbody'];
		
		$sendermail = $data['user_email'];
		$receipentemail = $data['client_email'];

        sendmail($receipentemail,  $sendermail ,  $subject, $body, $altbody);
		$link->query("UPDATE mailbox SET reminder_status ='1'  WHERE id= '$mailLogId' ");	
    }
	return true;
}
reminderCronTrigger();