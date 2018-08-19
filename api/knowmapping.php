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
 
	function getPDO( )
	{
		$settings = [
			'host' => 'localhost', 
			'user' => 'mycity29_root',
			'password' => 'zBi6h49~' ,
			'db' => 'mycity29_maindb' 
		] ;
		
		
		$pdo = new PDO("mysql:host=". $settings['host'] . ";dbname=". $settings['db'] . "", $settings['user'], 
		$settings['password'] );
		$pdo->exec("set names utf8"); 
		return $pdo; 
	}
	
	
	try
	{
		
		$pdo =getPDO($this);
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
	    
?>
