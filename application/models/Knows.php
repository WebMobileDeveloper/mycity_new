<?php
 


class Knows extends CI_Model
{
	 var $id ='';
	 var $user_idvar=''; 
	 var $client_namevar=''; 
	 var $client_professionvar=''; 
	 var $client_lifestylevar=''; 
	 var $client_phonevar=''; 
	 var $client_emailvar=''; 
	 var $client_locationvar=''; 
	 var $client_zipvar=''; 
	 var $client_notevar=''; 
	 var $user_groupvar=''; 
	 var $entrydatevar=''; 
	 var $updatedatevar=''; 
	 var $companyvar=''; 
	 var $isimportvar=''; 
	 var $lcidvar=''; 
	 var $tagsvar=''; 
	 var $refgeneratedvar=''; 
	 var $showfirstvar=''; 
	 var $isinvitedvar=''; 
	 var $deletedvar=''; 
	 var $total_rankvar='';
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$alltags = explode(',', $data['tags'] );
		
		if (in_array("Rated 25", $alltags )  ) 
		{
			$table='user_people';
			//$table='user_people_rated';
		}
		else 
		{
			$table='user_people';
		}
		
		$this->db->insert( $table , $data);
		$knowid = $this->db->insert_id() ; 
		return $knowid; 
	}
	 
	public function add_temporary($data)
	{ 
		$this->db->insert("user_people", $data);
		$knowid = $this->db->insert_id() ; 
		return $knowid; 
	}
  
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('user_people'); 
	}
	
	function getknow($member_id)
	{
		$this->db->select("*");
		$this->db->from("user_people"); 
		$this->db->where("user_id", $member_id);
		$result  = $this->db->get(); 
		return  $result ; 
	} 
	
	function get_know_profile($id)
	{
		$this->db->select("*");
		$this->db->from("user_people"); 
		$this->db->where("id", $id);
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	function get_myknows($member_id, $offset,  $limit ) 
	{
		$query = "select * from user_people as a inner join " .
		" (SELECT user_id, sum(ranking) as rank FROM user_rating  group by user_id order by rank) as b " .
		" on a.user_id=b.user_id where a.user_id='$member_id'   order by entrydate desc LIMIT  $offset, $limit ";
		
		$query = "select *, 0 rank  from user_people 
		where user_id='$member_id'  order by entrydate desc LIMIT  $offset, $limit ";
		
		$result  = $this->db->query($query);  
		$sql_query_count = "select count(*) as reccnt from user_people
		 where user_id  ='$member_id' "; 	
		$result_count = $this->db->query($sql_query_count); 
		$num_rows =  $result_count->row()->reccnt ;			
					
		if ($result->num_rows() > 0) 
		{
			
			foreach($result->result() as $item)
			{
				$sql = "select sum(ranking) as rank 
				from user_rating 
				where user_id='" .  $item->id .  "' group by user_id order by rank ";
				$rank_row = $this->db->query($sql);
				if($rank_row->num_rows() > 0)
				{
					$item->rank  = $rank_row->row()->rank;
				}
			}
			
			$jsonresult = array('error' =>  '0' , 'num_rows' =>  $num_rows,  'errmsg' =>  "Connections are fetched!" , 
			'results' =>  $result  );  
			return $jsonresult;   
        }
		return false;
   }
   
   function get_knows($filter, $start,  $limit ) 
	{
		$this->db->select("id , client_name , client_profession , client_lifestyle, 
		client_location, 0 rating");
		$this->db->from("user_people");  
		$this->db->where("client_name like '" . $filter['name'] . "%'"); 
		if($filter['vocation'] !='')
			$this->db->where('find_in_set("'. $filter['vocation'] .'", client_profession) = 0'   );
		$this->db->limit($limit, $start); 
        $result  = $this->db->get(); 
		if ($result->num_rows() > 0) 
		{
			
			foreach($result->result() as $item )
			{
				$know_id =  $item->id ;  
				//calculating average rating
				$query_know_rating =  "select sum(ranking) as total_rank from  user_rating  where user_id='$know_id' "  ;  
				 
				$knowrating = $this->db->query($query_know_rating); 
				$know_rating = $knowrating->row()->total_rank; 
				if(is_null( $know_rating ))
					$item->rating  = "0";
				else 
					$item->rating = $know_rating ; 
			}
			return $result;
        }
		return false; 
   }
   
   
   function show_introduction($data)
   {
	   $pagesize = $data['pagesize']; 
	   $offset =   $data['offset']  ; 
	   $user_id =   $data['uid']  ;
	   
	   $startid = $data['ssf'];
	   $startwhere='';		
	   
	   //fill the ranks  
	   $rs_rankfiller =  $this->db->query("select * from  referralsuggestions where  
	   knowenteredby = '$user_id' and ( source_rank = '0' and  target_rank='0' )"); 
	   if($rs_rankfiller->num_rows() > 0)
	   {
		   $rank_update_cnt=0;
		   foreach($rs_rankfiller->result() as $ref_row)
		   {
			   $refrowid = $ref_row->id;
			   $source = $ref_row->knowtorefer;
			   $target =  $ref_row->knowreferedto;
			   $trknowtorefer = $this->db->query("SELECT  sum( ranking) as totalscore from   user_rating where user_id in (" . $source  . ","  . $target . " )  group by user_id"); 
				
				$trrowcount = $trknowtorefer->num_rows();
				if($trrowcount == 0)
				{ 
					$sourcerank = $targetrank =0;
				}
				else if($trrowcount == 1)
				{
					$rankrow = $trknowtorefer->row(0);
					$sourcerank = $rankrow->totalscore ;  
					$targetrank =0;
				}
				else if($trrowcount >= 2)
				{
					$rankrow = $trknowtorefer->row(0);
					$sourcerank = $rankrow->totalscore ;  
					$rankrow = $trknowtorefer->row(1);
					$targetrank = $rankrow->totalscore ;
				}
				$this->db->query("update  referralsuggestions set source_rank='$sourcerank', target_rank='$targetrank' where id='$refrowid' ");   
			
				$rank_update_cnt++;
				if($rank_update_cnt > 100)
				{
					break;
				}
			}
		}
 
	   $allrecords =  $this->db->query("SELECT r.id, r.partnerid, r.knowtorefer, r.knowreferedto, r.entrydate, r.ranking, r.emaillog , r.emailstatus , r.sourcezip, r.targetzip , r.distance , r.distancecalculated , u.user_email, u.username, u.user_phone, u.image, u.user_status, 0 as marked 
	   FROM referralsuggestions as r  inner join mc_user as u 
	   ON r.partnerid= u.id inner join user_people as up on up.id= r.knowreferedto 
	   WHERE  emailstatus='0' AND 
	   r.isdeleted <> '1' AND r.isdeleted <> '2'  AND knowenteredby  = '$user_id' and markrem='0' 
		and up.client_email <> u.user_email and user_status='1' and 
		(r.distance >=  0 and r.distance < '30') and distancecalculated='1'   and ( source_rank >= '20' and target_rank >= '20' ) ORDER BY up.showfirst desc, r.id DESC limit $offset , 10 ");
	 
		$allrecordscount =  $this->db->query("SELECT count(*) as allcnt FROM referralsuggestions as r  inner join mc_user as u 
		ON r.partnerid= u.id inner join user_people as up on up.id= r.knowreferedto 
		WHERE  emailstatus='0' AND  
		r.isdeleted <> '1' AND r.isdeleted <> '2'  AND knowenteredby  = '$user_id' and markrem='0' 
		and up.client_email <> u.user_email and user_status='1' and 
		(r.distance >=  0 and r.distance < '30') and distancecalculated='1'   and ( source_rank >= '20' and target_rank >= '20' )  ");
	 
	$refsuggestions = [];
	if($allrecords->num_rows() > 0)
	{
		//scanning distance
		$j = 0; 
		foreach( $allrecords->result() as  $temprow)
		{
			$refsuggestions[] =array('id' =>$temprow->id  , 'partnerid' =>$temprow->partnerid , 
			'knowtorefer'=>$temprow->knowtorefer , 'knowreferedto'=>$temprow->knowreferedto , 
			'ranking'=>$temprow->ranking ,  'entrydate'=>$temprow->entrydate , 
			'emaillog'=>$temprow->emaillog , 'emailstatus'=>$temprow->emailstatus ,
			'sourcezip' =>$temprow->sourcezip , 'targetzip' =>$temprow->targetzip , 
			'distance'=>$temprow->distance ,  'distancecalculated'=>$temprow->distancecalculated , 
			'user_email' =>$temprow->user_email , 'username'=>$temprow->username , 
			'user_phone'=>$temprow->user_phone ,  'image'=>$temprow->image , 
			'user_status' =>$temprow->user_status , 'marked'=> '1' ); 
			$j++; 
		}   
		return array('totalpage' => $allrecordscount->row()->allcnt  ,  'records' => $refsuggestions); 
	}
		return array('totalpage' => 0, 'records' => []);    
   }
   
    
   function do_referral_mapping($userid, $unit)
   { 
	   $this->db->select("*");
		$this->db->from("user_details"); 
		$this->db->where("user_id", $userid);
		$result  = $this->db->get(); 
		if ($result->num_rows() > 0) 
		{
			$users=$result->row();
			$groups = explode(",", $users->groups );  
			$sql_query = "select p.*, a.answer from user_people  as p inner join 
			user_answers as a on p.id=a.user_id 
			where p.user_id='$userid' and refgenerated in (0, 10)  
			and a.answer <> 'null' order by  p.id desc , p.updatedate desc  " ;
		 
			$know_profile = $this->db->query( $sql_query );
			if($know_profile->num_rows() > 0)
			{
				$firstknow = $know_profile->row();
				$actualrefgenerate=0;
				$reccnt =0;  
				foreach($know_profile->result() as $newknows )
				{
					if($reccnt > 1) break;
					$knowprofessions =array_filter( array_map('trim', explode(",",    $newknows->client_profession ) ) );
					 				
					$interestedprofessions = $newknows->answer ;
					$newknowid = $newknows->id ; 
					$sourceziparr = array_filter(explode(',', $newknows->client_zip )) ;
					if( sizeof($sourceziparr) > 0)
						$sourcezip = $sourceziparr[0]; //zip code of the new know
					else 
						$sourcezip = '';			
									
					//mark referral suggestion
					 $this->db->query("update user_people set refgenerated='1' where id='$newknowid'") ;
					if($sourcezip   == '') 
					{
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
					
					 
					$qryInner = "select a.user_id from user_details as a inner join mc_user as b on b.id = a.user_id 
					where $where_in_set and b.id != '1' and user_pkg='Gold' " ;
					
					$mainQry = "select p.*, sum(r.ranking) as rank from user_people as p 
					inner join user_rating as r on p.id=r.user_id 
					where p.user_id in ( $qryInner )  and " . $where_group . 
					" group by p.id order by client_name" ;
					 
					$matchingknowrst = $this->db->query( $mainQry );
					if($matchingknowrst->num_rows() > 0)
					{
						$actualrefgenerate++;
						$pos =1;
						 
						foreach( $matchingknowrst->result() as $row )
						{
							$id = $row->id ; 
							 
							$user_ranking = $row->rank ; 
							$targetknowprofession = array_map('trim', explode(",",  $row->client_profession  ) );
							$matchingprofession = array_intersect($knowprofessions, $targetknowprofession);
							
							if($user_ranking  < 20) 
							{
								break;
							}
							// Count how many times each value exists
							$matchingprofessioncount  = array_count_values($matchingprofession); 
							$tmp = array_filter($matchingprofessioncount); 
							$rsrating  = $this->db->query("select count(*) as rowcnt from user_rating where user_id='$newknowid' "); 
							$rslocation  = $this->db->query("select client_location from user_people where id='$newknowid' ");
							
							if($rslocation->num_rows() > 0)
							{
								$clientlocationfield = $rslocation->row()->client_location ;
							}
							else 
							{
								$clientlocationfield = '';
							}
							
							if(  $clientlocationfield    !== NULL && $clientlocationfield   != '' && 
							$rsrating->row()->rowcnt  > 0 )
							{
								if( $user_ranking >= 20 && empty($tmp) )
								{
									$targetziparr = array_filter(explode(',', $row->client_zip )) ;
									
									if(sizeof($targetziparr)    == 0 ) 
									{ 
										continue;
									} 
									$targetzip = $targetziparr[0];
									if($targetzip != "")
									{
										
										$existingrefresult = $this->db->query("SELECT COUNT(*) AS rcnt FROM referralsuggestions 
										WHERE partnerid='" . $row->user_id  . "' AND 
										knowtorefer='" . $row->id   ."' AND 
										knowreferedto='$newknowid' AND knowenteredby='$userid' ");
										$existingrefcnt = $existingrefresult->row()->rcnt ;
										
										if( $existingrefcnt  > 0 )
										{
											$this->db->query("delete from referralsuggestions 
											where  partnerid='" . $row->user_id   . "' and 
											knowtorefer='" . $row->id   ."' and knowreferedto='$newknowid' AND knowenteredby='$userid' ");
										}
										
										if($row->user_id  != $userid )
										{
											//calculate distance
											if($targetzip == $sourcezip)
											{
												$refqry = "INSERT INTO referralsuggestions 
												( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby, 
												sourcezip, targetzip, ranking, distance, distancecalculated) 
												values ('".  $row->user_id . "', '". $row->id  . "', 
												'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' , 
												'$targetzip' , '$user_ranking', '0', '1' )" ; 
											}
											else if($targetzip != '' && $sourcezip  != '')
											{
												$zipqry = "select * from mc_city_geolocation where zip in (" . $targetzip  . ", " . $sourcezip  . " ) ";
												$rsgeolocs  = $this->db->query( $zipqry );
												if($rsgeolocs->num_rows() == 2)
												{
													$geolocs = 	$rsgeolocs->row() ; 
													$latitude1 = $geolocs->latitude  ;
													$longitude1 = $geolocs->longitude  ;  
													$geolocs = 	$rsgeolocs->row() ;  
													$latitude2 = $geolocs->latitude; 
													$longitude2 = $geolocs->longitude ;
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
													VALUES ('".  $row->user_id  . "', '". $row->id  . "',  
													'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' ,  
													'$targetzip' , '$user_ranking', '$distance', '1' )" ;  
												}
												else
												{
													$refqry =  "insert into referralsuggestions 
													( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby , sourcezip, targetzip, ranking) VALUES 
													('".  $row->user_id  . "', '". $row->id  . "', 
													'$newknowid' ,  NOW() ,  '$userid' , '$sourcezip' , '$targetzip' ,   '$user_ranking' )" ;
												}
											}
										 $this->db->query( $refqry ); 
										}  
									}  
								} 
							}  
						} //end of inner for
					}
					if($actualrefgenerate >= 20) break;
					$reccnt++;
				}
			} 
        }  
		$jsonresult = array('error' =>  '0' , 'errmsg'  =>   "Automatic referral complete!" );
		return json_encode($jsonresult)   ;  
   }
   
    
   
   function autocomplete_json($data)
   {
	   $user_id =  $data['user_id'];
	   $user_role =  $data['user_role'];
	   $searchTerm = $data['phrase'];
	   
	   if($user_role == 'admin')
	  {
		 $mainQry = "SELECT * FROM user_people where  client_name like '%$searchTerm%' ORDER by client_name LIMIT 50" ;

		 $connections = $this->db->query( $mainQry );
	
		$users = array();
		if( $connections->num_rows() > 0)
		{ 
			foreach($connections->result() as $row )
			{
				$users[] = array( 'code' => $row->id  , 'name' => $row->client_name ); 
			} 
		}
	  }
	 else 
	 {
		//read active user group and create where clause for group id search
		$userresult = $this->db->query("SELECT * FROM user_details where user_id = '$user_id'");
		$user = $userresult->row();
		$groups = array_filter(explode(",", $user->groups ));

		
		if(sizeof($groups > 0))
		{			
			$where_in_set = " and ( find_in_set('". implode("', groups ) OR find_in_set('", $groups)."', groups ) )";
		}
		else 
		{
			$where_in_set = '';
		}
		$qryInner = "SELECT a.user_id FROM user_details as a inner join mc_user as b on b.id = a.user_id 
		WHERE b.id != '1' and b.id != '$user_id' and user_pkg='Gold'  $where_in_set   " ; 
		//create main query to insert any new referrals
		$mainQry = "SELECT  id, client_name  FROM user_people  WHERE client_name LIKE '$searchTerm%' and   user_id IN  ( $qryInner )  ORDER BY client_name LIMIT 10" ;
		$connections = $this->db->query( $mainQry );
		$users = array();
		if( $connections->num_rows() > 0)
		{ 
			foreach( $connections->result() as $row )
			{
				$users[] = array( 'code' => $row->id  , 'name' => $row->client_name ); 
			} 
		}		 
	}
	echo  json_encode(  $users );
}


 
function autocomplete_my_know_name_json($data)
{
	$user_id =  $data['user_id'];
	$user_role =  $data['user_role'];
	$searchTerm = $data['phrase'];
	
	if($user_role == 'admin')
		$mainQry = "SELECT * FROM user_people where  client_name like '$searchTerm%' ORDER by client_name LIMIT 50" ;
	else
		$mainQry = "SELECT * FROM user_people where user_id = '$user_id' and client_name like '$searchTerm%' ORDER by client_name LIMIT 50" ;
	
	$connections = $this->db->query( $mainQry );
	
	$users = array();
	if( $connections->num_rows() > 0)
	{ 
		foreach(  $connections->result() as $row )
		{
			$users[] = array( 'code' => $row->id  , 'name' => $row->client_name ); 
		} 
	} 
	echo  json_encode(  $users );  
}



function autocomplete_member_know_name_json($data)
{
	 
	$user_id =  $data['mid']; 
	$searchTerm = $data['phrase'];
	
	$mainQry = "SELECT * FROM user_people where user_id = '$user_id' and client_name like '$searchTerm%' ORDER by client_name LIMIT 50" ;
	
	$connections = $this->db->query( $mainQry );
	
	$users = array();
	if( $connections->num_rows() > 0)
	{ 
		foreach(  $connections->result() as $row )
		{
			$users[] = array( 'code' => $row->id  , 'name' => $row->client_name ); 
		} 
	} 
	echo  json_encode(  $users );  
} 
public function search_nearest($data)
{
	$keyword  = $data['keyword']; 
	$city = $data['city'];
	$vocation = $data['vocation'];
	$userid = $data['userid'];
	$start = $data['offset']; 
	$start2 = $data['offset2']; 
	$iszip = $data['iszip'];  
	$utype = $data['utype']; 
	$where_member_ids = array();
	$pagesize = 10;   
	
	if($city == "")
	{
		$where_city ='';
		$knowwhere_city ='';
	}
	else 
	{
		if($iszip == 1 )
		{
			$neighbours = findneighbours($this, $city, '30');
			if(sizeof($neighbours ) > 0)
			{
				$neighbourzips = implode(",", $neighbours); 
				$where_city = "   zip in  (  " . $neighbourzips   . "  )  and ";
				$knowwhere_city = "   client_zip in  (  " . $neighbourzips   . "  )  and "; 
			}
			else 
			{
			  $where_city  = "  zip ='" .  $city . "' and "   ;
			  $knowwhere_city = " client_zip ='" .  $city . "' and "   ;
			}
		}
		else 
		{
			$cities = explode(",", $city);
			$where_city  = " ( FIND_IN_SET('" . implode("',  cities ) OR FIND_IN_SET('", $cities) . "',  city ) )  and"   ;
			$knowwhere_city = " ( FIND_IN_SET('" . implode("',  client_location ) OR FIND_IN_SET('", $cities) . "',  client_location ) )  and"   ;
		}
	}
	 
	if($vocation != '')
	{
		$keys = explode(",", $vocation);
		$where_vocations  = " or ( FIND_IN_SET('" . implode("',  vocations ) OR FIND_IN_SET('", $keys) . "',  vocations ) ) "   ;
		$knowwhere_vocations  = " and ( FIND_IN_SET('" . implode("',  client_profession ) OR FIND_IN_SET('", $keys) . "',  client_profession ) ) "   ;  
	}
	else if($keyword != '')
	{
		$keys = explode(",", $keyword);
		$where_vocations  = " or ( FIND_IN_SET('" . implode("',  vocations ) OR FIND_IN_SET('", $keys) . "',  vocations ) ) "   ; 
		$knowwhere_vocations  = " and ( FIND_IN_SET('" . implode("',  client_profession ) OR FIND_IN_SET('", $keys) . "',  client_profession ) ) "   ; 
	}
	
	if($keyword == '' && $city ==''  && $vocation == '')
	{
		$jsonresult = array('error' =>  '10' ,   'errmsg' =>  'Search parameter missing!' );
		$response->getBody()->write( json_encode($jsonresult) );
		return $response;   
	}
	
	$sql_query_ids =  " select a.id as ui from mc_user as a inner join  user_details  as b on a.id=b.user_id  where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations )   "; 
	$member_ids = $this->db->query($sql_query_ids); 
	if($member_ids->num_rows() > 0 )
	{
		$ids = array(); 
		foreach ($member_ids->result() as $row )  
		{
			$where_member_ids[] = $row->ui ; 
		}   
	} 
	$sql_query =  "select a.id as ui, 0 knid  , user_email as a, username  as b, user_role  as c, user_pkg  as d, user_phone  as e,  image  as f,  busi_name  as g,  user_type  as h, " .
	" busi_location_street  as i,  busi_location  as j,  busi_type  as k,  busi_hours  as l, busi_website  as m, current_company  as n, linkedin_profile  as o, " .
	" street  as p, city  as q, zip  as r, country  as s,  groups  as t,  target_clients  as u, target_referral_partners  as v, vocations  as w, about_your_self  as x , user_shortcode, " .
	" 0 as isconnected, 0 as rating from mc_user as a inner join  user_details  as b on a.id=b.user_id  " .
	" where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations )  order by username  limit $start, $pagesize "; 
	$sql_query1 = $sql_query;
		
		$sql_query_count = "select count(*) as reccnt from mc_user as a inner join  user_details  as b on a.id=b.user_id  " .
		" where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations ) ";
		
		$members = $this->db->query($sql_query);
		$membercount = $members->num_rows();
		if( $membercount > 0 )
		{
			foreach($members->result() as $item )
			{
				$sp =  $item->ui ; 
				$query_connect_check =  "select * from mc_member_connections where  status='1' and (  ( firstpartner='$userid' and secondpartner='$sp')  or ( firstpartner='$sp' and secondpartner='$userid') ) "  ; 
				
				$rstconcheck = $this->db->query($query_connect_check); 
				if($rstconcheck->num_rows() > 0 )
				{
					 
					$item->isconnected   = '1'; 
				} 
				//calculating average rating
				$query_avg_rating =  "select sum(ranking) as ranking from mc_user_rating where  user_id='$sp' group by rated_by "  ;  
				$avgraters = $this->db->query($query_avg_rating); 
				if($avgraters->num_rows() > 0 )
				{
					$avgrate =0;
					$count= 0 ;
					foreach($avgraters->result() as $ritem)
					{
						$avgrate += $ritem->ranking ;
						$count++;
					}
					if($count > 0)
						$item->rating = $avgrate / $count; 
				} 
			}
			
			//loggin search keyword
			//$memberrating = usort($members, memberrating ); 
			$result_count = $this->db->query($sql_query_count);   
			$pages =   $result_count->row()->reccnt ; 
			$jsonresult = array( 'pages' => $pages , 'result' => $members, 'msg1' => 'Member fetched successfully!'  ); 
		}
		
		$membername ='';
		if($membercount == 0)
		{
			$jsonresult = array( 'pages' => '0' , 'result' => '',  'msg1' => 'No matching members found!'   ); 
			$rsnameorvoc = $this->db->query("SELECT * FROM  groups  where islisted='1' and grp_name='$keyword' ORDER BY  grp_name "); 
			if( $rsnameorvoc->num_rows() == 0 )
			{
				$membername = " and client_name like '%$keyword%' "; 
			}
			else 
			{
				$membername = " and find_in_set('$keyword', client_profession) ";
			}
		}
		else 
		{
			$membername = " and find_in_set('$keyword', client_profession) ";
		}
		
		//fetching knows 
		if(sizeof( $where_member_ids) > 0)
		{
			$where_memids = " k.user_id in ( ". implode(',' , $where_member_ids ) .  " ) ";
			$where_memids_cnt = "  k.user_id in ( ". implode(',' , $where_member_ids ) .  " ) ";
			$sql_query =   " select  k.user_id  as ui, k.id as knid , u.username as un, k.client_email as a  ,  k.client_name as b , 
			'na' c, 'na' d,  k.client_phone as e, 'na' f, 
			'na' g, 'na' h, 'na' i, 'na' j,  'na' k, 'na' l,  'na' m, 'na' n, 
			'na' o, 'na'  p,  k.client_location as q,  k.client_zip as r, 'na'  s, 
			'na' t, 'na'  u, 'na'  v, k.client_profession as w, 'na'  x , 
			0 as isconnected, t2.rating , 0 as requestsent, 0 as ismember_connected, 0 as mem_id, 'no-photo.png' as mem_photo  
			from  user_people as k inner join mc_user as u on k.user_id=u.id  
			inner join 
			( select user_id, sum(ranking) as rating from  user_rating  group by user_id ) as t2  
			on k.id = t2.user_id 
			where $where_memids " ; 
			$sql_query_count = "select count(*) as reccnt 
			from  user_people as k inner join mc_user as u on k.user_id=u.id  
			inner join 
			( select user_id, sum(ranking) as rating from  user_rating  group by user_id ) as t2  
			on k.id = t2.user_id 
			where $where_memids_cnt "  ; 
			$rst_count = $this->db->query($sql_query_count);    
			$pages =  $rst_count->row()->reccnt ; 
			$jsonresult['know_pages'] =  $pages ;
			$rst = $this->db->query($sql_query);
			$i=0;
			$know_result = array();
			if($rst->num_rows() > 0 )
			 {
				 
				foreach( $rst->result_array() as $knows)
				{
					$invitecount = $this->db->query("select count(*) as tcnt from mc_claimprofile_invite 
					where user_id=" . $knows['knid'] . " and member_id='" . $this->session->id . "' ");
					$knows['requestsent'] = $invitecount->row()->tcnt; 
					
					$ismemberrow = $this->db->query("select * from mc_user where user_email='" . $knows['a'] . "'");
					
					if($ismemberrow->num_rows() > 0)
					{
						$member_profile  = $ismemberrow->row();  
						$knows['mem_id'] = $member_profile->id;
						$knows['mem_photo'] = $member_profile->image;
						$query_connect_check =  "select * from mc_member_connections where 
						status='1' and (  ( firstpartner='" .$knows['mem_id'] . "' and secondpartner='$userid')  
						or ( firstpartner='$userid' and secondpartner='" .$knows['mem_id'] . "') ) "  ; 
				
						$rstconcheck = $this->db->query($query_connect_check); 
						if($rstconcheck->num_rows() > 0 )
						{
							$knows['ismember_connected'] = 10;
						}  
						else 
						{
							$knows['ismember_connected'] = 1;
						}
					}
					$know_result[] = $knows;  
					$i++;
				}
				
				usort($know_result, array($this,'ranking_sort'));
				
				//$memberrating = usort($knows, memberrating );  
				$jsonresult['knows'] =  $know_result ;   
				$jsonresult['msg2'] =  'Matching knows found!' ;
			}
			else 
			{
				$jsonresult['msg2'] =  'Matching knows not found!' ;
				$jsonresult['knows'] =  '' ;  				
			}  
		}
		else 
		{
			$jsonresult['msg2'] =  'Matching knows not found!' ;
			$jsonresult['knows'] =  '' ;  	
		}
		
		$jsonresult['errmsg'] = $sql_query   . 'Member fetched successfully!';
		$jsonresult['error'] =  '0'  ; 
		return $jsonresult;    
}

	function ranking_sort($a, $b) 
    {
		if ($a['rating'] == $b['rating'] ) return 0;
		return (   $a['rating'] >  $b['rating']  ) ? -1:1; 
    }
	
	
	function findneighbours($appobj, $zip, $radius )
	{
		$sql_query  = "select * from mc_city_geolocation where zip='$zip'   ";
		$zipcodeList = array();
		$rst = $this->db->query($sql_query);
		
		if($rst->result() > 0)
		{
			$latlong = $rst->row(); 
			$lat = $latlong['latitude'];
			$lon = $latlong['longitude']; 
			
			$sql = 'select distinct(zip) from mc_city_geolocation  ' .
			' where (3958*3.1415926*sqrt((latitude-'.$lat.')*(longitude-'.$lat.') + ' .
			'cos(latitude/57.29578)*cos('.$lat.'/57.29578)*(longitude-'.$lon.')*(longitude-'.$lon.'))/180) <= '.$radius.';';
			$result = $this->db->query($sql);
			foreach($result->result() as $row) 
			{
				array_push($zipcodeList, $row['zip']);
			} 
		}
		return $zipcodeList    ; 
	} 
	
	
	function query($query ) 
	{
		$result  = $this->db->query($query);  
		return $result;
	}
	
	public function reverse_tracking($data)
	{
		$allcities =  auto_fill_zip_from_city( )  ; 
		
		$key = $data['key'];   
		$offset = $data['offset'];   
		$size = $data['size'];   
		$location = $data['location'];     
		$voclist = $data['vocations'];
		$tagslist = $data['tags'];
		$lifestylelist = $data['lifestyle'];
		$zip = $data['zip'];  
		$location_where = $zip_where = $searchVoc = $searchTag  = $searchLifeStyle =''; 
		$name = '%'. $key .'%';
		$phone = $key ;
		
		 
		if(isset($voclist ) && $voclist !='' && $voclist != 'null'  )
		{
			$voclist = array_filter($voclist);
			$searchVoc = " AND  client_profession  IN ('".  implode(',', $voclist ) . "')";  
		}
		
		if(isset($tagslist ) && $tagslist !='' && $tagslist !='null'  )
		{
			$searchTag  = " ";  
			for($i=0; $i < sizeof($tagslist) ; $i++ )
			{
				$searchTag  .= " FIND_IN_SET ( '". $tagslist[ $i ]   . "' , p.tags) "; 
				if($i < sizeof($tagslist) -1  )
				{
					$searchTag  .= " OR ";
				}
			}
			$searchTag  = " AND ( " . $searchTag . ")";  
		}

		
		if(isset($lifestylelist ) && $lifestylelist !='' && $lifestylelist != 'null'  )
		{
			$searchLifeStyle  = " ";  
			for($i=0; $i < sizeof($lifestylelist) ; $i++ )
			{
				$searchLifeStyle  .= " p.client_lifestyle =  '". $lifestylelist[ $i ]   . "'"; 
				if($i < sizeof($lifestylelist) -1  )
				{
					$searchLifeStyle  .= " OR ";
				}
			}
			$searchLifeStyle  = " AND ( " . $searchLifeStyle . ")"; 
		}  

		$location_where ='';
		if($location!='')
		{
			$location_where = " and p.client_location='$location'"; 
		} 
		 
		$zip_where ='';
		if($zip != '' )
		{
			$zip_where = " and p.client_zip='$zip'"; 
		} 
		$sql_query = "select   u.id ,  u.user_email , u.username , u.user_role , u.user_pkg ,  u.user_phone  , p.tags, 
		user_id, client_name, client_email, client_profession, client_phone , p.client_zip, p.client_location , p.id as knowid, 0 as ranking from user_people as p inner join mc_user as u on p.user_id=u.id 
		where ( p.client_name like '$name' or p.client_phone like '$phone%' ) " . $location_where . $zip_where . $searchVoc . $searchTag . $searchLifeStyle . " LIMIT $offset,$size " ;
  
		$sql_query_count = " select count(*) as reccnt from user_people as p inner join mc_user as u on p.user_id=u.id 
	    where  ( p.client_name like '$name' or p.client_phone= '$phone' ) " . $location_where . $zip_where . $searchVoc .$searchTag . $searchLifeStyle ;  
 
	   $result = $this->db->query($sql_query); 
	   if($result->num_rows() > 0 )
	   {
		   foreach($result->result() as $row)
		   {
			   $rate_q  = $this->db->query("select SUM(ranking) as user_ranking from user_rating where user_id = '" . $row->knowid ."'");
			   if($rate_q->num_rows() > 0)
				   $row->ranking  = $rate_q->row()->user_ranking ;  
		   
				 
				if($row->client_zip == '' && $row->client_location !='' && sizeof($allcities) > 0)
				{
					for($ci=0; $ci < sizeof($allcities); $ci++)
					{
						if($allcities[$ci][0] == $row->client_location)
						{
							$row->client_zip  = $allcities[$ci][2] ;
							break;
						} 
					}
				}  
		   }  
		   $result_count = $this->db->query($sql_query_count);
		   $num_rows =  $result_count->row()->reccnt ;
		   $jsonresult = array('error' =>  '0' , 'num_rows' =>  $num_rows,  'errmsg' =>  "Connections are fetched!" ,  
		   'results' =>  $result  );  
	   }	
	   else
		   $jsonresult = array('error' =>  '10' ,  'errmsg' =>   "No matching connection found!"  );
	   
	   return $jsonresult;
    }

	
   function search_knows($data)
   {
	   $limit =$data['limit'];  
	   $zipCode = $data['srchZipCode'];  
	   $voc = $data['locateVoc'];
	   $name = $data['ref_name'] ;
	   $nameparts = explode(' ', $name); 
	   $lifestyle = $data['lifestyle'];
	   $entrydate = $data['entrydate'];
	   $city = $data['city'];
	   $offset = $data['offset'];
	   $tag = $data['tag'];
	   $phone = $data['phone'];
	   $email = $data['email']; 
	   $member_id = $data['uid'];
	   $j=0;
	    
	   for($i=0 ; $i< strlen($phone); $i++  )
	   {
		   if(is_numeric( $phone[$i] ) )
		   {
			   $phonenumber[$j]  =$phone[$i] ; 
			   $phonenumberdot[$j]  =$phone[$i] ; 
			   $j++;
			   if($j==3 || $j == 7) 
			   {
				   $phonenumber[$j] = "-"; 
				   $phonenumberdot[$j] = ".";
				   $j++;
			   }
			   
		  } 
	  } 
	  $phonenumber='';
	  $phonenumberdot='';
	  if($phonenumber !=  null)
		  $phone =  implode("",$phonenumber);
	  if($phonenumberdot != null)
		  $phonedot =  implode("",$phonenumberdot);
	  
	  $where = '';
	  
	  if($zipCode != ''  )
	  {
		  $where .= " AND client_zip = '" .  $zipCode . "' ";
	  }
	    
	
	if ($lifestyle != ''  ) 
	{ 
		$where .= " AND (";
		for($i=0; $i < sizeof($lifestyle); $i++)
		{
			$where .= " FIND_IN_SET('". $lifestyle[$i] . "', client_lifestyle) "; 
			if($i < sizeof($lifestyle) -1) 
			{
				$where .= " OR "; 
			}
		} 
		$where .= ")";
	}
         
		if($voc != ''  )
		{ 
			$where .= " AND (";
			for($i=0; $i < sizeof($voc); $i++)
			{
				$where .= " FIND_IN_SET('". $voc[$i] . "', client_profession)  ";  	
				 
				//checking there is fuzzy search key
				$q = $this->db->query("select * from  mc_fuzzy_map  where  input_text = '" .  $voc[$i] . "'"); 
				if($q->num_rows() > 0)
				{
					$j=0;
					$where .= " OR "; 
					foreach( $q->result() as $row)
					{
						$mapped_text = $row->mapped_text ; 
						$where .= " FIND_IN_SET('". $mapped_text . "', client_profession)  ";  	
						if($j < $q->num_rows()  -1)
						{
							$where .= " OR "; 
						}
						$j++;		
					}
				} 
				if($i < sizeof($voc) -1)
				{
					$where .= " OR "; 
				} 
			} 
			$where .= ")";
		}

		if($city != ''   )
		{
			 
			$where .= " AND (";
			for($i=0; $i < sizeof($city); $i++)
			{
				$where .= " FIND_IN_SET('". $city[$i] . "', client_location)  "; 
				if($i < sizeof($city) -1)
				{
					$where .= " OR "; 
				}
			} 
			$where .= ")";
        } 
        
        if($tag != ''   )
		{
			 
			$where .= " AND (";
			for($i=0; $i < sizeof($tag); $i++)
			{
				$where .= " FIND_IN_SET('". $tag[$i] . "', tags)  "; 
				if($i < sizeof($tag) -1)
				{
					$where .= " OR "; 
				}
			} 
		$where .= ")";
    }
	
	if($entrydate != ''  )
    {
		$where .= " AND date(entrydate) =  '".  $entrydate  . "' ";
    } 
    
    if($this->session->role == 'admin')
	{
		if($phone != ''  )
        {	 
            $where .= " AND ( user_phone like  '$phone%' or user_phone like '$phonedot%' ) ";
        } 
		
		if($email != ''  )
        {	 
            $where .= " AND user_email =  '".  $email  . "' ";
        }  
    }
	else
	{
        if($phone != ''  )
        {	 
            $where .= " AND (client_phone like  '$phone%' or client_phone like '$phonedot%' ) ";
        } 
    }
  
    if($this->session->role == 'admin')
	{
		if($name != '')
		{
			$nameparts = explode(' ', $name); 
			$nameparts = array_filter($nameparts);
			$where .= "AND  username LIKE '$name%'    "; 
    	} 
		//echo   getMyCityUsersAdmin($page, $where , $voc, $name );
		$result ='';
    }
	else
	{
		if($name != '')
		{
			$where .= " AND client_name LIKE '$name%'";
		} 
        //echo searchReferences($user_id, $page,$where, $voc, $name);
		$query = "select * from user_people as a inner join " .
		" (SELECT user_id, sum(ranking) as rank FROM user_rating  group by user_id order by rank) as b " .
		"  on a.user_id=b.user_id where a.user_id='" .$this->session->id . "' $where  order by entrydate desc LIMIT  $offset, $limit ";
		$result = $this->db->query( $query );
		
		
		$query = "select *, 0 rank 
		from user_people 
		where user_id='" . $this->session->id . "' $where  order by entrydate desc LIMIT  $offset, $limit ";
		$result = $this->db->query( $query );
		
		$sql_query_count = "select count(*) as reccnt from user_people
		 where user_id  ='$member_id' $where  "; 	
		$result_count = $this->db->query($sql_query_count); 
		$num_rows =  $result_count->row()->reccnt ;	 		
		if ($result->num_rows() > 0) 
		{
			
			foreach($result->result() as $item)
			{
				$sql = "select sum(ranking) as rank 
				from user_rating 
				where user_id='" .  $item->id .  "' group by user_id order by rank ";
				$rank_row = $this->db->query($sql);
				if($rank_row->num_rows() > 0)
				{
					$item->rank  = $rank_row->row()->rank;
				}
			}
			 
			
			$jsonresult = array('error' =>  '0' , 'num_rows' =>  $num_rows,  'errmsg' =>  "Connections are fetched!" , 
			'results' =>  $result  );  
			return $jsonresult;   
        } 
    }
	return false; 
}
 
	function get_myknows_program($member_id, $offset,  $limit, $programid=1, $hasjoin = 'not in', $name='', $tags='' ) 
	{
		$ids = '';
		$where_id ='';
		$participants = $this->db->query("select clients_selected  from mc_program_client where client_id='$member_id'");
		if($participants->num_rows() > 0)
		{
			$participantsjson = $participants->row()->clients_selected;
			if($participantsjson !='')
			{
				$participantsid = json_decode($participantsjson, true);  
				$ids = implode(',', $participantsid);   
				$where_id =' and id ' . $hasjoin . ' ( ' . $ids . ' ) ';
			} 
		} 
		
		if($name !='')
		{
			$where_name = " and client_name like '$name%'" ;
		}
		else 
		{
			$where_name = '' ;
		}
		
		if($tags !='' && sizeof($tags)  >  0)
		{
			$where_tags = " and ( " ;
			for($i=0; $i < sizeof($tags) ; $i++) 
			{
				$where_tags .=  'find_in_set( "' .  $tags[$i]   .   '" ,  tags ) ';
				
				if( $i < sizeof($tags) - 1 )
				{
					$where_tags .= " or ";
				}
				
			}
			$where_tags .= " )  " ;  
		}
		else 
		{
			$where_tags = '' ;
		}
		
		
		if( $hasjoin == 0 )
		{
			$query = "select * from user_people as a inner join " .
			" (SELECT user_id, sum(ranking) as rank FROM user_rating  group by user_id order by rank) as b " .
			" on a.user_id=b.user_id where a.user_id='$member_id'  $where_id  $where_name $where_tags order by entrydate desc LIMIT  $offset, $limit ";
		}
		else 
		{
			$query = "select * from user_people as a inner join " .
			" (SELECT user_id, sum(ranking) as rank FROM user_rating  group by user_id order by rank) as b " .
			" on a.user_id=b.user_id where a.user_id='$member_id'   $where_id  $where_name  $where_tags order by entrydate desc LIMIT  $offset, $limit ";
		} 
		
		$result  = $this->db->query($query);  
		$sql_query_count = "select count(*) as reccnt from user_people 
		where user_id  ='$member_id'  $where_name $where_tags"; 	
		$result_count = $this->db->query($sql_query_count); 
		$num_rows =  $result_count->row()->reccnt ;			
		
		if ($result->num_rows() > 0) 
		{
			$jsonresult = array('error' =>  '0' , 'num_rows' =>  $num_rows,  'errmsg' =>  "Connections are fetched!" ,  
			'results' =>  $result  );   
			return $jsonresult;   
		}
		return false;
    }
	
	
	public function rank_updater($mid)
	{
		//fill the ranks  
		$rs_rankfiller =  $this->db->query("select * from  referralsuggestions where 
		knowenteredby = '$mid' and ( source_rank = '0' and  target_rank='0' )"); 
		if($rs_rankfiller->num_rows() > 0)
	    {
		   $rank_update_cnt=0;
		   foreach($rs_rankfiller->result() as $ref_row)
		   {
			   $refrowid = $ref_row->id;
			   $source = $ref_row->knowtorefer;
			   $target =  $ref_row->knowreferedto;
			   $trknowtorefer = $this->db->query("SELECT  sum( ranking) as totalscore from   user_rating where user_id in (" . $source  . ","  . $target . " )  group by user_id"); 
			   $trrowcount = $trknowtorefer->num_rows();
			   
			   if($trrowcount == 0)
				{ 
					$sourcerank = $targetrank =0;
				}
				else if($trrowcount == 1)
				{
					$rankrow = $trknowtorefer->row(0);
					$sourcerank = $rankrow->totalscore ;  
					$targetrank =0;
				}
				else if($trrowcount >= 2)
				{
					$rankrow = $trknowtorefer->row(0);
					$sourcerank = $rankrow->totalscore ;  
					$rankrow = $trknowtorefer->row(1);
					$targetrank = $rankrow->totalscore ;
				}
				
				$this->db->query("update  referralsuggestions set rank_calc='$targetrank', source_rank='$sourcerank', target_rank='$targetrank' where id='$refrowid' ");   
			
				$rank_update_cnt++;
				if($rank_update_cnt > 100)
				{
					break;
				}
			}
		} 
	}
	 
	
	public function get_duplicate_knows($data)
	{
		$mid = $data['mid'];
		$offset = $data['offset'];
		
		$q = $this->db->query("select * from user_people where user_id = '$mid' order by client_name asc limit $offset , 10");
		 
		if($q->num_rows() > 0)
		{
			foreach( $q->result()  as $row )
			{
				$id = $row->id;
				$client_name = $row->client_name;
				$client_profession = $row->client_profession;
				$client_phone = $row->client_phone;
				$client_email = $row->client_email;
				$client_location = $row->client_location;
				$user_group = $row->user_group  ;
				$userGrpName = '';
				$userVocName = '';
				
				$grpNameQ = $this->db->query("SELECT * FROM  groups  WHERE id='$user_group'");
				if($grpNameQ->num_rows() > 0)
				{ 
					$userGrpName = $grpNameQ->row()->grp_name ;
				}

				$vocNameQ = $this->db->query("SELECT * FROM `vocations` WHERE id = '$client_profession' ");
				if($vocNameQ->num_rows() > 0)
				{
					$userVocName = $vocNameQ->row()->voc_name ;
				} 
				$introduceeresult = $this->db->query( "select * from mc_user where id='". $mid . "'");		 
				if($introduceeresult->num_rows() > 0){
					$introducee = $introduceeresult->row_array();
					$introduceedetails = $introducee['username'] . "<br/>" . $introducee['user_email'];
				} 
				$rate_q = $this->db->query("select SUM(ranking ) user_ranking FROM user_rating WHERE `user_id` = '$id'");
				 $user_ranking = $rate_q->row_array()['user_ranking']; 
				
			}  
		}
		else 
		{
			return array('results' => '', 'page_count' => 0, 'err_msg' => 'No result found!', 'error' => 1);
		}
		
		return array('results' => $q, 'page_count' => 0, 'err_msg' => 'Duplicate know entries!', 'error' => 10); 
	} 
	
	//update tags
    function update_tags($tag, $email)
	{
		$this->db->select("*"); 
		$this->db->from("user_people");
		$this->db->where("client_email", $email); 
		$this->db->where( " !find_in_set('" . $tag. "' ,  tags ) " ); 
		$result = $this->db->get();
		return $result;
	} 
	function get_top_rated_knows($data)
	{
		$ranking = $data['ranking'];   
		$offset = $data['offset'];   
		$pagesize = 10 ;   
		$sql_query = " select a.*, b.* from user_people as a inner join " .
		" (select user_id, sum(ranking) as rate from user_rating group by user_id) as b on a.id=b.user_id " .
		" where b.rate = '$ranking' order by a.client_name asc limit $offset, $pagesize"  ; 
		$allknows = $this->db->query($sql_query); 
		$sql_query_count =  " select count(*) as reccnt from user_people as a inner join " .
		" (select user_id, sum(ranking) as rate from user_rating group by user_id) as b on a.id=b.user_id " .
		" where b.rate = '$ranking' order by a.client_name asc " ;
		$result_count = $this->db->query($sql_query_count); 
		$pages = $result_count->row()->reccnt ;
		$jsonresult = array('error' =>  '0' , 'num_rows' =>  $pages,   'errmsg' =>  "Top rated knows fetched successfully" , 
		'results' =>  $allknows  ); 
		return $jsonresult; 
	}
	
} 
?>