<?php 

	include_once ('mailer\PHPMailerAutoload.php ');
	if (! function_exists ( 'curl_version' ))
	{
		exit ( "Enable cURL in PHP" );
	} 
	
	function curlexecute($params=array(), $url)
	{    
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params ));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	
  
	$param = array('goto' => '1' );
	$signups = json_decode( curlexecute($param,   BASE_URL.'/api/api.php/signups/incomplete/'), true);
	$param = array('mailtype' => '5' ); 
	$mailtemplates = json_decode( curlexecute($param,   BASE_URL.'/api/api.php/getemailtemplatebytype/'), true);
	
	$headers = "From:  bob@mycity.com \r\n"  ; 
	$headers .= "Reply-To: bob@mycity.com\r\n";
	$headers .= "Return-Path: bob@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n"; 
	
	 
		 
	$ds = DIRECTORY_SEPARATOR;  
	$path =  $_SERVER['DOCUMENT_ROOT'] . $ds    ; 
	$mailbody  =""; 
	$filenofound = 0;
	if(  file_exists( $path . "templates/black_template_01.txt" ) )
	{
		
		$template_part = file_get_contents( $path . "templates/black_template_01.txt" ) ; 
		
		
		$unfinish_list = '<table cellpadding="0" cellspacing="0"  style="border:1px solid #efefef "  class="table table-responsive">';
		$unfinish_list .='<tr><td style="border-bottom:1px solid #efefef;border-right:1px solid #efefef;padding: 5px">Name</td><td style="border-bottom:1px solid #efefef; padding: 5px" >Email</td></tr>';
			 
		if(sizeof($mailtemplates) > 0 && sizeof($signups['results']) > 0 ):
			
			$subject= $mailtemplates[0]['subject'];
			$html  = $mailtemplates[0]['mailbody'];
			$i=1;
			$mailbody  = str_replace("{mail_body}",  html_entity_decode($html)  , $template_part ) ;
			  
			foreach(  $signups['results'] as $item )
			{
				$to = $item['user_email'];
				mail($to,$subject,$mailbody,$headers); 
				$unfinish_list .='<tr><td style="border-bottom:1px solid #efefef;border-right:1px solid #efefef;padding: 5px">' . (  $item['username'] =='' ? "No Specified" :   $item['username'] )  .  "</td><td style='border-bottom:1px solid #efefef; padding: 5px' >".$to ."</td></tr>";
				$i++;  
			}
			 
			if( $i > 1 )
			{
				echo mail("bob@mycity.com","List of unfinished signups who are informed today", $unfinish_list,$headers); 
			}
		 
		endif; 
		
	}
	  
	
?>
