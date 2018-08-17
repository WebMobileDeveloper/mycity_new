<?php
	include_once 'includes/functions.php';
    session_start();
    date_default_timezone_set('America/Los_Angeles');
    $tagline = getPageDetails("tagline"); 
	 
    if( isset( $_COOKIE['_mcu'] ) )
    {
		$mcu =  json_decode( "[" .     $_COOKIE['_mcu']  . "]", true ) ;   
        //get token
        $logintoken = $mcu[0]["token"]; 
		 
		
		if($mcu[0]["role"] == 'admin')
			$_SESSION['logintoken'] = $logintoken;
		  
        if($logintoken != '')
        {
            $loginlogrs = $link->query( "select * from  mc_user where  id =   (select userid from  mc_login_log where token='$logintoken' ) " ); 
			
			 
            if($loginlogrs->num_rows == 1 )
            {
                $loginlogrow = $loginlogrs->fetch_array();
				$_SESSION['user_id'] =  $loginlogrow['id']; 
                $_SESSION['username'] =  $loginlogrow['username']; 
                $_SESSION['user_email'] =  $loginlogrow['user_email']; ;
                $_SESSION['user_phone'] =  $loginlogrow['user_phone']; 
                $_SESSION['user_role'] =  $loginlogrow['user_role']; 
                $_SESSION['user_pic'] =  ("images/".((!empty( $loginlogrow['image'] ))?  $loginlogrow['image'] :"no-photo.png"));
				$_SESSION['user_zip'] =  $loginlogrow['zip']; 
				$_SESSION['user_groups'] =  $loginlogrow['groups'];
$_SESSION['isemployee'] =  $loginlogrow['is_employee']; 				
            }
			
			$usergrouprs = $link->query(" select groups from user_details where user_id = (select userid from  mc_login_log where token='$logintoken') "); 
		   if($usergrouprs->num_rows > 0)
				{ 
					$gcrowra = $usergrouprs->fetch_array();;
					if( $gcrowra ['groups'] != '' )
					{
						$grouparr = explode(',', $gcrowra ['groups']);
						$grouparr = array_filter($grouparr); 
						sort($grouparr); 
						$linkgroups =( array_unique($grouparr)  );
						$groupids  = implode(', ',  $linkgroups );
						$grounamesrs = $link->query("select group_concat( grp_name) as grp_names from groups where id in 
						(" . $groupids . " ) ");
						$groupnames = $grounamesrs->fetch_array() ['grp_names']  ;
					}
					else 
					{
						$profile['groupnames'] = 'Not Specified' ;
					}  
				}
				else 
				{
					$profile['groupnames'] = 'Not Specified' ;
				} 
			 
        }   
    } 
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" /> 
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /> 
	<?php
	$filename = basename($_SERVER['SCRIPT_FILENAME']);
	switch($filename )
	{
		case "dashboard.php":
			$page_title ='Turn your Social Media into Relationships & Referrals';
			$page_description ='A proactive system to grow relationships, grow referral partners, grow sales.';
			break;
	}   
	?> 
	<title><?php if(isset($page_title)) { echo  $page_title; echo " - ";  } ?>MyCity</title> 
	<meta content="<?php echo $page_description; ?>" name="description" />
	
    <meta name="author" content="MyCity"/> 
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />  
    <link href="https://mycity.com/admin/assets/css/bootstrap.min.css" rel="stylesheet" /> 
	<link rel="stylesheet" href="https://mycity.com/admin/css/jquery-ui.min.css">
    <link href="https://mycity.com/admin/assets/css/animate.min.css" rel="stylesheet"/>  
    <link href="https://mycity.com/admin/assets/css/style.css?v=<?php echo mt_rand(1,100000);?>" rel="stylesheet"/>   
    <link href="https://mycity.com/admin/assets/fa/css/font-awesome.min.css" rel="stylesheet">   
	<link rel="stylesheet" href="https://mycity.com/admin/css/chosen.css">    
	<link rel="stylesheet" href="https://mycity.com/admin/css/easy-autocomplete.min.css">
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-26668236-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date()); 
	  gtag('config', 'UA-26668236-1');
	</script> 
</head>

