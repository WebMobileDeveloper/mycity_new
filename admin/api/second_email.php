<?php 
date_default_timezone_set('America/Los_Angeles');
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
	
	$mailtemplatep1 = "<!DOCTYPE html><html>
	<head>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>Email from mycity.com</title>
    <style type='text/css'>
        body {margin: 10px 0; padding: 0 10px; background: #f3f3f3; font-size: 14px;}
        table {border-collapse: collapse;}
        td {font-family: arial, sans-serif; color: #333333;}

        @media only screen and (max-width: 480px) {
            body,table,td,p,a,li,blockquote {
                -webkit-text-size-adjust:none !important;
            }
            table {width: 100% !important;}

            .responsive-image img {
                height: auto !important;
                max-width: 100% !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
<table border='0' cellpadding='0' cellspacing='0' width='100%'>
    <tr>
        <td>
            <table border='0' cellpadding='0' cellspacing='0' align='center' width='640' bgcolor='#FFFFFF' style='color: #333333'>
                <tr>
                    <td bgcolor='#333333' style='font-size: 30px; color:#fff; padding: 0 10px;border-bottom:10px solid #78b0d1;' height='100' align='center'>
                        <a href='http://www.mycity.com' target='_blank'>
                            <img src='http://www.mycity.com/images/logo.png' width='100' alt='www.mycity.com' />
                        </a>
                    </td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='30'>&nbsp;</td></tr>
                <tr>
                    <td style='padding: 10px 10px 30px 10px;'> " ;
					  
					   
    $mailtemplatep2 = "
	<p style='line-height: 1.76;'>
Sincerely,<br/>
Bob Friedenthal<br/>
<a href='mailto:bob@mycity.com' target='_blank'>bob@mycity.com</a><br/>
310-736-5787 M<br/>
</p>
<p>
If you would like more information please call, text or email me<br/>
</p> 
  
</td>
                </tr>
                <tr>
                <td style='padding: 0 10px 10px 10px;'>If you are not the intended person or did not make this request, delete this email please.</td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
                <tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
                <tr>
                    <td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                            <tr>
                                <td style='padding: 0 10px; color: #cccccc;' align='center'>
                                    Copyright &copy; " . date('Y') . " | All Rights Reserved.
                                </td>
                            </tr>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>";
 
	$param = array('interval' => '4' ); 
	$signups = json_decode( curlexecute($param,   BASE_URL.'/api/api.php/signups/new/'), true);
	 
	$headers = "From:  bob@mycity.com \r\n" . "Cc: " . $cc . "\n";
	$headers .= "Reply-To: bob@mycity.com\r\n";
	$headers .= "Return-Path: bob@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";  
	$subject= "Notice from MyCity.com - Why You Should Download Your LinkedIn Data?";
	 
	foreach($signups['results'] as $item ): 
		 
		$to =  $item['user_email'];  
		$username = $item['username'] ;  
		$html  =   " <p>Hello ". $username . ",<br/><p> <p>If you have never downloaded your connections, you should. We really don’t know what changes LinkedIn will make in the future.</p>
		<p>Once logged into mycity.com there are instructions on downloading your LinkedIn connections. This feature is to the right of profile. Or simply click on the home button.</p>
		<p>Once downloaded the document is downloaded to YOUR computer and is named connections.csv</p>
		<p>Next please send it to us, support@edgeupnetwork.com and we then upload it to your mycity account.</p>
		<p>There was a concern recently that LinkedIn was stopping the ability to download. If you notice, several features have been deleted from the free accounts.  LinkedIn no longer gives you an ability to download phone numbers, city and zip codes.</p>
		<p>Mycity has a team of researchers that searches this data for you.</p>
		<p>As you can imagine, this is an extremely time consuming task. </p>";
		mail($to,$subject, $mailtemplatep1 . $html . $mailtemplatep2,$headers) ; 
		  
	endforeach; 
	 
	
?>
