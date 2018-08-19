<?php
 
	ini_set('memory_limit', '1024M');
	date_default_timezone_set('America/Los_Angeles'); 
	include_once '../includes/db.php';   
	 
	//field copy 
	$date = date('Y-m-d');
	$outer_loop_query = "SELECT userid  userid  FROM mc_login_log where date(logintime) = '$date' and userid <> '1'   " ; 
	$rs_outer = $link->query($outer_loop_query);
	 
	   
	    if($rs_outer->num_rows  > 0)
	    {
			while($current_row = $rs_outer->fetch_array())
			{
				$userid =  $mid = $current_row['userid'];
				$query_group = "select * from user_details where user_id='$mid'"; 
				
				$rs_group = $link->query($query_group);
				if($rs_group->num_rows  > 0)
				{
					$users= $rs_group->fetch_array();
					$groups = explode(",", $users['groups'] ); 
					$sql_query = "select p.*, a.answer 
					from user_people  as p inner join user_answers as a 
					on p.id=a.user_id 
					where p.user_id='$mid' and refgenerated in (0, 10)  
					and a.answer <> 'null' order by  p.id desc , p.updatedate desc " ;
				 
				 
					$know_profile = $link->query( $sql_query );
					if($know_profile->num_rows  > 0)
					{
						//$firstknow = $know_profile->fetch_array();
						$actualrefgenerate=0;
						$reccnt =0; 
						while($newknows = $know_profile->fetch_array()  )
						{
							if($reccnt > 1) break;
							$knowprofessions =array_filter( array_map('trim', explode(",",    $newknows['client_profession'] ) ) );
											
							$interestedprofessions = $newknows['answer'] ;
							$newknowid = $newknows['id'] ; 
							$sourceziparr = array_filter(explode(',', $newknows['client_zip'] )) ;
							if( sizeof($sourceziparr) > 0)
								$sourcezip = $sourceziparr[0]; //zip code of the new know
							else 
								$sourcezip = '';			
											
							//mark referral suggestion
							
							 $link->query("update user_people set refgenerated='1' where id='$newknowid'") ;
						 
							if($sourcezip   == '') 
							{
								continue;
							}
 
							//second making main query
							$professionlist =array_filter( explode(",",  $interestedprofessions) );  
							$where_group = "(find_in_set('".implode("', client_profession) OR find_in_set('", $professionlist)."', client_profession ))";
							
							
							//first getting subquery for retrieving partners
							$where_in_set = "(find_in_set('".implode("', groups) OR find_in_set('", $groups)."', groups ))";
							
							 
							$qryInner = "select a.user_id from user_details as a inner join mc_user as b on b.id = a.user_id 
							where $where_in_set and b.id != '1' and user_pkg='Gold' " ;
							
							$mainQry = "select p.*, sum(r.ranking) as rank from user_people as p 
							inner join user_rating as r on p.id=r.user_id 
							where p.user_id in ( $qryInner )  and " . $where_group . 
							" group by p.id order by client_name" ;
							
							 
							$matchingknowrst = $link->query( $mainQry );
							if($matchingknowrst->num_rows  > 0)
							{
								$actualrefgenerate++;
								$pos =1;
								 
								while($row = $matchingknowrst->fetch_array()   )
								{
									$id = $row['id'] ; 
									 
									$user_ranking = $row['rank'] ; 
									$targetknowprofession = array_map('trim', explode(",",  $row['client_profession']  ) );
									$matchingprofession = array_intersect($knowprofessions, $targetknowprofession);
									
									if($user_ranking  < 20) 
									{
										break;
									}
									  
									// Count how many times each value exists
									$matchingprofessioncount  = array_count_values($matchingprofession); 
									$tmp = array_filter($matchingprofessioncount); 
									
									$rsrating  = $link->query("select count(*) as rowcnt from user_rating where user_id='$newknowid' "); 
									$rslocation  = $link->query("select client_location from user_people where id='$newknowid' ");
									
									if($rslocation->num_rows  > 0)
									{
										$clientlocationfield = $rslocation->fetch_array()['client_location'] ;
									}
									else 
									{
										$clientlocationfield = '';
									}
									
									
									if(  $clientlocationfield    !== NULL && 
									$clientlocationfield   != '' && 
									$rsrating->fetch_array()['rowcnt']  > 0 )
									{
										if( $user_ranking >= 20 && empty($tmp) )
										{
											$targetziparr = array_filter(explode(',', $row['client_zip'] )) ;
											
											if(sizeof($targetziparr)    == 0 ) 
											{ 
												continue;
											} 
											$targetzip = $targetziparr[0];
											if($targetzip != "")
											{
												
												$existingrefresult = $link->query("SELECT COUNT(*) AS rcnt FROM referralsuggestions 
												WHERE partnerid='" . $row['user_id']  . "' AND 
												knowtorefer='" . $row['id']   ."' AND 
												knowreferedto='$newknowid' AND knowenteredby='$userid' ");
												$existingrefcnt = $existingrefresult->fetch_array()['rcnt'] ;
												
												if( $existingrefcnt  > 0 )
												{
													$link->query("delete from referralsuggestions 
													where  partnerid='" . $row['user_id'] . "' and 
													knowtorefer='" . $row['id'] . "' and knowreferedto='$newknowid' AND knowenteredby='$userid' ");
												}
												
												if($row['user_id']  != $userid )
												{
													//calculate distance
													if($targetzip == $sourcezip)
													{
														$refqry = "INSERT INTO referralsuggestions 
														( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby, 
														sourcezip, targetzip, ranking, distance, distancecalculated) 
														values ('".  $row['user_id'] . "', '". $row['id']  . "', 
														'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' , 
														'$targetzip' , '$user_ranking', '0', '1' )" ; 
														 
													}
													else if($targetzip != '' && $sourcezip  != '')
													{
														$zipqry = "select * from mc_city_geolocation where zip in (" . $targetzip  . ", " . $sourcezip  . " ) ";
														$rsgeolocs  = $link->query( $zipqry );
														if($rsgeolocs->num_rows  == 2)
														{
															$geolocs = 	$rsgeolocs->fetch_array() ; 
															$latitude1 = $geolocs['latitude']  ;
															$longitude1 = $geolocs['longitude']  ;  
															$geolocs = 	$rsgeolocs->fetch_array() ;  
															$latitude2 = $geolocs['latitude']; 
															$longitude2 = $geolocs['longitude'] ;
															$theta = $longitude1 - $longitude2;
															$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
															$distance = acos($distance);
															$distance = rad2deg($distance);
															$distance = $distance * 60 * 1.1515; 
															switch($unit) 
															{
																case 'Mi': break;
																case 'Km' : $distance = $distance * 1.609344;
															}
															
															$distance  = (round($distance,2));
															$refqry = "insert into referralsuggestions 
															( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby, 
															sourcezip, targetzip, ranking, distance, distancecalculated)  
															VALUES ('".  $row['user_id']  . "', '". $row['id']  . "',  
															'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' ,  
															'$targetzip' , '$user_ranking', '$distance', '1' )" ;  
														}
														else
														{
															$refqry =  "insert into referralsuggestions 
															( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby , sourcezip, targetzip, ranking) VALUES 
															('".  $row['user_id']  . "', '". $row['id']  . "', 
															'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' , '$targetzip' ,   '$user_ranking' )" ;
														}
													} 
												 $link->query( $refqry ); 
												}  
											}  
										} 
									} 
								}/* end of while loop */ 
							}
							if($actualrefgenerate >= 20) break;
								$reccnt++; 
						} 
					} 
				} 
			}
		}		 
	 
	  
?>
