<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/google-api-php-client/vendor/autoload.php';
 
require( APPPATH . "/third_party/PHPMailer/src/PHPMailer.php");
require( APPPATH . "/third_party/PHPMailer/src/SMTP.php");
require( APPPATH . "/third_party/PHPMailer/src/Exception.php");


 
if(!function_exists('send_email'))
{
  function send_email( $receipent,  $sender, $sendername, $subject, $email, $cc1='', $cc2=''  )
  {
	 
	 $mail = new PHPMailer\PHPMailer\PHPMailer(); 
		$mail->IsSMTP();  
		$mail->Mailer = "smtp"; 
		$mail->SMTPDebug = 0;  
		$mail->SMTPAuth = true;  
		$mail->SMTPSecure = 'ssl';  
		$mail->Host = "smtp.gmail.com"; 
		$mail->Port = 465;  
		$mail->IsHTML(true);   
		$mail->Username = "referralsmycity@gmail.com"; 
		$mail->Password = "Rfq#2707"; 
		$mail->SetFrom( $sender );  
		$mail->Subject =  $subject ; 
		$mail->Body =  $email; 
		$mail->AddAddress( $receipent );
	 
		if(!$mail->Send()) 
		{
			return 0; 
		} 
		else 
		{
			return 1;
		} 
  }
}

if(!function_exists('get_reminder_details'))
{
	function get_reminder_details($userid)
	{
		$CI = get_instance();
		$CI->load->model('MyReminders');
		$reminder_details = $CI->MyReminders->get_reminder_counts($userid);
		return $reminder_details ;
	}
}


function get_mail_count($userid, $email)
{
		$CI = get_instance();
		$CI->load->model('Mailbox');
		$mail_count = $CI->Mailbox->get_mail_count($userid, $email);
		return $mail_count ;
	}
	 
	 
	 
if(!function_exists('getClient'))
{
	function getClient()
	{
		$client = new Google_Client();
		$client->setApplicationName('MyCityWorkSheet');
		$client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
		$client->setAuthConfig( __DIR__ . '/credentials.json' );
		$client->setAccessType('offline'); 
		return $client;
	} 
}
 
 
if(!function_exists('auto_fill_zip_from_city'))
{
	function auto_fill_zip_from_city( )
	{
		$spreadsheet_data = array();
		 
		//$spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vTMQEU2B-XHhfdJvRbMMvWoAAFJYOvDcVLS6N39T_NUKPqAu76bg6GQUlUUxSCw9k2prtQhHkRtSdcN/pub?output=csv";
		 
		$spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vTPil2UDQRQolqgdVjn8oYQ4mKtZYtnU5EaVCv5N8rsAMhH9pJMBQmI_Kn9-nRzoUi0cCZaWxCYMUud/pub?gid=0&single=true&output=csv";
		if(!ini_set('default_socket_timeout', 15)) echo "<!-- unable to change socket timeout -->";
		
		$handle =   fopen($spreadsheet_url, "r");
		
		if ( $handle  !== FALSE) 
		{
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
			{
				$spreadsheet_data[] = $data;
			} 
			fclose($handle);
		} 
		return $spreadsheet_data;
	}
}   