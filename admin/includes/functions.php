<?php
/**
 * Created by PhpStorm.
 * User: Frontend
 * Date: 3/16/2016
 * Time: 9:30 PM
 */

if(preg_match('/functions.php/', $_SERVER['REQUEST_URI'])){header('location: ./');}

include_once 'db.php';
include_once './mailer/PHPMailerAutoload.php';


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

function getHelpsButtons(){
    global $link;
    $help_q = $link->query("SELECT * FROM helpsbuttons order by id");
    $help_data_button = array();
    while($q_row = $help_q->fetch_array()){
        $help_data_button[] = ["id" => $q_row['id'], "helptitle" => $q_row['helptitle'], "helpvideo" => $q_row['helpvideo']];
    }
    return $help_data_button;
}

// Get All Questions
function getHelps()
{
    global $link;
    $help_q = $link->query("SELECT * FROM helps");
    $help_data = array();
    while($q_row = $help_q->fetch_array())
	{
		$help_data[] = ["id" => $q_row['id'], "helptitle" => $q_row['helptitle'], "helpvideo" => $q_row['helpvideo']];
    }
    return $help_data;
}

// Get All Questions
function getQues($link){
    global $link;
    $ques_q = $link->query("SELECT * FROM questions");
    $ques_data = array();
    while($q_row = $ques_q->fetch_array()){
        $ques_data[] = ["id" => $q_row['id'], "question" => $q_row['question'], "question_type" => $q_row['question_type']];
    }
    return $ques_data;
}
// Get All Testimonials
function getTestimonials(){
    global $link;
    $help_q = $link->query("select * from mc_testimonial order by printorder ");
    $help_data = array();
    while($q_row = $help_q->fetch_array()){
        $help_data[] = ["id" => $q_row['id'], "videolink" => $q_row['videolink'], "summary" => $q_row['summary']];
    }
    return $help_data;
}

// Get All Tags
function getTags(){
    global $link;
    $tags = $link->query("select * from mc_tags order by tagname");
    $all_tags = array();
    while($row = $tags->fetch_array()){
        $all_tags[] = ["id" => $row['id'], "tagname" => $row['tagname'] ];
    }
    return $all_tags;
}


// Get Al Groups
function getGroups($link){
    global $link;
    $ques_q = $link->query("SELECT * FROM `groups` where islisted='1' ORDER BY `grp_name` ");
    $ques_data = array();
    while($q_row = $ques_q->fetch_array()){
        $ques_data[] = ["id" => $q_row['id'], "name" => $q_row['grp_name']];
    }
    return $ques_data;
}

// Get All Cities
function getCities($link){
    global $link;
    $ques_q = $link->query(" select distinct client_location from user_people order by client_location");
    $ques_data = array();
    while($q_row = $ques_q->fetch_array()){
        $ques_data[] = [  "name" => $q_row['client_location']];
    }
    return $ques_data;
}

//get member profile 
function getUserProfile($id)
{
	global $pdo;
	 
	try
	{
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
		$sql_query = "select *,  0 AS group_names from user_details as a inner join mc_user as b on b.id = a.user_id 
		where user_id='$id' ";
			   
		
		$rst = $pdo->query($sql_query);
		if($rst->rowCount() > 0 )
		{
			$jsonresult = $rst->fetchAll(PDO::FETCH_ASSOC);
		
			if($jsonresult[0]['groups'] !='')
			{
				$sql_query_group =   "select group_concat(grp_name) as group_names FROM groups where id in  (" . $jsonresult[0]['groups'] . ") ";
				
							$groupresult = $pdo->query($sql_query_group);
							if($groupresult->rowCount() > 0 )
							$groupnames= $groupresult->fetchAll(PDO::FETCH_ASSOC)[0]['group_names'];
							$jsonresult[0]['group_names'] =  str_replace( ",", ", ", $groupnames) ;
			}
			else 
			{
				$jsonresult[0]['group_names'] = 'Not specified' ;
			}
		}
		else
		{
			$sql_query = "select  id ,  user_email ,  user_pass ,  username, user_role, user_pkg, user_phone, " .
			" image, createdOn, user_status, group_status, resPWToken, resPWExp, publicprofile, " .
			" profileisvisible, tags, signup_type, busi_name, user_type, busi_location_street, busi_location, " .
			" busi_type, busi_hours, busi_website , " .
			"  0 AS current_company, 0 AS linkedin_profile,0 AS street, 'No Specified' AS city, 0 AS zip, 0 AS country, 0 AS groups, " .
			" 'No Specified' AS target_clients,  'No Specified' AS target_referral_partners, 'No Specified' AS vocations, 0 AS about_your_self, " .
			" 0 AS upd_public_private, 0 AS upd_reminder_email,  0 AS lcid,  0 AS group_names " .
			" from  mc_user  where id='$id' ";
			
			$rst = $pdo->query($sql_query);
			if($rst->rowCount() > 0 )
			{
				$jsonresult= $rst->fetchAll(PDO::FETCH_ASSOC); 
				if($jsonresult[0]['groups'] !='')
				{
					$sql_query_group =   "select group_concat(grp_name) as group_names FROM groups where id in  (" . $jsonresult[0]['groups'] . ") ";
					
								$groupresult = $pdo->query($sql_query_group);
								if($groupresult->rowCount() > 0 )
								$groupnames= $groupresult->fetchAll(PDO::FETCH_ASSOC)[0]['group_names'];
								$jsonresult[0]['group_names'] =  str_replace( ",", ", ", $groupnames) ;
				}
				else 
				{
					$jsonresult[0]['group_names'] = 'Not specified' ;
				}
			}
			else 
			{
				$jsonresult = array('error' =>  '10' ,  'errmsg' =>  'No member found!' );
			} 
		} 	
	}
	catch(PDOException $e)
	{
		$jsonresult = array('error' =>  '1' ,  'errmsg' =>  $e->getMessage() ); 
	}  
	return $jsonresult;  
}


//api
// Get User
function getUser($user_id)
{
	global $link;
    $q = $link->query("SELECT user_email, username FROM mc_user WHERE id = '$user_id'");
    $row = $q->fetch_array();
    return $row;
}

function getNotes($userid, $goto)
{
	global $pdo;
	
	  
	
	$pagesize = 1 ; 
	$start = ($goto-1)* $pagesize ;
	
	
		try
		{
			 
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$sql_query = "select * from mc_statements   order by id desc limit $start, $pagesize ";
			
			$sql_query_count = "select count(*) as reccnt  from mc_statements " ; 
			   
			 
			$rst = $pdo->query($sql_query);  
			$notes = $rst->fetchAll(PDO::FETCH_ASSOC); 
			$rst_count = $pdo->query($sql_query_count);
			$result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);
			$pages = ceil($result_count[0]['reccnt']/10);
			$jsonresult = array('error' =>  '0' , 'pages' =>  $pages,  'errmsg' =>  'Notes retrieved!', 'results' => $notes  ); 
			  	    
		}
		catch(PDOException $e)
		{
			$jsonresult = array( 'error' =>  '1' ,  'errmsg' =>  'Something went wrong. Please retry!'  ); 
		} 
 
	return $jsonresult;
}

//Search member by name as key
function getMemberKnows($userid)
{
 	global $pdo; 
	$sql_query =  "SELECT * FROM user_people where user_id = '$userid'  ORDER by client_name" ;
	 
	try
	{
		 
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
		$rst = $pdo->query($sql_query); 
		$jsonresult = array(); 
		  
		if($rst->rowCount() > 0 )
		{
			foreach ($rst as $row) 
			{
				$jsonresult[] = array('id' =>  $row['id']  ,  'username' =>  $row['client_name']   ); 
			} 
		} 
		else
			$jsonresult = array('error' =>  '10' ,  'errmsg' =>  'No member found!' ); 
	}
	catch(PDOException $e)
	{
		$jsonresult = array('error' =>  '1' ,  'errmsg' =>  $e->getMessage() ); 
	} 
	 
	return  $jsonresult; 
}

//get all members
function getAutoSuggestedMembers($userid, $goto, $vocations='', $name='', $city='' )
{
 	global $pdo;   
	  
	
	if($name !== NULL && $name != '')
	{
		$name_where  =" and username like '%$name%' ";  
	} 

	if($city !== NULL && $city != '' && $city != 'null')
	{
		 
		$city_where =' and (';
		$citylist = explode(',', $city);

		for($i=0; $i < sizeof($citylist); $i++)
		{
			$city_where .= " city = '" .  $citylist[$i] . "' ";
			if($i < sizeof($citylist) -1)
			{
				$city_where .= ' or ';
			}
		} 
		$city_where .=' ) ';
 
	} 
 
	if($vocations !== NULL && $vocations != '' && $vocations != 'null')
	{
		$voc_where =' and (';
		$vocationlist = explode(',', $vocations);

		for($i=0; $i < sizeof($vocationlist); $i++)
		{
			$voc_where .= " find_in_set ( '" .  $vocationlist[$i] . "' , vocations) ";
			if($i < sizeof($vocationlist) -1)
			{
				$voc_where .= ' OR ';
			}
		} 
		$voc_where .=' )';
	}  

	$ds = DIRECTORY_SEPARATOR;  
	$imagepath =  $_SERVER['DOCUMENT_ROOT'] . $ds  ."images/"   ;
	
	try
	{
		 
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql_query =    "select groups from user_details where user_id='$userid' " ;
		$rst = $pdo->query($sql_query);
		if($rst->rowCount() > 0 )
		{
			$groupslist= $rst->fetchAll(PDO::FETCH_ASSOC)[0]['groups'];
			$groupids = explode(',', $groupslist);
			$jsonresult = array('error' =>  '10' ,  'errmsg' =>  'Group IDs found!', 'groupids' => $groupslist );
			
			$where  ='';
			for($i=0; $i < sizeof($groupids); $i++)
			{
				$where .= "FIND_IN_SET ( '" . $groupids[$i]  . "', groups) ";
				if($i < sizeof($groupids) -1)
				{
					$where .= ' OR ';
				}
			} 
			if( $where !='')
			{
				$sql_query_count = "select count(*) as reccnt from mc_user as u inner join user_details as d on u.id=d.user_id 
				where u.id !='$userid' and u.id !='1' and (" . $where . ")"  .  $voc_where .  $name_where . $city_where  ;
				$rst_count = $pdo->query($sql_query_count); 
				$result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);   
				$pages = ceil($result_count[0]['reccnt']/10);  
				
				if($goto == 0)
				{ 
					$goto = mt_rand(1, $pages);$pagesize = 11 ; 
				}
				else 
				{  
					$pagesize = 10 ; 
				} 
				$start = ($goto-1)* $pagesize ;
				$sql_query = "select u.id as id, user_email ,  username ,  user_phone ,  publicprofile ,  profileisvisible , image,  current_company ,  linkedin_profile ,  city ,  zip ,  country ,  groups ,  target_clients ,  target_referral_partners ,  vocations ,  about_your_self  from mc_user as u inner join user_details as d on u.id=d.user_id 
				where u.id !='$userid' and u.id !='1'  and  (" . $where . ") " .  $voc_where  .  $name_where . $city_where   ;
				$rst = $pdo->query($sql_query); 

				$allmembers = $rst->fetchAll(PDO::FETCH_ASSOC);
				for($i=0; $i < $rst->rowCount(); $i++ )
				{
					$sp =  $allmembers[$i][id]; 
					$query_connect_check =  " select * from mc_member_connections where  status='1' and (  ( firstpartner='$userid' and secondpartner='$sp')  or ( firstpartner='$sp' and secondpartner='$userid') ) "  ; 
				
					$rstconcheck = $pdo->query($query_connect_check); 
					if($rstconcheck->rowCount() > 0 )
					{
						$allmembers[$i][isconnected]  = '1'; 
					}
					
					if( $allmembers[$i]['image'] !== NULL && $allmembers[$i]['image'] != '' && $allmembers[$i]['image'] != 'null')
					{
						$user_picture =  (  file_exists( $imagepath. $allmembers[$i]['image']  )?   $allmembers[$i]['image'] :  "no-photo.png" ) ; 
						$allmembers[$i]['image'] =$user_picture; 
					}
					else 
					{
						$allmembers[$i]['image'] =  "no-photo.png";
					}
				} 
				 
				usort($allmembers, function($a, $b) 
				{
					return $b['isconnected'] - $a['isconnected'];
				});
				$final = array();
				for($i=0; $i < 10 ; $i++ )
				{
					$final[] = $allmembers[$start+$i];
				}
				
				$jsonresult = array('error' =>  '0' , 'path'=>$user_picture, 'pages' => $pages ,  'errmsg' =>  'Members you may be interested to contact found!', 'results' =>   $final ); 
			}
			else 
				$jsonresult = array('error' =>  '10' ,  'errmsg' =>  'No member suggestion found!' ); 
		}
		else
			$jsonresult = array('error' =>  '10' ,  'errmsg' =>  'No member suggestion found!' );  
	}
	catch(PDOException $e)
	{
		$jsonresult = array('error' =>  '1' ,  'errmsg' =>  "Something went wrong. Please try again!" ); 
	} 
	 
	return $jsonresult;
}


function getRecentUpdatedKnows()
{
	global $pdo;
	
	try
	{
		 
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
		$sql_query =  "select * from mc_know_update_log as a inner join user_people as b on a.know_id=b.id order by a.entrydate desc limit  10" ;  
		$rst = $pdo->query($sql_query); 
		$jsonresult = array('error' =>  '0' ,  'errmsg' =>  'Search log save!', 'results' =>  $rst->fetchAll(PDO::FETCH_ASSOC)  ); 
	}
	catch(PDOException $e)
	{
		$jsonresult = array('error' =>  '1' ,  'errmsg' =>  'Search log could not be save!' ); 
	}  
	return $jsonresult;      
}	


// Get All Vocations
function getVocations($link)
{
    global $link;
    $q = $link->query("SELECT * FROM `vocations` ORDER BY `voc_name` ");
    while($q_row = $q->fetch_array())
	{
		$data[] = ["id" => $q_row['id'], "name" => $q_row['voc_name']];
    }
    return $data;
}
// Get All Lifestyles
function getLifestyles($link)
{
    global $link;
    $q = $link->query("SELECT * FROM lifestyles  ORDER BY  ls_name  ");
    while($q_row = $q->fetch_array())
	{
		$data[] = ["id" => $q_row['id'], "name" => $q_row['ls_name']];
    }
    return $data;
}

// Get All Lifestyles
function getAllCities($link)
{
    global $link;
    $q = $link->query("select distinct client_location from user_people  ");
    while($q_row = $q->fetch_array())
	{
		$data[] = [ "name" => $q_row['client_location']];
    }
    return $data;
}


// get user suggested papol
function getSuggested($user_id)
{
	global $link;
	$final = array();
	$html = "";
	$user = $link->query("SELECT * FROM user_details WHERE user_id = '$user_id'");
	$user = $user->fetch_array();
	$groups = explode(",", $user['groups']);
	$target_clients = explode(",", $user['target_clients']);
	
	if(empty($user['target_clients']))
	{
		$html = "No results found";
	}
	else
	{
		$whereGroup = "(FIND_IN_SET('".implode("', `groups`) OR FIND_IN_SET('", $groups)."', `groups`))";
		$whereTargetClient = "(FIND_IN_SET('".implode("', `vocations`) OR FIND_IN_SET('", $target_clients)."', `vocations`))"; 
		$suggested = $link->query('SELECT user_id, vocations FROM user_details WHERE ' . $whereGroup . ' AND ' . $whereTargetClient); 
        $ids = ""; 
        if($suggested->num_rows > 0) {
			while($row = $suggested->fetch_array()) {
				$id = $row['user_id'];
				$ids .= $id . ",";
				$final[$id]['vocations'] = $row['vocations'];
			}
		} 
        $ids = rtrim($ids, ",");
        $suggested = $link->query('SELECT * FROM mc_user WHERE `id` IN (' . $ids . ')');

        if($suggested->num_rows > 0)
		{
			while($row = $suggested->fetch_array())
			{
				$id = $row['id'];
				$final[$id]['username'] = $row['username'];
				$final[$id]['email'] = $row['user_email'];
				$final[$id]['phone'] = $row['user_phone'];
			}
		}



        foreach(explode(",", $ids) as $item)
        {
			$str = "abcdefghijklmnopqrstuvwxyz";
			$rand = substr(str_shuffle($str),0,3);
			$html .= '<tr id="$rand-$item">
                    <td>' . $final[$item]["username"] . '</td>
                    <td>' . $final[$item]["email"] . '</td>
                    <td>' . $final[$item]["phone"] . '</td>
                    <td>' . $final[$item]["vocations"] . '</td>
					<td></td>
                </tr>';
		}

	}

    echo $html;
}


// get user suggested partners papol
function getSuggestedPartners($user_id)
{
    global $link;
	$final = array();
	$html = "";
	$user = $link->query("SELECT * FROM user_details WHERE user_id = '$user_id'");
	$user = $user->fetch_array();
	$groups = explode(",", $user['groups']);
	$target_referral_partners = explode(",", $user['target_referral_partners']);
	
	if(empty($user['target_referral_partners']))
    {
		$html = "No results found";
	}
    else
    {
		$whereGroup = "(FIND_IN_SET('".implode("', `groups`) OR FIND_IN_SET('", $groups)."', `groups`))";
		$whereTargetClient = "(FIND_IN_SET('".implode("', `vocations`) OR FIND_IN_SET('", $target_referral_partners)."', `vocations`))";
		
		$suggested = $link->query('SELECT user_id, vocations FROM user_details WHERE ' . $whereGroup . ' AND ' . $whereTargetClient);
		
		
		$ids = "";
		
		if($suggested->num_rows > 0) {
			while($row = $suggested->fetch_array()) {
				$id = $row['user_id'];
				$ids .= $id . ",";
				$final[$id]['vocations'] = $row['vocations'];
			}
		}
		
		$ids = rtrim($ids, ",");
		
		$suggested = $link->query('SELECT * FROM mc_user WHERE `id` IN (' . $ids . ')');
		
		if($suggested->num_rows > 0) {
			while($row = $suggested->fetch_array()) {
				$id = $row['id'];
				$final[$id]['username'] = $row['username'];
				$final[$id]['email'] = $row['user_email'];
				$final[$id]['phone'] = $row['user_phone'];
			}
		}
		
		foreach(explode(",", $ids) as $item) {
			$str = "abcdefghijklmnopqrstuvwxyz";
			$rand = substr(str_shuffle($str),0,3);
			$html .= '<tr id="$rand-$item">
			<td>'.$final[$item]["username"].'</td>
			<td>'.$final[$item]["email"].'</td>
			<td>'.$final[$item]["phone"].'</td>
					<td>'.$final[$item]["vocations"].'</td>
					<td>
						
					</td>
				</tr>';
		}
	} 
	echo $html;
}

// Get User References
function getReferences($user_id, $goto,$where=""){

    $start = ($goto-1)*10;

    global $link;
    $q = $link->query("SELECT * FROM user_people WHERE user_id = '$user_id' ".$where." ORDER BY client_name ASC LIMIT $start,10");
    $html = "No records found!";
    if($q->num_rows > 0){
        $pg = $link->query("SELECT id FROM user_people WHERE user_id = '$user_id' ".$where);
        $pages = ceil($pg->num_rows/10);

        $html = "";
        while($row = $q->fetch_array()){
            $id = $row['id'];
            $client_name = $row['client_name'];
            $client_profession = $row['client_profession'];
            $client_phone = $row['client_phone'];
            $client_email = $row['client_email'];
            $client_location = $row['client_location'];
            $user_group = $row['user_group'];
            $userGrpName = '';
            $userVocName = '';

            $grpNameQ = $link->query("SELECT * FROM `groups` WHERE id='$user_group'");
            if($grpNameQ->num_rows > 0){
                $grpNameFet = $grpNameQ->fetch_assoc();
                $userGrpName = $grpNameFet['grp_name'];
            }

            $vocNameQ = $link->query("SELECT * FROM `vocations` WHERE id = '$client_profession' ");
            if($vocNameQ->num_rows > 0){
                $vocNameFet = $vocNameQ->fetch_assoc();
                $userVocName = $vocNameFet['voc_name'];
            }
			
			$introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");		 
			if($introduceeresult->num_rows > 0){
				$introducee = $introduceeresult->fetch_array();
				$introduceedetails = $introducee['username'] . "<br/>" . $introducee['user_email'];
			}
			
            $rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$id'");
            $rate_row = $rate_q->fetch_array();
            $user_ranking = $rate_row['user_ranking'];

            $str = "abcdefghijklmnopqrstuvwxyz";
            $rand = substr(str_shuffle($str),0,3);

            $html .= "<tr id='$rand-$id'>
                <td>$client_name</td>
                <td>$client_profession</td>
                <td>$client_phone</td>
                <td>$client_email</td>
                <td>$client_location</td>
                <td>$userGrpName</td>
                <td>$user_ranking</td>
                <td  > 
					 <div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i></a>
						<ul class='dropdown-menu'>     
							<li class=close_drop'><a data-toggle='modal' data-target='#edit_people_details' class='editPeopleDetails'><i class='fa fa-pencil'></i> Edit Details</a></li>  
							<li><a class='btnselecttrigger' data-rpt='" . $id  . "' 
							data-rname='" . $client_name  . "'  data-introducee='".$introducee['username']."'  data-remid='" . $client_email  . "'  data-phone='".$introducee['user_phone']."'  ><i class='fa fa-envelope'></i> Message</a></li>  
							<li><a class='delUserClient' data-id='$id'><i class='fa fa-times-circle'></i> Remove Know</a></li>  
							<li><a class='view_comm_vocation' data-user='$id' ><i class='fa fa-link' data-toggle='tooltip' title='Common Vocations'></i> Check Vocations</a>
							</li>     
						 </ul>
				 </div>
                </td>
            </tr>";
        } 
		
		
		 $lastpage = $pages ;
		 $prev = $goto == 1 ? 1 : $goto-1;
		 $next = $goto == $pages ? $pages : $goto+1; 
		 $html .=  "<tr><td colspan='8'><ul class='pagination pagiAd'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
		 if( $goto > 10) 
		 $html .=  "<li><a  data-func='next' title='Show last few pages' data-pg='1'> ... </a></li>";
	 
		 if($goto < 10)
		 { 
			 for($j= 1 ; $j  <=  10  ; $j++)
			 {
				 if($j > $pages)
				 {
					 break;
				 }
				
				 $active = $j == $goto ? 'active' : '';
				 $html .=  "<li class='$active'><a  data-pg='$j'>$j</a></li>";
			 }
		 }
		 else
		 {
			for($i= $goto - 5; $i<= $goto + 4; $i++)
			{
				if($i > $pages)
				{
					 break;
				}
				$active = $i == $goto ? 'active' : '';
			    $html .=  "<li class='$active'><a data-pg='$i'>$i</a></li>";
			 }
		 }
	 	if( $goto < ($lastpage - 10 ) )
         $html .=  "<li><a   data-func='next' title='Show last few pages' data-pg='$lastpage'> ... </a></li>";
         $html .= "<li> <input type='text' id='knowlistgotopageno' style='width: 120px; height: 32px; margin-top: 2px; margin-right: 5px; float: left; display: inline-block;' class= 'form-control' placeholder= 'Go to page ...' > </li>";
         $html .= "<li> <input type='button' id='knowlistgopage' value='Go' style='width: 50px; float: left; height: 32px; margin-top: 2px; display: inline-block;  background-color: #2e353d; color: #fff;' class= 'btn '  > </li>";
         $html .=  "<li><a  data-func='next' title='Next Page' data-pg='$next'>»</a></li></ul></td></tr>"; 
    }
    echo $html;
}


// Get User References
function searchReferences($user_id, $goto,$where="", $voc, $name)
{ 
    $start = ($goto-1)*10; 
    global $link;
	
    $q = $link->query("SELECT * FROM user_people WHERE user_id = '$user_id' ".$where." ORDER BY client_name ASC LIMIT $start,10");
    $html = "No records found!";
    if($q->num_rows > 0){
        $pg = $link->query("SELECT id FROM user_people WHERE user_id = '$user_id' ".$where);
        $pages = ceil($pg->num_rows/10);

        $html = "";
        while($row = $q->fetch_array())
		{
			$id = $row['id'];
            $client_name = $row['client_name'];
            $client_profession = $row['client_profession'];
            $client_phone = $row['client_phone'];
            $client_email = $row['client_email'];
            $client_location = $row['client_location'];
            $user_group = $row['user_group'];
            $userGrpName = '';
            $userVocName = '';

            $grpNameQ = $link->query("SELECT * FROM `groups` WHERE id='$user_group'");
            if($grpNameQ->num_rows > 0){
                $grpNameFet = $grpNameQ->fetch_assoc();
                $userGrpName = $grpNameFet['grp_name'];
            }

            $vocNameQ = $link->query("SELECT * FROM `vocations` WHERE id = '$client_profession' ");
            if($vocNameQ->num_rows > 0){
                $vocNameFet = $vocNameQ->fetch_assoc();
                $userVocName = $vocNameFet['voc_name'];
            }
            
            $introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");       
            if($introduceeresult->num_rows > 0){
                $introducee = $introduceeresult->fetch_array();
                $introduceedetails = $introducee['username'] . "<br/>" . $introducee['user_email'];
            }
            
            $rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$id'");
            $rate_row = $rate_q->fetch_array();
            $user_ranking = $rate_row['user_ranking']; 
			
            $str = "abcdefghijklmnopqrstuvwxyz";
            $rand = substr(str_shuffle($str),0,3);
			
			$html .= "<tr id='$rand-$id'>
                <td>$client_name</td>
                <td>$client_profession</td>
                <td>$client_phone</td>
                <td>$client_email</td>
                <td>$client_location</td>
                <td>$userGrpName</td>
                <td>$user_ranking</td>
               <td  > 
					 <div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i></a>
						<ul class='dropdown-menu'>     
							<li class=close_drop'><a data-toggle='modal' data-target='#edit_people_details' class='editPeopleDetails'><i class='fa fa-pencil'></i> Edit Details</a></li>  
							<li><a class='btnselecttrigger' data-rpt='" . $id  . "' 
							data-rname='" . $client_name  . "'  data-introducee='".$introducee['username']."'  data-remid='" . $client_email  . "'  data-phone='".$introducee['user_phone']."'  ><i class='fa fa-envelope'></i> Message</a></li>  
							<li><a class='delUserClient' data-id='$id'><i class='fa fa-times-circle'></i> Remove Know</a></li>  
							<li><a class='view_comm_vocation' data-user='$id' ><i class='fa fa-link' data-toggle='tooltip' title='Common Vocations'></i> Check Vocations</a>
							</li>     
						 </ul>
				 </div>
                </td>
            </tr>";
        }
		
        $prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1;
		
		$html .= "<tr><td colspan='8'><ul class='pagination gorefsearch'><li><a data-func='prev' data-voc='$voc' data-name='$name' data-pg='$prev'>«</a></li>";
        for($i=1; $i<=$pages; $i++){
            $active = $i == $goto ? 'active' : '';
            $html .= "<li class='$active'><a data-voc='$voc' data-name='$name' data-pg='$i'>$i</a></li>";
        }
        
		$html .= "<li><a data-func='next' data-voc='$voc' data-name='$name' data-pg='$next'>»</a></li></ul></td></tr>";
    }
	
    echo $html;
}


//converted
// Get Search Logs
function getSearchLogs($goto){

    $start = ($goto-1)*10;

    global $link;
    $q = $link->query("SELECT vsl.*,mu.username FROM `vocation_search_logs` vsl INNER JOIN `mc_user` mu on vsl.user_id=mu.id ORDER BY created_at DESC LIMIT $start,10");
    $html = "No records found!";
    if($q->num_rows > 0){
        $pg = $link->query("SELECT code FROM `vocation_search_logs`");
        $pages = ceil($pg->num_rows/10);

        $html = "";
        while($row = $q->fetch_array()){
            $client_name = $row['username'];
            $client_profession = $row['vocation'];
            $client_phone = $row['location'];
            $client_email = date("m-d-Y H:i:s",strtotime($row['created_at']));

            $html .= "<tr id='$rand-$id'>
                <td>".$client_name."</td>
                <td>".$client_profession."</td>
                <td>".$client_phone."</td>
                <td>".$client_email."</td>
            </tr>";
        }
        $prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1;

        $html .= "<tr><td colspan='8'><ul class='pagination pageslog'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
        for($i=1; $i<=$pages; $i++){
            $active = $i == $goto ? 'active' : '';
            $html .= "<li class='$active'><a data-pg='$i'>$i</a></li>";
        }
        $html .= "<li><a data-func='next' data-pg='$next'>»</a></li></ul></td></tr>";
    }
    echo $html;
}


// Get Search Logs
function getHomeSearchLogs($goto){

    $start = ($goto-1)*10;

    global $link;
    $q = $link->query("SELECT * FROM `home_search_log` ORDER BY created_at DESC LIMIT $start,10");
    $html = "No records found!";
    if($q->num_rows > 0){
        $pg = $link->query("SELECT city FROM `home_search_log`");
        $pages = ceil($pg->num_rows/10);

        $html = "";
        while($row = $q->fetch_array()){
            $client_name = $row['city'];
            $client_profession = $row['zip'];
            $client_phone = $row['vocation'];
            $client_email = date("m-d-Y H:i:s",strtotime($row['created_at']));

            $html .= "<tr id='$rand-$id'>
                <td>".$client_name."</td>
                <td>".$client_profession."</td>
                <td>".$client_phone."</td>
                <td>".$client_email."</td>
            </tr>";
        }
        $prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1;

        $html .= "<tr><td colspan='8'><ul class='pagination pageslog'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
        for($i=1; $i<=$pages; $i++){
            $active = $i == $goto ? 'active' : '';
            $html .= "<li class='$active'><a data-pg='$i'>$i</a></li>";
        }
        $html .= "<li><a data-func='next' data-pg='$next'>»</a></li></ul></td></tr>";
    }
    echo $html;
}

// View User References
function viewReferences($user_id, $name='', $voc='', $ema='', $loc='', $goto){

    $start = ($goto-1)*10;

    global $link;
    $qry = "SELECT * FROM user_people WHERE user_id = '$user_id'";
	
	if(!empty($name)) {
		$name = $link->real_escape_string($name);
		$qry .= " AND client_name LIKE '".$name."%'"; 
	}
	if(!empty($voc)) {
		$voc = $link->real_escape_string($voc);
		$qry .= " AND client_profession LIKE '".$voc."%'"; 
	}
	if(!empty($ema)) {
		$ema = $link->real_escape_string($ema);
		$qry .= " AND client_email LIKE '".$ema."%'"; 
	}
	if(!empty($loc)) {
		$loc = $link->real_escape_string($loc);
		$qry .= " AND client_location LIKE '".$loc."%'"; 
	}
	
	$qry .= " ORDER BY client_name ASC LIMIT $start,10";
	
    $html = "No records found!";
	$q = $link->query($qry);
    if($q->num_rows > 0){
		$qry1 = "SELECT id FROM user_people WHERE user_id = '$user_id'";
		if(!empty($name)) {
		$name = $link->real_escape_string($name);
		$qry1 .= " AND client_name LIKE '".$name."%'"; 
		}
		if(!empty($voc)) {
			$voc = $link->real_escape_string($voc);
			$qry1 .= " AND client_profession LIKE '".$voc."%'"; 
		}
		if(!empty($ema)) {
			$ema = $link->real_escape_string($ema);
			$qry1 .= " AND client_email LIKE '".$ema."%'"; 
		}
		if(!empty($loc)) {
			$loc = $link->real_escape_string($loc);
			$qry1 .= " AND client_location LIKE '".$loc."%'"; 
		}
        $pg = $link->query($qry1);
        $pages = ceil($pg->num_rows/10);

        $html = "";
		$html .= "<input type='hidden' class='form-control' id='viewuseridi' name='viewuseridi' value=".$user_id."><div class='msearchdiv'><div class='col-xs-2'>
						<input type='text' style='width:150px;' placeholder='Search By Name' class='form-control' id='searchnam' value=".$name.">
					</div><div class='col-xs-2'>
						<input type='text' style='width:160px;' placeholder='Search By Vocation' class='form-control' id='searchvoc' value=".$voc.">
					</div><div class='col-xs-2'>
						<input type='text' style='width:150px;margin-left:10px;' placeholder='Search By Email' class='form-control' id='searchema' value=".$ema.">
					</div><div class='col-xs-2'>
						<input type='text' style='width:160px;margin-left:10px;' placeholder='Search By Location' class='form-control' id='searchloc' value=".$loc.">
					</div><div class='col-xs-1'><button style='width:80px;' data-user=".$user_id." class='btnblock viewUser'>Search</button></div><div class='col-xs-1'><button style='width:80px;' data-user=".$user_id." class='btnblock resetUser'>Reset</button></div></div><table class='table'>
                       <thead>
                            <tr>
                                <th>Reference Name (Knows to her/him)</th>
                                <th>Vocation</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Location</th>
                                <th>Group</th>
                                <th>Ratings</th>
                            </tr>
                            </thead><tbody>";
        while($row = $q->fetch_array()){
            $id = $row['id'];
            $client_name = $row['client_name'];
            $client_profession = $row['client_profession'];
            $client_phone = ($row['client_phone'] !='' ? $row['client_phone'] : 'not specified');
            $client_email = $row['client_email'];
            $client_location = ($row['client_location'] !='' ? $row['client_location'] : 'not specified'); 
            $user_group =  $row['user_group']  ;  
            $userGrpName = '';
            $userVocName = '';


            $grpNameQ = $link->query("SELECT * FROM `groups` WHERE id='$user_group'");
            if($grpNameQ->num_rows > 0){
                $grpNameFet = $grpNameQ->fetch_assoc();
                $userGrpName = $grpNameFet['grp_name'];
            } else 
            {
                $userGrpName = "Not Specified";
            }


            $vocNameQ = $link->query("SELECT * FROM `vocations` WHERE id='$client_profession' ");
            if($vocNameQ->num_rows > 0){
                $vocNameFet = $vocNameQ->fetch_assoc();
                $userVocName = $vocNameFet['voc_name'];
            }

            $rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$id'");
            if($rate_q->num_rows > 0){
                $rate_row = $rate_q->fetch_array(); 
                $user_ranking = is_null($rate_row['user_ranking']) ? '0' : $rate_row['user_ranking'] ; 
            }
            else 
            {
                $user_ranking = 0;
            }
            $str = "abcdefghijklmnopqrstuvwxyz";
            $rand = substr(str_shuffle($str),0,3);

            $html .= "<tr id='$rand-$id'>
                <td><a data-toggle='modal' data-target='#edit_people_details' class='editPeopleDetails'>$client_name</a></td>
                <td>$client_profession</td>
                <td>$client_phone</td>
                <td>$client_email</td>
                <td>$client_location</td>
                <td>$userGrpName</td>
                <td>$user_ranking</td>
            </tr>";
        }
        $prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1;

        $html .= "<tr><td colspan='8'><ul class='pagination paginationU'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
        for($i=1; $i<=$pages; $i++){
            $active = $i == $goto ? 'active' : '';
            $html .= "<li class='$active'><a data-pg='$i'>$i</a></li>";
        }
        $html .= "<li><a data-func='next' data-pg='$next'>»</a></li></ul></td></tr>";
    }
	$html .= "</tbody></table>";
    echo $html;
}

 
// Get Clients Details
function getMyCityUsers($goto,$where="" ){

    $start = ($goto-1)*10; 
    global $link; 
    $q = $link->query("SELECT mc_user.*, (SELECT grp_name FROM groups WHERE id = (SELECT user_details.groups FROM user_details WHERE user_id = mc_user.id limit 1)) user_group,
                      (SELECT vocations FROM user_details WHERE user_details.user_id = mc_user.id limit 1) client_profession,
                      (SELECT city FROM user_details WHERE user_details.user_id = mc_user.id limit 1) client_location,
					  (SELECT COUNT(user_people.id) FROM user_people WHERE user_people.user_id = mc_user.id limit 1) user_ref  FROM mc_user WHERE user_role <> 'admin' HAVING 1 ".$where."  ORDER BY username LIMIT $start,10");
 
    $html = "No records found!";
    if($q->num_rows > 0){

        $sel_pkg = $link->query("SELECT `id`, `package_title`, `pkg_status` FROM `packages`");
        $html_pkg = "<li>No Package!</li>";
        if($sel_pkg->num_rows > 0){
            $html_pkg = "";
            while($pkg_row = $sel_pkg->fetch_array()){
                $pkg_id = $pkg_row['id'];
                $pkg_name = $pkg_row['package_title'];
                $pkg_sts = $pkg_row['pkg_status'];
                $status = $pkg_sts == 'activate' ? '' : 'disabled';
                $html_pkg .= "<li class='selUserPkg $status'><a data-id='$pkg_id'>$pkg_name</a></li>";
            }
        }

		
        $pg = $link->query("SELECT * FROM mc_user WHERE user_role <> 'admin' "  );
        $pages = ceil($pg->num_rows/10);

        $html = "";
        $i = 0;
        while($row = $q->fetch_array()){
            $id = $row['id'];
            $client_name = $row['username'];
            $client_profession = $row['client_profession'];
            $client_phone = $row['user_phone'];
            $client_email = $row['user_email'];
            $client_location = $row['client_location'];
            $user_group = $row['user_group'];
            $user_pkg = $row['user_pkg'];
            $user_ref = $row['user_ref'];
            $createdOn = $row['createdOn'];
            $user_status = $row['user_status'];
			$path = $row['image'];

            $tr = "";
            $ico = "fa-eye";
            $sts = "deactivate";
            if($user_status == 0){
                $tr = "danger";
                $ico = "fa-eye-slash";
                $sts = "activate";
            	$menu_text  = "Activate User";
            }
			else 
			{
				$menu_text  = "Disable User";
			}
			

            $html .= "<tr class='$tr' data-id='$id'>
                <td>$client_name</td>
                <td>$client_profession</td>
                <td>$client_phone</td>
                <td>$client_email</td>
                <td>$client_location</td>
                <td>$user_group</td>
                <td>
                $user_pkg
                <div class='dropdown'>
                <i class='fa fa-money dropdown-toggle' type='button' data-toggle='dropdown'><span class='caret' title='Change package' data-toggle='tooltip'></span></i>
                <ul class='dropdown-menu'>
                $html_pkg
                </ul>
                </div>
                </td>
                <td>$user_ref</td>
                <td>$createdOn</td>
                <td> 
				<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i></a>
						 <ul class='dropdown-menu'>   
						    
					<li class=close_drop'><a class='changeAccSett' data-id='$id' data-toggle='modal' data-target='#changeAccSett'>
                        <i class='fa fa-user' data-toggle='tooltip' title='View client'></i> View client
						</a></li>  
					<li><a class='changeProfilePhoto' data-id='$id'   data-path='$path' >
							<i class='fa fa-file-photo-o' data-toggle='tooltip' title='Upload Profile Photo'></i> Upload Photo
						</a></li>  
					<li><a class='viewUser' data-user='$id' data-toggle='modal' data-target='#userModal'>
							<i class='fa fa-users' data-toggle='tooltip' title='View references'></i> View references
						</a></li>  
					<li><a class='importmemberknows' data-user='$id'><i class='fa fa-cloud-upload' data-toggle='tooltip' title='Import Knows from CSV'></i> Import Knows</a></li>  
					<li><a class='composememberemail' data-toggle='tab'  data-target='#menu57' data-cn='$client_name' data-ce='$client_email' data-i='$id'><i class='fa fa-envelope' data-toggle='tooltip' title='Compose Email'></i> Compose Email</a></li>  
					<li><a class='switchuser' data-user='$id'><i class='fa fa-exchange' data-toggle='tooltip' title='Switch to Member Account'></i> Switch to Member Account</a>
					</li>  
					<li><a class='showreferrals' data-pagesize='10' data-pageno='1' data-user='$id' ><i class='fa fa-link' data-toggle='tooltip' title='Introduction/Referral'></i> Introduction/Referral</a>
					</li>   
					<li><a data-toggle='tab' href='#menu86' class='show_duplicate_referrals' data-pagesize='10' data-pageno='1' data-user='$id' ><i class='fa fa-link' data-toggle='tooltip' title='Check Duplicate Knows'></i> Check Duplicate Connections</a>
					</li>  
					<li><a class='ref_wizard_byadmin' data-refname='$client_name' data-refemail='$client_email' data-user='$id' data-role='$userrole'><i class='fa fa-link' data-toggle='tooltip' title='Referral Wizard'></i> Referral Wizard</a>
					</li>
					<li><a class='ref_wiz_rated6_byadmin' data-refname='$client_name' data-refemail='$client_email' data-user='$id' data-role='$userrole'><i class='fa fa-link' data-toggle='tooltip' title='Rated 6 Referral Wizard'></i> Rated 6 Referral Wizard</a>
					</li>
					<li><a class='seo_modal_admin' data-refname='$client_name' data-refemail='$client_email' data-user='$id' data-role='$userrole'><i class='fa fa-link' data-toggle='tooltip' title='SEO Keywords'></i> SEO Keywords</a></li> 
					
					";
				 if($user_status == 0)
				 {
					$html .= "<li><a class='btn_statechange' data-s='1' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
				 }
				 else if($user_status == 1)
				 {
					$html .= "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
				 }
				 else if($user_status == 10)
				 {
					$html .= "<li><a class='btn_statechange' data-s='1' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
				 }
				 else if($user_status == 100)
				 {
					$html .= "<li><a class='btn_statechange' data-s='1'  data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>"; 
				 }
				 
				$html .= "
					<li><a class='rmvUser' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> ". $menu_text . "</a></li>
					<li><a class='delUser' data-user='$id'><i class='fa fa-trash text-danger' data-toggle='tooltip' title='Delete'></i> Delete User</a>
					</li>    
					 </ul>
				 </div>
					     
                </td>
            </tr>";
            $i++;
        }
		
        $prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1; 
        $html .= "<tr><td colspan='10'><ul class='pagination pagiAd'>
		<li><a   data-func='prev' data-pg='$prev'>«</a></li>";
        for($i=1; $i<=$pages; $i++)
		{
			$active = $i == $goto ? 'active' : '';
			$html .= "<li class='$active'><a  data-pg='$i'>$i</a></li>";
        }
        $html .= "<li><a   data-func='next' data-pg='$next'>»</a></li></ul></td></tr>";
    }
    echo $html ;
	
}

// Get Clients Details
function getMyCityUsersAdmin($goto,$where="",  $voc="", $name="" ){

    $start = ($goto-1)*10; 
    global $link; 
    $q = $link->query("SELECT mc_user.*, (SELECT grp_name FROM groups WHERE id = (SELECT user_details.groups FROM user_details WHERE user_id = mc_user.id limit 1)) user_group,
                      (SELECT vocations FROM user_details WHERE user_details.user_id = mc_user.id limit 1) client_profession,
                      (SELECT city FROM user_details WHERE user_details.user_id = mc_user.id limit 1) client_location,
					  (SELECT COUNT(user_people.id) FROM user_people WHERE user_people.user_id = mc_user.id    limit 1) user_ref  FROM mc_user WHERE user_role <> 'admin' HAVING 1 ".$where."  ORDER BY username LIMIT $start,10");
 
    $html = "No records found!";
    if($q->num_rows > 0){

        $sel_pkg = $link->query("SELECT `id`, `package_title`, `pkg_status` FROM `packages`");
        $html_pkg = "<li>No Package!</li>";
        if($sel_pkg->num_rows > 0){
            $html_pkg = "";
            while($pkg_row = $sel_pkg->fetch_array()){
                $pkg_id = $pkg_row['id'];
                $pkg_name = $pkg_row['package_title'];
                $pkg_sts = $pkg_row['pkg_status'];
                $status = $pkg_sts == 'activate' ? '' : 'disabled';
                $html_pkg .= "<li class='selUserPkg $status'><a data-id='$pkg_id'>$pkg_name</a></li>";
            }
        } 
		
        //$pg = $link->query("SELECT * FROM mc_user WHERE user_role <> 'admin' "  );
        $pages = ceil($q->num_rows/10);

        $html = "";
        $i = 0;
        while($row = $q->fetch_array()){
            $id = $row['id'];
            $client_name = $row['username'];
            $client_profession = $row['client_profession'];
            $client_phone = $row['user_phone'];
            $client_email = $row['user_email'];
            $client_location = $row['client_location'];
            $user_group = $row['user_group'];
            $user_pkg = $row['user_pkg'];
            $user_ref = $row['user_ref'];
            $createdOn = $row['createdOn'];
            $user_status = $row['user_status'];
			$path = $row['image'];


            $tr = "";
            $ico = "fa-eye";
            $sts = "deactivate";
            if($user_status == 0){
                $tr = "danger";
                $ico = "fa-eye-slash";
                $sts = "activate";
				$menu_text  = "Activate User";
            }
			else 
			{
				$menu_text  = "Disable User";
			}

            $html .= "<tr class='$tr' data-id='$id'>
                <td>$client_name</td>
                <td>$client_profession</td>
                <td>$client_phone</td>
                <td>$client_email</td>
                <td>$client_location</td>
                <td>$user_group</td>
                <td>
                $user_pkg
                <div class='dropdown'>
                <i class='fa fa-money dropdown-toggle' type='button' data-toggle='dropdown'><span class='caret' title='Change package' data-toggle='tooltip'></span></i>
                <ul class='dropdown-menu'>
                $html_pkg
                </ul>
                </div>
                </td>
                <td>$user_ref</td>
                <td>$createdOn</td>
                <td>
             <div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>
				  
				
				<ul class='dropdown-menu'>   
						    
					<li class=close_drop'><a class='changeAccSett' data-id='$id' data-toggle='modal' data-target='#changeAccSett'>
                        <i class='fa fa-user' data-toggle='tooltip' title='View client'></i> View client
						</a></li>  
					<li><a class='changeProfilePhoto' data-id='$id'   data-path='$path' >
							<i class='fa fa-file-photo-o' data-toggle='tooltip' title='Upload Profile Photo'></i> Upload Photo
						</a></li>  
					<li><a class='viewUser' data-user='$id' data-toggle='modal' data-target='#userModal'>
							<i class='fa fa-users' data-toggle='tooltip' title='View references'></i> View references
						</a></li>  
					<li><a class='importmemberknows' data-user='$id'><i class='fa fa-cloud-upload' data-toggle='tooltip' title='Import Knows from CSV'></i> Import Knows</a></li>  
					<li><a class='composememberemail' data-toggle='tab'  data-target='#menu57' data-cn='$client_name' data-ce='$client_email' data-i='$id'><i class='fa fa-envelope' data-toggle='tooltip' title='Compose Email'></i> Compose Email</a></li>  
					<li><a href='https://mycity.com/member/switch-account/$id'><i class='fa fa-exchange' data-toggle='tooltip' title='Switch to Member Account'></i> Switch to Member Account</a>
					</li>  
					<li><a class='showreferrals' data-pagesize='10' data-pageno='1' data-user='$id' ><i class='fa fa-link' data-toggle='tooltip' title='Introduction/Referral'></i> Introduction/Referral</a>
					</li>  
					<li><a data-toggle='tab' href='#menu86' class='show_duplicate_referrals' data-pagesize='10' data-pageno='1' data-user='$id' ><i class='fa fa-link' data-toggle='tooltip' title='Check Duplicate Knows'></i> Check Duplicate Connections</a>
					</li> 
					<li><a class='ref_wizard_byadmin' data-refname='$client_name' data-refemail='$client_email' data-user='$id' data-role='$userrole'><i class='fa fa-link' data-toggle='tooltip' title='Referral Wizard'></i> Referral Wizard</a>
					</li>
					<li><a class='ref_wiz_rated6_byadmin' data-refname='$client_name' data-refemail='$client_email' data-user='$id' data-role='$userrole'><i class='fa fa-link' data-toggle='tooltip' title='Rated 6 Referral Wizard'></i> Rated 6 Referral Wizard</a>
					</li>
					<li><a class='seo_modal_admin' data-refname='$client_name' data-refemail='$client_email' data-user='$id' data-role='$userrole'><i class='fa fa-link' data-toggle='tooltip' title='SEO Keywords'></i> SEO Keywords</a></li> 
					";
				 if($user_status == 0)
				 {
					$html .= "<li><a class='btn_statechange' data-s='1' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
				 }
				 else if($user_status == 1)
				 {
					$html .= "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
				 }
				 else if($user_status == 10)
				 {
					$html .= "<li><a class='btn_statechange' data-s='1' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
				 }
				 else if($user_status == 100)
				 {
					$html .= "<li><a class='btn_statechange' data-s='1'  data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
					$html .= "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>"; 
				 }
				 
				$html .= " 
					<li><a class='delUser' data-user='$id'><i class='fa fa-trash text-danger' data-toggle='tooltip' title='Delete'></i> Delete User</a>
					</li>    
					 </ul> 
				 </div> 
				 </td>
            </tr>";
            $i++;
        }

        $prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1;

        $html .= "<tr><td colspan='10'><ul class='pagination pageml'>
		<li><a   data-func='prev' data-pg='$prev'>«</a></li>";
        for($i=1; $i< $pages; $i++){
            $active = $i == $goto ? 'active' : '';
            $html .= "<li class='$active'><a  data-pg='$i'>$i</a></li>";
        }
		$html .= "<li  ><a  data-pg='$i'>$i</a></li>";
        $html .= "<li><a   data-func='next' data-pg='$next'>»</a></li></ul></td></tr>";
    }
    echo $html;
}


// Get all packages
function getPackages(){
    global $link;
    $q = $link->query("SELECT * FROM `packages`");
    if($q->num_rows > 0){
        $packages = array();
        while($row = $q->fetch_array())
		{
			$packages[$row['id']]['row'] = $row; 
            $q1 = $link->query("SELECT `services` FROM `package_services` WHERE `pkg_id` = '".$row['id']."'");
            if($q1->num_rows > 0)
			{
                while($row1 = $q1->fetch_array()){
                    $packages[$row['id']]['services'][] = $row1;
                }
            }
        }
        return $packages;
    }
	else
	{
        return '0';
    }
}


function fetchPackages(){
    $allPackages = getPackages();
    ?>
    <button class="btnblock" data-toggle="modal" data-target="#edit_package">Add Packages</button>
    <?php
    foreach ($allPackages as $allPackage) {
        $id_pack = $allPackage['row']['id'];
        $title_pack = $allPackage['row']['package_title'];
        $price_pack = $allPackage['row']['package_price'];
        $conn_limit = $allPackage['row']['conn_limit'];
        $share_limit = $allPackage['row']['share_limit'];
        $ref_limit = $allPackage['row']['ref_limit'];
        $conn_desc = $allPackage['row']['conn_desc'];
        $share_desc = $allPackage['row']['share_desc'];
        $ref_desc = $allPackage['row']['ref_desc'];
        $package_limit = $allPackage['row']['package_limit'];
        $pkg_status = $allPackage['row']['pkg_status'];
        $services = $allPackage['services'];

        $min = $package_limit == 0 ? '' : "<h3>".$package_limit. " months minimum</h3>";
        $status = $pkg_status == 'activate' ? 'deactivate' : 'activate';

        $share_limit = $share_limit == 0 ? 'Unlimited' : $share_limit;
        $conn_limit = $conn_limit == 0 ? 'No' : $conn_limit;
        $partnersSharing = $ref_limit == 0 ? 'Unlimited' : $ref_limit;

        ?>
        <div class="box text-center">
            <h4 class="bg"><?php echo $title_pack; ?>
                <i class="fa fa-power-off del_pkg" data-id="<?php echo $id_pack; ?>" data-toggle="tooltip" title="<?php echo $status ?>"></i>
                <i data-toggle="modal" data-target="#edit_package" class="fa fa-pencil edit_package"
                   data-id="<?php echo $id_pack; ?>"></i>
            </h4>
            <h4><span>$<?php echo $price_pack; ?></span></h4>
            <?php echo $min ?>
            <ul>
                <li><?php echo $conn_limit . " " . $conn_desc; ?></li>
                <li><?php echo $share_limit . " " . $share_desc; ?></li>
                <li><?php echo $partnersSharing . " " . $ref_desc; ?></li>
                <?php
                foreach ($services as $service) 
				{
					echo "<li>".$service['services']."</li>";
                }
                ?>
            </ul>
        </div>
    <?php }
}


// Get pages data
function getPageDetails($page){
    global $link;
    $q = $link->query("SELECT * FROM pages_data WHERE page_name = '$page'");
    if($q->num_rows > 0){
        $arr = array();
        while($row = $q->fetch_array()){
            $arr[] = $row;
        }
        return $arr;
    }else{
        return '0';
    }
} 
// Get Blogs
function getBlogs(){
    global $link;
    $blog_q = $link->query("SELECT `blog_name` FROM `blog_details` GROUP BY `blog_name`");
    if($blog_q->num_rows > 0) {
        $i = 0;
        while ($blog_row = $blog_q->fetch_array()) {
            $blog = $blog_row['blog_name'];
            $class = $i == 0 ? "" : "collapsed";
            ?>
            <div class="panel panel-default">
                <div class="panel-heading border" role="tab"
                     id="blog<?php echo $i ?>">
                    <a class="<?php echo $class ?>" role="button"
                       data-toggle="collapse" data-parent="#accordion"
                       href="#collapse<?php echo $i ?>" aria-expanded="false"
                       aria-controls="collapse<?php echo $i ?>"> <?php echo $blog ?>
                    </a>
                </div>
                <div id="collapse<?php echo $i ?>" class="panel-collapse collapse"
                     role="tabpanel" aria-labelledby="blog<?php echo $i ?>">
                    <div class="panel-body">
                        <?php
                        $q = $link->query("SELECT * FROM `blog_details` WHERE `blog_name` = '$blog'");
                        if ($q->num_rows > 0) {
                            while ($blogItem = $q->fetch_array()) {
                                $blog_id = $blogItem['id'];
                                $blog_name = $blogItem['blog_name'];
                                $content_title = $blogItem['content_title'];
                                $blog_content = $blogItem['blog_content'];
                                $blog_image = $blogItem['blog_image'];
                                $blog_video = $blogItem['blog_video'];
                                ?>
                                <i class="fa fa-trash rmvBlogContent" data-id="<?php echo $blog_id ?>"></i>
                                <i data-toggle="modal" data-target="#edit-2" class="fa fa-pencil editBlogData" data-id="<?php echo $blog_id ?>"></i>
                                <div class="clearfix"></div>
                                <?php
                                if (!empty($content_title)) {
                                    echo "<h4>$content_title</h4>";
                                }
                                ?>
                                <p><?php
                                    echo nl2br($blog_content);
                                    if (!empty($blog_image)) {
                                        echo "<img src='blog/$blog_image' alt='$blog_image'/>";
                                    }
                                    if (!empty($blog_video)) {
                                        echo "<video src='blog/$blog_video' controls/>";
                                    }
                                    ?></p>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
            $i++;
        }
    }
} 
//Debug to Console
function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}

// Get All Vocations
function getTriggers($link){
    global $link;
    $q = $link->query("SELECT * FROM  my_triggers ORDER BY  trigger_question ");
    while($q_row = $q->fetch_array()){
        $data[] = ["id" => $q_row['id'], "trigger_question" => $q_row['trigger_question']];
    }
    return $data;
}

// Get All Vocations
function getMyTriggers($link, $id){
    global $link;
    $q = $link->query("SELECT * FROM  my_triggers WHERE user_id='$id' ORDER BY  trigger_question ");
    while($q_row = $q->fetch_array()){
        $data[] = ["id" => $q_row['id'], "trigger_question" => $q_row['trigger_question']];
    }
    return $data;
}

// Get All Groups
function getMyGroups($link, $user_id){
    global $link; 
	$user = $link->query("SELECT * FROM user_details where user_id = '$user_id'");
	$user = $user->fetch_array();
	$groups = explode(",", $user['groups']);
	 
	$where_group = "(";
	
	for($i=0; $i < sizeof($groups); $i++ )
	{
		$where_group .= $groups[$i];
		if( $i < sizeof($groups) - 1)
		{
			$where_group .= ",";
		} 
	}
	$where_group .= ")";
	
	$q = $link->query("SELECT * FROM groups where id in " .$where_group . " and islisted='1' ORDER BY grp_name"); 
	if($q->num_rows > 0)
	{
		while($q_row = $q->fetch_array()){
			$data[] = ["id" => $q_row['id'], "grp_name" => $q_row['grp_name']];
		}	
	} 
    return $data;
}
// Get All Mail Templates
function getMailTemplates($link ){
    global $link;
    $q = $link->query("SELECT * FROM  mc_mail_templates  where status='0' order by templatename ");
    while($q_row = $q->fetch_array()){
         if($q_row['mailtype'] == 0)
        {
            $mailtype='Trigger Mail';
        }
        else  if($q_row['mailtype'] == 1)
        {
            $mailtype='Introduction Mail';
        }
        else  if($q_row['mailtype'] == 2)
        {
            $mailtype='LinkedIn Invitation';
        }
        else  if($q_row['mailtype'] == 3)
        {
            $mailtype='Testimonial Videos';
        }
 
        $data[] = ["id" => $q_row['id'], "template" => $q_row['templatename'], "subject" => $q_row['subject'] , "mailbody" => $q_row['mailbody'], "mailtype" => $mailtype ];
    }
    return $data;
}
 
//Get User Loyalty Point
function getLoyaltyPoint($link, $user_id){
    global $link; 
	$loyaltypoints = $link->query("SELECT SUM(pointearned) as point FROM loyalty_point where user_id = '$user_id' and status='0'");
	$row = $loyaltypoints->fetch_array(); 
	if( $row['point'] == "")
	{
		return "0";
	}
	else
	{
		return $row['point'];
	} 
} 


function getMyKnows($link, $user_id)
{
    global $link; 
    $totalknows = $link->query("SELECT COUNT(*) as totalknows FROM user_people where user_id = '$user_id' ");
    $row = $totalknows->fetch_array(); 
    return $row['totalknows'];
} 

function getMyReferences($link, $uid, $professions)
{ 
	$professionlist = explode(",",  $professions); 
	$where_group = "("; 
	for($i=0; $i < sizeof($professionlist); $i++ )
	{
		$where_group .= "'". $professionlist[$i] . "'";
		if( $i < sizeof($professionlist) - 1)
		{
			$where_group .= ",";
		}
	}
	$where_group .= ")"; 
	$userpeople = $link->query("SELECT * FROM user_people WHERE user_id = '$uid' AND client_profession IN  $where_group ORDER BY client_name "); 
	$references = array();
	
	if($userpeople->num_rows > 0)
	{
		while($row = $userpeople->fetch_array())
		{
			$id = $row['id'];
			$grpNameQ = $link->query("SELECT * FROM `groups` WHERE id='$user_group'");
			if($grpNameQ->num_rows > 0)
			{
				$grpNameFet = $grpNameQ->fetch_assoc();
				$userGrpName = $grpNameFet['grp_name'];
			}
			$vocNameQ = $link->query("SELECT * FROM `vocations` WHERE id = '$client_profession' ");
			if($vocNameQ->num_rows > 0)
			{
				$vocNameFet = $vocNameQ->fetch_assoc();
				$userVocName = $vocNameFet['voc_name'];
			}
			$rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$uid'");
            $rate_row = $rate_q->fetch_array();
            $user_ranking = $rate_row['user_ranking'];
			if($user_ranking > 20 )
			{
				$references[] = array('id' =>  $row['id']  ,
				'user_id' =>  $row['user_id'],
				'client_name' =>  $row['client_name'],
				'client_profession' => $row['client_profession'],
				'client_phone' =>$row['client_phone'],
				'client_email' => $row['client_email'],
				'client_location' => $row['client_location'],
				'user_group' => $row['user_group'], 
				'userGrpName' =>  $userGrpName ,
				'userVocName' =>  $userVocName,
				'user_ranking'=> $user_ranking ,
				'marked_selected'=> '0');
			}
        }
	}  
	//$json = json_encode($references);
	//return $json;
	
	return $references;
}


function getAllReferences($link, $uid, $professions)
{
	$professionlist = explode(",",  $professions);
	 
	$where_group = "(";
	
	for($i=0; $i < sizeof($professionlist); $i++ )
	{  
		$where_group .= " find_in_set ( p.client_profession , ('". $professionlist[$i] . "' ) ) "; 
		
		if( $i < sizeof($professionlist)-1 )
		{
			$where_group .=  " OR "; 
		} 
	}
	$where_group .= ")"; 
	
	 
	$userpeople = $link->query("SELECT p.*,  SUM(r.ranking) as rank
				FROM user_people as p INNER JOIN user_rating as r on p.id=r.user_id INNER JOIN user_answers as a on p.id = a.user_id
				WHERE p.user_id = '$uid' AND " . $where_group . "  
				GROUP BY p.id ORDER BY client_name");
				
	
	$references = array();	 
	if($userpeople->num_rows > 0)
	{
		while($row = $userpeople->fetch_array())
		{
			$id = $row['id']; 
			$user_ranking = $row['rank']; 
			 
			if($user_ranking > 20 )
			{
				$references[] = array('id' =>  $row['id']  ,
				'user_id' =>  $row['user_id'],
				'client_name' =>  $row['client_name'],
				'client_profession' => $row['client_profession'],
				'client_phone' =>$row['client_phone'],
				'client_email' => $row['client_email'],
				'client_location' => $row['client_location'],
				'client_zip' => $row['client_zip'],
				'user_group' => $row['user_group'], 
				'userGrpName' =>  '' ,
				'userVocName' =>  '' ,
				'user_ranking'=> $user_ranking ,
				'marked_selected'=> '0',
				'distance'=> '0');
			}
        }
	}  
	//$json = json_encode($references);
	//return $json;
	 return $references;
}

function calculatedistance($sourcezip, $targetzip)
{
	/*
		$details = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=' .$sourcezip . 
				'&destinations=' . $targetzip.'&key=AIzaSyDmS-6AB24gpXHATvtfnrWuaN3VK4Xb3Ek';
		$json = file_get_contents($details);
		$details = json_decode($json, TRUE); 
		$distanceinmiles  =  ( $details['rows'][0]['elements'][0]['distance']['value'] * 0.000621371); 
		return $distanceinmiles;
	*/
	return 10;
} 

function checkExistingReferral($partnerid, $knowtorefer, $knowtoreferedto, $userid)
{
	global $link; 
    $result = $link->query("SELECT COUNT(*) AS rcnt FROM referralsuggestions 
        WHERE partnerid='$partnerid' AND knowtorefer='$knowtorefer' AND knowreferedto='$knowtoreferedto' AND knowenteredby='$userid' ");
  
    $row = $result->fetch_array();
    return $row['rcnt'];
} 

function getConnections($user_id)
{
	global $link;
    //read active user group and create where clause for group id search
	$user = $link->query("SELECT * FROM user_details where user_id = '$user_id'");
    $user = $user->fetch_array();
    $groups = explode(",", $user['groups']);  
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
	
	//create main query to insert any new referrals
	$mainQry = "SELECT  id, client_name  FROM user_people  WHERE client_name LIKE 'a%' and   user_id IN  ( $qryInner )  ORDER BY client_name LIMIT 50" ;
	  
	$connections = $link->query( $mainQry );
	
	$users = array();
	if( $connections->num_rows > 0)
	{ 
		while($row = $connections->fetch_array() )
		{ 
			$users[] = array( 'id' => $row['id'] , 'client_name' => $row['client_name']); 
		} 
	} 
	return  json_encode(  $users ); 		 
	//return  (  $connections->fetch_all()  ); 
} 

// Get User References: copied to API
function getImportedKnows($user_id, $goto,$where=""){

    $start = ($goto-1)*10;
	global $link;
	if($user_id ==1)
	{
		$q = $link->query("SELECT * FROM user_people WHERE isimport ='1'  ".$where." ORDER BY client_name ASC LIMIT $start,10");
		$html = "No records found!";
		if($q->num_rows > 0){
			$pg = $link->query("SELECT id FROM user_people WHERE isimport='1' ".$where);
			$pages = ceil($pg->num_rows/10);

			$html = "";
			while($row = $q->fetch_array())
			{
				$id = $row['id'];
				$client_name = $row['client_name'];
				$client_profession = $row['client_profession'];
				$client_phone = $row['client_phone'];
				$client_email = $row['client_email'];
				$client_location = $row['client_location'];
				$user_group = $row['user_group'];
				$userGrpName = '';
				$userVocName = '';


				$grpNameQ = $link->query("SELECT * FROM `groups` WHERE id='$user_group'");
				if($grpNameQ->num_rows > 0)
				{
					$grpNameFet = $grpNameQ->fetch_assoc();
					$userGrpName = $grpNameFet['grp_name'];
				} 
				$vocNameQ = $link->query("SELECT * FROM `vocations` WHERE id = '$client_profession' ");
				if($vocNameQ->num_rows > 0)
				{
					$vocNameFet = $vocNameQ->fetch_assoc();
					$userVocName = $vocNameFet['voc_name'];
				} 
				$rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$id'");
				$rate_row = $rate_q->fetch_array();
				$user_ranking = $rate_row['user_ranking'];

				$str = "abcdefghijklmnopqrstuvwxyz";
				$rand = substr(str_shuffle($str),0,3); 
				$html .= "<tr id='$rand-$id'>
					<td>$client_name</td>
					<td>$client_profession</td>
					<td>$client_phone</td>
					<td>$client_email</td>
					<td>$client_location</td>
					<td>$userGrpName</td> 
					<td> 
			 <button data-toggle='modal' data-target='#edit_people_details' class='btn-primary btn btn-xs editPeopleDetails'><i class='fa fa-pencil'></i></button>
				<button data-id='$id' class='btn-success btn btn-xs editcommonvocation'><i class='fa fa-briefcase'></i></button>	
					<button class='btn-danger btn btn-xs delUserClient' data-id='$id'><i class='fa fa-times-circle'></i></button>
					</td>
				</tr>";
			}
			$prev = $goto == 1 ? 1 : $goto-1;
			$next = $goto == $pages ? $pages : $goto+1; 
			$html .= "<tr><td colspan='8'><ul class='pagination pagiimportknow'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
			for($i=1; $i<=$pages; $i++)
			{
				$active = $i == $goto ? 'active' : '';
				$html .= "<li class='$active'><a data-pg='$i'>$i</a></li>";
			}
			$html .= "<li><a data-func='next' data-pg='$next'>»</a></li></ul></td></tr>";
		}	
	}
	else 
	{
		
		$q = $link->query("SELECT * FROM user_people WHERE isimport ='1' and user_id = '$user_id' ".$where." ORDER BY client_name ASC LIMIT $start,10");
		$html = "No records found!";
		if($q->num_rows > 0){
			$pg = $link->query("SELECT id FROM user_people WHERE isimport='1' and  user_id = '$user_id' ".$where);
			$pages = ceil($pg->num_rows/10);

			$html = "";
			while($row = $q->fetch_array())
			{
				$id = $row['id'];
				$client_name = $row['client_name'];
				$client_profession = $row['client_profession'];
				$client_phone = $row['client_phone'];
				$client_email = $row['client_email'];
				$client_location = $row['client_location'];
				$user_group = $row['user_group'];
				$userGrpName = '';
				$userVocName = '';


				$grpNameQ = $link->query("SELECT * FROM `groups` WHERE id='$user_group'");
				if($grpNameQ->num_rows > 0)
				{
					$grpNameFet = $grpNameQ->fetch_assoc();
					$userGrpName = $grpNameFet['grp_name'];
				} 
				$vocNameQ = $link->query("SELECT * FROM `vocations` WHERE id = '$client_profession' ");
				if($vocNameQ->num_rows > 0)
				{
					$vocNameFet = $vocNameQ->fetch_assoc();
					$userVocName = $vocNameFet['voc_name'];
				} 
				$rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$id'");
				$rate_row = $rate_q->fetch_array();
				$user_ranking = $rate_row['user_ranking'];

				$str = "abcdefghijklmnopqrstuvwxyz";
				$rand = substr(str_shuffle($str),0,3); 
				$html .= "<tr id='$rand-$id'>
					<td>$client_name</td>
					<td>$client_profession</td>
					<td>$client_phone</td>
					<td>$client_email</td>
					<td>$client_location</td>
					<td>$userGrpName</td>
					<td>$user_ranking</td>
					<td> 
						 <button data-toggle='modal' data-target='#edit_people_details' class='btn-primary btn btn-xs editPeopleDetails'><i class='fa fa-pencil'></i></button>
						<button class='btn-danger btn btn-xs delUserClient' data-id='$id'><i class='fa fa-times-circle'></i></button>
					</td>
				</tr>";
			}
			$prev = $goto == 1 ? 1 : $goto-1;
			$next = $goto == $pages ? $pages : $goto+1; 
			$html .= "<tr><td colspan='8'><ul class='pagination pagiimportknow'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
			for($i=1; $i<=$pages; $i++)
			{
				$active = $i == $goto ? 'active' : '';
				$html .= "<li class='$active'><a data-pg='$i'>$i</a></li>";
			}
			$html .= "<li><a data-func='next' data-pg='$next'>»</a></li></ul></td></tr>";
		}
	}
    echo $html;
}



// Get All Testimonials
function getwhoinvitedwhom()
{
	$date = date('Y-m-d', strtotime("-2 week"))  ;
	global $link;
	$invites = $link->query("select m.id, m.username, c.invitecount  
	from mc_user as m inner join 
	( select count(*) as invitecount, user_id from user_people where date(entrydate) > '$date' group by user_id ) 
	as c on m.id=c.user_id order by invitecount desc");
	$invites_data = array();
	while($row = $invites->fetch_array())
	{
       $invites_data[] = ["id" => $row['id'], 
						  "member" => $row['username'],
						  "invite_count" => $row['invitecount']];
    }
    return $invites_data;
}


//generic email using active user signature
function sendmailusersigned($to, $from , $fromname,  $subject, $body, $altbody)
{
	$headers = 'From: '.  $fromname . '<' . $from . '>' . "\r\n"; 
	$headers .=  'Reply-To: '.  $fromname . '<' . $from . '>' . "\r\n"; 
	$headers .=  'Return-Path:  '.  $fromname . '<' . $from . '>' . "\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	if ( mail( $to ,$subject,$body,$headers) ) {
		return "1";
	} else {
		return "0" ;
	}   
}  

//generic email
function sendmail($to, $from,  $subject, $body, $altbody)
{
	$headers = "From: " . $from  . "\r\n";
	$headers .= "Reply-To: " . $from  . "\r\n";
	$headers .= "Return-Path: " . $from  . "\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	if ( mail($to,$subject,$body,$headers) ) {
		return "1";
	} else {
		return "0" ;
	}   
}

function sendreferralmail($to,  $subject, $body, $altbody ,  $cc ='referrals@mycity.com', $ccname ='Referral MyCity', $cc1 ='', $ccname1 ='')
{
	if($cc1 != "")
    {
		$to.="," . $cc1;
	}
	 
	if($cc != "")
    {
        $headers = "From: referrals@mycity.com\r\n" . "Cc: " . $cc . "\r\n";
    }
	else 
	{ 	
		$headers = "From: referrals@mycity.com\r\n";
	}
	$headers .= "Reply-To: referrals@mycity.com\r\n";
	$headers .= "Return-Path: referrals@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	if ( mail($to,$subject,$body,$headers) ) 
	{  
		return "1";
	}
	else
	{
		return "0" ;
	} 
}


function sendmailfrommycityalert($to,  $subject, $body, $altbody ,  $cc ='referrals@mycity.com', $ccname ='Referral MyCity' , $cc1 ='', $ccname1 ='')
{ 
	if($cc1 != "")
    {
		$to.="," . $cc1;
	}
	 
	if($cc != "")
    {
        $headers = "From: referrals@mycity.com\r\n" . "Cc: " . $cc . "\r\n";
    }
	else 
	{ 	
		$headers = "From: referrals@mycity.com\r\n";
	}
	$headers .= "Reply-To: referrals@mycity.com\r\n";
	$headers .= "Return-Path: referrals@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	if ( mail($to,$subject,$body,$headers) ) 
	{  
		return "1";
	}
	else
	{
		return "0" ;
	} 
}
 

function sendemail($to,  $subject, $body, $altbody ,  $cc ='referrals@mycity.com', $ccname ='Referral MyCity' , $cc1 ='', $ccname1 ='')
{ 
	if($cc1 != "")
    {
		$to.="," . $cc1;
	} 
	if($cc != "")
    {
        $headers = "From: referrals@mycity.com\r\n" . "Cc: " . $cc . "\r\n";
    }
	else 
	{ 	
		$headers = "From: referrals@mycity.com\r\n";
	}
	$headers .= "Reply-To: referrals@mycity.com\r\n";
	$headers .= "Return-Path: referrals@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	if ( mail($to,$subject,$body,$headers) ) 
	{  
		return "1";
	}
	else
	{
		return "0" ;
	} 
}

function member_rating($a, $b )
{
	if ($a['rating'] ==$b['rating'] ) return 0;
		return (   $a['rating'] >  $b['rating']  )?-1:1;
}

function find_neighbour_cities( $zip, $radius )
{
	global $pdo;
	try
		{ 
			 
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
			$sql_query  = "select * from mc_city_geolocation where zip='$zip'   ";
			$zipcodeList = array();
			$rst = $pdo->query($sql_query);
			
			if($rst->rowCount() > 0)
			{
				$latlong = $rst->fetchAll(PDO::FETCH_ASSOC)[0]; 
				
				$lat = $latlong['latitude'];
				$lon = $latlong['longitude']; 
				
				$sql = 'select distinct(zip) from mc_city_geolocation  ' .
				' where (3958*3.1415926*sqrt((latitude-'.$lat.')*(longitude-'.$lat.') + ' .
				'cos(latitude/57.29578)*cos('.$lat.'/57.29578)*(longitude-'.$lon.')*(longitude-'.$lon.'))/180) <= '.$radius.';';
				$result = $pdo->query($sql);
				 
				$rszip= $result->fetchAll(PDO::FETCH_ASSOC);
				foreach($rszip as $row) 
				{
					array_push($zipcodeList, $row['zip']);
				}    
			}	 
		}
		catch(PDOException $e)
		{
			  
		}  
		return $zipcodeList    ; 
}



// Get User References
function getDuplicateReferences($user_id, $goto,$where="")
{
	$start = ($goto-1)*10;
	global $link;
	$q = $link->query("SELECT * FROM user_people WHERE user_id = '$user_id' ".$where." ORDER BY client_name ASC LIMIT $start,10");
    $html = "No records found!";
    if($q->num_rows > 0){
        $pg = $link->query("SELECT id FROM user_people WHERE user_id = '$user_id' ".$where);
        $pages = ceil($pg->num_rows/10);
	$html = "";
	
	
	$html .= "<table class='table table-border table-alternate'><tr id='$rand-$id'>
                <td>Name</td>
                <td>Profession</td>
                <td>Phone</td>
                <td>Email</td>
                <td>Location</td>
                <td>Group</td>
                <td>Ranking</td>
                <td  > </td></tr>";
				
				
	while($row = $q->fetch_array())
	{
		$id = $row['id'];
		$client_name = $row['client_name'];
		$client_profession = $row['client_profession'];
		$client_phone = $row['client_phone'];
		$client_email = $row['client_email'];
		$client_location = $row['client_location'];
		$user_group = $row['user_group'];
		$userGrpName = '';
		$userVocName = '';
		
		$grpNameQ = $link->query("SELECT * FROM `groups` WHERE id='$user_group'");
		if($grpNameQ->num_rows > 0)
		{
			$grpNameFet = $grpNameQ->fetch_assoc();
			$userGrpName = $grpNameFet['grp_name'];
		}

            $vocNameQ = $link->query("SELECT * FROM `vocations` WHERE id = '$client_profession' ");
            if($vocNameQ->num_rows > 0){
                $vocNameFet = $vocNameQ->fetch_assoc();
                $userVocName = $vocNameFet['voc_name'];
            }
			
			$introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");		 
			if($introduceeresult->num_rows > 0){
				$introducee = $introduceeresult->fetch_array();
				$introduceedetails = $introducee['username'] . "<br/>" . $introducee['user_email'];
			}
			
            $rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$id'");
            $rate_row = $rate_q->fetch_array();
            $user_ranking = $rate_row['user_ranking'];

            $str = "abcdefghijklmnopqrstuvwxyz";
            $rand = substr(str_shuffle($str),0,3);

            $html .= "<tr id='$rand-$id'>
                <td>$client_name</td>
                <td>$client_profession</td>
                <td>$client_phone</td>
                <td>$client_email</td>
                <td>$client_location</td>
                <td>$userGrpName</td>
                <td>$user_ranking</td>
                <td  > 
					<input data-id='$id' type='checkbox' name='select_id[]' class='select_id'/>  
                </td>
            </tr>";
        } 
		$html .= "<tr  >
                <td col-span='8'>
				<input type='hidden' id='userid' value='$user_id' />
				<input type='hidden' id='crpage' value='$goto' />
				<button class='btn btn-danger remdupknows'>Delete Selected</button></td>
                
            </tr>";
		$lastpage = $pages ;
		$prev = $goto == 1 ? 1 : $goto-1;
		$next = $goto == $pages ? $pages : $goto+1; 
		$html .=  "<tr><td colspan='8'><ul class='pagination pagidupref'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
		if( $goto > 10) 
			$html .=  "<li><a  data-func='next' title='Show last few pages' data-pg='1'> ... </a></li>";
		
		if($goto < 10)
		{ 
			 for($j= 1 ; $j  <=  10  ; $j++)
			 {
				 if($j > $pages)
				 {
					 break;
				 }
				
				 $active = $j == $goto ? 'active' : '';
				 $html .=  "<li class='$active'><a  data-pg='$j'>$j</a></li>";
			 }
		 }
		 else
		 {
			 for($i= $goto - 5; $i<= $goto + 4; $i++)
			{
				if($i > $pages)
				{
					break;
				}
				$active = $i == $goto ? 'active' : '';
			    $html .=  "<li class='$active'><a data-pg='$i'>$i</a></li>";
			 }
		 }
	 	if( $goto < ($lastpage - 10 ) )
         $html .=  "<li><a   data-func='next' title='Show last few pages' data-pg='$lastpage'> ... </a></li>";
         $html .= "<li> <input type='text' id='klgotopageno' style='width: 120px; height: 32px; margin-top: 2px; margin-right: 5px; float: left; display: inline-block;' class= 'form-control' placeholder= 'Go to page ...' > </li>";
         $html .= "<li> <input type='button' id='klgopage' value='Go' style='width: 50px; float: left; height: 32px; margin-top: 2px; display: inline-block;  background-color: #2e353d; color: #fff;' class= 'btn '  > </li>";
         $html .=  "<li><a  data-func='next' title='Next Page' data-pg='$next'>»</a></li></ul></td></tr>"; 
    } 
	$html .= "</table>"; 
    echo $html;
}



