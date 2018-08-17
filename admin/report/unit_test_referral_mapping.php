<?php 

/*

UNIT TESTING FOR Know MAPPING

*/
$host = 'localhost';
$user = 'mycity29_root';
$pass = 'zBi6h49~';
$db = 'mycity29_maindb';

function memberrank($a, $b )
	{
		if ($a['rank'] ==$b['rank'] ) return 0;
		return (   $a['rank'] >  $b['rank']  )?-1:1;
	}

$pdo = new PDO("mysql:host=". $host . ";dbname=". $db . "",  $user , $pass );
$pdo->exec("set names utf8");

$userid =  '323';
  
	try
	{
		 
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
 
		$userrst = $pdo->query("SELECT * FROM user_details where user_id = '$userid'");
		$user = $userrst->fetchAll(PDO::FETCH_ASSOC)[0];
		$groups = explode(",", $user['groups']);
		
		$sql_query = "select p.*, a.answer from user_people as p inner join user_answers as a on p.id=a.user_id where p.user_id='$userid' and refgenerated in (0, 10)  and a.answer <> 'null' order by  p.id desc , p.updatedate desc  " ;
		$rst = $pdo->query($sql_query); 
		  
		if($rst->rowCount() > 0 )
		{
			echo "---------------------------------------------------------------------------------------------------------<br/>";
			echo "<Storng>TESTING AUTOMATIC REFERRAL</STRONG><br/>"; 
			echo "---------------------------------------------------------------------------------------------------------<br/>";
		    echo "---------------------------------------------------------------------------------------------------------<br/>";
			echo "Member Name: Bruce Jolliff, ( San Diego, Bend OR )<br/>"; 
			echo "---------------------------------------------------------------------------------------------------------<br/>";
		   
		 
			echo "---------------------------------------------------------------------------------------------------------<br/>";
			echo "Total Know Fetched :" . $rst->rowCount() . "<br/>"; 
			echo "---------------------------------------------------------------------------------------------------------<br/>";
		 
		
			//work only for the first row 
			$allnewknows = 	$rst->fetchAll(PDO::FETCH_ASSOC) ;
			$actualrefgenerate=0;
			for($reccnt =0;  $reccnt < $rst->rowCount(); $reccnt++)
			{
				 $newknows = $allnewknows[$reccnt];
				  
		   $knowprofessions = explode(",",  $newknows['client_profession']);
		 
		 echo "---------------------------------------------------------------------------------------------------------<br/>";
		 echo "Working on the Know # $reccnt. Total Success:  $actualrefgenerate<br/>"; 
		 echo "---------------------------------------------------------------------------------------------------------<br/>";
		
		 echo "Profession: " . $newknows['client_profession'] . "<br/>"; 
		 echo "Who do they want to meet by vocation: " . $newknows['answer']. "<br/>"; 
		 echo "System ID: " . $newknows['id']. "<br/>"; 
		 echo "Zip: " . $newknows['client_zip']. "<br/>"; 
		  
		 echo "---------------------------------------------------------------------------------------------------------<br/>";
		 
		   $interestedprofessions = $newknows['answer'];
		   $newknowid = $newknows['id'];
		   $sourcezip = $newknows['client_zip']; //zip code of the new know
			 
		 
		 
			 if($sourcezip   =='') 
					{
						
							echo "---------------------------------------------------------------------------------------------------------<br/>";
							echo "<span  style='background-color:red'>SOURCE ZIP MISSING  . RECORD SKIPPED</span><br/>"; 
							echo "---------------------------------------------------------------------------------------------------------<br/>";
							 
						continue;
					}
					
					
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
				
				echo "---------------------------------------------------------------------------------------------------------<br/>";
				 echo "Mapping with knows from other member: <br/>"; 
				 echo "---------------------------------------------------------------------------------------------------------<br/>";
				$memberrank = usort($matchingknows, memberrank ); 
				
				$memcount =0;
				
			 
				$actualrefgenerate++;
				$pos =1;
				foreach( $matchingknows as $row )
				{
					$pos++;
					$id = $row['id']; 
					$user_ranking = $row['rank']; 
					$targetknowprofession = explode(",",  $row['client_profession'] );
					$matchingprofession = array_intersect($knowprofessions, $targetknowprofession);
					
					 
					if($user_ranking  < 20) 
					{
						print_r($row);
						echo "---------------------------------------------------------------------------------------------------------<br/>";
						echo "<span  style='background-color:red'>RANK < 20. RECORD SKIPPED</span><br/>"; 
						echo "---------------------------------------------------------------------------------------------------------<br/>";
						break;
					}
					 echo "---------------------------------------------------------------------------------------------------------<br/>";
					 echo "Other member's Know: Record # " . $pos . "<br/>"; 
					 echo "---------------------------------------------------------------------------------------------------------<br/>";
					 echo "System ID for Know: " . $id . "<br/>"; 
					 echo "Rank: " . $user_ranking . "<br/>"; 
					 echo "Matching Professions: " . implode(",", $matchingprofession) . "<br/>"; 
					 echo "Target Profession: " . $row['client_profession'] . "<br/>";
					 echo "---------------------------------------------------------------------------------------------------------<br/>";
			
		
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
						if( $user_ranking >= 20 && empty($tmp) )
						{
							echo "---------------------------------------------------------------------------------------------------------<br/>";
							echo "<span  style='background-color:green'>Record Selected for Processing</span><br/>"; 
							echo "---------------------------------------------------------------------------------------------------------<br/>";
							  
							//$targetzip = $row['client_zip'];
							
							echo "---------------------------------------------------------------------------------------------------------<br/>";
							echo "<span  style='background-color:green'>Target ZIP: " . $targetzip . "</span><br/>"; 
							echo "---------------------------------------------------------------------------------------------------------<br/>";
							
					if($targetzip   =='') 
					{
						
						echo "---------------------------------------------------------------------------------------------------------<br/>";
							echo "<span  style='background-color:red'>TARGET ZIP MISSING  . RECORD SKIPPED</span><br/>"; 
							echo "---------------------------------------------------------------------------------------------------------<br/>";
							 
						continue;
					}

					
							if($targetzip != "")
							{
								$existingrefresult = $pdo->query("SELECT COUNT(*) AS rcnt FROM referralsuggestions 
								WHERE partnerid='" . $row['user_id']  . "' AND 
								knowtorefer='" . $row['id']  ."' AND 
								knowreferedto='$newknowid' AND knowenteredby='$userid' ");
								
								echo "---------------------------------------------------------------------------------------------------------<br/>";
								echo  "Check Existing Suggestions<br/>"; 
								echo "---------------------------------------------------------------------------------------------------------<br/>";
								echo "SELECT COUNT(*) AS rcnt FROM referralsuggestions 
								WHERE partnerid='" . $row['user_id']  . "' AND 
								knowtorefer='" . $row['id']  ."' AND 
								knowreferedto='$newknowid' AND knowenteredby='$userid'  <br/>"; 
								echo "---------------------------------------------------------------------------------------------------------<br/>";
							
								
								$existingrefcnt = $existingrefresult->fetchAll(PDO::FETCH_ASSOC)[0]['rcnt'];
								
								if( $existingrefcnt  > 0 )
								{
									//$pdo->query("delete from referralsuggestions 
									//where  partnerid='" . $row['user_id']  . "' and 
									//knowtorefer='" . $row['id']  ."' and knowreferedto='$newknowid' AND knowenteredby='$userid' ");
								
								echo "---------------------------------------------------------------------------------------------------------<br/>";
								echo  "Existing Suggestions Found.<br/>"; 
								echo "---------------------------------------------------------------------------------------------------------<br/>";
								 
							
								}
								else 
								{
								echo "---------------------------------------------------------------------------------------------------------<br/>";
								echo  "No-Existing Suggestions Found.<br/>"; 
								echo "---------------------------------------------------------------------------------------------------------<br/>";
								  
								} 
								 
									if($row['user_id'] != $userid)
									{
										echo "---------------------------------------------------------------------------------------------------------<br/>";
										echo  "Source ZIP: $sourcezip Target ZIP: $targetzip.<br/>"; 
										echo "---------------------------------------------------------------------------------------------------------<br/>";
										  
										//calculate distance
										if($targetzip == $sourcezip)
										{
											$refqry = "INSERT INTO referralsuggestions 
											 ( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby,
											 sourcezip, targetzip, ranking, distance, distancecalculated) 
											 VALUES ('".  $row['user_id'] . "', '". $row['id'] . "',
											 '$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' ,
											 '$targetzip' , '$user_ranking', '0', '1' )" ; 
											  
										}
										else  if($targetzip != '' && $sourcezip  != '')
										{
											 
											$zipqry = "select * from  mc_city_geolocation where zip in (" . $targetzip  . ", " . $sourcezip  . " ) ";
											
											
								echo "---------------------------------------------------------------------------------------------------------<br/>";
								echo  "Distance Check Query <br/>"; 
								echo "---------------------------------------------------------------------------------------------------------<br/>";
								echo  $zipqry . "<br/>"; 
								echo "---------------------------------------------------------------------------------------------------------<br/>";
							
								$rsgeolocs  = $pdo->query($zipqry);
							
											if($rsgeolocs->rowCount() == 2)
											{
												 
												$geolocs = 	$rsgeolocs->fetchAll(PDO::FETCH_ASSOC) ; 
												$latitude1 = $geolocs[0]['latitude'] ;
												$longitude1 = $geolocs[0]['longitude'] ;  
												$latitude2 = $geolocs[1]['latitude'] ; 
												$longitude2 = $geolocs[1]['longitude'] ;
												 
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
										}
										else 
										{
											echo "---------------------------------------------------------------------------------------------------------<br/>";
								 echo   "<span  style='background-color:green'>Either of the two ZIP Codes Missing</span><br/>"; 
								echo "---------------------------------------------------------------------------------------------------------<br/>";
							
										}
										//$pdo->query( $refqry ); 
										 echo "---------------------------------------------------------------------------------------------------------<br/>";
							
										echo $refqry . "<br/>"; echo "---------------------------------------------------------------------------------------------------------<br/>";
							
									} 
								 
					}  
					else
					{
						echo "---------------------------------------------------------------------------------------------------------<br/>";
						echo "<span style='background-color:red'>Target ZIP blank. So, record skipped</span><br/>"; 
						echo "---------------------------------------------------------------------------------------------------------<br/>";
					}
						} 
						else
						{
							echo "---------------------------------------------------------------------------------------------------------<br/>";
							echo "<span style='background-color:red'>RANK < 20. So, record skipped</span><br/>"; 
							echo "---------------------------------------------------------------------------------------------------------<br/>";
						}
					}
					 	
				}
			}
			//mark referral suggestion
			//$pdo->query("update  user_people  set refgenerated='1' where id='$newknowid'") ;
			echo "---------------------------------------------------------------------------------------------------------<br/>";
			echo "Updating First Know Mapping<br/>"; 
			echo "---------------------------------------------------------------------------------------------------------<br/>";
			echo "update  user_people  set refgenerated='1' where id='$newknowid' <br/>"; 
			echo "---------------------------------------------------------------------------------------------------------<br/>";
			
			if($actualrefgenerate >= 20) break; //break loop after generating maps for 10 knows
		}
		
		}
		
		$jsonresult = array('error' =>  '0' , 'qry' =>  $mainQry  ,   'errmsg'  =>   "Automatic referral complete!" );   
	}
	catch(PDOException $e)
	{
		$jsonresult = array('error' =>  '1' ,  'qry' =>  $mainQry  , 'errmsg' =>  $e->getMessage() ); 
	}
	 
	var_dump( $jsonresult );
	
	

?>