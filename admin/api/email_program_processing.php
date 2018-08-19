<?php 
	date_default_timezone_set('America/Los_Angeles');
	include_once ('mailer\PHPMailerAutoload.php ');
	include_once '../includes/db.php';
	  
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
					  
					   
    $mailtemplatep2 = "<p style='line-height: 1.76;'>
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

	$headers = "From:  referrals@mycity.com \r\n" ;
	$headers .= "Reply-To: referrals@mycity.com\r\n";
	$headers .= "Return-Path: referrals@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
	 
	try
	{
		$dates = date("Y-m-d H:i:s", strtotime("-10 minutes")); 
		$datee = date("Y-m-d H:i:s", strtotime("+10 minutes"));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql_query = "select a.*, b.username, b.user_email from  mc_email_program_assigned as a 
		inner join mc_user as b on a.client_id=b.id  where (assigned_date between '$dates' and '$datee') and a.status='0' ";
		
		  
		$rst = $pdo->query($sql_query);  
		$members = $rst->fetchAll(PDO::FETCH_ASSOC);
		foreach($members  as $item ):
			
			$to = $item['user_email'] ;  
			$name  = $item['username'] ; 
			$mailid  = $item['mail_id'] ; 
			$seqid  = $item['id'] ;
			
			if($mailid > 0)
			{
				$sql_query = "select * from mc_email_program where id='$mailid'";
				$rstmail = $pdo->query($sql_query);   
				if($rstmail->rowCount() > 0)
				{
					$mailcontent = $rstmail->fetchAll(PDO::FETCH_ASSOC); 
					$mailheading = $mailcontent[0]['mail_heading'];
					$mailbody = $mailcontent[0]['email_body'];
					
					if($mailheading !='' && $mailbody !='' )
					{ 
						$mailbody  = '<p>Hi ' . $name . ",</p>" . $mailbody;
						 
						
					  if(  mail( $to, $mailheading, $mailtemplatep1 . $mailbody . $mailtemplatep2,$headers) )
					   {
						  $pdo->query( " update mc_email_program_assigned set status='1' where id='$seqid ' "  );				
					   }  
					}
				}
			}
		endforeach;
	}
	catch(PDOException $e)
	{
		$jsonresult = array( 'error' =>  '1' ,  'errmsg' =>  'Something went wrong. Please retry!'  ); 
	} 
	
	

// know mapping

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



try
{ 
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
		$rst = $pdo->query(" select p.*, a.answer from user_people as p inner join user_answers as a on p.id=a.user_id where   refgenerated in (0, 10) order by p.id desc , p.updatedate desc ");
		 
		if($rst->rowCount() > 0 )
		{
			//work only for the first row
			$newknows = 	$rst->fetchAll(PDO::FETCH_ASSOC)[0];
			$userid = $newknows['user_id'];
			
			$knowprofessions = explode(",",  $newknows['client_profession']);
			$interestedprofessions = $newknows['answer'];
			$newknowid = $newknows['id'];
			$sourcezip = $newknows['client_zip']; //zip code of the new know
			 
		    //second making main query
			$professionlist = explode(",",  $interestedprofessions); 
			$where_group = " ( "; 
			for($i=0; $i < sizeof($professionlist); $i++ )
			{
				$where_group .= " find_in_set ( '". $professionlist[$i] . "' , client_profession  ) ";
				if( $i < sizeof($professionlist)-1 )
				{
					$where_group .=  " OR ";
				}
			}  
			$userrst = $pdo->query("SELECT * FROM user_details where user_id = '$userid'");
			$user = $userrst->fetchAll(PDO::FETCH_ASSOC)[0];
			$groups = explode(",", $user['groups']);
			
			$where_group .= " ) ";  
			//first getting subquery for retrieving partners
			$where_in_set = " (  " ;
			for($i=0; $i < sizeof($groups); $i++ )
			{
				$groupid = $groups[$i];
				$where_in_set .= " FIND_IN_SET('$groupid', groups) "; 
				if( $i < sizeof($groups)-1 )
				{
					$where_in_set .= " OR "; 
				}
			}
			$where_in_set .=" ) " ;
			
			$qryInner = " SELECT a.user_id FROM user_details as a inner join mc_user as b on b.id = a.user_id 
			WHERE $where_in_set  AND  b.id != '1' and user_pkg='Gold'  " ;
			
			 
			$mainQry = "SELECT p.*,  SUM(r.ranking) as rank 
			FROM user_people as p INNER JOIN user_rating as r on p.id=r.user_id 
			WHERE p.user_id IN  ( $qryInner )  AND " . $where_group . " GROUP BY p.id ORDER BY client_name" ;
			 
			 
			$matchingknowrst = $pdo->query($mainQry);
			if($matchingknowrst->rowCount() > 0 )
			{
				$matchingknows = $matchingknowrst->fetchAll(PDO::FETCH_ASSOC); 
				foreach( $matchingknows as $row )
				{
					$id = $row['id']; 
					$user_ranking = $row['rank']; 
					$targetknowprofession = explode(",",  $row['client_profession'] );
					$matchingprofession = array_intersect($knowprofessions, $targetknowprofession);
					
					// Count how many times each value exists
					$matchingprofessioncount  = array_count_values($matchingprofession); 
					$tmp = array_filter($matchingprofessioncount);
 
					$rsrating  = $pdo->query("select count(*) as rowcnt from user_rating where user_id='$newknowid' "); 
					$rslocation  = $pdo->query("select client_location from user_people where id='$newknowid' ");
					
					if($rslocation->rowCount() > 0)
					{
						$clientlocationfield = $rslocation->fetchAll(PDO::FETCH_ASSOC)[0]['client_location'];
					}
					else 
					{
						$clientlocationfield = '';
					}
					
					if(   $clientlocationfield    !== NULL &&
					$clientlocationfield   != '' && 
					$rsrating->fetchAll( PDO::FETCH_ASSOC)[0]['rowcnt'] > 0 )
					{
						if( $user_ranking > 20 && empty($tmp) )
						{
							$targetzip = $row['client_zip'];
							if($targetzip != "")
							{
								$existingrefresult = $pdo->query("SELECT COUNT(*) AS rcnt FROM referralsuggestions 
								WHERE partnerid='" . $row['user_id']  . "' AND 
								knowtorefer='" . $row['id']  ."' AND 
								knowreferedto='$newknowid' AND knowenteredby='$userid' ");
								$existingrefcnt = $existingrefresult->fetchAll(PDO::FETCH_ASSOC)['rcnt'];
								
								if( $existingrefcnt  == 0 )
								{
									if($row['user_id'] != $userid)
									{ 
										//calculate distance
										$zipqry = "select * from  mc_city_geolocation where zip in (" . $targetzip  . ", " . $sourcezip  . " ) ";
										$rsgeolocs  = $pdo->query($zipqry);
										
										if($rsgeolocs->rowCount() == 2)
										{
											$geolocs = 	$rsgeolocs->fetchAll(PDO::FETCH_ASSOC)[0];
											$latitude1 = $geolocs['latitude'] ;
											$longitude1 = $geolocs['longitude'] ; 
											$geolocs = 	$rsgeolocs->fetchAll(PDO::FETCH_ASSOC)[1];
											$latitude2 = $geolocs['latitude'] ; 
											$longitude2 = $geolocs['longitude'] ;
											 
											$theta = $longitude1 - $longitude2;
											 $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
											 $distance = acos($distance);
											 $distance = rad2deg($distance);
											 $distance = $distance * 60 * 1.1515; switch($unit) 
											 {
												 case 'Mi': break; case 'Km' : $distance = $distance * 1.609344;
											 }
											 
											 $distance  = (round($distance,2));
											 
											 $refqry = "INSERT INTO referralsuggestions 
											 ( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby,
											 sourcezip, targetzip, ranking, distance, distancecalculated) 
											 VALUES ('".  $row['user_id'] . "', '". $row['id'] . "',
											 '$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' ,
											 '$targetzip' , '$user_ranking', '$distance', '1' )" ;  
										}
										else 
										{
											$refqry =  "INSERT INTO referralsuggestions 
											( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby , sourcezip, targetzip, ranking) VALUES 
											('".  $row['user_id'] . "', '". $row['id'] . "', 
											'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' , '$targetzip' ,   '$user_ranking' )" ;
										}
										$pdo->query( $refqry ); 
									}
								}
							}
						}
					}
				}
			}
			//mark referral suggestion
			$pdo->query("update  user_people  set refgenerated='1' where id='$newknowid'") ;  
		} 
	}
	catch(PDOException $e)
	{ 
	}
 	
	
//Send Alert Reminder
 
	 
	$dates = date("Y-m-d H:i:s", strtotime("now")) ; 
	$datee = date("Y-m-d H:i:s", strtotime("+30 minutes")); 
  
	$param = array('dates' =>  $dates, 'datee' =>  $datee ); 
	$reminders = json_decode( curlexecute($param, BASE_URL.'/api/api.php/reminders/getall/'), true);
  
   
	$headers = "From:  bob@mycity.com \r\n"  ;
	$headers .= "Reply-To: bob@mycity.com\r\n";
	$headers .= "Return-Path: bob@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";  
	$subject= "Notice from MyCity.com";
	
	foreach($reminders['result'] as $item ): 
		 
		$to = $item['user_email'] ;  
		$html  =  "<h2>You have a reminder message that was created by yourself or our team at mycity.com</h2>"; 
		$html  .= "<hr/>";   
		$html  .= "<p>Log into your MyCity account and then click on the red alert near the top of the page to see the message.</p>";
		 
	 
		 if(mail($to,$subject, $mailtemplatep1 . $html . $mailtemplatep2,$headers) )
		{
			//update mail has been sent 
			 $param = array('remid' =>  $item['id']   ); 
			$reminders = json_decode( curlexecute($param,   BASE_URL.'/api/api.php/reminder/markalerted/'), true);
		}  
	endforeach; 	
	
	
?>
