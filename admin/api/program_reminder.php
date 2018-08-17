<?php
	date_default_timezone_set('America/Los_Angeles');
	include_once ('mailer\PHPMailerAutoload.php ');
	include_once '../includes/db.php'; 
	
	
	  

	$headers = "From:  bob@mycity.com \r\n"  ;
	$headers .= "Reply-To: bob@mycity.com\r\n";
	$headers .= "Return-Path: bob@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";  
	$subject= "Reminder to send introduction/referral to your connections"; 
	
	
	$ds = DIRECTORY_SEPARATOR;  
	$path =  $_SERVER['DOCUMENT_ROOT'] . $ds    ; 
	$mailbody  =""; 
	$filenofound = 0;
	if(  file_exists( $path . "templates/black_template_01.txt" ) )
	{
		$template_part = file_get_contents( $path . "templates/black_template_01.txt" ) ; 
		if(  file_exists( $path . "templates/task_alert_3tparticipant.txt" ) )
		{
			$mail_inner_body = file_get_contents( $path . "templates/task_alert_3tparticipant.txt" ) ;  
			$mailbody  = str_replace("{mail_body}",  $mail_inner_body  , $template_part ) ;
			
			try
			{
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$sql_query = "SELECT  a.id as i, a.client_id as ppid,a.relation_id,  b.user_email, b.username 
				from  mc_program_client_answer as a inner join mc_user as b on a.client_id=b.id 
				where date(a.adate) = date_sub( curdate(), interval 7 day ) and a.reminder_sent='0'  ";  
				$rst = $pdo->query($sql_query);  
				$members = $rst->fetchAll(PDO::FETCH_ASSOC); 
				
				
				foreach($members  as $item ): 
					$mail_body_temp = $mailbody;
					$recordid = $item['i'] ;
					$ppid = $item['ppid'] ;
					$name = $item['username'] ;
					$client_email = $item['user_email'];
					$relationid = $item['relation_id']; 
					//email towards admin 
					$participant_rs = $pdo->query("select client_name, client_email from user_people where id='$relationid'");
					$participant = $participant_rs->fetchAll(PDO::FETCH_ASSOC);
					$know_name = $participant[0]['client_name'];
					  
					$mail_body_temp= str_replace("{receipent_name}", $name , $mail_body_temp ) ; 
					$mail_body_temp= str_replace("{client_name}", $know_name , $mail_body_temp ) ;  
					
				 
					if(  mail( $client_email , $subject,  $mail_body_temp ,$headers ) )
					{
						$pdo->query( "update mc_program_client_answer set reminder_sent='1' where id='$recordid' ");  
					}

					
				endforeach;   
			}
			catch(PDOException $e)
			{
				$jsonresult = array( 'error' =>  '1' ,  'errmsg' =>  'Something went wrong. Please retry!'  ); 
			} 
		}
		else
		{
			$filenofound=1;
		}
	}
	else
	{
		$filenofound=1;
	} 
	
?>
