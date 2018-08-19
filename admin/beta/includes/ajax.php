<?php
/**
 * Created by PhpStorm.
 * User: Frontend
 * Date: 3/10/2016
 * Time: 8:51 PM
 */
date_default_timezone_set('America/Los_Angeles');
set_time_limit(60*10);

ini_set('post_max_size', '64M');
ini_set('upload_max_filesize', '64M');


if(!isset($_SESSION))session_start();
include_once 'db.php';
include_once 'functions.php';


if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.dev")
{
    $siteurl = 'http://'. $_SERVER['HTTP_HOST'] . "/";
} 
else
{
    $siteurl =  'https://mycity.com/';
}
$param = array('id' => '0');
$alltags = json_decode(   curlexecute($param, $siteurl . 'api/api.php/tags/'), true);



// Valid file extensions.
$valid_extension = array('jpg', 'jpeg', 'gif', 'png');
$date_time = date("Y-m-d H:i:s");

//if(preg_match('/ajax.php/', $_SERVER['REQUEST_URI'])){header('location: ../dashboard.php');}


$user_id = @$_SESSION['user_id'];
$_username = @$_SESSION['username'];
$_user_email = @$_SESSION['user_email'];
$_user_phone = @$_SESSION['user_phone'];
$_user_role = @$_SESSION['user_role'];
 
// Put email in DB from get started
if(isset($_POST['storeEmail'])){
    $reg_email = $_POST['storeEmail'];
    $chEmail = $link->query("SELECT * FROM mc_user WHERE user_email='$reg_email' ");
    if($chEmail->num_rows > 0)
	{
		$RespMsg = array
		(
			"MsgType" => "Error",
            "Msg" => "You are already registered."
        );
        header('Content-Type: application/json');
        echo json_encode($RespMsg);
        exit();
    }
	
	$insQstmnt = "INSERT INTO mc_user (user_email) VALUES ('$reg_email')";
    $insQ = $link->query($insQstmnt);
    $qError = $link->error;

    if($insQ)
	{
		$insID = $link->insert_id;
        $RespMsg = array
		(
            "MsgType" => "Done",
            "Msg" => "Email stored",
            "insID" => $insID
        );
    }
    else
	{
        $RespMsg = array(
            "MsgType" => "Error",
            "Msg" =>  $link->error,
            "Error" => $qError,
            "Qry" => $insQstmnt
        );
    }
    header('Content-Type: application/json');
    echo json_encode($RespMsg);
} 
// Register user
if (isset($_POST['updProf']) && isset($_POST['reg_email'])) {
    $data = $_POST;
    $Msg = ''; 
    $insID = isset($_POST['insID']) ? $_POST['insID'] : '';
    $reg_email = $_POST['reg_email'];
    $reg_first_name = $_POST['reg_first_name'];
    $reg_last_name = $_POST['reg_last_name'];
    $reg_password = md5($_POST['reg_password']);
    $reg_country = $_POST['reg_country'];
    $reg_zip = $_POST['reg_zip'];
    $reg_city = $_POST['reg_city'];
    $reg_pkg = isset($_POST['reg_pkg']) ? $_POST['reg_pkg'] : 'Basic';
      
    /*$groups_result = $_POST['groups_result']; #array
    $target_clients = $_POST['target_clients']; #array
    $vocation_result = $_POST['vocation_result']; #array*/

    $chEmail = $link->query("SELECT * FROM mc_user WHERE user_email='$reg_email' AND id != '$insID' ");
    if($chEmail->num_rows > 0){
        $RespMsg = array(
            "MsgType" => "Error",
            "Msg" => "You are already registered.",
            "Test" => $data
        );
        header('Content-Type: application/json');
        echo json_encode($RespMsg);
        exit();
    }

    if(!empty($insID) AND $insID > 0)
	{
        $insQstmnt = "UPDATE mc_user SET user_pass='$reg_password',username='$reg_first_name $reg_last_name' WHERE id='$insID' ";
        $insQ = $link->query($insQstmnt);
    }
	else
	{
        $insQstmnt = "INSERT INTO mc_user (user_email,user_pass,username, user_pkg) VALUES ('$reg_email','$reg_password','$reg_first_name $reg_last_name' ,'$reg_pkg')";
        $insQ = $link->query($insQstmnt);
        $insID = $link->insert_id;
    } 
    $qError = $link->error; 
	if($insQ)
	{
		$ggrrpp = '';
        
		/*
            foreach ($groups_result as $item) 
            {
                $ggrrpp .= "'$item',";
            }
            
            $ggrrpp = rtrim($ggrrpp, ',');
            $q_grp = $link->query("SELECT grp_name FROM groups WHERE groups.id IN ($groups_result)");
            $allGrps = '';
            while($row_grp = $q_grp->fetch_array())
            {
                $allGrps .= ['grp_name'];
            }

		*/
		
		$_SESSION['user_id'] = $insID;
        $_SESSION['username'] = $reg_first_name . " " . $reg_last_name;
        $_SESSION['user_email'] = $reg_email;
        $_SESSION['user_phone'] = "";
        $_SESSION['user_role'] = "user";
		$_SESSION['user_group'] = "";
        if($reg_pkg == 'Invite')
        {
            $lcidrs = $link->query("select lcid from user_people where id='" . $_SESSION['linkeduserid'] . "'"); 
            if($lcidrs->num_rows > 0)
            {
                $lcid  = $lcidrs->fetch_array()['lcid'] ;
            }
            else
            {
                $lcid = 0; 
            }
        }
        else 
        {
           $_SESSION['linkeduserid']  = 0;
        }
        //$_SESSION['user_group'] = $allGrps; 
        /*$qryStmnt = "INSERT INTO user_details (user_id,country,zip,city,groups,target_clients,vocations) VALUES ('$insID','$reg_country','$reg_zip','$reg_city','$groups_result','$target_clients','$vocation_result')";*/
		$qryStmnt = "INSERT INTO user_details (user_id, city, zip, country, lcid) VALUES ('$insID', '$reg_city','$reg_zip', '$reg_country', $lcid)";
        $link->query($qryStmnt);
 
        // logo upload
       /* 
		if (isset($_FILES['image']))
		{
			$files = $_FILES['image'];
            $FName = $files['name'];
            $Ftype = $files['type'];
            $Fsize = $files['size'];
            $Ftmp = $files['tmp_name'];
			
			$fExt = pathinfo($FName, PATHINFO_EXTENSION);
			
			if (!in_array($fExt, $valid_extension))
			{
                $Msg = "Select image with extensions:" . implode(",",$valid_extension) . " please.";
            }
            else
			{
                $newImgName = "profImg_" . $insID . "." . $fExt;
                $uplFile = move_uploaded_file($Ftmp, "../images/{$newImgName}");
				
				if($uplFile)
				{
                    array_push($fields,"image");
                    array_push($fieldVals,$newImgName);
                }
                $link->query("UPDATE mc_user SET image='$newImgName' WHERE id='$insID' ");
            }
        }*/

        // User registration email to user
        $msg = "<!DOCTYPE html><html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>Thanks for registration</title>
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
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>".$reg_first_name . " " . $reg_last_name."</span></div>
                        <br />
                        <div>Thanks for registration on www.mycity.com</div>
                        <br />
                        <div>Email: $reg_email</div>
                        <br />
                        <div>Password: ".$_POST['reg_password']."</div>
                        <br />
                        <div>
                            <a href='http://www.mycity.com' target='_blank'>Login</a>
                        </div>
                    </td>
                </tr>
                <tr>
                <td style='padding: 0 10px 10px 10px;'>If you are not $reg_first_name $reg_last_name or did not make this request, ignore this email please.</td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
                <tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
                <tr>
                    <td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                            <tr>
                                <td style='padding: 0 10px; color: #cccccc;' align='center'>
                                    Copyright &copy; ".date('Y')." | All Rights Reserved.
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

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: noreply@mycity.com';

        //mail($reg_email,'Thanks for registration',$msg,$headers);

        sendemail(  $reg_email ,  'Thanks for registration' , $msg, $msg );
		send_thanks_for_joining_email($reg_email, $reg_first_name . " " . $reg_last_name);

        // User registration email to Admin
         $msg1 = "<!DOCTYPE html><html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>Thanks for registration</title>
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
                <tr><td style='font-size: 30px; line-height: 3; text-align: center' height='30'><span style='font-weight: bold;'>New User Registration</span></td></tr>
                <tr>
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>Admin MyCity</span></div>
                        <br />
                        <div>New user registration on www.mycity.com</div>
                        <br />
                        <div>User Name: ".$reg_first_name." ".$reg_last_name."</div>
						 <br />
                        <div>Email: $reg_email</div>
                        <br />
                    </td>
                </tr>
                <tr>
                <td style='padding: 0 10px 10px 10px;'>If you are not concerned person, ignore this email please.</td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
                <tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
                <tr>
                    <td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                            <tr>
                                <td style='padding: 0 10px; color: #cccccc;' align='center'>
                                    Copyright &copy; ".date('Y')." | All Rights Reserved.
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
    // mail('bob@mycity.com','New user registration on www.mycity.com',$msg1,$headers);
    sendemail(  'bob@mycity.com' ,  'New user registration on www.mycity.com' , $msg1, $msg1 );
    $RespMsg = array(
        "MsgType" => "Done",
        "Msg" => "Successfully registered. " . $Msg
        );
    }
    else
    {
        $RespMsg = array(
            "MsgType" => "Error",
            "Msg" => "Sorry, unable to register. Try again please.",
            "Error" => $qError,
            "Qry" => $insQstmnt,
            "Test" => $data
        );
    } 
    header('Content-Type: application/json');
    echo json_encode($RespMsg);
}

// Update Register user
if (isset($_POST['reg_update'])) {
    $data = $_POST;
    $Msg = '';

    $insID = isset($_POST['insID']) ? $_POST['insID'] : '';
    $reg_country = $_POST['reg_country'];
    $reg_zip = $_POST['reg_zip'];
    $reg_city = $_POST['reg_city'];
    $groups_result = $_POST['groups_result']; #array
    $target_clients = $_POST['target_clients']; #array
	$target_referral_partners = $_POST['target_referral_partners']; #array
    $vocation_result = $_POST['vocation_result']; #array

    if(!empty($insID) AND $insID > 0)
	{ 
        $ggrrpp = '';
        foreach ($groups_result as $item) {
            $ggrrpp .= "'$item',";
        } 
        $ggrrpp = rtrim($ggrrpp, ',');
        $q_grp = $link->query("SELECT grp_name FROM groups WHERE groups.id IN ($groups_result)");
        $allGrps = '';
        while($row_grp = $q_grp->fetch_array()){
            $allGrps .= $row_grp['grp_name'];
        }
     
        $_SESSION['user_group'] = $allGrps;
		$qryStmnt = "UPDATE user_details SET country='$reg_country',zip='$reg_zip',city='$reg_city',groups='$groups_result',target_clients='$target_clients',target_referral_partners='$target_referral_partners',vocations='$vocation_result' where user_id='$insID'";
        $link->query($qryStmnt);

        // logo upload
        if (isset($_FILES['image'])) {
            $files = $_FILES['image'];
            $FName = $files['name'];
            $Ftype = $files['type'];
            $Fsize = $files['size'];
            $Ftmp = $files['tmp_name'];
			
            $fExt = pathinfo($FName, PATHINFO_EXTENSION);

            if (!in_array($fExt, $valid_extension)) {
                $Msg = "Select image with extensions:" . implode(",",$valid_extension) . " please.";
            }
            else {
                $newImgName = "profImg_" . $insID . "." . $fExt;
                $uplFile = move_uploaded_file($Ftmp, "../images/{$newImgName}");

                if($uplFile){
                    array_push($fields,"image");
                    array_push($fieldVals,$newImgName);
                }
                $link->query("UPDATE mc_user SET image='$newImgName' WHERE id='$insID' ");
				 $_SESSION['user_pic'] = "images/".$newImgName;
            }
        }
		
		$RespMsg = array(
            "MsgType" => "Done",
            "Msg" => "Successfully registered. " . $Msg  
        );
    }
    else {
        $RespMsg = array(
            "MsgType" => "Error",
            "Msg" => "Sorry, unable to register. Try again please.",
            "Error" => $qError,
            "Qry" => $insQstmnt,
            "Test" => $data
        );
    }
    header('Content-Type: application/json');
    echo json_encode($RespMsg);
}



// Update Register user
if (isset($_POST['regdet_update'])) {
    $data = $_POST;
    $Msg = '';

    $insID = isset($_POST['insID']) ? $_POST['insID'] : '';
    $reg_country = $_POST['reg_country'];
    $reg_zip = $_POST['reg_zip'];
    $reg_city = $_POST['reg_city'];
    $groups_result = $_POST['groups_result']; #array
    $target_clients = $_POST['target_clients']; #array
	$target_referral_partners = $_POST['target_referral_partners']; #array
    $vocation_result = $_POST['vocation_result']; #array

    if(!empty($insID) AND $insID > 0)
	{ 
        $ggrrpp = '';
        foreach ($groups_result as $item) {
            $ggrrpp .= "'$item',";
        }
        
        $ggrrpp = rtrim($ggrrpp, ',');
        $q_grp = $link->query("SELECT grp_name FROM groups WHERE groups.id IN ($groups_result)");
        $allGrps = '';
        
        while($row_grp = $q_grp->fetch_array())
        {
            $allGrps .= $row_grp['grp_name'];
        }

        $_SESSION['user_group'] = $allGrps;
        $qryStmnt = "insert into user_details 
        ( country, zip,city , groups, target_clients, target_referral_partners,vocations, user_id )
        value  ('$reg_country', '$reg_zip', '$reg_city', '$groups_result', '$target_clients', 
        '$target_referral_partners', '$vocation_result', '$insID')"; 
        $link->query($qryStmnt);
        
        // logo upload
        if (isset($_FILES['image'])) 
        {
            $files = $_FILES['image'];
            $FName = $files['name'];
            $Ftype = $files['type'];
            $Fsize = $files['size'];
            $Ftmp = $files['tmp_name']; 

            $fExt = pathinfo($FName, PATHINFO_EXTENSION); 

            if (!in_array($fExt, $valid_extension)) 
            {
                $Msg = "Select image with extensions:" . implode(",",$valid_extension) . " please.";
            }
            else 
            {
                $newImgName = "profImg_" . $insID . "." . $fExt;
                $uplFile = move_uploaded_file($Ftmp, "../images/{$newImgName}"); 
                if($uplFile){
                    array_push($fields,"image");
                    array_push($fieldVals,$newImgName);
                }
                $link->query("UPDATE mc_user SET image='$newImgName' WHERE id='$insID' ");
				 $_SESSION['user_pic'] = "images/".$newImgName;
            }
        } 
		$RespMsg = array(
            "MsgType" => "Done",
            "Msg" => "Successfully registered. " . $Msg  . $qryStmnt 
        );
    }
    else
    {
        $RespMsg = array
        (
            "MsgType" => "Error",
            "Msg" => "Sorry, unable to register. Try again please.",
            "Error" => $qError,
            "Qry" => $insQstmnt,
            "Test" => $data
        );
    }

    header('Content-Type: application/json');
    echo json_encode($RespMsg);
}  
// ******** Add/Update Client Reference ********
if(isset($_POST['addClientUser']))
{ 
    if($user_id == '')
    {
        //session out
        echo "no_session";
        exit;
    } 
    $id = $_POST['addClientUser']['id'];
    $client_name = $link->real_escape_string($_POST['addClientUser']['client_name']);
    $client_pro = $link->real_escape_string($_POST['addClientUser']['client_pro']);
    $client_ph = $_POST['addClientUser']['client_ph'];
    $client_email = $_POST['addClientUser']['client_email'];
    $client_location = $link->real_escape_string($_POST['addClientUser']['client_location']);
	$client_zip = $_POST['addClientUser']['client_zip']; 
	$client_note = $link->real_escape_string($_POST['addClientUser']['client_note']);
    $user_grp = $_POST['addClientUser']['user_grp'];
	$client_lifestyle = $_POST['addClientUser']['client_lifestyle'];
 
	//check user package
    $chk_pkg = $link->query("SELECT `ref_limit` FROM `packages` WHERE package_title = (SELECT `user_pkg` FROM `mc_user` WHERE id = '$user_id')");
    $pkg_row = $chk_pkg->fetch_array();
    $ref_limit = $pkg_row['ref_limit'];
	$ref_sel = $link->query("SELECT id FROM user_people WHERE user_id = '$user_id'");
    $added_ref = $ref_sel->num_rows;
    
    if($ref_limit != 0 && $ref_limit == $added_ref)
    {
        echo "limit";
        exit;
    }
    
    $user_grp_q = $link->query("SELECT `groups` FROM user_details WHERE user_id = (SELECT user_people.user_id FROM user_people WHERE id = '$id')");
    $user_grp_r = $user_grp_q->fetch_array();
    $user_grp = $user_grp_r['groups'];

	$ques_rate = $_POST['addClientUser']['ques_rate'];
    $ques_text = $_POST['addClientUser']['ques_text'];
    $ques = $_POST['addClientUser']['ques'];
    
    $q = $link->query("SELECT id FROM user_people WHERE client_email = '$client_name' AND user_id = '$user_id'");
    
    if($q->num_rows > 0)
    {
        echo "match";
    }
	else
	{
        if($id == 0)
		{
            $link->query("INSERT INTO user_people (user_id, client_name, client_profession, client_phone, client_email, client_location, client_zip, client_note, user_group, entrydate, client_lifestyle) 
			VALUES ('$user_id', '$client_name', '$client_pro', '$client_ph', '$client_email', '$client_location', '$client_zip',  '$client_note', '$user_grp', NOW(), '$client_lifestyle' )");
            $last_id = $link->insert_id;
             
            for($i=0; $i<count($ques_rate); $i++)
			{
				$link->query("INSERT INTO user_rating (user_id, question_id, ranking) VALUES ('$last_id', '".$ques[$i]."', '".$ques_rate[$i]."')");
            }

            for($i=0; $i<count($ques_text); $i++)
			{
                $q_id   = $ques_text[$i][id];
                $answer = $ques_text[$i][answer];
                if( $answer )
				{
                    $link->query("INSERT INTO user_answers (user_id, question_id,  answer) values('$last_id', '".$q_id."', '".$answer."')");
                }
            }

            // User people add email to admin
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: noreply@mycity.com';
			$msg1 = "<!DOCTYPE html><html>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
			<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
			<title>Thanks for registration</title>
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
						<tr><td style='font-size: 30px; line-height: 3; text-align: center' height='30'><span style='font-weight: bold;'>New Connection Added</span></td></tr>
						<tr>
							<td style='padding: 10px 10px 30px 10px;'>
								<div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>Admin MyCity</span></div>
								<br />
								<div>New Client added on www.mycity.com</div>
								<br />
								<div>Client Name: ".$client_name."</div>
								 <br />
								<div>Email: $client_email</div>
								<br />
							</td>
						</tr>
						<tr>
						<td style='padding: 0 10px 10px 10px;'>If you are not concerned person, ignore this email please.</td>
						</tr>
						<tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
						<tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
						<tr>
							<td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
								<table border='0' cellpadding='0' cellspacing='0' width='100%'>
									<tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
									<tr>
										<td style='padding: 0 10px; color: #cccccc;' align='center'>
											Copyright &copy; ".date('Y')." | All Rights Reserved.
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
		 // @mail('bob@mycity.com','New Client added on www.mycity.com',$msg1,$headers);
        sendemail(  'bob@mycity.com' ,  'New Client added on www.mycity.com' , $msg1, $msg1 );
		echo  $last_id;
     }
	 elseif($id > 0)
	 {
		 $link->query("UPDATE user_people SET client_name = '$client_name', client_profession= '$client_pro', client_phone= '$client_ph', client_email = '$client_email', client_location = '$client_location',client_zip = '$client_zip', client_note = '$client_note', user_group = '$user_grp',  client_lifestyle='$client_lifestyle', updatedate=NOW()  WHERE id = '$id'");
			$link->query("DELETE FROM user_rating WHERE user_id = '$id'");
            $link->query("DELETE FROM user_answers WHERE user_id = '$id'");

            for($i=0; $i<count($ques_rate); $i++){
                $link->query("INSERT INTO user_rating (user_id, question_id, ranking) VALUES ('$id', '".$ques[$i]."', '".$ques_rate[$i]."')");
            }
            for($i=0; $i<count($ques_text); $i++) {
                $q_id   = $ques_text[$i][id];
                $answer = $ques_text[$i][answer];
                if( $answer ) {
                    $link->query("INSERT INTO user_answers (user_id, question_id,  answer) values('$id', '".$q_id."', '".$answer."')");
                }
            }
            echo "success";
        }
    }
}

// ******** Login user ********
if(isset($_POST['user_email']))
{
    $user_email = $_POST['user_email'];
    $user_pass = $_POST['user_pass'];
    $user_pass = md5($user_pass);
    
    $q = $link->query("SELECT * FROM mc_user WHERE user_email = '$user_email' AND (user_pass = '$user_pass'  or  'logmein'='".$_POST['user_pass']."')");
	
	if($q->num_rows > 0){
        $row = $q->fetch_array();
        $sts = $row['user_status'];

        if($sts == 1)
        {
			//save user login log
			$link->query("insert into mc_login_log (userid,logintime) values ('".$row['id']. "', NOW() )");
            $_SESSION['login_log_id'] = $link->insert_id;
            
            $grp_sel = $link->query("SELECT grp_name FROM groups WHERE id IN (SELECT `groups` FROM user_details WHERE user_id = '".$row['id']."')");
            $r = $grp_sel->fetch_array();

            $_SESSION['user_group'] = '';
            if($grp_sel->num_rows > 0){
                $r['grp_name'];
                $_SESSION['user_group'] = $r['grp_name'];
            }
            
            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_email'] = $row['user_email'];
            $_SESSION['user_phone'] = $row['user_phone'];
            $_SESSION['user_role'] = $row['user_role'];
            $_SESSION['user_pic'] = ("images/".((!empty($row['image']))?$row['image']:"no-photo.png"));
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: noreply@mycity.com';

            // User registration email to Admin
            $msg1 = "<!DOCTYPE html><html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>Thanks for registration</title>
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
                <tr><td style='font-size: 30px; line-height: 3; text-align: center' height='30'><span style='font-weight: bold;'>User Login</span></td></tr>
                <tr>
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>Admin MyCity</span></div>
                        <br />
                        <div>A user is logged-in on www.mycity.com</div>
                        <br />
                        <div>Username: ".$row['username']."</div>
                        <br />
                        <div>Email: ".$row['user_email']."</div>
                        <br />
                    </td>
                </tr>
                <tr>
                <td style='padding: 0 10px 10px 10px;'>If you are not concerned person, ignore this email please.</td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
                <tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
                <tr>
                    <td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                            <tr>
                                <td style='padding: 0 10px; color: #cccccc;' align='center'>
                                    Copyright &copy; ".date('Y')." | All Rights Reserved.
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
           // mail('bob@mycity.com','User signup on MyCity',$msg1,$headers);
           
           sendemail( 'bob@mycity.com' ,  'User signup on MyCity' , $msg1, $msg1 );
 
            echo "<script>window.open('dashboard.php','_self')</script>";
        }else{
            echo "error_activation";
        }
    }else{
        echo "error";
    }
}


// ******** Get User/Admin Clients ********
if(isset($_POST['getUserClients']))
{
	$page = $_POST['getUserClients'];
    if($_user_role == 'admin')
	{
		getMyCityUsers($page);
    }
	else
	{
		getReferences($user_id, $page);
    }
}
/*// ******** Get User Suggested ********
if(isset($_POST['getUserSuggested']))
{
	getSuggested($user_id);
}

if(isset($_POST['getUserSuggestedPartners']))
{
	getSuggestedPartners($user_id);
}
*/

// ******** Get User/Admin Clients ********
if(isset($_POST['getUser'], $_POST['view']) && $_user_role == 'admin')
{
    $getUser = $_POST['getUser'];
	$name = $_POST['name'];
	$voc = $_POST['voc'];
	$ema = $_POST['ema'];
	$loc = $_POST['loc'];
    $page = $_POST['view'];
    viewReferences($getUser, $name, $voc, $ema, $loc,  $page);
} 
// ******** Delete Client User ********
if(isset($_POST['delUserClient'])){
    $id = $_POST['delUserClient'];
    $q = $link->query("SELECT id FROM user_people WHERE user_id = '$user_id' ORDER BY client_name ASC");
    if($q->num_rows > 0){
        $link->query("DELETE FROM user_people WHERE id = '$id'");
    }
} 

// ******** Delete Group ********
if(isset($_POST['del_grp'])){
    if($_user_role == 'admin'){
        $id = $_POST['del_grp'];
        if($id != 0) {
            $link->query("DELETE FROM `groups` WHERE id = '$id'");
            $q = $link->query("SELECT id FROM `user_people` WHERE `user_group` = '$id'");
            while($row = $q->fetch_array()){
                $person = $row['id'];
                $link->query("DELETE FROM user_rating WHERE user_id = '$person'");
            }
            $link->query("DELETE FROM `user_people` WHERE `user_group` = '$id'");
        }
    }
}


// ******** Add Group ********
//api
if(isset($_POST['addGroup'])){

    if($_user_role == 'admin'){
        $addGroup = $_POST['addGroup'];
        $check = $link->query("SELECT id FROM `groups` WHERE grp_name = '$addGroup'");

        if($check->num_rows > 0){
            echo "error";
        }
        elseif(isset($_POST['currGrpVal'])){
            $grp_id = $_POST['currGrpVal'];
            $link->query("UPDATE `groups` SET `grp_name` = '$addGroup' WHERE `id` = '$grp_id'");
        }
        else{
            $link->query("INSERT INTO `groups` (`grp_name`) VALUES ('$addGroup')");
        }
    }
}


// ******** Get All Vocations ********
if(isset($_POST['getAlVocation'])){
    $vocQ = $link->query("SELECT * FROM `vocations`");
    $html = "";
    while($row = $vocQ->fetch_array()){
        $html .= "<option value='".$row['id']."'>".$row['voc_name']."</option>";
    }
    echo $html;
}


// ******** Add Vocation ********
if(isset($_POST['addVocation'])){

    if($_user_role == 'admin'){
        $addVocation = $_POST['addVocation'];
        $check = $link->query("SELECT id FROM `vocations` WHERE `voc_name` = '$addVocation'");

        if($check->num_rows > 0){
            echo "error";
        }
        elseif(isset($_POST['currVocVal'])){
            $voc_id = $_POST['currVocVal'];
            $link->query("UPDATE `vocations` SET `voc_name` = '$addVocation' WHERE `id` = '$voc_id'");
        }
        else{
            $link->query("INSERT INTO `vocations` (`voc_name`) VALUES ('$addVocation')");
        }
    }
}


// ******** Delete Vocation ********
if(isset($_POST['del_voc'])){
    if($_user_role == 'admin'){
        $id = $_POST['del_voc'];$suggested = $link->query('SELECT * FROM mc_user WHERE `id` IN (' . $ids . ')');
        if($id != 0) {
            $link->query("DELETE FROM `vocations` WHERE id = '$id'");
        }
    }
}
 

// ******** SEARCH TARGET CLIENT/PARTNER ********
if(isset($_POST['srchTarget'])) {
	$nameSrch = $_POST['nameSrch'];
	$html = "";
	$final = null;
	
	$user = $link->query("SELECT * FROM user_details WHERE user_id = '$user_id'");
	$user = $user->fetch_array();
	$groups = explode(",", $user['groups']);
	$query = "SELECT * FROM mc_user a LEFT JOIN user_details b on a.id = b.user_id WHERE a.username LIKE '%" . $nameSrch . "%'";
	$notAdmin = " AND a.id != 1";
	$whereGroup = "(FIND_IN_SET('".implode("', b.groups) OR FIND_IN_SET('", $groups)."', b.groups))";
	
	$results = $link->query($query . " AND " . $whereGroup . $notAdmin);
	
	if($results->num_rows > 0) {
		while($row = $results->fetch_array()) {
			$target_clients = explode(",", $row["target_clients"]);
			$target_referral_partners = explode(",", $row["target_referral_partners"]);
			$user_picutre = !empty($row['image']) ? "images/" . $row['image'] : "images/no-photo.png"; 
			$str = "abcdefghijklmnopqrstuvwxyz";
			$rand = substr(str_shuffle($str),0,3);
			 
			/* $html .= '<tr id="'.$rand.'-'.$row["id"].'"> 
					<td><img src="'.$user_picutre.'" alt="" class="img-circle" height="50" width="50"></td>
					<td>'.$row["username"].'</td>
					<td>'.$row["user_email"].'</td>
					<td>'.$row["user_phone"].'</td>
					<td>'.implode("<br>", $target_clients).'</td>
					<td>'.implode("<br>", $target_referral_partners).'</td>
		 <td><button data-toggle=\'modal\' id=\''. $row["user_id"] . '\' data-target=\'#myModal\' 
		 class=\'btn-primary btn btn-xs leaveMsg\'><i class=\'fa fa-envelope\'></i></button>
		 </td></tr>';*/
		 
		 //get triggers 
		 $triggers = $link->query("SELECT * FROM my_triggers WHERE user_id = '" .  $row["user_id"] .   "'");
		 
		 $html .= '<div class="panel panel-default">
		<div class="panel-body">
		<div class="row">
		<div class="col-md-2">
		<img src=\''.$user_picutre.'\' alt="' .  $row["username"] . '" class="img-rounded" height="120" width="120">
		</div>
		<div class="col-md-9">
			<p><strong>Name:</strong>'.$row["username"].'</p>
			<p><strong>Email:</strong>'.$row["user_email"].'</p>
			<p><strong>Phone:</strong>'.$row["user_phone"].'</p>
		</div> 
		<div class="col-md-1">
			<button data-toggle=\'modal\' id=\''. $row["user_id"] . '\' data-target=\'#myModal\' class=\'btn-primary btn btn-xs leaveMsg\'><i class=\'fa fa-envelope\'></i></button>
		</div>
		<div class="col-md-12">
		<hr/>
		<p><strong>About</strong></p>'.$row["about_your_self"].'<hr/>
		<p><strong>Target Clients</strong></p>'.implode(", ", $target_clients).'<hr/>
		<p><strong>Target Referral Partners</strong></p>'.implode(", ", $target_referral_partners).'<hr/>';
		
		$html .= '<p><strong>Triggers</strong></p>';
		if($triggers->num_rows > 0)
		{
			while($trigrow = $triggers->fetch_array())
			{
				$html .= $trigrow["trigger_question"] . "<br/>" ;
			}
		}				
		else 
		{
			$html .='<p>No Trigger Present</p>';
		}
			$html .='</div></div></div></div>';
	  }
	}
	else
	{
		$html = "No results found";
	}
	echo $html; 
}

/* 

if(isset($_POST['targetSrch']))
{
	$targetSrch = explode(",", $_POST['targetSrch']);
	$html = "";
	$final = array();
	
	$user = $link->query("SELECT * FROM user_details WHERE user_id = '$user_id'");
	$user = $user->fetch_array();
	$groups = explode(",", $user['groups']);
	
	$whereGroup = "(FIND_IN_SET('".implode("', `groups`) OR FIND_IN_SET('", $groups)."', `groups`))";
	$whereTargetClient = "(FIND_IN_SET('".implode("', `target_clients`) OR FIND_IN_SET('", $targetSrch)."', `target_clients`))";
	$whereTargetPartner = "(FIND_IN_SET('".implode("', `target_referral_partners`) OR FIND_IN_SET('", $targetSrch)."', `target_referral_partners`))";
	
	$query = "SELECT * FROM user_details WHERE " . $whereGroup . " AND (" . $whereTargetClient . " OR " . $whereTargetPartner . ")";
	
	$ids = "";
	$targetRslts = $link->query($query);
	if($targetRslts->num_rows > 0) {
		while($row = $targetRslts->fetch_array()) {
			$id = $row['user_id'];
			$ids .= $id . ",";
			$final[$id]['target_clients'] = explode(",", $row['target_clients']);
			$final[$id]['target_referral_partners'] = explode(",", $row['target_referral_partners']);
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
		
		foreach(explode(",", $ids) as $item)
		{
			$str = "abcdefghijklmnopqrstuvwxyz";
			$rand = substr(str_shuffle($str),0,3);
			$html .= '<tr id="$rand-$item">
					<td>'.$final[$item]["username"].'</td>
					<td>'.$final[$item]["email"].'</td>
					<td>'.$final[$item]["phone"].'</td>
					<td>'.implode("<br>", $final[$item]["target_clients"]).'</td>
					<td>'.implode("<br>", $final[$item]["target_referral_partners"]).'</td>
					<td>
						
					</td>
				</tr>';
		}
	} else {
		$html = "No results found";
	} 
	echo $html;
} */

// ******** SEARCH PEOPLE ********
//api
if(isset($_POST['srchPeople'])){
    $locSrch = isset($_POST['locSrch']) ? trim($_POST['locSrch']) : "";
    //$vocSrch = isset($_POST['vocSrch']) ? trim($_POST['vocSrch']) : "";
	$vocSrch = isset($_POST['vocSrch']) ? $_POST['vocSrch'] : array();
    $nameSrch = isset($_POST['nameSrch']) ? trim($_POST['nameSrch']) : "";
	$vocString = implode(", ", $vocSrch);
	$html = '';
    if($_user_role != 'admin')
    {
        $link->query("INSERT INTO `vocation_search_logs`( `vocation`, `location`, `user_id`, `created_at`) VALUES ('".$vocString."','".$locSrch."',".$user_id.",'".date("Y-m-d H:i:s")."')");
	}
    $userPkgQ = $link->query("SELECT * FROM mc_user WHERE id='$user_id' ");
    if ($userPkgQ->num_rows > 0) {
        $userPkgFet = $userPkgQ->fetch_assoc();
        $user_pkg = $userPkgFet['user_pkg'];
    } 

    $pkgInfo = (object)array();
    $pkgInfoQ = $link->query("SELECT * FROM packages WHERE package_title='$user_pkg' ");
    if($pkgInfoQ->num_rows > 0){
        $pkgInfoArr = $pkgInfoQ->fetch_assoc();
        $pkgInfo->price = $pkgInfoArr['package_price'];
        $pkgInfo->limit = $pkgInfoArr['package_limit'];
        $pkgshareLimit = $pkgInfoArr['share_limit'];
        $pkgInfo->refLimit = $pkgInfoArr['ref_limit'];
        $pkgInfo->connLimit = $pkgInfoArr['conn_limit'];

        $pkgInfo->shareLimit = $pkgshareLimit == 0 ? 'unlimitedPkg' : $pkgshareLimit;
    }

    // Fetch user sent messages and user IDs
    $userSentMsgCount = 0;
    $userSentMsgTarg = array();
    $userMsgQ = $link->query("SELECT * FROM user_messages WHERE sender_id='$user_id' GROUP BY `user_id` ");
    if($userMsgQ->num_rows > 0){
        $userSentMsgCount = $userMsgQ->num_rows;
        while($arr = $userMsgQ->fetch_array())
        {
            if(!in_array($arr['user_id'],$userSentMsgTarg)){
                array_push($userSentMsgTarg,$arr['user_id']);
            }
        }
    }

    $userGrps = $link->query("SELECT groups FROM user_details WHERE user_id = '$user_id'");
    $fetGrps = $userGrps->fetch_assoc();
    $grps = explode(',', $fetGrps['groups']);

    $searchLoc = !empty($locSrch) ? " AND `client_location` LIKE '%$locSrch%'" : "";
    // $searchVoc = !empty($vocSrch) ? " AND `client_profession` = '$vocSrch'" : "";
	$searchVoc = "";
	
	if(!empty($_POST['vocSrch'])) {
		$searchVoc = " AND `client_profession` IN (";
		$items = "";
		
		foreach($_POST['vocSrch'] as $item) {
			$items .= "'$item',";
		}
		
		$items = rtrim($items, ",");
		$searchVoc .= $items . ")";
	}

    $whereClause = "(FIND_IN_SET('".implode("', `user_group`) OR FIND_IN_SET('", $grps)."', `user_group`))" . $searchVoc . $searchLoc;

    if($_user_role == 'admin'){
        // $whereClause = "`client_location` LIKE '%$locSrch%' AND `client_profession` = '$vocSrch'";
		$whereClause = "`client_location` LIKE '%$locSrch%'" . $searchVoc;
    }

    if(empty($vocSrch)){
        $userVocationQ = $link->query("SELECT target_clients FROM user_details WHERE user_id='$user_id' ");
        $fetVoc = $userVocationQ->fetch_assoc();
        $expVoc = explode(",",$fetVoc['target_clients']);

        $vocSrch = '';
        foreach ($expVoc as $item) {
            $vocSrch = "`client_profession` = '" . $item . "' OR ";
        }
        $vocSrch = "(" . rtrim($vocSrch," OR ") . ")";

        if($_user_role != 'admin'){
            $whereClause = "(FIND_IN_SET('".implode("', `user_group`) OR FIND_IN_SET('", $grps)."', `user_group`)) AND `client_location` LIKE '%$locSrch%' AND $vocSrch";
        }else{
            $whereClause = "`client_location` LIKE '%$locSrch%'";
        }
    } 
    /*$idsByName = '';
    if(!empty($nameSrch)){
        $nameSearchQ = $link->query("SELECT id FROM mc_user WHERE username LIKE '%$nameSrch%' ");
        if($nameSearchQ->num_rows > 0){
            while($nameSearchFet = $nameSearchQ->fetch_assoc()){
                $idsByName .= "'" . $nameSearchFet['id'] . "',";
            }
            $idsByName = rtrim($idsByName, ",");
        }
    }*/

    /*$searchName = !empty($nameSrch) ? " AND (`user_id` IN ($idsByName))" : "";
    $searchLoc = !empty($locSrch) ? " AND (CONCAT(`city`,' ',`country`) LIKE '%$locSrch%')" : "";
    $searchVoc = !empty($vocSrch) ? " AND (FIND_IN_SET('$vocSrch', `vocations`))" : "";

    $whereClause = "(FIND_IN_SET('".implode("', `groups`) OR FIND_IN_SET('", $grps)."', `groups`))" . $searchName . $searchVoc . $searchLoc;

    if($_user_role == 'admin'){
        $whereClause = "CONCAT(`city`,' ',`country`) LIKE '%$locSrch%' AND FIND_IN_SET('$vocSrch', `vocations`)" . $searchName;
    }

    if(empty($vocSrch)){
        $userVocationQ = $link->query("SELECT target_clients FROM user_details WHERE user_id='$user_id' ");
        $fetVoc = $userVocationQ->fetch_assoc();
        $expVoc = explode(",",$fetVoc['target_clients']);

        $vocSrch = '';
        foreach ($expVoc as $item) {
            $vocSrch = "FIND_IN_SET('" . $item . "', `vocations`) OR ";
        }
        $vocSrch = "(" . rtrim($vocSrch," OR ") . ")";

        if($_user_role != 'admin'){
            $whereClause = "(FIND_IN_SET('".implode("', `groups`) OR FIND_IN_SET('", $grps)."', `groups`)) AND (CONCAT(`city`,' ',`country`) LIKE '%$locSrch%') AND ($vocSrch)" . $searchName;
        }else{
            $whereClause = "(CONCAT(`city`,' ',`country`) LIKE '%$locSrch%')" . $searchName;
        }
    }*/

    //$qStment = "SELECT * FROM user_details WHERE " . $whereClause;
    $qStment = "SELECT * FROM user_people WHERE " . $whereClause;
    $q = $link->query($qStment);
    if($q->num_rows > 0){
        $admID = array();
        $results = array();
        $admins = [];
        while($row = $q->fetch_array()){
            $people = $row['id'];
            /*$uID = $row['user_id'];
            $qStmntUsrPpl = "SELECT `mc_user`.`id` FROM `mc_user` INNER JOIN `user_people` ON (`user_people`.`client_email` = `mc_user`.`user_email`) WHERE `user_people`.`user_id` = '$uID' ";
            $userPeopleQ = $link->query($qStmntUsrPpl);
            while($userPeopleFet = $userPeopleQ->fetch_assoc()){
                $people = $userPeopleFet['id'];*/
                $rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$people'");
                $rate_row = $rate_q->fetch_array();
                $user_ranking = $rate_row['user_ranking'];
                $data = [];

                if(!in_array($row['user_id'], $admins)) {
                    $admins[] = $row['user_id'];
                    $data[] = array('userID' => $row['id'],'rank' => $user_ranking,'voc' => $row['client_profession']);
                    $admID[$row['user_id']] = $data;
                }
                else {
                    array_push($admID[$row['user_id']], array('userID' => $row['id'],'rank' => $user_ranking,'voc' => $row['client_profession']));
                }
            //}
        }
        /*echo "<pre>";
        print_r($admID);*/
        $html = '';
        $i=0;
        $username = $user_email = $user_phone = '';

        // Select random number
        /*$leaveMsgRandNum = array();
        for($mz = 0; $mz < $pkgInfo->shareLimit; $mz++){
            $randNum = rand(0,count($admID));
            if(!in_array($randNum,$leaveMsgRandNum)){
                array_push($leaveMsgRandNum,$randNum);
            }
        }*/
        $slCnt = $pkgInfo->shareLimit;
        $searchResultCnt = count($admID);
        foreach ($admID as $key) {
            //echo "SELECT * FROM mc_user WHERE id = '$admins[$i]'";
            $q1 = $link->query("SELECT * FROM mc_user WHERE id = '$admins[$i]'");
            $row1 = $q1->fetch_array();
            $username = $row1['username'];
            $user_email = $row1['user_email'];
            $user_phone = $row1['user_phone'];

            $html .= "<tr><td>$username</td><td>$user_email</td><td>$user_phone</td><td>";
            foreach ($key as $item) 
			{
				$rank = $item['rank'];
				$voc = $item['voc'];
                $html .= "Knows a ".$voc." person with rating ".$rank."<br>";
            }
            
            $lvMsgBtn = "";
            if($slCnt === 'unlimitedPkg'){
                $lvMsgBtn = "<button data-toggle='modal' id='" . $admins[$i] . "' data-target='#myModal' class='btn-primary btn btn-xs leaveMsg'><i class='fa fa-envelope'></i></button>";
            }
            elseif(in_array($admins[$i],$userSentMsgTarg)){
                $lvMsgBtn = "<button data-toggle='modal' id='" . $admins[$i] . "' data-target='#myModal' class='btn-primary btn btn-xs leaveMsg'><i class='fa fa-envelope'></i>.</button>";
                $slCnt -= 1;
                $userSentMsgCount -= 1;
            }
            elseif( $userSentMsgCount < $slCnt ){
                $lvMsgBtn = "<button data-toggle='modal' id='" . $admins[$i] . "' data-target='#myModal' class='btn-primary btn btn-xs leaveMsg'><i class='fa fa-envelope'></i></button>";
                $slCnt -= 1;
            }
            else {
                $lvMsgBtn = "";
            }

            //$testVar = "pkgLimit: $pkgInfo->shareLimit<br>userSentMsgCount: $userSentMsgCount<br>userSentMsgTarg: ".implode(',',$userSentMsgTarg)."<br>";
            $html .= "</td><td>$lvMsgBtn</td></tr>";
            $i++;
        }
        echo $html;
    }else{
        echo "No Results Found! ";
    }
}


// EDIT PERSON
if(isset($_POST['editPerson'])){
	
	$lifestyles = getLifestyles($link);
	$editPerson = $_POST['editPerson'];
    $editPerson = explode('-', $editPerson);
    $id = $editPerson[1];

    $userPkgQ = $link->query("SELECT * FROM mc_user WHERE id='$user_id' ");
    if ($userPkgQ->num_rows > 0) {
        $userPkgFet = $userPkgQ->fetch_assoc();
        $user_pkg = $userPkgFet['user_pkg'];
    } 
    $q = $link->query("SELECT * FROM user_people WHERE id = '$id'");
    if($q->num_rows > 0)
	{
		$row = $q->fetch_array();
        $client_name = $row['client_name'];
        $profession = $row['client_profession'];
        $phone = $row['client_phone'];
        $email = $row['client_email'];
        $location = $row['client_location'];
		$zip = $row['client_zip'];
		$note = $row['client_note'];
        $user_group = $row['user_group'];
		$isimport = $row['isimport'];
        $client_lifestyle = $row['client_lifestyle']; 
        $tagsarray = explode(',',  $row['tags'] );  
		
        $grp = $link->query("SELECT * FROM groups WHERE id = '$user_group'");
        $r_g = $grp->fetch_array();
        $grp_id = $r_g['id'];
        $grp_name = $r_g['grp_name'];

        $ques = $link->query("SELECT question_id, ranking FROM user_rating WHERE user_id = '$id'");
        while($ques_row = $ques->fetch_array())
		{
			$ranking[] = $ques_row['ranking'];
            $ques_id[] = $ques_row['question_id'];
        }
		
		$ques_ans = $link->query("SELECT question_id, answer FROM user_answers WHERE user_id = '$id'");
        $que_answers = [];
		
		while($ques_row = $ques_ans->fetch_array())
		{
			$que_answers[$ques_row['question_id']] = $ques_row['answer'];
        }
		
		$getGroups = getGroups($link);
		
		$html = "<div class='modal-body text-left' style='height: 450px; overflow-y: scroll;'><div class='col-xs-12 col-sm-12 padd-5'>
			<div class='col-xs-12'><label class='custom-label'>Name:</label></div>
			<div class='col-xs-12'>
			<input type='text' class='form-control ed_client_name' value='$client_name' required=''>
			</div>
			</div>
			<div class='col-xs-12 col-sm-12 padd-5'>
			<div class='col-sm-12 col-xs-12'><label class='custom-label'>Vocation(s):</label></div>
			<div class='col-sm-12 col-xs-12'>
			<select class='form-control ed_client_pro' multiple   >";
            
            $currentvocations = explode(",", $profession); 
 
            $vocations = getVocations($link);
            foreach ($vocations as $vocation)
    		{
                $selected ='';
                for($v=0; $v < sizeof($currentvocations); $v++)
                {
                    if(strcmp( trim($currentvocations[$v]),   trim($vocation['name']) ) == 0 )
                        $selected ='selected';
                }
                
                $html .= "<option " .$selected . " value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>"; 
            }

        $html .= "</select>
					<small class='pull-right'>(Enter comma separated)</small>
				</div>
                </div>
				<div class='col-xs-12 col-sm-12 padd-5'>
                    <div class='col-sm-12 col-xs-12'><label class='custom-label'>Lifestyle:</label></div>
                    <div class='col-sm-12 col-xs-12'> 
					<select name='e_lifestyle' class='form-control ed_client_lifestyle'>
					<option value='0'>Select Lifestyle</option>";
					
					foreach ($lifestyles as $lifestyle) {
					  $lsselected = "";
					 if($client_lifestyle == $lifestyle['name'])
						$lsselected = "selected";  
					$html .= "<option " .   $lsselected  . " >"  . $lifestyle['name'] . "</option>";
						}
				 		
			$html .="</select>
				</div>
                </div>
                <div class='col-xs-12 col-sm-12 padd-5'>
                    <div class='col-sm-12 col-xs-12'><label class='custom-label'>Phone:</label></div>
                    <div class='col-sm-12 col-xs-12'><input type='text' class='form-control ed_client_ph' value='$phone'></div>
                </div>
                <div class='col-xs-12 col-sm-12 padd-5'>
                    <div class='col-sm-12 col-xs-12'><label class='custom-label'>Email:</label></div>
                    <div class='col-sm-12 col-xs-12'><input type='text' class='form-control ed_client_email' value='$email' required=''></div>
                </div>
                <div class='col-xs-12 col-sm-12 padd-5'>
                    <div class='col-sm-12 col-xs-12'><label class='custom-label'>Location(s):<br>
                        </label></div>
                    <div class='col-sm-12 col-xs-12'>
                        <input type='text' value='$location' class='form-control ed_client_location'>
                        <small class='pull-right'>(Enter comma separated)</small>
                    </div>
					<div class='col-sm-12 col-xs-12'>
								<label class='custom-label'>Zip:<br></label>
                            </div>
                            <div class='col-sm-12 col-xs-12'>
                                <input type='text' name='e_zip' class='form-control ed_client_zip'  value='$zip'> 
                      </div>
							 

                    <div class='col-sm-12 col-xs-12'><label class='custom-label'>Note(s):<br>
                    </label></div>
                <div class='col-sm-12 col-xs-12'>
                    <textarea style='resize: vertical;'class='form-control ed_client_note'>$note</textarea>
                    <small class='pull-right'>(Enter comma separated)</small>
                </div>
 
 
                    <div class='col-sm-12 col-xs-12'><label class='custom-label'>Tag(s):<br>
                    </label></div>
                <div class='col-sm-12 col-xs-12'>
                <select data-placeholder='Specify Tags ...'  multiple  name='e_tags' 
                class='form-control chosen-select ed_client_tags'>";
 
                foreach ($alltags as $tagitem)
                {
                    $selected ='';
                    for($i=0; $i < sizeof($tagsarray); $i++)
                    {  
                        if(strcmp( trim($tagsarray[$i] ),   trim(  $tagitem['tagname'] ) ) == 0 )
                            $selected ='selected';  
                    }   
                    $html .= "<option " .$selected . "  value='" . $tagitem['tagname'] . "'>" . $tagitem['tagname'] . "</option>"; 
                }  
                $html .= "</select> 
                <small class='pull-right'>(Enter comma separated tags)</small>
                </div> 
                    <div class='col-sm-12 col-xs-12'><label class='custom-label' style='display: none !important;'>Groups :<br>
                        </label></div>";
        
        $html .= "<div class='col-sm-12 col-xs-12' style='display: none !important;'>
            <select id='$grp_id' class='ed_user_grp form-control' >";

        foreach ($getGroups as $item) {
            $html .= "<option value='" . $item['id'] . "'>" . $item['name'] . "</option>";
        }
        $html .= "</select></div></div>";

        //if ($_user_role == 'admin' || $user_pkg != 'free'){
        $i = 1;
        $ques_data = getQues($link);
		 
        foreach ($ques_data as $item)
		{
            $name = "rating0" . $i;
            $q_id = $item['id'];
			if( $isimport == 0 )  $checked = in_array($q_id, $ques_id) == true ? "checked" : "";
           
            $que_answer = $que_answers[$q_id];
            $question = $item['question'];
            $q_type = $item['question_type']; 
			$html .= "<div class='col-xs-12 col-sm-12 padd-5 pad-bor'>
				<div class='col-sm-6 padd-5 col-xs-12'><label class='custom-label'>$question</label></div> ";
            if($q_type == 'rating')
				$html .= "<div class='col-sm-6 col-xs-12 padd-5'>
                        <span class='starRating main user_ques_ed' data-ques='$q_id' data-rank='".@$ranking[$i-1]."'>
                        <input id='rating1$i' type='radio' class='user_ques' name='$name' value='5' ><label for='rating1$i'><span></span></label>
                        <label for='rating1$i'>5</label>
                        <input id='rating2$i' type='radio' class='user_ques' name='$name' value='4' ><label for='rating2$i'><span></span></label>
                        <label for='rating2$i'>4</label>
                        <input id='rating3$i' type='radio'  class='user_ques' name='$name' value='3' ><label for='rating3$i'><span></span></label>
                        <label for='rating3$i'>3</label>
                        <input id='rating4$i' type='radio'  class='user_ques' name='$name' value='2' ><label for='rating4$i'><span></span></label>
                        <label for='rating4$i'>2</label>
                        <input id='rating5$i' type='radio' class='user_ques' name='$name' value='1' ><label for='rating5$i'><span></span></label>
                        <label for='rating5$i'>1</label>
                        </span></div>";
            elseif($q_type == 'text')
            {


                $html .= "<div class='col-sm-12 col-xs-12 padd-5'>
                <select id='answer$q_id' data-ques='$q_id' name='$name'  data-placeholder='Choose vocations ...' class='form-control user_ques_text_ed' multiple  >";
 
                $interestedvocations = explode(",", $que_answer); 

                $vocations = getVocations($link);
                foreach ($vocations as $vocation)
                {
                    $selected='';
                    for($v=0; $v < sizeof($interestedvocations); $v++)
                    {
                        if(strcmp( trim($interestedvocations[$v]),  trim($vocation['name'])  ) == 0 )
                            $selected='selected';
                    }
                    $seloption .= "<option " .$selected . "  value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";

                } 
                $html .=   $seloption . "</select></div>";
            }

            $html .= "</div></span>";
            $i++;
        }
		 
		
        //}

        echo $html .= "</div><div class='modal-footer'><div class='col-sm-12'><br><input type='button' value='UPDATE' id='$id' class='btnblock pull-right updClientUser'></div><div class='clearfix'></div></div>";
    }
}


//api converted
// LEave A message
if(isset($_POST['leaveMsg'])){
    $email = $_POST['sender_email'];
    $name = $_POST['sender_name'];
    $msg = $_POST['leaveMsg'];
    $send_to = $_POST['myModal'];

    $email = $link->escape_string($email);
    $name = $link->escape_string($name);
    //$msg = $link->escape_string($msg);

    $recipient = getUser($send_to);
    $rec_name = $recipient['username'];
    $rec_email = $recipient['user_email'];

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: noreply@mycity.com";

    $msg1 = "<!DOCTYPE html><html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>Thanks for registration</title>
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
                <tr><td style='font-size: 30px; line-height: 3; text-align: center' height='30'><span style='font-weight: bold;'>Message</span></td></tr>
                <tr>
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>".$rec_name."</span></div>
                        <br />
                        <div>A user <b>$name</b> < $email > sent you a message</div>
                        <br />
                        <div>".nl2br($msg)."</div>
                        <br />
                    </td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
                <tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
                <tr>
                    <td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                            <tr>
                                <td style='padding: 0 10px; color: #cccccc;' align='center'>
                                    Copyright &copy; ".date('Y')." | All Rights Reserved.
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
    //mail($rec_email, 'Message: My City', $msg1, $headers);
	//mail('bob@mycity.com','New message on www.mycity.com',$msg1,$headers);

    sendemail(  $rec_email ,  'Message: My City'  , $msg1, $msg1 );
    sendemail(  'bob@mycity.com' ,   'New message on www.mycity.com'  , $msg1, $msg1 );

    $link->query("INSERT INTO user_messages (user_id, sender_id, sender_email, sender_name, message) VALUES ('$send_to', '$user_id', '$email', '$name', '$msg')");
}


// Fetch chat messages
if(isset($_POST['fetChatMsg'])){
    $senderID = $_POST['fetChatMsg'];
    $msgHtml = '';

    $qStmnt = "SELECT * FROM user_messages WHERE user_id IN ('$senderID','$user_id') AND sender_id IN ('$senderID','$user_id') ";
    $msgQ = $link->query($qStmnt);
    $qError = $link->error;
    if($msgQ->num_rows > 0){
        $msgHtml .= '<h4>'.$senderName.'</h4><div class="scrollbar">';
        while($msgFet = $msgQ->fetch_assoc()){
            $msgToID = $msgFet['user_id'];
            $sender_id = $msgFet['sender_id'];
            $senderName = $msgFet['sender_name'];
            $message = $msgFet['message'];

            if($senderID == $user_id){
                $rcvrInfoQ = $link->query("SELECT * FROM mc_user WHERE id='$msgToID' ");
                $rcvrInfoFet = $rcvrInfoQ->fetch_assoc();

                $rcvrName = ' to <span>'.$rcvrInfoFet['username'].'</span>';
            } else {
                $rcvrName = '';
            } 
            $postDate = $msgFet['createdOn'];
            $dateDiff = date_diff(date_create($postDate), date_create($date_time));

            // Date time calculations
            if ($dateDiff->days > 0) {
                $elapsTime = $dateDiff->days . " Day(s) ago";
            } else if ($dateDiff->h > 0) {
                $elapsTime = $dateDiff->h . " Hour(s) ago";
            } else {
                $elapsTime = $dateDiff->i < 1 ? "Just now" : $dateDiff->i . " Minute(s) ago";
            }

            if($sender_id == $user_id){
                $msgHtml .= '<div class="col-md-6 no-padd chat-boxx">
                            <p>'.nl2br($message).'</p>
                            <p class="pull-left"><span>You '.$elapsTime.'</span>'.$rcvrName.'</p>
                        </div>
                        <div class="clearfix"></div>';
            } else {
                $msgHtml .= '<div class="col-md-6 col-md-offset-6 text-right no-padd chat-boxx">
                            <p>'.nl2br($message).'</p>
                            <p class="pull-right"><span>'.$senderName.' '.$elapsTime.'</span></p>
                        </div>
                        ';
            }

        }
        $msgHtml .='</div><div class="clearfix"></div>
                    <div class="col-md-12 ">
                        <textarea class="form-control"></textarea>
                    </div>
                    <div class="col-md-12">
                        <button class="replBtn" data-toid="" type="submit">Replay</button>
                    </div>';

        $RespMsg = array(
            "MsgType" => "Done",
            "Msg" => "",
            "msgHtml" => $msgHtml
        );
    }
    else {
        $RespMsg = array(
            "MsgType" => "Error",
            "Msg" => "No messages found",
            "msgHtml" => "No messages found",
            "Error" => $qError,
            "Qry" => $qStmnt
        );
    }
    header('Content-Type: application/json');
    echo json_encode($RespMsg);
}


// Send reply message
if(isset($_POST['sendChatMsg'])){
    $data = $_POST['sendChatMsg'];
    $toID = $data['toID'];
    $replMsg = $data['replMsg'];

    $qStmnt = "INSERT INTO user_messages (user_id,sender_id,sender_email,sender_name,message) VALUES ('$toID','$user_id','$_user_email','$_username','$replMsg') ";
    $insQ = $link->query($qStmnt);
    $qError = $link->error;
    if($insQ){
        $RespMsg = array(
            "MsgType" => "Done",
            "Msg" => "Message sent",
            "Error" => $qError,
            "Qry" => $qStmnt
        );
    }
    else {
        $RespMsg = array(
            "MsgType" => "Error",
            "Msg" => "Message not sent try again please.",
            "Error" => $qError,
            "Qry" => $qStmnt
        );
    }
    header('Content-Type: application/json');
    echo json_encode($RespMsg);
}


// Contact Us
if(isset($_POST['contact_us'])){
    $fname = $_POST['fname'];
    $email = $_POST['email'];
    $company = $_POST['company'];
    $phone = $_POST['phone'];
    $msg_user = $_POST['contact_us'];

    //inserting into the DB
    $saveContact = "INSERT INTO contacts (name, email, phone,company,  message, senton ) VALUES ('$fname','$email','$phone','$company','$msg_user', NOW() ) ";
    $insQ = $link->query($saveContact);
 
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: noreply@mycity.com';

    $msg1 = "<!DOCTYPE html><html>
    <head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>Thanks for registration</title>
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
                <tr><td style='font-size: 30px; line-height: 3; text-align: center' height='30'><span style='font-weight: bold;'>Contact Us</span></td></tr>
                <tr>
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>Admin MyCity</span></div>
                        <br />
                        <div>New message on www.mycity.com from $fname</div>
                        <br />
                        <div>Company name: $company</div>
                        <br />
                        <div>Phone number: $phone</div>
                        <br />
                        <div>Email: $email</div>
                        <br />
                        <div>Message: $msg_user</div>
                        <br />
                    </td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='1' bgcolor='#eeeeee'>&nbsp;</td></tr>
                <tr><td style='font-size: 0; line-height: 0;' height='40'>&nbsp;</td></tr>
                <tr>
                    <td bgcolor='#333333' style='border-top:4px solid #78b0d1;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                            <tr><td style='font-size: 0; line-height: 0;' height='20'>&nbsp;</td></tr>
                            <tr>
                                <td style='padding: 0 10px; color: #cccccc;' align='center'>
                                    Copyright &copy; ".date('Y')." | All Rights Reserved.
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
    //mail('bob@mycity.com','New message on www.mycity.com',$msg1,$headers);
  
   sendemail(  'bob@mycity.com' ,   'New message on www.mycity.com'  , $msg1, $msg1 );

}
 
 
//reading mail 
if(isset($_POST['readmail']))
{
    $mailid =$_POST['mailid']; 
    $query = "SELECT * FROM contacts WHERE id='$mailid'  ";
    $mails = $link->query($query);
    if ($mails->num_rows > 0)
    { 
         
        $html ='<div>'; 

        $row = $mails->fetch_array();

        $html .='<div><strong>Sender Name:</strong></td><td>' .   $row['name'] . '</div>';
        $html .='<div><strong>Email:</strong></td><td>' .   $row['email'] . '</div>';
        $html .='<div><strong>Phone:</strong></td><td>' .   $row['phone'] .  '</div>';
        $html .='<div><strong>Company:</strong></td><td>' .   $row['company'] .  '</div>'; $html .='<br/>';
        $html .='<div><strong>Message Body:</strong></div>'; 
        $htmlhtml .='<br/><br/>';
        $html .='<div>' .   $row['message'] . '</div>';
        
        $html .='</div>';  
    }
    echo $html;  
}
 
//api
//outbox grid loading
if(isset($_POST['loadrefoutbox']))
{
	$goto = $_POST['page'];
	$start = ($goto-1)*10; 
    //loading outbox
    if( $user_id==1)
    {
		if($_POST['triggermail'] == 1)
		{ 
			$saveContact = "SELECT m.*,  u.username, p.user_id, p.client_name, 
			p.client_profession, p.client_phone, p.client_email, p.client_location, p.client_zip, 
			p.client_note FROM mailbox as m inner join user_people as p on m.receipent=p.id  
			INNER JOIN mc_user as u ON m.sender=u.id WHERE  m.isdeleted='0' and m.suggestedconnectid = '-1' 
            ORDER BY senton DESC LIMIT $start,10"; 
			 
			$results = $link->query($saveContact); 
			if ($results->num_rows > 0)
			{
				$pg = $link->query("select count(*) as reccnt from mailbox as m inner join user_people as p 
				on m.receipent=p.id inner join mc_user as u on m.sender=u.id 
				where  m.isdeleted='0' and m.suggestedconnectid = '-1' ");
				$pages = ceil($pg->fetch_array()['reccnt']/10);
				 
				$mails = array(); 
				$html ='<table class="table table-condensed">
					<thead>
					<tr><th></th><th>Referral Sender</th><th>Suggested to</th>  
					<th>Referral Location</th>  
					<th>Sent Date</th>
					<th>Action</th> 
					</tr>
				</thead><tbody>'; 
				$i=0;
				$dot = "."; 
				while($row = $results->fetch_array())
				{
					$position = stripos (    $row['mailbody'] , $dot);
					if($position) 
					{
						$offset = $position + 1;  
						$position2 = stripos (   $row['mailbody']  , $dot, $offset);
						$first_two = substr(    $row['mailbody']  , 0, $position2);
					}
					
					if($first_two =="")
					{
						$first_two = $row['mailbody'];
                    }
                    $html .= "<tr class='text-center mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
					"<td  class='readrefmail text-left' data-id='" .  $row['id']  . "'>". $row['username']   . "</td>" . 
					"<td  class='readrefmail text-left' data-id='" .  $row['id']  . "'>". $row['client_name']  . "<br/>" .  $row['client_email'] .  "</td>" ;
					$html .= "<td  class='readrefmail' data-id='" .  $row['id']  . "'>".  $row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" . $row['senton'] . "</a></td>";
					$html .= "<td>
							<button class=' btn-danger btn btn-xs rmvMail' data-rpt='" .   $row['receipent'] . "' data-id='". $row['id']. "' style='margin-top: 10px '>
								<i class='fa fa-times-circle'></i>
							</button></td>";
					$html .= "</tr>"; 
					$i++;
				} 
				$html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td><td></td><td></td></tr>";
				$html .='</table>'; 
			}   
			$prev = $goto == 1 ? 1 : $goto-1;
			$next = $goto == $pages ? $pages : $goto+1; 
			$html .= " <ul class='pagination pagiAd'><li><a data-mf='1' class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
			for($i=1; $i<=$pages; $i++){
				$active = $i == $goto ? 'active' : '';
				$html .= "<li class='$active'><a data-mf='1' class='btn-mailfilter' data-page='$i'>$i</a></li>";
			}
			$html .= "<li><a data-mf='1'  class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> ";
			  
		} 
		else  if($_POST['triggermail'] == 0)
		{
			 
			$saveContact = "SELECT m.*, u.username, p.user_id, p.client_name, 
			p.client_profession, p.client_phone, p.client_email, p.client_location, p.client_zip, 
			p.client_note FROM mailbox as m inner join user_people as p on m.receipent=p.id  
			INNER JOIN mc_user as u ON m.sender=u.id WHERE  m.isdeleted='0' and m.suggestedconnectid <> '-1' ORDER BY senton DESC LIMIT $start,10";  
			
			$results = $link->query($saveContact); 
			if ($results->num_rows > 0)
			{
				$pg = $link->query("select count(*) as reccnt from mailbox as m inner join user_people as p 
				on m.receipent=p.id inner join mc_user as u on m.sender=u.id 
				where  m.isdeleted='0' and m.suggestedconnectid <> '-1' ");
				$pages = ceil($pg->fetch_array()['reccnt']/10);
		  
				$mails = array(); 
				$html ='<table class="table table-condensed">
					<thead>
					<tr><th></th><th>Referral Sender</th><th>Suggested to</th>  
					<th>Referral Location</th>  
					<th>Sent Date</th>
					<th>Action</th> 
					</tr>
				</thead><tbody>';
				
				$i=0;
				$dot = "."; 
				while($row = $results->fetch_array())
				{
					$position = stripos (    $row['mailbody'] , $dot);
					if($position) 
					{
						$offset = $position + 1;  
						$position2 = stripos (   $row['mailbody']  , $dot, $offset);
						$first_two = substr(    $row['mailbody']  , 0, $position2);
					} 
					if($first_two =="")
					{
						$first_two = $row['mailbody'];
					} 
					$html .= "<tr class='text-center mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
					"<td  class='readrefmail text-left' data-id='" .  $row['id']  . "'>". $row['username']   . "</td>" . 
					"<td  class='readrefmail text-left' data-id='" .  $row['id']  . "'>". $row['client_name']  . "<br/>" .  $row['client_email'] .  "</td>" ;
					$html .= "<td  class='readrefmail' data-id='" .  $row['id']  . "'>".  $row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" . $row['senton'] . "</a></td>";
					$html .= "<td>
							<button class=' btn-danger btn btn-xs rmvMail' data-rpt='" .   $row['receipent'] . "' data-id='". $row['id']. "' style='margin-top: 10px '>
								<i class='fa fa-times-circle'></i>
							</button></td>";
					$html .= "</tr>"; 
					$i++;
				}  
				$html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td><td></td><td></td></tr>";
				$html .='</table>'; 
            }
            $prev = $goto == 1 ? 1 : $goto-1;
			$next = $goto == $pages ? $pages : $goto+1; 
			$html .= " <ul class='pagination pagiAd'><li><a data-mf='0'  class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
			for($i=1; $i<=$pages; $i++){
				$active = $i == $goto ? 'active' : '';
				$html .= "<li class='$active'><a data-mf='0'  class='btn-mailfilter' data-page='$i'>$i</a></li>";
			}
			$html .= "<li><a data-mf='0' class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> "; 
		}    
    }
    else 
    { 
		if($_POST['triggermail'] ==1)
		{
			$saveContact = "SELECT m.*, p.user_id, p.client_name, p.client_profession, p.client_phone, 
			p.client_email, p.client_location, p.client_zip, p.client_note 
			FROM mailbox as m inner join user_people as p on m.receipent=p.id 
			WHERE m.sender='$user_id' and m.isdeleted='0'  and m.suggestedconnectid = '-1'   ORDER BY senton DESC LIMIT $start,10"; 
			 
			$results = $link->query($saveContact); 
			if ($results->num_rows > 0)
			{
				$pg = $link->query("select count(*) as reccnt 
				from mailbox as m inner join user_people as p on m.receipent=p.id 
				where m.sender='$user_id' and m.isdeleted='0' and m.suggestedconnectid = '-1'  "); 
				$pages = ceil($pg->fetch_array()['reccnt']/10);
				 
				$mails = array(); 
				$html ='<table class="table table-condensed">
					<thead>
					<tr><th></th><th>Trigger Mail Receipent</th> 
					<th>Sent On</th>
					<th>Action</th> 
					</tr>
				</thead><tbody>'; 
				$i=0;
				$dot = "."; 
				while($row = $results->fetch_array())
				{
					$position = stripos (    $row['mailbody'] , $dot);
					if($position) 
					{
						$offset = $position + 1;  
						$position2 = stripos (   $row['mailbody']  , $dot, $offset);
						$first_two = substr(    $row['mailbody']  , 0, $position2);
					} 
					if($first_two =="")
					{
						$first_two = $row['mailbody'];
                    } 
					/*$introduceeresult = $link->query( "select * from user_people where id='". $row['suggestedconnectid'] . "'");
					 
					if($introduceeresult->num_rows > 0)
					{
						$introducee = $introduceeresult->fetch_array();
						
						$introduceedetails =$introducee['client_name'] 
						. "<br/>" . 
						$introducee['client_email']
						. "<br/>" .  $introducee['client_phone'] 
						. "<br/>" . $introducee['client_location'];
					}*/
					$introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");
					 
					if($introduceeresult->num_rows > 0)
					{
						$introducee = $introduceeresult->fetch_array();
						$introduceedetails = $introducee['username'] . "<br/>" 
											. $introducee['user_email'];
					}
					else
					{
						$introduceedetails ='Not Found';
					}
					 $html .= "<tr class=' mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
						"<td  class='readrefmail' data-id='" .  $row['id']  . "'>". $row['client_name']  . "<br/>" .  
						$row['client_email'] .  "<br/>".  
						$row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" .   $row['senton']  . "</a></td>";
						$html .= "<td>
						<button class=' btn-primary btn btn-xs queryfeedback' data-rpt='" .   $row['receipent'] . "'  data-id='". $row['id']. "' style='margin-top: 10px '>Feedback</button>
						<button style='margin-top: 10px' data-rpt='" .   $row['receipent'] . "' data-rname='" . $row['client_name'] . 
						"' data-introducee='".$introducee['username']."'  data-remid='" . $row['client_email'] . 
						"' class='btn btn-success btn-xs btnselecttrigger'><i class='fa fa-envelope'></i></button>  
								<button class='btn-danger btn btn-xs rmvMail'  data-id='". $row['id']. "' style='margin-top: 10px '>
									<i class='fa fa-times-circle'></i>
								</button></td>";
						$html .= "</tr>";
					$i++; 
				}
				$html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td> </tr>";
				$html .='</table>';  
			} 
			
			$prev = $goto == 1 ? 1 : $goto-1;
			$next = $goto == $pages ? $pages : $goto+1; 
			$html .= " <ul class='pagination pagiAd'><li><a data-mf='1' class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
			for($i=1; $i<=$pages; $i++){
				$active = $i == $goto ? 'active' : '';
				$html .= "<li class='$active'><a data-mf='1' class='btn-mailfilter' data-page='$i'>$i</a></li>";
			}
			$html .= "<li><a data-mf='1' class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> "; 
		}
		else if($_POST['triggermail'] == 2) //linkedin invite
     	{
            $saveContact = "SELECT m.*,  u.username, p.user_id, p.client_name, 
            p.client_profession, p.client_phone, p.client_email, p.client_location, p.client_zip, 
            p.client_note FROM mailbox as m inner join user_people as p on m.receipent=p.id  
            INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$user_id' and m.suggestedconnectid = '-1' 
            and m.email_type='linkedin-invite'  and p.client_name like '%$searchkey%' and m.isdeleted='0'  ORDER BY senton DESC LIMIT $start,10"; 
      
            $results = $link->query($saveContact); 
            if ($results->num_rows > 0)
            {
                $pg = $link->query("select count(*) as reccnt FROM mailbox as m inner join user_people as p on m.receipent=p.id  
                INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$user_id' and m.suggestedconnectid = '-1' and m.email_type='linkedin-invite'  and p.client_name like '%$searchkey%' and m.isdeleted='0' ");
 
                $pages = ceil($pg->fetch_array()['reccnt']/10);
                 
                $mails = array(); 
                $html ='<table class="table table-condensed">
                    <thead>
                    <tr><th></th><th>LinkedIn Invite Mail Receipent</th> 
                    <th>Sent On</th>
                    <th>Action</th> 
                    </tr>
                </thead><tbody>'; 
                $i=0;
                $dot = "."; 
                while($row = $results->fetch_array())
                {
                    $position = stripos (    $row['mailbody'] , $dot);
                    if($position) 
                    {
                        $offset = $position + 1;  
                        $position2 = stripos (   $row['mailbody']  , $dot, $offset);
                        $first_two = substr(    $row['mailbody']  , 0, $position2);
                    } 
                    if($first_two =="")
                    {
                        $first_two = $row['mailbody'];
                    } 
                   
                    $introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");
                     
                    if($introduceeresult->num_rows > 0)
                    {
                        $introducee = $introduceeresult->fetch_array();
                        $introduceedetails = $introducee['username'] . "<br/>" 
                                            . $introducee['user_email'];
                    }
                    else
                    {
                        $introduceedetails ='Not Found';
                    }
                     $html .= "<tr class=' mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
                        "<td  class='readrefmail' data-id='" .  $row['id']  . "'>". $row['client_name']  . "<br/>" .  
                        $row['client_email'] .  "<br/>".  
                        $row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" .   $row['senton']  . "</a></td>";
                        $html .= "<td>
                        <button class=' btn-primary btn btn-xs queryfeedback' data-rpt='" .   $row['receipent'] . "'  data-id='". $row['id']. "' style='margin-top: 10px '>Feedback</button>
                        <button style='margin-top: 10px' data-rpt='" .   $row['receipent'] . "' data-rname='" . $row['client_name'] . 
                        "' data-introducee='".$introducee['username']."'  data-remid='" . $row['client_email'] . 
                        "' class='btn btn-success btn-xs btnselecttrigger'><i class='fa fa-envelope'></i></button>  
                                <button class='btn-danger btn btn-xs rmvMail'  data-id='". $row['id']. "' style='margin-top: 10px '>
                                    <i class='fa fa-times-circle'></i>
                                </button></td>";
                        $html .= "</tr>";
                    $i++; 
                }
                $html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td> </tr>";
                $html .='</table>';  
            } 
            
            $prev = $goto == 1 ? 1 : $goto-1;
            $next = $goto == $pages ? $pages : $goto+1; 

            if($i >1)
            {
               $html .= " <ul class='pagination pagiAd'><li><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
                for($i=1; $i<=$pages; $i++){
                    $active = $i == $goto ? 'active' : '';
                    $html .= "<li class='$active'><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-page='$i'>$i</a></li>";
                }
                $html .= "<li><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> ";  
            }
            
    	} 
		else   if($_POST['triggermail'] == 0)
		{
			$saveContact = "SELECT m.*, p.user_id, p.client_name, p.client_profession, p.client_phone, 
			p.client_email, p.client_location, p.client_zip, p.client_note 
			FROM mailbox as m inner join user_people as p on m.receipent=p.id 
			WHERE m.sender='$user_id' and m.isdeleted='0'  and m.suggestedconnectid <> '-1'   ORDER BY senton DESC LIMIT $start,10"; 
			
			$results = $link->query($saveContact); 
			if ($results->num_rows > 0)
			{
				$pg = $link->query("select count(*) as reccnt 
				from mailbox as m inner join user_people as p on m.receipent=p.id 
				where m.sender='$user_id' and m.isdeleted='0' and m.suggestedconnectid <> '-1'   "); 
				$pages = ceil($pg->fetch_array()['reccnt']/10);
				
				
				$mails = array(); 
				$html ='<table class="table table-condensed">
					<thead>
					<tr><th></th><th>Referral Introducee</th><th>Referral Introduction Receipent</th> 
					<th>Sent On</th>
					<th>Action</th> 
					</tr>
				</thead><tbody>'; 
				$i=0;
				$dot = "."; 
				while($row = $results->fetch_array())
				{
					$position = stripos (    $row['mailbody'] , $dot);
					if($position) 
					{
						$offset = $position + 1;  
						$position2 = stripos (   $row['mailbody']  , $dot, $offset);
						$first_two = substr(    $row['mailbody']  , 0, $position2);
					} 
					if($first_two =="")
					{
						$first_two = $row['mailbody'];
					} 
					$introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");
					 
					if($introduceeresult->num_rows > 0)
					{
						$introducee = $introduceeresult->fetch_array();
						$introduceedetails = $introducee['username'] . "<br/>" 
											. $introducee['user_email'];
					}
					else
					{
						$introduceedetails ='Not Found';
					}
					 $html .= "<tr class=' mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
						"<td  class='readrefmail' data-id='" .  $row['id']  . "'>". 
						 $introduceedetails .
						"</td>".
						"<td  class='readrefmail' data-id='" .  $row['id']  . "'>". $row['client_name']  . "<br/>" .  
						$row['client_email'] .  "<br/>".  
						$row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" .   $row['senton']  . "</a></td>";
						$html .= "<td>
						<button class=' btn-primary btn btn-xs queryfeedback' data-rpt='" .   $row['receipent'] . "'  data-id='". $row['id']. "' style='margin-top: 10px '>Feedback</button>
						<button style='margin-top: 10px' data-rpt='" .   $row['receipent'] . "' data-rname='" . $row['client_name'] . 
						"'  data-introducee='".$introducee['username']."'  data-remid='" . $row['client_email'] . 
						"' class='btn btn-success btn-xs btnselecttrigger'><i class='fa fa-envelope'></i></button>  
								<button class='btn-danger btn btn-xs rmvMail'  data-id='". $row['id']. "' style='margin-top: 10px '>
									<i class='fa fa-times-circle'></i>
								</button></td>";
						$html .= "</tr>";
					$i++; 
				}
				$html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td><td></td> </tr>";
				$html .='</table>';  
			}
		  
			$prev = $goto == 1 ? 1 : $goto-1;
			$next = $goto == $pages ? $pages : $goto+1; 
			$html .= " <ul class='pagination pagiAd'><li><a data-mf='0' class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
			for($i=1; $i<=$pages; $i++){
				$active = $i == $goto ? 'active' : '';
				$html .= "<li class='$active'><a data-mf='0'  class='btn-mailfilter' data-page='$i'>$i</a></li>";
			}
			$html .= "<li><a data-mf='0'  class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> "; 
        }
		else   
		{
			$saveContact = "select * from mc_linkedin_import where userid='$user_id' 
			and mailsent='1' order by id desc limit $start,10"; 
			
			$results = $link->query($saveContact); 
			if ($results->num_rows > 0)
			{
				$pg = $link->query("select count(*) as reccnt 
				from mc_linkedin_import where userid='$user_id' and mailsent='1'"); 
				$pages = ceil($pg->fetch_array()['reccnt']/10);
				
				$mails = array(); 
				$html ='<table class="table table-condensed">
					<thead>
					<tr><th></th><th>LinkedIn Contact</th>
					<th>Contact/Profession</th>  
					<th>Sent On</th>
					<th>Action</th> 
					</tr>
				</thead><tbody>'; 
				$i=0;
				$dot = "."; 
				while($row = $results->fetch_array())
				{
                    $html .= "<tr class=' mailrow' id='row-" . $row['id']  . "' >
                        <td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
						"<td  class='readrefmail' data-id='" .  $row['id']  . "'>". 
						 $row['fullname']  .
						"</td>".
						"<td  class='readrefmail' data-id='" .  $row['id']  . "'>". $row['email']  . "<br/>" .  
						$row['company'] .  "<br/>".  
						$row['client_location'] . "</td>
						<td> " .   $row['senddate']  . " </td>";
						$html .= "<td>
						<button class='btn-danger btn btn-xs rmvMail'  data-id='". $row['id']. "' style='margin-top: 10px '>
									<i class='fa fa-times-circle'></i>
								</button></td>";
						$html .= "</tr>";
					$i++; 
				}
				$html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td><td></td> </tr>";
				$html .='</table>';  
			}
		  if( $i > 0)
   		 {
			$prev = $goto == 1 ? 1 : $goto-1;
			$next = $goto == $pages ? $pages : $goto+1; 
			$html .= " <ul class='pagination pagiAd'><li><a data-mf='2' class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
			for($i=1; $i<=$pages; $i++){
				$active = $i == $goto ? 'active' : '';
				$html .= "<li class='$active'><a data-mf='2'  class='btn-mailfilter' data-page='$i'>$i</a></li>";
			}
			$html .= "<li><a data-mf='2'  class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> "; 
			 }
		} 
    }
     
	if( $i==0)
    {
      echo "<p class='alert alert-info'>All caught up! No new email!</p>";
    }
    else
    {
		//embed feedback dialog 
		$html .= '<div class="modal fade mine-modal" id="queryfeedback" tabindex="-1" role="dialog">
		<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Did you meet the introduction/referral?</h3>
            </div> 
			  <div class="modal-body text-left" id="queryfeedbackform">
			<div  class="form-group">
				<label>Question #1:</label>
			 <select class="form-control" name="quest1">
				<option>Have you met the contact who was introduced to you?</option>
			 </select>
			</div>
			<div  class="form-group"> 
			 <label>Question #2:</label>
			 <select class="form-control"  name="quest2">
				<option>How was the meeting?</option>
			 </select>
			 </div> 
			  </div>
			<div class="modal-footer">
				<input type="hidden" id="datamid"/>
				<input type="hidden" id="datarpt"/>
				<button  class="btn btn-primary sendmeetingfeedback">Send Feedback Enquiry Mail</button>
			</div>
		  </div>
		  </div>
		</div>';
		 
			
		echo $html;
    }
}


if(isset($_POST['searchmailbox']))
{
    $goto = $_POST['page'];
    $start = ($goto-1)*10; 
    if($start  < 0) $start =0;
    $searchkey = $_POST['receipent'];


     if($_POST['triggermail'] == 1)
     {
            $saveContact = "SELECT m.*,  u.username, p.user_id, p.client_name, 
            p.client_profession, p.client_phone, p.client_email, p.client_location, p.client_zip, 
            p.client_note FROM mailbox as m inner join user_people as p on m.receipent=p.id  
            INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$user_id' and m.suggestedconnectid = '-1'  and p.client_name like '%$searchkey%' and m.isdeleted='0'  ORDER BY senton DESC LIMIT $start,10"; 
      
            $results = $link->query($saveContact); 
            if ($results->num_rows > 0)
            {
                $pg = $link->query("select count(*) as reccnt FROM mailbox as m inner join user_people as p on m.receipent=p.id  
                INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$user_id' and m.suggestedconnectid = '-1'  and p.client_name like '%$searchkey%' and m.isdeleted='0' ");
 
                $pages = ceil($pg->fetch_array()['reccnt']/10);
                 
                $mails = array(); 
                $html ='<table class="table table-condensed">
                    <thead>
                    <tr><th></th><th>Trigger Mail Receipent</th> 
                    <th>Sent On</th>
                    <th>Action</th> 
                    </tr>
                </thead><tbody>'; 
                $i=0;
                $dot = "."; 
                while($row = $results->fetch_array())
                {
                    $position = stripos (    $row['mailbody'] , $dot);
                    if($position) 
                    {
                        $offset = $position + 1;  
                        $position2 = stripos (   $row['mailbody']  , $dot, $offset);
                        $first_two = substr(    $row['mailbody']  , 0, $position2);
                    } 
                    if($first_two =="")
                    {
                        $first_two = $row['mailbody'];
                    } 
                   
                    $introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");
                     
                    if($introduceeresult->num_rows > 0)
                    {
                        $introducee = $introduceeresult->fetch_array();
                        $introduceedetails = $introducee['username'] . "<br/>" 
                                            . $introducee['user_email'];
                    }
                    else
                    {
                        $introduceedetails ='Not Found';
                    }
                     $html .= "<tr class=' mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
                        "<td  class='readrefmail' data-id='" .  $row['id']  . "'>". $row['client_name']  . "<br/>" .  
                        $row['client_email'] .  "<br/>".  
                        $row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" .   $row['senton']  . "</a></td>";
                        $html .= "<td>
                        <button class=' btn-primary btn btn-xs queryfeedback' data-rpt='" .   $row['receipent'] . "'  data-id='". $row['id']. "' style='margin-top: 10px '>Feedback</button>
                        <button style='margin-top: 10px' data-rpt='" .   $row['receipent'] . "' data-rname='" . $row['client_name'] . 
                        "' data-introducee='".$introducee['username']."'  data-remid='" . $row['client_email'] . 
                        "' class='btn btn-success btn-xs btnselecttrigger'><i class='fa fa-envelope'></i></button>  
                                <button class='btn-danger btn btn-xs rmvMail'  data-id='". $row['id']. "' style='margin-top: 10px '>
                                    <i class='fa fa-times-circle'></i>
                                </button></td>";
                        $html .= "</tr>";
                    $i++; 
                }
                $html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td> </tr>";
                $html .='</table>';  
            } 
            
            $prev = $goto == 1 ? 1 : $goto-1;
            $next = $goto == $pages ? $pages : $goto+1; 

            if($i >1)
            {
               $html .= " <ul class='pagination pagiAd'><li><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
                for($i=1; $i<=$pages; $i++){
                    $active = $i == $goto ? 'active' : '';
                    $html .= "<li class='$active'><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-page='$i'>$i</a></li>";
                }
                $html .= "<li><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> ";  
            }
            
    }
	else if($_POST['triggermail'] == 2) //linkedin invite
     {
            $saveContact = "SELECT m.*,  u.username, p.user_id, p.client_name, 
            p.client_profession, p.client_phone, p.client_email, p.client_location, p.client_zip, 
            p.client_note FROM mailbox as m inner join user_people as p on m.receipent=p.id  
            INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$user_id' and m.suggestedconnectid = '-1' and m.email_type='linkedin-invite'  and p.client_name like '%$searchkey%' and m.isdeleted='0'  ORDER BY senton DESC LIMIT $start,10"; 
      
            $results = $link->query($saveContact); 
            if ($results->num_rows > 0)
            {
                $pg = $link->query("select count(*) as reccnt FROM mailbox as m inner join user_people as p on m.receipent=p.id  
                INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$user_id' and m.suggestedconnectid = '-1' and m.email_type='linkedin-invite'  and p.client_name like '%$searchkey%' and m.isdeleted='0' ");
 
                $pages = ceil($pg->fetch_array()['reccnt']/10);
                 
                $mails = array(); 
                $html ='<table class="table table-condensed">
                    <thead>
                    <tr><th></th><th>LinkedIn Invite Mail Receipent</th> 
                    <th>Sent On</th>
                    <th>Action</th> 
                    </tr>
                </thead><tbody>'; 
                $i=0;
                $dot = "."; 
                while($row = $results->fetch_array())
                {
                    $position = stripos (    $row['mailbody'] , $dot);
                    if($position) 
                    {
                        $offset = $position + 1;  
                        $position2 = stripos (   $row['mailbody']  , $dot, $offset);
                        $first_two = substr(    $row['mailbody']  , 0, $position2);
                    } 
                    if($first_two =="")
                    {
                        $first_two = $row['mailbody'];
                    } 
                   
                    $introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");
                     
                    if($introduceeresult->num_rows > 0)
                    {
                        $introducee = $introduceeresult->fetch_array();
                        $introduceedetails = $introducee['username'] . "<br/>" 
                                            . $introducee['user_email'];
                    }
                    else
                    {
                        $introduceedetails ='Not Found';
                    }
                     $html .= "<tr class=' mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
                        "<td  class='readrefmail' data-id='" .  $row['id']  . "'>". $row['client_name']  . "<br/>" .  
                        $row['client_email'] .  "<br/>".  
                        $row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" .   $row['senton']  . "</a></td>";
                        $html .= "<td>
                        <button class=' btn-primary btn btn-xs queryfeedback' data-rpt='" .   $row['receipent'] . "'  data-id='". $row['id']. "' style='margin-top: 10px '>Feedback</button>
                        <button style='margin-top: 10px' data-rpt='" .   $row['receipent'] . "' data-rname='" . $row['client_name'] . 
                        "' data-introducee='".$introducee['username']."'  data-remid='" . $row['client_email'] . 
                        "' class='btn btn-success btn-xs btnselecttrigger'><i class='fa fa-envelope'></i></button>  
                                <button class='btn-danger btn btn-xs rmvMail'  data-id='". $row['id']. "' style='margin-top: 10px '>
                                    <i class='fa fa-times-circle'></i>
                                </button></td>";
                        $html .= "</tr>";
                    $i++; 
                }
                $html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td> </tr>";
                $html .='</table>';  
            } 
            
            $prev = $goto == 1 ? 1 : $goto-1;
            $next = $goto == $pages ? $pages : $goto+1; 

            if($i >1)
            {
               $html .= " <ul class='pagination pagiAd'><li><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
                for($i=1; $i<=$pages; $i++){
                    $active = $i == $goto ? 'active' : '';
                    $html .= "<li class='$active'><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-page='$i'>$i</a></li>";
                }
                $html .= "<li><a data-mf='1' data-skey='". $searchkey .  "' class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> ";  
            }
            
    } 
    else  if($_POST['triggermail'] == 0)
    {
            $saveContact = "SELECT m.*,  u.username, p.user_id, p.client_name, 
            p.client_profession, p.client_phone, p.client_email, p.client_location, p.client_zip, 
            p.client_note FROM mailbox as m inner join user_people as p on m.receipent=p.id  
            INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$user_id' and m.suggestedconnectid <> '-1'  and p.client_name like '%$searchkey%' and m.isdeleted='0'  ORDER BY senton DESC LIMIT $start,10"; 


            $results = $link->query($saveContact); 
            if ($results->num_rows > 0)
            {
                $pg = $link->query("select count(*) as reccnt 
               FROM mailbox as m inner join user_people as p on m.receipent=p.id  
                INNER JOIN mc_user as u ON m.sender=u.id WHERE sender='$user_id' and m.suggestedconnectid <> '-1'  
                and p.client_name like '%$searchkey%' and m.isdeleted='0'   "); 
                $pages = ceil($pg->fetch_array()['reccnt']/10);
                
            $pages = ceil($pg->fetch_array()['reccnt']/10);
            $mails = array(); 
                $html ='<table class="table table-condensed">
                    <thead>
                    <tr><th></th><th>Referral Introducee</th><th>Referral Introduction Receipent</th> 
                    <th>Sent On</th>
                    <th>Action</th> 
                    </tr>
                </thead><tbody>'; 
                $i=0;
                $dot = "."; 
                while($row = $results->fetch_array())
                {
                    $position = stripos (    $row['mailbody'] , $dot);
                    if($position) 
                    {
                        $offset = $position + 1;  
                        $position2 = stripos (   $row['mailbody']  , $dot, $offset);
                        $first_two = substr(    $row['mailbody']  , 0, $position2);
                    } 
                    if($first_two =="")
                    {
                        $first_two = $row['mailbody'];
                    }
                   
                    $introduceeresult = $link->query( "select * from mc_user where id='". $user_id . "'");
                     
                    if($introduceeresult->num_rows > 0)
                    {
                        $introducee = $introduceeresult->fetch_array();
                        $introduceedetails = $introducee['username'] . "<br/>" 
                                            . $introducee['user_email'];
                    }
                    else
                    {
                        $introduceedetails ='Not Found';
                    }
                     $html .= "<tr class=' mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td>" .
                        "<td  class='readrefmail' data-id='" .  $row['id']  . "'>". 
                         $introduceedetails .
                        "</td>".
                        "<td  class='readrefmail' data-id='" .  $row['id']  . "'>". $row['client_name']  . "<br/>" .  
                        $row['client_email'] .  "<br/>".  
                        $row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" .   $row['senton']  . "</a></td>";
                        $html .= "<td>
                        <button class=' btn-primary btn btn-xs queryfeedback' data-rpt='" .   $row['receipent'] . "'  data-id='". $row['id']. "' style='margin-top: 10px '>Feedback</button>
                        <button style='margin-top: 10px' data-rpt='" .   $row['receipent'] . "' data-rname='" . $row['client_name'] . 
                        "'  data-introducee='".$introducee['username']."'  data-remid='" . $row['client_email'] . 
                        "' class='btn btn-success btn-xs btnselecttrigger'><i class='fa fa-envelope'></i></button>  
                                <button class='btn-danger btn btn-xs rmvMail'  data-id='". $row['id']. "' style='margin-top: 10px '>
                                    <i class='fa fa-times-circle'></i>
                                </button></td>";
                        $html .= "</tr>";
                    $i++; 
                }
                $html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td><td></td> </tr>";
                $html .='</table>';  
            } 
            $prev = $goto == 1 ? 1 : $goto-1;
            $next = $goto == $pages ? $pages : $goto+1;  
            if($i >1)
            {
                $html .= " <ul class='pagination pagiAd'><li><a data-mf='0' data-skey='". $searchkey .  "' class='btn-mailfilter' data-func='prev' data-page='$prev'>«</a></li>";
                for($i=1; $i<=$pages; $i++)
                {
                    $active = $i == $goto ? 'active' : '';
                    $html .= "<li class='$active'><a data-mf='0'  data-skey='". $searchkey .  "' class='btn-mailfilter' data-page='$i'>$i</a></li>";
                }
                $html .= "<li><a data-mf='0'  data-skey='". $searchkey .  "' class='btn-mailfilter' data-func='next' data-page='$next'>»</a></li></ul> ";  
            }
    } 
    if( $i==0)
    {
      echo "<p class='alert alert-info'>All caught up! No new email!</p>";
    } 
    echo $html; 
}  
//check if feedback email has already been sent 
if(isset($_POST['feedbackmailcheck']))
{
	$mailcount=0;
	$mailid =  $_POST['mailid'];
	$rsmailcount = $link->query("select feedbackmailsent from mailbox where id='$mailid' ");  
	if($rsmailcount->num_rows  > 0)
	{
		$mailcount = $rsmailcount->fetch_array()['feedbackmailsent'];
	}
	echo $mailcount;
}

//send feedback request mail
if(isset($_POST['sendfeedbackmail']))
{
	$q1 =  $_POST['q1'];
	$q2 =  $_POST['q2'];
	$mailid =  $_POST['mailid'];
	$rpt =  $_POST['rpt'];
	$link->query("update mailbox set feedbackmailsent='1' where id='$mailid' ");  
    //get both details of who is being introduced and to whom he/she was introduced
	$referralleft = $link->query("SELECT * FROM  user_people  where  id='$rpt'"); 
	if($referralleft->num_rows  > 0)
	{
		$leftperson  = $referralleft->fetch_array(); 
	}
	$referralright =  $link->query(" Select * from user_people where id= (SELECT suggestedconnectid FROM mailbox where id='$mailid' ) "); 
	if($referralright->num_rows  > 0)
	{
		$rightperson = $referralright->fetch_array(); 
	}
	
	if($leftperson['client_email']):
	$hash = md5($mailid);
	$mailhash = $hash.$mailid;
	$body = "<!DOCTYPE html><html>
	<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'/>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
    <title>Contact Meeting Feedback Email from mycity.com</title>
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
                 <img src='http://www.mycity.com/includes/readmail.php?i=$hash&c=$mailhash' width='100' alt='www.mycity.com' /></a>
                    </td>
                </tr>
                <tr><td style='font-size: 0; line-height: 0;' height='30'>&nbsp;</td></tr>
                <tr>
				<td style='padding: 10px 10px 30px 10px;'>
				<div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>" . $leftperson['client_name'] . "</span></div>
                        <br />
                        <div>
						<p>You have recently been introduced a contact with the following details.</p>
				   
                    <div style='border: 1px solid #efefef; padding: 10px;'>
						<p>Full Name: ".$rightperson['client_name']."</p> 
						<p>Email: ".$rightperson['client_email']."</p> 
						<p>Comment: ".$rightperson['client_note']."</p> 
                    </div> 
				 <p>Regarding this introduction, we would be very grateful if you could 
						spare some time to answer the following feedback questions.
						"
						. "<h4>" . $q1 . "</h4>" 
						. "<h4>" . $q2 . "</h4>" .
						" </div>
                        <br />
                    </td>
                </tr>
                <tr>
                <td style='padding: 0 10px 10px 10px;'> </td>
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
 sendemail( $rightperson['client_email'] ,  'Contact feedback enquiry from MyCity.com', $body, $body  ); 
 endif; 
}

//outbox grid loading
if(isset($_POST['loadrefinbox']))
{
    //loading outbox
    $saveContact = "SELECT m.*, p.user_id, p.client_name, p.client_profession, p.client_phone, p.client_email, p.client_location, p.client_zip, p.client_note FROM mailbox as m inner join user_people as p on m.sender=p.id WHERE m.receipent='$user_id' ORDER BY senton DESC"; 
    $results = $link->query($saveContact);
 
 
    if ($results->num_rows > 0)
    {
        $mails = array();
         
        $html ='<table class="table table-condensed">
            <thead>
            <tr><th></th><th>Receipent</th>
            <th>Email</th>  
            <th>Location</th>  
            <th>Message</th>
            <th>Action</th> 
            </tr>
        </thead><tbody>';
        
        $i=0;
        $dot = ".";

        while($row = $results->fetch_array())
        {
            $position = stripos (    $row['mailbody'] , $dot);
            if($position) 
            {
                $offset = $position + 1;  
                $position2 = stripos (   $row['mailbody']  , $dot, $offset);
                $first_two = substr(    $row['mailbody']  , 0, $position2);
            }
            
            if($first_two =="")
            {
                $first_two = $row['mailbody'];
            }
            
           $html .= "<tr class='text-center mailrow' id='row-" . $row['id']  . "' ><td ><input type='checkbox' class='delmail' data-id='" . $row['id'] .   "'></td><td  class='readrefmail' data-id='" .  $row['id']  . "'>". $row['client_name']  . "</td><td  class='readrefmail' data-id='" .  $row['id']  . "'>" .  $row['client_email'] .  "</td>" ;
            $html .= "<td  class='readrefmail' data-id='" .  $row['id']  . "'>".  $row['client_location'] . "</td><td><a href='#' data-id='" .  $row['id']  . "' class='readrefmail'>" . $row['subject'] . "</a></td>";
            $html .= "<td>
                    <button class=' btn-danger btn btn-xs rmvMail' data-id='". $row['id']. "' style='margin-top: 10px '>
                        <i class='fa fa-times-circle'></i>
                    </button></td>";
            $html .= "</tr>";

            $i++;
        }

        $html.= "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td><td></td><td></td></tr>";

        $html .='</table>';

    }
 if( $i==0)
    {
      echo "<p class='alert alert-info'>All caught up! No new email!</p>";
    }
    else
    {
echo $html;
    }

}
//reward point 
if(isset($_POST['rememail'] ))
{
    $mailid =  $_POST['mailid'];
    $link->query("DELETE FROM mailbox WHERE id='$mailid' ");  
    echo $link->affected_rows;
}




//reading mail 
if(isset($_POST['readrefmail']))
{
    $mailid =$_POST['mailid']; 
    $query = "SELECT * FROM mailbox  WHERE id='$mailid'  ";
    $mails = $link->query($query);
    if ($mails->num_rows > 0)
    {  
        $html ='<div>';  
        $row = $mails->fetch_array(); 
        $html .='<div><strong>Subject:</strong></td><td>' .   $row['subject'] . '</div>';  
        $html .='<div><strong>Message Body:</strong></div>'; 
        $htmlhtml .='<br/><br/>';
        $html .='<div>' .   $row['mailbody'] . '</div>'; 
        /*$html .='<div><p>
				Sincerely,<br/>
				Referrals@mycity.com<br/>
				</p>
				<p>
				If you would like more information,<br/>
				please email or call<br/>
				310-736-5787<br/>
				</p></div>'; */
        $html .='</div>'; 
    } 
    echo $html; 
}


//group members for tracking referrals 
if(isset($_POST['reftracker']))
{ 
	$groupid = $_POST['groupid']; 
	/*
	$groups = $link->query("SELECT d.groups as groupids FROM mc_user as u 
	inner join user_details as d on u.id=d.user_id where u.id='$user_id'");
	
	$arrgroupids = explode("," ,  $groups->fetch_array()['groupids'] ); 
	
	//first getting subquery for retrieving partners
	$where_in_set = " (  " ;
	for($i=0; $i < sizeof($arrgroupids); $i++ )
    {
        $groupid = $arrgroupids[$i];
		$where_in_set .= " find_in_set('$groupid', groups) "; 
		if( $i < sizeof($arrgroupids)-1 )
		{
			$where_in_set .= " or "; 
		}
	}
	$where_in_set .=" ) " ;
	*/
	
	$users = $link->query("select u.id, u.username, u.user_email, u.user_phone, u.user_pkg, u.image, d.city, d.zip, d.country, d.target_clients, d.target_referral_partners, d.vocations from mc_user  as u inner join user_details as d on u.id=d.user_id 
	where find_in_set('$groupid', groups) and u.id <> '$user_id'"  );
	$html = '';
	if($users->num_rows > 0 )
	{
		while($row = $users->fetch_array())
		{
			//count total referrals sent 
			$refcount = $link->query('select count(*) as totalreferrals from referralsuggestions where knowenteredby=\''
			.  $row["id"] . '\'' ); 
			if($refcount->num_rows > 0 )
			{
				$totalref = $refcount->fetch_array()['totalreferrals'];
			}
			
			$refsentcount = $link->query( "select count(*) as totalreferralssent from referralsuggestions where knowenteredby='". $row["id"] . "' and emailstatus='1'" ); 
			if($refcount->num_rows > 0 )
			{
				$totalrefsent = $refsentcount->fetch_array()['totalreferralssent'];
			}
			
			
			$user_picutre = !empty($row['image']) ? "images/" . $row['image'] : "images/no-photo.png";
			$html .= '<div class="col-md-6"><div class="panel panel-default cardmember">
			<div class="panel-body">
			<div class="row">
				<div class="col-md-4">
					<img src=\''.$user_picutre.'\' alt="' .  $row["username"] . '" class="img-rounded" height="120" width="120">
				</div>
				<div class="col-md-6">
					<p><strong>Name:</strong>'.$row["username"].'</p>
					<p><strong>Email:</strong>'.$row["user_email"].'</p>
					<p><strong>Phone:</strong>'.$row["user_phone"].'</p>
				</div>
				<div class="col-md-12"> 
				<hr/>
				</div>
			<div class="col-md-6"> 
			<span class="txtmd">Total Referrals: <a data-rs="0" data-count="'. $totalref . '" data-id="'. $row["id"] . '"   data-goto="1"  class="badge badge-green viewrefs">' . $totalref . '</a></span>
			</div>
				<div class="col-md-6"> 
			<span class="txtmd">Total Referrals: <a data-rs="1" data-count="'. $totalref . '" data-id="'. $row["id"] . '"   data-goto="1"  class="badge badge-orange viewrefs">' . $totalrefsent . '</a></span>
			</div>
			</div>
			</div>
			</div></div> ';
		}
		$html .=  '<div class="modal fade reftrackingboard" tabindex="-1" role="dialog" aria-labelledby="reftrackingboard"
         id="reftrackingboard">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >All Referrals</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style="max-height: 520px; overflow-y:scroll; text-align:left">
					<div class="text-center" id="loading">
						<img src="images/processing.gif" alt="Loading progress ..."/>
					</div>
					<div id="reflist" >
					</div>
                </div>
				 <div class="modal-footer" >
				 </div>
            </div>
        </div>
</div>'
 ;
 } 
    echo $html;  
}


if(isset($_POST['reftrackervoc']))
{ 
	$groupid = $_POST['groupid']; 
	
	$users = $link->query("select u.id, u.username, u.user_email, u.user_phone, u.user_pkg, u.image, d.city, d.zip, d.country, d.target_clients, d.target_referral_partners, d.vocations from mc_user  as u inner join user_details as d on u.id=d.user_id 
	where find_in_set('$groupid', d.vocations) and u.id <> '$user_id'"  );
	$html = '';
	if($users->num_rows > 0 )
	{
		while($row = $users->fetch_array())
		{ 
			//count total referrals sent 
			$refcount = $link->query('select count(*) as totalreferrals from referralsuggestions where knowenteredby=\''
			.  $row["id"] . '\'' ); 
			if($refcount->num_rows > 0 )
			{
				$totalref = $refcount->fetch_array()['totalreferrals'];
			}
			
			$refsentcount = $link->query( "select count(*) as totalreferralssent from referralsuggestions where knowenteredby='". $row["id"] . "' and emailstatus='1'" ); 
			if($refcount->num_rows > 0 )
			{
				$totalrefsent = $refsentcount->fetch_array()['totalreferralssent'];
			}
			$refcount1 = $link->query("select count(*) as totalreferrals from user_people WHERE user_id = '".$row['id']."'"); 
			if($refcount1->num_rows > 0 )
			{
				$totalref1 = $refcount1->fetch_array()['totalreferrals'];
			}
			
			$user_picutre = !empty($row['image']) ? "images/" . $row['image'] : "images/no-photo.png";
			$html .= '<div class="col-md-6"><div class="panel panel-default cardmember">
			<div class="panel-body">
			<div class="row">
				<div class="col-md-4">
					<img src=\''.$user_picutre.'\' alt="' .  $row["username"] . '" class="img-rounded" height="120" width="120">
				</div>
				<div class="col-md-6">
					<p><strong>Name:</strong>'.$row["username"].'</p>
					<p><strong>Email:</strong>'.$row["user_email"].'</p>
					<p><strong>Phone:</strong>'.$row["user_phone"].'</p>';
					$html .= "<p style='word-break: break-all !important;'><strong>Vocations:</strong>".$row["vocations"]."</p>";
				$html .='</div>
				<div class="col-md-12"> 
				<hr/>
				</div>
			<div class="col-md-6">
			<span class="txtmd">View Reference(s): <a data-target="#userModal" data-toggle="modal" data-user="'. $row["id"] . '" class="badge badge-blue viewUser">
                        '. $totalref1 .'
                    </a></span>
			<span class="txtmd">Total Referrals: <a data-rs="0" data-count="'. $totalref . '" data-id="'. $row["id"] . '"   data-goto="1"  class="badge badge-green viewrefsvoc">' . $totalref . '</a></span>
			</div>
				<div class="col-md-6"> 
			<span class="txtmd">Total Referrals: <a data-rs="1" data-count="'. $totalref . '" data-id="'. $row["id"] . '"   data-goto="1"  class="badge badge-orange viewrefsvoc">' . $totalrefsent . '</a></span>
			</div>
			</div>
			</div>
			</div></div> ';
		}
		$html .=  '<div class="modal fade reftrackingboardvoc" tabindex="-1" role="dialog" aria-labelledby="reftrackingboardvoc"
         id="reftrackingboardvoc">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >All Referrals</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style="max-height: 520px; overflow-y:scroll; text-align:left">
					<div class="text-center" id="loadingvoc">
						<img src="images/processing.gif" alt="Loading progress ..."/>
					</div>
					<div id="reflistvoc" >
					</div>
                </div>
				 <div class="modal-footer" >
				 </div>
            </div>
        </div>
</div>'
 ;
 }
	echo $html;
}
 
if(isset($_POST['trackreferrals']))
{
	$goto =$_POST['goto'];
	$start = ($goto -1)*10;
	$mid = $_POST['mid']; 
	$rs = $_POST['rs']; 
	$users = $link->query("SELECT id, partnerid, knowtorefer, knowreferedto, isdeleted, emailstatus, senton FROM referralsuggestions where knowenteredby='$mid' and emailstatus='$rs' LIMIT $start,10 "  );
	 
	$allusers = $link->query("SELECT count(*) as rowcnt FROM referralsuggestions where knowenteredby='$mid' and emailstatus='$rs' "  );
	$totalrows = $allusers->fetch_array()['rowcnt'];
	
	$pages = ceil( $totalrows  /10); 
	$html =  '';
	if($users->num_rows > 0 )
	{
		$html .= '<table class="table table-bordered table-striped">';
		$html .= '<tr><th>Introducing</th><th>Know Introduced To</th><th>Referral Mail Status</th></tr>';
		
		while($row = $users->fetch_array())
		{
			$userdetail = $link->query(
			"(select client_name, client_email, client_profession from user_people where id='". $row['knowtorefer'] . "') UNION 
			(select client_name, client_email, client_profession from user_people where id='". $row['knowreferedto'] . "')" );
			if($userdetail->num_rows == 2 )
			{
				$left = $userdetail->fetch_array();
				$right = $userdetail->fetch_array();
			} 
			$html .= '<tr><td>' .$left['client_name'] . "<br/>" . $left['client_email'] . '</td>'. 
			'<td>' . $right['client_name'] .  "<br/>" . $right['client_email']   . '</td>'; 
			if($row['emailstatus'] == 0 )
			{
				$html .= '<td>Not Sent</td>';
			}
			else
			{
				$html .= '<td>Sent on ' .  $row['senton'] . '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';
		 
		$prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1;
		$html .= "<ul class='pagination pagiAd'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
        for($i=1; $i<=$pages; $i++)
		{
			$active = $i == $goto ? 'active' : '';
			$html .= "<li class='$active'><a class='viewrefs'  data-pager=''  data-count='$totalrows' data-id='$mid' data-goto='$i'>$i</a></li>";
        }
		$html .= "<li><a data-func='next' data-pg='$next'>»</a></li></ul> ";
	}
	  
	echo $html;
}

if(isset($_POST['trackreferralsvoc']))
{
	$goto =$_POST['goto'];
	$start = ($goto -1)*10;
	$mid = $_POST['mid']; 
	$rs = $_POST['rs']; 
	$users = $link->query("SELECT id, partnerid, knowtorefer, knowreferedto, isdeleted, emailstatus, senton FROM referralsuggestions where knowenteredby='$mid' and emailstatus='$rs' LIMIT $start,10 "  );
	 
	$allusers = $link->query("SELECT count(*) as rowcnt FROM referralsuggestions where knowenteredby='$mid' and emailstatus='$rs' "  );
	$totalrows = $allusers->fetch_array()['rowcnt'];
	
	$pages = ceil( $totalrows  /10); 
	$html =  '';
	if($users->num_rows > 0 )
	{
		$html .= '<table class="table table-bordered table-striped">';
		$html .= '<tr><th>Introducing</th><th>Know Introduced To</th><th>Referral Mail Status</th></tr>';
		
		while($row = $users->fetch_array())
		{
			$userdetail = $link->query(
			"(select client_name, client_email, client_profession from user_people where id='". $row['knowtorefer'] . "') UNION 
			(select client_name, client_email, client_profession from user_people where id='". $row['knowreferedto'] . "')" );
			if($userdetail->num_rows == 2 )
			{
				$left = $userdetail->fetch_array();
				$right = $userdetail->fetch_array();
			} 
			$html .= '<tr><td>' .$left['client_name'] . "<br/>" . $left['client_email'] . '</td>'. 
			'<td>' . $right['client_name'] .  "<br/>" . $right['client_email']   . '</td>'; 
			if($row['emailstatus'] == 0 )
			{
				$html .= '<td>Not Sent</td>';
			}
			else
			{
				$html .= '<td>Sent on ' .  $row['senton'] . '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';
		 
		$prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1;
		$html .= "<ul class='pagination pagiAd'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
        for($i=1; $i<=$pages; $i++)
		{
			$active = $i == $goto ? 'active' : '';
			$html .= "<li class='$active'><a class='viewrefsvoc'  data-pager=''  data-count='$totalrows' data-id='$mid' data-goto='$i'>$i</a></li>";
        }
		$html .= "<li><a data-func='next' data-pg='$next'>»</a></li></ul> ";
	}
	  
	echo $html;
}

// Update user profile
if(isset($_POST['changeAccSett'], $_SESSION['user_email']))
{
	
	$id = $_POST['changeAccSett'];
    $q = $link->query("select * from mc_user  where id = '$id'");
	
	//loading user account info 
    if ($q->num_rows > 0)
	{
		$html = "";
        $row = $q->fetch_array();
        $username = $row['username'];
        $user_email = $row['user_email'];
        $user_phone = $row['user_phone'];  
	}
	
	$country = ''; 
	$city = ''; 
	$zip = ''; 
	$grp = ''; 
	$target_clients = ''; 
	$target_referral_partners = ''; 
	$voc =  ''; 
	$about_your_self = ''; 
	$upd_public_private = ''; 
	$upd_reminder_email = ''; 
	$profileincomplete = '1'; 
	
	//loading account profile
	$q = $link->query("select * from user_details  where user_id = '$id'"); 
	if ($q->num_rows > 0)
	{
		$row = $q->fetch_array();
        $country = $row['country'];
        $city = $row['city'];
        $zip = $row['zip'];
        $grp = $row['groups'];
        $target_clients = $row['target_clients'];
		$target_referral_partners = $row['target_referral_partners'];
        $voc = $row['vocations'];
		$about_your_self = $row['about_your_self'];
		$upd_public_private = $row['upd_public_private'];
		$upd_reminder_email = $row['upd_reminder_email'];
		$profileincomplete = '0';
	} 
	
     $resp = array(
            "username" => $username,
            "user_phone" => $user_phone,
            "user_email" => $user_email,
            "country" => $country,
            "city" => $city,
            "zip" => $zip,
            "grp" => $grp,
            "target_clients" => $target_clients,
			"target_referral_partners" => $target_referral_partners,
            "voc" => $voc,
			"about_your_self" => $about_your_self,
			"upd_public_private" => $upd_public_private,
			"upd_reminder_email" => $upd_reminder_email,
		 	"profile_missing" => $profileincomplete
        );
        header('Content-type: application/json');
        echo json_encode($resp); 
	 
}


if(isset($_POST['upd_username'], $_SESSION['user_email'])){
    $data_id = $_POST['data_id'];
    $upd_username = $_POST['upd_username'];
    $upd_phone = $_POST['upd_phone'];
    $upd_country = $_POST['upd_country'];
    $upd_city = $_POST['upd_city'];
    $upd_cityov = $_POST['upd_cityov']; 
    $upd_zip = $_POST['upd_zip'];
    $upd_email = $_POST['upd_email'];
	$upd_public_private = $_POST['upd_public_private'];
	$upd_reminder_email = $_POST['upd_reminder_email'];
    $upd_usergrp = $_POST['upd_usergrp']; #array
    $upd_uservoc = $_POST['upd_uservoc']; #array
    $upd_usertarget = $_POST['upd_usertarget']; #array
	$upd_usertargetreferral = $_POST['upd_usertargetreferral']; #array
	$about_your_self = $link->real_escape_string($_POST['about_your_self']);

    $groups = '';
    foreach ($upd_usergrp as $item1) {
        $groups .= $item1 . ",";
    }
    $groups = rtrim($groups, ',');

    $voc = '';
    foreach ($upd_uservoc as $item2) {
        $voc .= $item2 . ",";
    }
    $voc = rtrim($voc, ',');

    $target = '';
    foreach ($upd_usertarget as $item3) {
        $target .= $item3 . ",";
    }
    $target = rtrim($target, ',');
	
	$referral = '';
	foreach($upd_usertargetreferral as $item4) {
		$referral .= $item4 . ",";
	}
	$referral = rtrim($referral, ',');

    $link->query("UPDATE mc_user SET user_email = '$upd_email', user_phone = '$upd_phone', username = '$upd_username' WHERE id = '$data_id' ");
    
    //check if record exists 
    $reccnt = $link->query("select count(*) as reccnt from user_details where user_id = '$data_id' " );

    if( $reccnt->fetch_array()['reccnt'] == 0)
    {
        $qryStmnt = "INSERT INTO user_details (user_id, city, zip, country, lcid) VALUES ('$insID', '$reg_city','$reg_zip', '$reg_country', $lcid)";

        $link->query("insert into user_details  
        (user_id, city, zip,country, groups, target_clients,  target_referral_partners, 
        vocations,  about_your_self ,  upd_reminder_email, upd_public_private ) values 
        ('$data_id', '$upd_city', '$upd_zip', '$upd_country', '$groups', '$target', '$referral', 
        '$voc' , '$about_your_self', '$upd_reminder_email', '$upd_public_private' )"); 
    }
    else 
    {
        $link->query("UPDATE user_details SET city = '$upd_city', zip = '$upd_zip', 
        country = '$upd_country', groups = '$groups', target_clients = '$target', target_referral_partners = '$referral', vocations = '$voc' ,about_your_self = '$about_your_self',upd_reminder_email='$upd_reminder_email',upd_public_private='$upd_public_private' WHERE user_id = '$data_id' ");

        //check group  

        $groupidrow =  $link->query("select id from groups  where grp_name ='$upd_city'");
        if( $groupidrow->num_rows > 0)
        {
            $groupid  = $groupidrow->fetch_array()['id'];
            if( $groupid != '')
            {
                
                $groupexists = $link->query("select groups from user_details where user_id = '$data_id' " );

                
                if($groupexists->num_rows > 0)
                {
                    $groupscsv = $groupexists->fetch_array()['groups'];
                    $currentgroups = explode(',', $groupscsv); 
                    if ( !in_array($groupid ,$currentgroups )) 
                    {
                        if(sizeof($currentgroups) > 0)
                        {
                            $groupscsv .= "," . $groupid;
                        }
                        else 
                        {
                            $groupscsv  =  $groupid;
                        } 
                        $link->query("update user_details set groups ='$groupscsv' where user_id = '$data_id'" );
                    }  
                }
            } 
        } 
    } 
 

}

if(isset($_POST['old_pass'], $_SESSION['username']))
{
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $data_id = $_POST['data_id'];

    $q = $link->query("SELECT user_pass FROM mc_user WHERE id = '$data_id' AND user_pass = '".md5($old_pass)."' ");
    if($q->num_rows > 0){
        $link->query("UPDATE mc_user SET user_pass = '".md5($new_pass)."' WHERE id = '$data_id'");
        echo "Password successfully updated!";
    }else{
        echo "error";
    }
}


// Add/Update Packages
if(isset($_POST['packageData'], $_SESSION['username'])){
    if($_user_role == 'admin') {
        $id = $_POST['packageData']['edit_package'];
        $package_title = $link->real_escape_string($_POST['packageData']['package_name']);
        $package_price = $link->real_escape_string($_POST['packageData']['package_price']);
        $package_limit = $link->real_escape_string($_POST['packageData']['package_dur']);
        $share_limit = $link->real_escape_string($_POST['packageData']['ref_sh_conn']);
        $share_desc = $link->real_escape_string($_POST['packageData']['ref_sh_conn_desc']);
        $ref_limit = $link->real_escape_string($_POST['packageData']['ref_conn']);
        $ref_desc = $link->real_escape_string($_POST['packageData']['ref_conn_desc']);
        $conn_limit = $link->real_escape_string($_POST['packageData']['tar_conn']);
        $conn_desc = $link->real_escape_string($_POST['packageData']['tar_conn_desc']);

        $package_services = $_POST['packageData']['package_services']; #array

        if ($id == 0) {

            $link->query("INSERT INTO `packages` (`package_title`, `package_price`, `package_limit`, `share_limit`, `share_desc`,
                                                  `ref_limit` ,`ref_desc`, `conn_limit`, `conn_desc`) VALUES
                                                  ('$package_title', '$package_price', '$package_limit', '$share_limit', '$share_desc',
                                                    '$ref_limit', '$ref_desc' , '$conn_limit', '$conn_desc')");

            $pkg_id = $link->insert_id;
            foreach ($package_services as $package_service) {
                $link->query("INSERT INTO package_services (`pkg_id`, `services`) VALUES ('$pkg_id', '$package_service')");
            }

        } elseif ($id > 0) {

            $q = $link->query("SELECT `id` FROM `packages` WHERE id = '$id'");

            if ($q->num_rows > 0) {

                $link->query("UPDATE `packages` SET package_title = '$package_title', package_price = '$package_price', package_limit = '$package_limit',
                                                    share_limit = '$share_limit', share_desc = '$share_desc', ref_limit = '$ref_limit', ref_desc = '$ref_desc',
                                                    conn_limit = '$conn_limit', conn_desc = '$conn_desc' WHERE id = '$id'");

                $link->query("DELETE FROM package_services WHERE pkg_id = '$id'");

                foreach ($package_services as $package_service) {
                    $link->query("INSERT INTO package_services (`pkg_id`, `services`) VALUES ('$id', '$package_service')");
                }
            }
        }
        fetchPackages();
    }
}


//Edit package
if(isset($_POST['getPackageData'], $_SESSION['username'])){
    if($_user_role == 'admin'){
        $id = $_POST['getPackageData'];
        $sel_pkg = $link->query("SELECT * FROM packages WHERE id = '$id'");
        if($sel_pkg->num_rows > 0){
            $row_pkg = $sel_pkg->fetch_array();
            $package_title = $row_pkg['package_title'];
            $package_price = $row_pkg['package_price'];
            $package_limit = $row_pkg['package_limit'];
            $share_limit = $row_pkg['share_limit'];
            $share_desc = $row_pkg['share_desc'];
            $ref_limit = $row_pkg['ref_limit'];
            $ref_desc = $row_pkg['ref_desc'];
            $conn_limit = $row_pkg['conn_limit'];
            $conn_desc = $row_pkg['conn_desc'];

            $services = array();
            $srvs_sel = $link->query("SELECT `services` FROM package_services WHERE pkg_id = '$id'");
            if($srvs_sel->num_rows > 0){
                while($row = $srvs_sel->fetch_array()){
                    $services[] = $row['services'];
                }
            }

            $RespMsg = array(
                "package_title" => $package_title,
                "package_price" => $package_price,
                "package_limit" => $package_limit,
                "share_limit" => $share_limit,
                "share_desc" => $share_desc,
                "ref_limit" => $ref_limit,
                "ref_desc" => $ref_desc,
                "conn_limit" => $conn_limit,
                "conn_desc" => $conn_desc,
                "services" => $services
            );
            header('Content-Type: application/json');
            echo json_encode($RespMsg);
        }
    }
}


//Activate/Deactivate package
if(isset($_POST['changePkgStatus'], $_SESSION['username'])){
    if($_user_role == 'admin'){
        $id = $_POST['changePkgStatus'];
        $q = $link->query("SELECT `pkg_status` FROM `packages` WHERE id = '$id'");
        if($q->num_rows > 0){
            $row = $q->fetch_array();
            $pkg_status = $row['pkg_status'];
            $status = $pkg_status == 'activate' ? 'deactivate' : 'activate';
            $link->query("UPDATE `packages` SET `pkg_status` = '$status' WHERE id = '$id'");
            fetchPackages();
        }
    }
}


// Change User Package
if(isset($_POST['changeUserPkg'], $_SESSION['username'])){
    $pkg_id = $_POST['changeUserPkg'];
    $user = $_POST['user'];

    if($_user_role == 'admin'){
        $sel_pkg = $link->query("SELECT `package_title` FROM `packages` WHERE `id` = '$pkg_id'");
        if($sel_pkg->num_rows > 0){
            $pkg_row = $sel_pkg->fetch_array();
            $name = $pkg_row['package_title'];

            $sel_user = $link->query("SELECT `id` FROM `mc_user` WHERE `id` = '$user'");
            if($sel_user->num_rows > 0){
                $link->query("UPDATE `mc_user` SET `user_pkg` = '$name' WHERE id = '$user'");
                echo "200";
            }else{
                echo "user_error";
            }
        }else{
            echo "package_error";
        }
    }
}


// Save page content
if(isset($_POST['savePagesContent'], $_SESSION['user_email'])){
    if($_user_role == 'admin'){
        $data_page = $_POST['savePagesContent'];
        $title = $link->real_escape_string($_POST['title']);
        $content = $link->real_escape_string($_POST['content']);
        $id = $_POST['id_page'];

        if ($id == 0) {
            $link->query("INSERT INTO `pages_data` (`page_name`, `page_title`, `page_content`) VALUES ('$data_page', '$title', '$content')");
        } elseif ($id > 0) {
            $q = $link->query("SELECT `id` FROM `pages_data` WHERE id = '$id'");
            if ($q->num_rows > 0) {
                $link->query("UPDATE `pages_data` SET page_title = '$title', page_content = '$content' WHERE id = '$id'");
            }
        }
    }
}


// Delete page content
if(isset($_POST['delContent'], $_SESSION['user_email'])){
    $id = $_POST['delContent'];
    if($_user_role == 'admin'){
        $link->query("DELETE FROM pages_data WHERE id = '$id'");
    }
}


//Edit page content
if(isset($_POST['edit_content'], $_SESSION['user_email'])){
    $id = $_POST['edit_content'];
    if($_user_role == 'admin'){
        $q = $link->query("SELECT * FROM pages_data WHERE id = '$id'");
        if($q->num_rows > 0){
            $row = $q->fetch_array();

            $RespMsg = array(
                "title" => $row['page_title'],
                "content" => $row['page_content']
            );
            header('Content-Type: application/json');
            echo json_encode($RespMsg);
        }
    }
}


// Add Blog
if(isset($_POST['addBLogName'], $_SESSION['user_email'])){
    $name = $link->real_escape_string($_POST['addBLogName']);
    if(isset($_SESSION['username']) && $_user_role == 'admin'){
        $q = $link->query("SELECt * FROM `blogs` WHERE `blog_name` = '$name'");
        if($q->num_rows > 0){
            echo "match";
        }else{
            if(isset($_POST['update_name'])){
                $update_name = $_POST['update_name'];
                $link->query("UPDATE `blogs` SET `blog_name` = '$name' WHERE `blog_name` = '$update_name'");
                $link->query("UPDATE `blog_details` SET `blog_name` = '$name' WHERE `blog_name` = '$update_name'");
            }else{
                $link->query("INSERT INTO `blogs` (`blog_name`) VALUES ('$name')");
            }
            getBlogs();
        }
    }
}


// Get Blog Name List
if(isset($_POST['BLogNames'], $_SESSION['username'])){
    if($_user_role == 'admin'){
        $q = $link->query("SELECt * FROM `blogs`");
        if($q->num_rows > 0){
            $html = "";
            while($row = $q->fetch_array()){
                $html .= "<option value='".$row['blog_name']."'>".$row['blog_name']."</option>";
            }
            echo $html;
        }
    }
}


// ADD/UPDATE Blog
if(isset($_POST['addBlogContent'], $_SESSION['user_email'])){
    if($_user_role == 'admin'){
        $id = '';
        $blog_name = $_POST['blog_list'];
        $content_title = $_POST['blogTitle'];
        $blog_content = $_POST['blogContent'];

        if(!isset($_POST['data_id'])){
            $link->query("INSERT INTO `blog_details` (`blog_name`, `content_title`, `blog_content`) VALUES ('$blog_name', '$content_title', '$blog_content')");
            $last_id = $link->insert_id;

            if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){
                $img = $_FILES['image']['name'];
                $tmp_img = $_FILES['image']['tmp_name'];
                $ext_img = pathinfo($img, PATHINFO_EXTENSION);
                $name_img = strtotime(date('y-m-d h:i:s')).".".$ext_img;
                move_uploaded_file($tmp_img, '../blog/'.$name_img);

                $link->query("UPDATE `blog_details` SET `blog_image` = '$name_img' WHERE `id` = '$last_id'");
            }

            if(isset($_FILES['video']) && $_FILES['video']['size'] > 0){
                $video = $_FILES['video']['name'];
                $tmp = $_FILES['video']['tmp_name'];
                $ext = pathinfo($video, PATHINFO_EXTENSION);
                $name_vd = strtotime(date('y-m-d h:i:s')).".".$ext;
                move_uploaded_file($tmp, '../blog/'.$name_vd);

                $link->query("UPDATE `blog_details` SET `blog_video` = '$name_vd' WHERE `id` = '$last_id'");
            }
        }elseif(isset($_POST['data_id'])){

            $id = $_POST['data_id'];
            $link->query("UPDATE `blog_details` SET `blog_name` = '$blog_name', `content_title` = '$content_title', `blog_content` = '$blog_content' WHERE id = '$id'");

            if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){
                $img = $_FILES['image']['name'];
                $tmp_img = $_FILES['image']['tmp_name'];
                $ext_img = pathinfo($img, PATHINFO_EXTENSION);
                $name_img = strtotime(date('y-m-d h:i:s')).".".$ext_img;
                move_uploaded_file($tmp_img, '../blog/'.$name_img);

                $link->query("UPDATE `blog_details` SET `blog_image` = '$name_img' WHERE `id` = '$id'");
            }

            if(isset($_FILES['video']) && $_FILES['video']['size'] > 0)
			{
                $video = $_FILES['video']['name'];
                $tmp = $_FILES['video']['tmp_name'];
                $ext = pathinfo($video, PATHINFO_EXTENSION);
                $name_vd = strtotime(date('y-m-d h:i:s')).".".$ext;
                move_uploaded_file($tmp, '../blog/'.$name_vd);
				$link->query("UPDATE `blog_details` SET `blog_video` = '$name_vd' WHERE `id` = '$id'");
            }
        }
        getBlogs();
    }
}

if(isset($_POST['getBlogContent'], $_SESSION['user_email'])){

    if($_user_role == 'admin'){
        $id = $_POST['getBlogContent'];
        $q = $link->query("SELECT * FROM `blog_details` WHERE `id` = '$id'");
        if($q->num_rows > 0){
            $row = $q->fetch_array();
            $blog_name = $row['blog_name'];
            $content_title = $row['content_title'];
            $blog_content = $row['blog_content'];
            $blog_image = $row['blog_image'];
            $blog_video = $row['blog_video'];

            $RespMsg = array(
                "blog_name" => $blog_name,
                "content_title" => $content_title,
                "blog_content" => $blog_content,
                "blog_image" => $blog_image,
                "blog_video" => $blog_video
            );
            header('Content-Type: application/json');
            echo json_encode($RespMsg);
        }
    }
}

//Delete Blogs
if(isset($_POST['deleteBlog'], $_SESSION['username'])){
    if($_user_role == 'admin'){
        $name = $_POST['deleteBlog'];
        $link->query("DELETE FROM blogs WHERE `blog_name` = '$name'");
        $link->query("DELETE FROM blog_details WHERE `blog_name` = '$name'");
        getBlogs();
    }
}


// Delete Blog Content
if(isset($_POST['deleteBlogData'], $_SESSION['username'])){
    if($_user_role == 'admin'){
        $id = $_POST['deleteBlogData'];
        $link->query("DELETE FROM blog_details WHERE `id` = '$id'");
        getBlogs();
    }
}


// Activate / deactivate user
if(isset($_POST['changeUserSts'], $_SESSION['username'])){
    if($_user_role == 'admin'){
        $id = $_POST['changeUserSts'];

        $q = $link->query("SELECT `user_status` FROM `mc_user` WHERE `id` = '$id'");
        if($q->num_rows > 0){
            $row = $q->fetch_array();
            $user_status = $row['user_status'];

            $sts = $user_status == '0' ? 1 : 0;
            $link->query("UPDATE mc_user SET `user_status` = '$sts' WHERE `id` = '$id'");
        }
    }
}


// Search References
if(isset($_POST['locateVoc'])){
    $zipCode = $_POST['srchZipCode']; // filter_input(INPUT_POST, 'srchZipCode', FILTER_SANITIZE_STRING);
    $voc = $_POST['locateVoc'];
    $name = $link->real_escape_string($_POST['ref_name']);
	$lifestyle = $_POST['lifestyle'];
    $entrydate = $_POST['entrydate'];
	$city = $_POST['city'];
    $page = $_POST['pageno'];
    $tag = $_POST['tag'];
    $phone = $_POST['phone'];
 
	
    $where = '';
	
    
        if($zipCode != ''  )
        {
            $where .= " AND client_zip = '" . $link->real_escape_string($zipCode) . "' ";
        }
        
	
	if ($lifestyle != ''  ) {
		$arrlifestyles = explode(',', $lifestyle);
		$where .= " AND (";
		for($i=0; $i < sizeof($arrlifestyles); $i++) {
			$where .= " FIND_IN_SET('". $arrlifestyles[$i] . "', client_lifestyle) "; 
			if($i < sizeof($arrlifestyles) -1) {
				$where .= " OR "; 
			}
		} 
		$where .= ")";
	}
        

		if($voc != ''  )
		{
			$arrvoc = explode(',', $voc);
			$where .= " AND (";
			for($i=0; $i < sizeof($arrvoc); $i++)
			{
				$where .= " FIND_IN_SET('". $arrvoc[$i] . "', client_profession)  ";  	
				if($i < sizeof($arrvoc) -1)
				{
					$where .= " OR "; 
				}
			} $where .= ")";
		}

		if($city != ''   )
		{
			$arrcity = explode(',', $city);
			$where .= " AND (";
			for($i=0; $i < sizeof($arrcity); $i++)
			{
				$where .= " FIND_IN_SET('". $arrcity[$i] . "', client_location)  "; 
				if($i < sizeof($arrcity) -1)
				{
					$where .= " OR "; 
				}
			} 
			$where .= ")";
        } 
        
        if($tag != ''   )
		{
			$arrtags = explode(',', $tag);
			$where .= " AND (";
			for($i=0; $i < sizeof($arrtags); $i++)
			{
				$where .= " FIND_IN_SET('". $arrtags[$i] . "', tags)  "; 
				if($i < sizeof($arrtags) -1)
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
    
    if($_user_role == 'admin')
	{
 
        if($phone != ''  )
        {	 
            $where .= " AND user_phone =  '".  $phone  . "' ";
        } 

    }
	else
	{
        if($phone != ''  )
        {	 
            $where .= " AND client_phone =  '".  $phone  . "' ";
        } 
    }

 
   
    if($page == 0) 
        $page = 1;
	 
    if($_user_role == 'admin')
	{
		if($name != '')
		{
        	$where .= " AND username LIKE '$name%'";
    	} 
		echo  getMyCityUsersAdmin($page, $where , $voc, $name );
    }
	else
	{   
		if($name != ''){
        $where .= " AND client_name LIKE '$name%'";
 	   } 
        echo    searchReferences($user_id, $page,$where, $voc, $name);
    }  
}
 
// Search logs
if(isset($_POST['getSearchlogs'])){

	$page = $_POST['getSearchlogs'];
    if($_user_role == 'admin'){
        getSearchLogs($page);
    }
}
// Home Search logs
if(isset($_POST['getHomeSearchlogs']))
{
    $page = $_POST['getHomeSearchlogs'];
    if($_user_role == 'admin'){
        getHomeSearchLogs($page);
    }
}

// Delete user
if(isset($_POST['delUser'])){
    $userID = $_POST['delUser'];
    if(!$userID || empty($userID) || $userID == '')
	{
        echo "user_error";
        exit();
    }
    else
	{
        global $link;  
        $link->query("DELETE FROM user_rating WHERE user_id='$userID' ");
        $user_rating = array("Error:" => $link->error, "AffRows:" => $link->affected_rows);

        $link->query("DELETE FROM user_people WHERE user_id='$userID' ");
        $user_people = array("Error:" => $link->error, "AffRows:" => $link->affected_rows);

        $link->query("DELETE FROM user_messages WHERE user_id='$userID' OR sender_id='$userID' ");
        $user_messages = array("Error:" => $link->error, "AffRows:" => $link->affected_rows);

        $link->query("DELETE FROM user_details WHERE user_id='$userID' ");
        $user_details = array("Error:" => $link->error, "AffRows:" => $link->affected_rows);

        $delQ5 = $link->query("DELETE FROM mc_user WHERE id='$userID' ");
        $mc_user = array("Error:" => $link->error, "AffRows:" => $link->affected_rows);

        $resp = array(
            "userID" => $userID,
            "user_rating" => $user_rating,
            "user_people" => $user_people,
            "user_messages" => $user_messages,
            "user_details" => $user_details,
            "mc_user" => $mc_user,
        );
        $delResp = (object)$resp;
        print_r($delResp);
    }
}


// Reset password
if(isset($_POST['resetPW'])){
    $emAdd = $_POST['resetPW'];
    
    if(empty($emAdd) || $emAdd == ''){
        return "Sorry, email address cannot be empty.";
    }
    global $link;

    $userInfoQ = $link->query("SELECT * FROM mc_user WHERE user_email = '$emAdd' ");
    if($userInfoQ->num_rows < 1){
        echo "Sorry, email address not found.";
        exit();
    }  
    $userInfoFet = $userInfoQ->fetch_assoc();
    $userID = $userInfoFet['id'];
    $userName = $userInfoFet['username'];

    $token = md5($date_time);
    $tokenExp = date("Y-m-d H:i:s", strtotime('+1 days', strtotime($date_time)));

    $updQ = $link->query("UPDATE mc_user SET resPWToken='$token', resPWExp='$tokenExp', user_pass='123' WHERE id='$userID' ");
    if($updQ) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: noreply@mycity.com';

        $msg = "<!DOCTYPE html><html>
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
                            <td style='padding: 10px 10px 30px 10px;'>
                                <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>" . $userName . "</span></div>
                                <br />
                                <div>This link is valid for limited time period.</div>
                                <br />
                                <div>Reset your password by clicking <a href='http://www.mycity.com/verify/?token=" . $token . "&id=" . $userID . "' target='_blank'>here</a> </div>
                                <br />
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
        //  mail($emAdd, 'Reset password', $msg, $headers);
        sendemail( $emAdd ,   'Reset password'  , $msg, $msg1 );
        $msg2 = "<!DOCTYPE html><html>
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
                            <td style='padding: 10px 10px 30px 10px;'>
                                <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>" . $userName . "</span></div>
                                <br />
                                <div>$userName (ID:$userID) has opted to reset his password.</div>
                                <br />
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

    //mail("bob@mycity.com", "$userName is about to Reset his password", $msg2, $headers);
    sendemail( "bob@mycity.com"  ,  "$userName is about to Reset his password"  , $msg2, $msg1 );
    echo "success";
    }
    else
    {
        echo "Sorry, something went wrong. Try again please.";
    }
}

// Resend Token for rest password
if(isset($_POST['resetToken'])){
    $data = $_POST['resetToken'];
    $userID = $data['userID'];
    $emAdd = $data['userEmail'];
    $userName = $data['username'];

    $userInfoQ = $link->query("SELECT * FROM mc_user WHERE user_email = '$emAdd' ");
    if($userInfoQ->num_rows < 1){
        echo "Sorry, email address not found.";
        exit();
    }
    
    $token = md5($date_time);
    $tokenExp = date("Y-m-d H:i:s", strtotime('+1 days', strtotime($date_time)));

    $updQ = $link->query("UPDATE mc_user SET resPWToken='$token', resPWExp='$tokenExp', user_pass='123' WHERE id='$userID' ");
    if($updQ) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: noreply@mycity.com';

        $msg = "<!DOCTYPE html><html>
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
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>" . $userName . "</span></div>
                        <br />
                        <div>This link is valid for limited time period.</div>
                        <br />
                        <div>Reset your password by clicking <a href='http://www.mycity.com/verify/?token=" . $token . "&id=" . $userID . "' target='_blank'>here</a> </div>
                        <br />
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
        //mail($emAdd, 'Reset password', $msg, $headers);
        sendemail( $emAdd ,  "Reset password"  , $msg, $msg );


        $msg2 = "<!DOCTYPE html><html>
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
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>" . $userName . "</span></div>
                        <br />
                        <div>$userName (ID:$userID) has opted to reset his password.</div>
                        <br />
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
       // mail("bob@mycity.com", "$userName is about to Reset his password", $msg2, $headers);

        sendemail( "bob@mycity.com"  ,   "$userName is about to Reset his password"  , $msg2, $msg2 );


        echo "success";
    }
    else {
        echo "Sorry, something went wrong. Try again please.";
    }
}

// Update password
if(isset($_POST['updPW'])){
    $data = $_POST['updPW'];
    $userID = $data['userID'];
    $emAdd = $data['userEmail'];
    $userName = $data['userName'];
    $newPW = $data['newPW'];
    $newPW2 = $data['newPW2'];

    if($newPW != $newPW2){
        echo "Sorry, password not matched";
        exit();
    }

    $userInfoQ = $link->query("SELECT * FROM mc_user WHERE user_email = '$emAdd' ");
    if($userInfoQ->num_rows < 1){
        echo "Sorry, email address not found.";
        exit();
    }
    $newPWmd5 = md5($newPW);

    $updQ = $link->query("UPDATE mc_user SET resPWToken='', resPWExp='', user_pass='$newPWmd5' WHERE id='$userID' ");
    if($updQ) {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: noreply@mycity.com';

        $msg = "<!DOCTYPE html><html>
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
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>" . $userName . "</span></div>
                        <br />
                        <div>Your password is updated.</div>
                        <br />
                        <div>You may now login by clicking <a href='http://www.mycity.com/' target='_blank'>here</a> </div>
                        <br />
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
       // mail($emAdd, 'Reset password', $msg, $headers);
        sendemail(  $emAdd ,   "Reset password"  , $msg, $msg );


        $msg2 = "<!DOCTYPE html><html>
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
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'>" . $userName . "</span></div>
                        <br />
                        <div>$userName (ID:$userID) has reset his password.</div>
                        <br />
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

        //mail("bob@mycity.com", "$userName has Reset his password", $msg2, $headers);
        sendemail(  "bob@mycity.com"  ,   "$userName has Reset his password"  , $msg2, $msg2 );
        echo "success";
    }
    else {
        echo "Sorry, something went wrong. Try again please.";
    }
}

 
//api
//Send Feedback
if(isset($_POST['fback_coment'])){

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: noreply@mycity.com';

        $msg2 = "<!DOCTYPE html><html>
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
                    <td style='padding: 10px 10px 30px 10px;'>
                      
                        <div>Full Name: ".$_POST['fback_name']."</div>
                        <br />
						 <div>Email: ".$_POST['fback_email']."</div>
                        <br />
						 <div>Comment: ".$_POST['fback_coment']."</div>
                        <br />
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
    //mail("bob@mycity.com", "Mycity.com Feedback submitted", $msg2, $headers);
    sendemail(  "bob@mycity.com"  ,   "Mycity.com Feedback submitted"  , $msg2, $msg2 );
    echo "success"  ; 
}
 

// ******** Add help explanation ********
//api
if(isset($_POST['addhelpexp']))
{
	if($_user_role == 'admin')
	{
		$title = $_POST['title'];
		$helpbody = $_POST['helpbody'];
		$faqid = $_POST['faqid'];
		
		if($faqid == 0 )
		{
			//insert
			$link->query("INSERT INTO helps (helptitle, helptext, publish) VALUES ('$title','$helpbody', '1')");
		}
		else
		{
			//update
			$link->query("UPDATE helps SET helptitle ='$title' , helptext = '$helpbody' WHERE id= '$faqid' ");
		}  
    } 
}  

//api converted
//for public
if(isset($_GET['getallfaqs']) && $_GET['getallfaqs'] == '2')
{  
	$helpStmt = "SELECT * FROM helps ORDER BY position asc";
    $faqs = $link->query($helpStmt);
	 
    if($faqs->num_rows > 0)
	{ 
		$html = "<div class='panel-group' id='accordion' role='tablist' aria-multiselectable='true'>";
							
		$i = 1;					
        while($row_help = $faqs->fetch_array())
		{
             $html .="<div  class='panel panel-default'>
    <div class='panel-heading' role='tab' id='head$i'>
      <h4 class='panel-title'>
        <a role='button' data-toggle='collapse' data-parent='#accordion' href='#col$i' aria-expanded='true' aria-controls='collapseOne'>
          " . $row_help['helptitle'] . "
        </a>
      </h4>
    </div>
    <div id='col$i' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head$i'>
      <div class='panel-body'>
          " . $row_help['helptext'] . "
      </div>
    </div>
  </div>";
			$i++;
		} 
	} 
	
	$html .= "</div>";
	echo $html; 
} 

//Update FAQ position
if(isset($_POST['updfaqpos']))
{
	if($_user_role == 'admin')
	{
		$position = $_POST['position']+1;
		$id = $_POST['id']; 
		
		if($id >  0 )
		{ 
			//update
			$link->query("UPDATE helps SET position ='$position'  WHERE id= '$id' ");
		}  
    } 
}
//Get All Trigger
if(isset($_POST['getTriggers'])){
    $trigQ = $link->query("SELECT * FROM my_triggers ");
    $html = "";
    while($row = $trigQ->fetch_array()){
        $html .= "<option value='".$row['id']."'>".$row['trigger_question']."</option>";
    }
    echo $html;
} 

//fetch group partners
if(isset($_POST['srchnewusers']))
{ 
	$groupid = $_POST['groupid'];
	$userlist = $link->query("SELECT * FROM user_details as a inner join mc_user as b on b.id = a.user_id WHERE b.group_status  = '0' ");
	
	if($userlist->num_rows > 0)
	{
		$users = array();
		$html ='<table class="table table-condensed">
			<thead>
			<tr><th>Name</th>
			<th>Email</th> 
			<th>Group Name</th>
			<th>Package</th>
			<th>Approve Group</th>
			</tr>
		</thead><tbody>';
		while($row = $userlist->fetch_array())
		{
			$groupids = explode(',', $row['groups'] );
			$groupname='';
			for($i=0; $i< sizeof($groupids); $i++ )
			{
				$groups = $link->query("SELECT * FROM groups WHERE id='". $groupids[$i] ."'");
				if($groups->num_rows > 0 )
				{
					$grow = $groups->fetch_array();
					$groupname .= $grow['grp_name'];
					
					if($i < sizeof($groupids) - 1)
					{
						$groupname .= ", ";
					}
				}
			} 
			$html .='<tr>
			<td>'. $row['username'] . '</td>
			<td>'. $row['user_email'] . '</td> 
			<td><strong>'.  $groupname . '</strong></td>
			<td>'. $row['user_pkg'] . '</td>
			<td style="width: 140px">
			 <div class="btn-group" id="status" data-toggle="buttons">
              <label class="btn btn-default btn-on ">
              <input class="grpstatus" type="radio" value="1" data-userid="'.$row['user_id'].'" name="grpstatus"  >YES</label>
              <label class="btn btn-default btn-off active">
              <input class="grpstatus" type="radio" value="0" data-userid="'.$row['user_id'].'" name="grpstatus" checked="checked">NO</label>
            </div>
			</td>
			</tr>';
		} 
		$html .= "</tbody></table>";
		echo $html;
	} 
	else
	{
		echo "<p class='alert alert-info'>All clients group access permission processed!</p>";
	}
} 

//Update Group Status
if(isset($_POST['updgrpstate'])){
	
	$grpstatus = $_POST['grpstatus'];
	$userid = $_POST['userid'];
	$trigid = $_POST['currTrigID'];
	$link->query("UPDATE  mc_user  SET  group_status  = '$grpstatus' WHERE `id` = '$userid'"); 
}

//api converted
//fetch group partners
if(isset($_POST['getgrpmembers'])){
	
	$groupid = $_POST['groupid'];
	$userlist = $link->query("SELECT * FROM user_details as a inner join mc_user as b on b.id = a.user_id 
	where (FIND_IN_SET('$groupid', groups)) AND  b.id != '1' and b.id!='$user_id' and user_pkg='Gold'");
	  
	if($userlist->num_rows > 0)
	{
		$users = array();
		while($row = $userlist->fetch_array())
		{
		   $users[] = array('id' =>  $row['user_id']  ,
		   'username' =>  $row['username']   ); 
		}
		$json = json_encode($users);
		echo $json;
	} 
}
 

//api
 // Get Profile 
if(isset($_POST['srchClient'])){
	
	$clientid = $_POST['clientid'];
	$results = $link->query("SELECT * FROM user_details as a inner join mc_user as b on b.id = a.user_id 
    where user_id='$clientid' AND  b.id != 1");
    
    if($results->num_rows > 0)
	{
        $users = array();
        while($row = $results->fetch_array())
        {
            $target_clients = explode(",", $row["target_clients"]);
			$target_referral_partners = explode(",", $row["target_referral_partneand b.id!='$user_id' and user_pkg='Gold'rs"]);
			$user_picutre = !empty($row['image']) ? "images/" . $row['image'] : "images/no-photo.png"; 
			$str = "abcdefghijklmnopqrstuvwxyz";
			$rand = substr(str_shuffle($str),0,3);
			 
            //get triggers 
            $triggers = $link->query("SELECT * FROM my_triggers WHERE user_id = '" .  $row["user_id"] .   "'");
            
            
		 
            $html .= '<div class="panel panel-default">
            <div class="panel-body">
            <div class="row">
            <div class="col-md-2">
            <img src=\''.$user_picutre.'\' alt="' .  $row["username"] . '" class="img-rounded" height="120" width="120">
            </div>
            <div class="col-md-9">
                <p><strong>Name:</strong>'.$row["username"].'</p>
                <p><strong>Email:</strong>'.$row["user_email"].'</p>
                <p><strong>Phone:</strong>'.$row["user_phone"].'</p>';
            
            
       $refcounter = $link->query("SELECT count(*) as refcnt FROM `referralsuggestions` where knowreferedto='". $row["user_id"] ."' 
          and knowenteredby='$user_id'")->fetch_array();
          
          
           if($refcounter['refcnt']==0)
           {
               $html .='<p class="badge-red-o">You have not sent any referral to '.$row["username"].'</p>';
           }
            else
            {
                $html .='<p class="badge-green-o">You have sent '. $refcounter['refcnt']. ' referral to '. $row["username"].'</p>';
            }
            
            $html .='</div> 
		<div class="col-md-1">
			<button data-toggle=\'modal\' id=\''. $row["user_id"] . '\' data-target=\'#myModal\' class=\'btn-primary btn btn-xs leaveMsg\'><i class=\'fa fa-envelope\'></i></button>
		</div>
		<div class="col-md-12">
		<hr/>
		<p><strong>About</strong></p>'.$row["about_your_self"].'<hr/>
		<p><strong>Target Clients</strong></p>'.implode(", ", $target_clients).'<hr/>
		<p><strong>Target Referral Partners</strong></p>'.implode(", ", $target_referral_partners).'<hr/>';
		
		$html .= '<p><strong>Triggers</strong></p>';
		if($triggers->num_rows > 0)
		{
			while($trigrow = $triggers->fetch_array())
			{
				$html .= $trigrow["trigger_question"] . "<br/>" ;
			}
		}				
		else 
		{
			$html .='<p>No Trigger Present</p>';
		}
			$html .='</div></div></div></div>';
	  }
	  
	  echo $html;
	  
	} 
} 

// Autocomplete Namepart 
if(isset($_POST['namekey']))
{
    $namekey = $_POST['namekey'];
	$userlist = $link->query("SELECT * FROM mc_user where username like '%$namekey%' and user_role='user'");
	 
	if($userlist->num_rows > 0)
	{
		$users = array();
		while($row = $userlist->fetch_array())
		{
		   $users[] = array('value' =>  $row['username']  ,
		   'label' =>  $row['username']  ,
		   'desc' => "a pure-JavaScrip",
		   'icon' =>  $row['image']    ); 
		}
		$json = json_encode($users);
		echo $json;
	} 
}
 
if(isset($_POST['srcgrppartners']))
{
    
    $html = "";
	$final = null; 
	$user = $link->query("SELECT * FROM user_people where user_id = '$user_id'");
	$user = $user->fetch_array();
	$groups = explode(",", $user['groups']);
	$query = "SELECT * FROM mc_user a LEFT JOIN user_details b on a.id = b.user_id WHERE a.username LIKE '%" . $nameSrch . "%'";
	$notAdmin = " AND a.id != 1";
	$whereGroup = "(FIND_IN_SET('".implode("', b.groups) OR FIND_IN_SET('", $groups)."', b.groups))";
	
	$results = $link->query($query . " AND " . $whereGroup . $notAdmin);
	
	if($results->num_rows > 0) {
		while($row = $results->fetch_array()) {
			$target_clients = explode(",", $row["target_clients"]);
			$target_referral_partners = explode(",", $row["target_referral_partners"]);
			$user_picutre = !empty($row['image']) ? "images/" . $row['image'] : "images/no-photo.png"; 
			$str = "abcdefghijklmnopqrstuvwxyz";
			$rand = substr(str_shuffle($str),0,3);
			 
		//get triggers 
		$triggers = $link->query("SELECT * FROM my_triggers WHERE user_id = '" .  $row["user_id"] .   "'");
		 
		$html .= '<div class="panel panel-default">
		<div class="panel-body">
		<div class="row">
		<div class="col-md-2">
		<img src=\''.$user_picutre.'\' alt="' .  $row["username"] . '" class="img-rounded" height="120" width="120">
		</div>
		<div class="col-md-9">
			<p><strong>Name:</strong>'.$row["username"].'</p>
			<p><strong>Email:</strong>'.$row["user_email"].'</p>
			<p><strong>Phone:</strong>'.$row["user_phone"].'</p>
		</div> 
		<div class="col-md-1">
			<button data-toggle=\'modal\' id=\''. $row["user_id"] . '\' data-target=\'#myModal\' class=\'btn-primary btn btn-xs leaveMsg\'><i class=\'fa fa-envelope\'></i></button>
		</div>
		<div class="col-md-12">
		<hr/>
		<p><strong>About</strong></p>'.$row["about_your_self"].'<hr/>
		<p><strong>Target Clients</strong></p>'.implode(", ", $target_clients).'<hr/>
		<p><strong>Target Referral Partners</strong></p>'.implode(", ", $target_referral_partners).'<hr/>';
		
		$html .= '<p><strong>Triggers</strong></p>';
		if($triggers->num_rows > 0)
		{
			while($trigrow = $triggers->fetch_array())
			{
				$html .= $trigrow["trigger_question"] . "<br/>" ;
			}
		}				
		else 
		{
			$html .='<p>No Trigger Present</p>';
		}
			$html .='</div></div></div></div>';
	  }
	}
	else
	{
		$html = "No results found";
	}
	echo $html; 
}


//fetch group partners
if(isset($_POST['getallpost']))
{
	$posts = $link->query("SELECT * FROM blog_posts order by post_date desc "); 
	if($posts->num_rows > 0)
	{
		$html ='<table class="table table-condensed">
			<thead>
			<tr>
			<th>Post Title</th> 
			<th>Post Date</th> 
			<th>Publish</th> 
			<th>Manage</th> 
			</tr>
		</thead><tbody>'; 
		while($row = $posts->fetch_array())
		{
			$html .='<tr><td>'. $row['post_title'] . '</td> 
			<td>'. $row['post_date'] . '</td>  
			<td style="width: 140px">
			 <div class="btn-group" id="status" data-toggle="buttons">';
			  
			  if($row['post_status'] == "publish")
			  {
				$html .='<label class="btn btn-default btn-on active">
				<input  type="radio" value="publish" data-postid="'.$row['id'].'" name="poststatus" checked="checked" >YES</label>';
				$html .=' <label class="btn btn-default btn-off ">
				<input  type="radio" value="draft" data-postid="'.$row['id'].'" name="poststatus" >NO</label>'; 
			  }
			  else
			  {
				$html .='<label class="btn btn-default btn-on ">
				<input  type="radio" value="publish" data-postid="'.$row['id'].'" name="poststatus"  >YES</label>';
				$html .=' <label class="btn btn-default btn-off active">
				<input  type="radio" value="draft" data-postid="'.$row['id'].'" name="poststatus" checked="checked" >NO</label>'; 
			  }
			  
			  $html .='</div></td><td>
			  <a href="#editpost"  data-toggle="tab" data-postid="'.$row['id'].'" class="btn btn-primary editpost">Edit</a>
			  </td>
			  </tr>' ;
		} 
		$html .= "</tbody></table>";
		echo $html;
	} 
	else
	{
		echo "<p class='alert alert-info'>No blog post added yet!</p>";
	}
}
 

//api
// Save page content
if(isset($_POST['savepost'] ))
{
    if($_user_role == 'admin')
	{ 
       if(  $_POST['savepost'] == 2)
	   {
		   $postid =  $_POST['postid'] ;
		   $title = $link->real_escape_string($_POST['title']);
		   $post = $link->real_escape_string($_POST['content']);
		   $id = $link->real_escape_string($_POST['content']);
		   $link->query("UPDATE blog_posts SET  post_title= '$title', post_content = '$post' WHERE id='$postid' ");
	   }
	   else if(  $_POST['savepost'] == 3)
	   {
		   //save status 
		   $status =  $_POST['status'] ;
		   $postid =  $_POST['postid'] ;
		    $link->query("UPDATE blog_posts SET  post_status= '$status'  WHERE id='$postid' ");
	   }
	   else if(  $_POST['savepost'] == 1)
	   {
		   $title = $link->real_escape_string($_POST['title']);
		   $post = $link->real_escape_string($_POST['content']);
		   $link->query("INSERT INTO blog_posts ( post_author, post_date, post_title, post_content ) 
		   VALUES ('admin', NOW(), '$title', '$post')");
	   }
    }
}


if(isset($_POST['getpostforediting'] ))
{
	if($_user_role == 'admin')
	{
        $id = $_POST['postid'];
        $q = $link->query("SELECT * FROM `blog_posts` WHERE `id` = '$id'");
        if($q->num_rows > 0){
            $row = $q->fetch_array();
            $post_title = $row['post_title']; 
            $post_content = $row['post_content'];
            $post_id = $row['id'];
            $RespMsg = array(
                "post_title" => $post_title,
                "post_content" => $post_content ,
				"post_id" => $post_id 
            );
            header('Content-Type: application/json');
            echo json_encode($RespMsg);
        }
    }
}
//get all post
if(isset($_POST['getallpost'] ))
{
	if($_user_role == 'admin')
	{
		$id = $_POST['postid'];
        $q = $link->query("SELECT * FROM `blog_posts` WHERE `id` = '$id'");
        if($q->num_rows > 0){
            $row = $q->fetch_array();
            $post_title = $row['post_title']; 
            $post_content = $row['post_content'];
            $post_id = $row['id'];
			$post_date = date('m/d/Y',strtotime($row['post_date'] ));
			$comment_count =  $row['comment_count'];
			$comment_status =  $row['comment_status'];
            $RespMsg = array(
                "post_title" => $post_title,
                "post_content" => $post_content ,
				"post_id" => $post_id ,
				"post_date" => $post_date,
				"comment_count" => $comment_count,
				"comment_status" => $comment_status 				
            );
            header('Content-Type: application/json');
            echo json_encode($RespMsg);
        }
    }
} 

//get all post
if(isset($_POST['getrecentpost'] ))
{
	$id = $_POST['postid'];
	$posts = $link->query("SELECT * FROM `blog_posts` WHERE post_status='publish' ORDER By ID desc");
	if($posts->num_rows > 0)
	{
		while($row = $posts->fetch_array())
		{
			$post_title = $row['post_title']; 
			$post_content = $row['post_content'];
			$post_id = $row['id'];
			$post_date = date('m/d/Y',strtotime($row['post_date'] ));
			$comment_count =  $row['comment_count'];
			$comment_status =  $row['comment_status'];
			$postdetails[] = array(
				"post_title" => $post_title,
				"post_date" => $post_date,
				"post_content" =>  substr( $post_content , 0, 300)  ,
				"post_id" => $post_id ,
				"comment_count" => $comment_count,
				"comment_status" => $comment_status 
			);
		}  
		header('Content-Type: application/json');
		echo json_encode($postdetails);
    } 
}
//read post
if(isset($_POST['readpost'] ))
{
	$id = $_POST['postid'];
	$q = $link->query("SELECT * FROM `blog_posts` WHERE `id` = '$id'");
	if($q->num_rows > 0)
	{
		$row = $q->fetch_array();
		$post_title = $row['post_title']; 
		$post_content = $row['post_content'];
		$post_id = $row['id'];
		$post_date = date('m/d/Y',strtotime($row['post_date'] ));
		$comment_count =  $row['comment_count'];
		$comment_status =  $row['comment_status'];
		$RespMsg = array(
                "post_title" => $post_title,
                "post_content" => $post_content ,
				"post_id" => $post_id ,
				"post_date" => $post_date,
				"comment_count" => $comment_count,
				"comment_status" => $comment_status
		);
		header('Content-Type: application/json');
		echo json_encode($RespMsg);
	}
}

// Save post comment
if(isset($_POST['savecomment'] ))
{
    if(  isset($_POST['postid'] ) && isset($_POST['name'] ) 
	&& isset($_POST['email'] ) && isset($_POST['comment'] ) )
	{
		$comment = $link->real_escape_string($_POST['comment']);
		$email =  $_POST['email'] ;
		$name =  $_POST['name'] ;
		$postid =  $_POST['postid'] ;
		$link->query("INSERT INTO blog_comment ( post_id, name, email, comment , status, post_date ) 
		   VALUES ('$postid', '$name', '$email', '$comment', '0', NOW())");
    } 
}


// Autocomplete vocationsearch 
if(isset($_POST['vocationsearch']))
{
    $vocation = $_POST['vocation'];
	$vocationlist = $link->query("SELECT * FROM vocations where voc_name like '%$vocation%' ORDER BY voc_name");
	 
	if($vocationlist->num_rows > 0)
	{
		$vocs = array();
		while($row = $vocationlist->fetch_array())
		{
		   $vocs[] = array('name' =>  $row['voc_name'] ); 
		}
		$json = json_encode($vocs);
		echo $json;
	} 
}


//converted to api
//fetched rated partners
if(isset($_POST['getratedpartners']))
{
	$selGrp = $_POST['selGrp'];
    $selVoc = $_POST['selVoc']; 
    $q = $link->query("SELECT * FROM user_people WHERE user_id = '$user_id' ".$where." ORDER BY client_name ASC  ");
	$html = "No records found!";
    if($q->num_rows > 0)
	{ 
        $pg = $link->query("SELECT * FROM user_people WHERE user_id = '$user_id' AND client_profession='$selVoc'
        AND  FIND_IN_SET('$selGrp',  user_group ) > 0  " ); 
        $html = '<table class="table table-responsive">
			<thead>
			<tr>
			<th>Reference Name</th>
			<th>Vocation</th>
			<th>Phone</th>
			<th>Email</th>
			<th>Location</th>
			<th>Group</th>
			<th>Ratings</th>
			</tr>
        </thead>';  
 

        while($row = $pg->fetch_array())
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
			
			if($user_ranking >= 20 )
			{ 
				$html .= "<tr id='$rand-$id'>
					<td>$client_name</td>
					<td>$client_profession</td>
					<td>$client_phone</td>
					<td>$client_email</td>
					<td>$client_location</td>
					<td>$userGrpName</td>
					<td>$user_ranking</td> 
				</tr>";
			}
        }
        $html .= '</table>';
    }
	echo $html;
}




//fetch interested vocations THIS CODE HAS BEEN REPLACED BY generatesmartsuggest
if(isset($_POST['savesmartsuggest']))
{
	$user = $link->query("SELECT * FROM user_details where user_id = '$user_id'");
	$user = $user->fetch_array();
	$groups = explode(",", $user['groups']);
	$professions = $_POST['professions'];
	$newknowid = $_POST['newknowid'];
	
	//get all partners
	$partners = array();
	for($i=0; $i < sizeof($groups); $i++ )
	{
		$groupid = $groups[$i];
		$userlist = $link->query("SELECT * FROM user_details as a inner join mc_user as b on b.id = a.user_id 
		where (FIND_IN_SET('$groupid', groups)) AND  b.id != '1' and b.id!='$user_id' and user_pkg='Gold'");
		
		if($userlist->num_rows > 0)
		{
			while($row = $userlist->fetch_array())
			{
				$partners[] = array('id' =>  $row['user_id']  , 'username' =>  $row['username'], 'useremail' =>  $row['user_email'],  'useremail' =>  $row['user_email']  ); 
			}
		} 
	} 
	//my knows/references
	$myknows  =  getMyReferences($link,  $user_id, $professions)  ;
	//saving my knows referrals 
	for($k=0; $k < sizeof($myknows); $k++)
	{ 
		if($myknows[$k]['user_id'] != $user_id) {
			if($myknows[$k]['client_email'] != $row['user_email']) {
		$link->query("INSERT INTO referralsuggestions 
		( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby) 
			VALUES ('". $myknows[$k]['user_id'] . "', '". $myknows[$k]['id'] . "', 
			'$newknowid' ,  NOW() ,  '$user_id') ");
			}
		}
	}
  
	// from the partners get each rows references. Then make an algorithm to show them.
	$partnerknows = array();
	for($i=0; $i < sizeof($partners); $i++)
	{
		$temparray =  getMyReferences($link,  $partners[$i]['id'], $professions);
		if (!empty($temparray))
		{ 
			for($k=0; $k < sizeof($temparray); $k++)
			{ 
				if($temparray[$k]['user_id'] != $user_id) {
					if($temparray[$k]['client_email'] != $partners[$i]['useremail']) {
				$link->query("INSERT INTO referralsuggestions 
				( partnerid, knowtorefer,knowreferedto, entrydate, knowenteredby) 
					VALUES ('". $temparray[$k]['user_id'] . "', '". $temparray[$k]['id'] . "', 
					'$newknowid' ,  NOW() ,  '$user_id') ");
					}
				}
			  
			} 
		} 	
	} 
} 
  
    
//generate smarty suggestion
if(isset($_POST['generatesmartsuggest']))
{
    $user = $link->query("SELECT * FROM user_details where user_id = '$user_id'");
    $user = $user->fetch_array();
    $groups = explode(",", $user['groups']);
    $professions = $_POST['professions'];
    $newknowid = $_POST['newknowid'];
    $sourcezip = $_POST['sourcezip']; //zip code of the new know
	
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
     
    //second making main query
    $professionlist = explode(",",  $professions); 
	$where_group = " ( "; 
	for($i=0; $i < sizeof($professionlist); $i++ )
	{
        $where_group .= " find_in_set ( '". $professionlist[$i] . "' , p.client_profession  ) "; 
        if( $i < sizeof($professionlist)-1 )
		{
            $where_group .=  " OR ";
		}
	}
    $where_group .= " ) ";
    
    $mainQry = "SELECT p.*,  SUM(r.ranking) as rank 
    FROM user_people as p INNER JOIN user_rating as r on p.id=r.user_id INNER JOIN user_answers as a on p.id = a.user_id 
    WHERE p.user_id IN  ( $qryInner )  AND " . $where_group . " GROUP BY p.id ORDER BY client_name" ;
				
	$userpeople = $link->query( $mainQry );
                 
    
	$references = array();	 
	if($userpeople->num_rows > 0)
	{
		while($row = $userpeople->fetch_array())
		{
			$id = $row['id']; 
			$user_ranking = $row['rank']; 
			$userm = $link->query("SELECT user_email FROM mc_user where id = '".$row['user_id']."'");
            $userm = $userm->fetch_array();
            
            

			if($userm['user_email'] != $row['client_email']) {
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
				$targetzip = $row['client_zip'];
				if($targetzip != "")
				{
					 if(checkExistingReferral( $row['user_id'] ,$row['id'] ,$newknowid,$user_id  ) == 0 )
					 {
						 if($row['user_id'] != $user_id) {
						 $link->query("INSERT INTO referralsuggestions 
							( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby , sourcezip, targetzip, ranking) 
							VALUES ('".  $row['user_id'] . "', '". $row['id'] . "', 
                            '$newknowid' ,  NOW() ,  '$user_id' , '$sourcezip' , '$targetzip' ,   '$user_ranking' )");
                            


                            echo "INSERT INTO referralsuggestions 
							( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby , sourcezip, targetzip, ranking) 
							VALUES ('".  $row['user_id'] . "', '". $row['id'] . "', 
							'$newknowid' ,  NOW() ,  '$user_id' , '$sourcezip' , '$targetzip' ,   '$user_ranking' )";
                         }
                         
                        
						
					 } 
				} 
		}}
        }
	}       
}


//Get Suggested Connections
if(isset($_POST['getsuggestedconnects']))
{
	$group =  $_POST['group'];
	$vocation = $_POST['vocation']; 
	//echo "SELECT * FROM user_people WHERE user_id = '$user_id' ".$where." ORDER BY client_name ASC  ";
	
	$q = $link->query("SELECT * FROM user_people WHERE user_id = '$user_id' ".$where." ORDER BY client_name ASC  ");
	$html = "No records found!";
    if($q->num_rows > 0)
	{
	 
		$pg = $link->query( "SELECT * FROM user_people WHERE user_id IN 
		( SELECT user_ID  FROM user_details WHERE FIND_IN_SET('$group', groups ) > 0 ) 
		AND client_profession='$vocation'" );
		$html = '<table class="table table-responsive">
		<thead>
			<tr>
			<th>Reference Name</th> 
			<th>Phone</th>
			<th>Email</th>
			<th>Location</th>
			<th>Group</th>
			<th>Ratings</th>
			<th>Partner</th>
			</tr>
        </thead>';
        
		while($row = $pg->fetch_array())
		{
            $id = $row['id'];
            $client_name = $row['client_name']; 
            $client_phone = $row['client_phone'];
            $client_email = $row['client_email'];
            $client_location = $row['client_location'];
            $user_group = $row['user_group'];
			$partner_id= $row['user_id'];
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
			$rate_q = $link->query("SELECT SUM(`ranking`) user_ranking FROM user_rating WHERE `user_id` = '$id'");
            $rate_row = $rate_q->fetch_array();
            $user_ranking = $rate_row['user_ranking'];
			$p_det = $link->query("SELECT user_email, username, city, zip, country, vocations FROM mc_user as u inner join user_details as ud on u.id=ud.user_id  where u.id='$partner_id'");
            $partner = $p_det->fetch_array();
            $username = $partner['username'];
			$useremail = $partner['user_email'];
			
            $str = "abcdefghijklmnopqrstuvwxyz";
            $rand = substr(str_shuffle($str),0,3);
			
			if($user_ranking >= 20 )
			{
				$html .= "<tr id='$rand-$id'>
					<td>$client_name</td> 
					<td>$client_phone</td>
					<td>$client_email</td>
					<td>$client_location</td>
					<td>$userGrpName</td>
					<td>$user_ranking</td>
					<td>$username</td>
                </tr>";
                $htmld .= "<div class='col-md-6'>
				<div class='panel panel-success'>
				<div class='panel-heading'>
				<h2 class='panel-title text-center'>$client_name</h2> 
				<span class='vrating'>$user_ranking</span>
				</div>
				<div class='panel-body text-center'>";
				
				if($client_phone != "")
				{
					$htmld .= "<p><strong>Phone:</strong> $client_phone</p>";
				}
				if($client_email != "")
				{
					$htmld .= "<p><strong>Email:</strong> $client_email</p>";
				}
				$htmld .= "<p><strong>Profession:</strong> $client_profession</p>
				<p><strong>Location:</strong> $client_location</p>
				<p><strong>Group:</strong> $userGrpName</p>
				<hr/>
				<div class='row'>
				<div class='col-xs-6'>
				<strong>Partner Info:</strong>
				</div>
				<div class='col-xs-6'>
				<a href='#' >$username</a>
				</div></div>
				</div></div></div>";
			}
        } 
		$html .= '</table>';
    }
	echo $html; 
} 

// Suggest referrals 
if(isset($_POST['suggestreff'])){
	
	$email = $_POST['email']; 
	$results = $link->query("SELECT * FROM user_details as ud inner join mc_user as u on u.id = ud.user_id
	WHERE u.id IN (SELECT user_id from user_people where client_email= '$email' ) AND  u.id != 1 and u.id != '$user_id' ");
	$html  = '<p class="alert alert-info">These people are suggested as per your newly added contact. Connect with them to grow!</p>';
	if($results->num_rows > 0)
	{
		$users = array();
		while($row = $results->fetch_array()) {
			$target_clients = explode(",", $row["target_clients"]);
			$target_referral_partners = explode(",", $row["target_referral_partners"]);
			$user_picutre = !empty($row['image']) ? "images/" . $row['image'] : "images/no-photo.png"; 
			$str = "abcdefghijklmnopqrstuvwxyz";
			$rand = substr(str_shuffle($str),0,3);
			 
		//get triggers 
        $triggers = $link->query("SELECT * FROM my_triggers WHERE user_id = '" .  $row["user_id"] .   "'");
        $html .= '<div class="panel panel-default">
            <div class="panel-body">
			<div class="row">
			<div class="col-md-3">
			<img src=\''.$user_picutre.'\' alt="' .  $row["username"] . '" class="img-rounded" height="120" width="120">
			</div>
			<div class="col-md-7">
				<p><strong>Name:</strong> '.$row["username"].'</p>
				<p><strong>Email:</strong> '.$row["user_email"].'</p>
				<p><strong>Phone:</strong> '.$row["user_phone"].'</p>
			</div>
			<div class="col-md-2">
				<button data-toggle=\'modal\' id=\''. $row["user_id"] . '\' data-target=\'#myModal\' class=\'btn-primary btn btn-xs leaveMsg\'><i class=\'fa fa-envelope\'></i></button>
			</div>';
		
		 $html .='</div></div></div>';
	  } 
	  echo $html; 
	}
}

//api (tested)
// Suggest referrals 
if(isset($_POST['sendintroducemail']))
{
    ///mails/sendintroducemail/ 
}
 

// Suggest referrals 
if(isset($_POST['readmailogs']))
{
    $pagesize = $_POST['pagesize'];  // eg. 10
    $activepage = $_POST['activepage']  ;   // eg. 5 
    
    $pg =  $link->query("SELECT count(*) as recnt FROM referralsuggestions as r  inner join mc_user as u 
    ON r.partnerid= u.id inner join user_people as up on up.id= r.knowreferedto 
	WHERE  emailstatus='0' AND  
	r.isdeleted <> '1' AND r.isdeleted <> '2'  AND knowenteredby  = '$user_id' and markrem='0'   
	and up.client_email <> u.user_email and user_status='1' and 
	(r.distance >=  0 and r.distance < '30')  ");
	$pages = ceil($pg->fetch_array()['recnt'] /10);

    if($activepage > $pages)
    {
        $goto =  1; 
    }
    else 
    {
        $goto =  $activepage ; 
    }
    
    $start = ($goto-1)*10;  
    //spot the starting position 
    $startfrom = $pagesize * ($goto -1  );
    $results = $link->query("SELECT r.*, u.user_email, u.username, u.user_phone, u.image, u.user_status 
    FROM referralsuggestions as r  inner join mc_user as u 
    ON r.partnerid= u.id inner join user_people as up on up.id= r.knowreferedto 
	WHERE  emailstatus='0' AND  
	r.isdeleted <> '1' AND r.isdeleted <> '2'  AND knowenteredby  = '$user_id' and markrem='0' 
    and up.client_email <> u.user_email and user_status='1' and 
    (r.distance >=  0 and r.distance < '30') ORDER BY r.id DESC LIMIT $startfrom, $pagesize");

   
    
    $html  = '<p class="alert alert-info">These people are suggested as per your newly added contact. Connect with them to grow!</p>';
    $msg = "<p class='alert alert-info'>No matching suggestion!</p>";
    
    if($results->num_rows > 0)
	{
		$users = array();
		$help_q = $link->query("SELECT * FROM helpsbuttons order by id");
		$help_data_buttons = array();
		while($q_row = $help_q->fetch_array()){
			$help_data_buttons[] = ["id" => $q_row['id'], "helptitle" => $q_row['helptitle'], "helpvideo" => $q_row['helpvideo']];
		}
        $html ='<table class="table table-condensed">
			<thead>
			<tr><th>Connect to suggest <a href="'.$help_data_buttons[4]['helpvideo'].'" target="_blank" ><i class="glyphicon glyphicon-arrow-right" ></i><span style="color:red;"> Help</span></a></th>
            <th>Partner Info <a href="'.$help_data_buttons[5]['helpvideo'].'" target="_blank" ><i class="glyphicon glyphicon-arrow-right" ></i><span style="color:red;"> Help</span></a></th>  
			<th>Introduced to <a href="'.$help_data_buttons[6]['helpvideo'].'" target="_blank" ><i class="glyphicon glyphicon-arrow-right" ></i><span style="color:red;"> Help</span></a></th>  
			<th>Action</th> 
			</tr>
		</thead><tbody>';
		$i=0;

		while($row = $results->fetch_array())
		{ 
            $str = "abcdefghijklmnopqrstuvwxyz";

            $trknowtorefer = $link->query(" SELECT  sum( ranking) as totalscore FROM user_rating where user_id='" . $row['knowtorefer'] ."'  ");
            
            if( $trknowtorefer->fetch_array( )[0]   >= 20  ): 

            $treferedto = $link->query(" SELECT u.*, sum(r.ranking) as ranking  FROM user_people as u inner join user_rating as r on u.id=r.user_id where u.id='" . $row['knowreferedto'] ."' group by u.id   ");
			  
            //if($treferedto->num_rows > 0 )
            {
                $row_treferedto = $treferedto->fetch_array() ;
                //if(  $row_treferedto['client_location']  !== NULL  ||   $row_treferedto['client_location']  != '' )
                {
                    $treferto = $link->query("SELECT * FROM `user_people` WHERE `id` = '" . $row['knowtorefer'] ."'  ");
                    $row_referto = $treferto->fetch_array() ;
                    $rate = $row['ranking']; 
                    $starcount =  intval ( $rate/5 ) ;  
                    $ratingstr ='';
                    for($sc=0; $sc < $starcount; $sc++)
                    {
                        $ratingstr .="<i class='fa fa-star'></i>";
                    }
                    if( $rate < 5 )
                    {
                        $ratingstr ="";
                    }
                    
                    $rate2 = $row_treferedto['ranking']; 
                    $starcount2 =  intval ( $rate2/5 ) ;  
                    $ratingstr2 ='';
                    for($sc=0; $sc < $starcount2; $sc++)
                    {
                        $ratingstr2 .="<i class='fa fa-star'></i>";
                    }
                    if( $rate < 5 )
                    {
                        $ratingstr2 ="";
                    }
        
                    $html .= "<tr id='row-" . $row['id']  . "'>
                            <td>". $row_referto['client_name'] .  "<br/>" . $row_referto['client_email'] . 
                            " <span class='tooltip refsummary' data-tooltip-content='#tooltip_content$i'><i class='fa fa-info-circle'></i></span>  
                            <div class='tooltip_templates'>
                                <span id='tooltip_content$i'>
                                     <strong>Name:".  $row_referto['client_name'] ."</strong> $ratingstr <br/>
                                     <strong>Profession:". $row_referto['client_profession'] ."</strong><br/>
                                     <strong>Phone:". $row_referto['client_phone'] ."</strong><br/>
                                     <strong>Email:". $row_referto['client_email'] ."</strong><br/>
                                     <strong>Location:". $row_referto['client_location'] ."</strong><br/>
                                     <strong>Client Note:". $row_referto['client_note'] ."</strong><br/>
                                </span>
                            </div>
                            </td>";
                     $html .= "<td>";
                             
                    if($row['partnerid'] != $user_id)
                    {  
                        $html .= " 
                                <span id='tooltip_partner$i'>
                                     <strong>". $row['username'] ."</strong><br/> 
                                     <strong>". $row['user_phone'] ."</strong><br/>
                                     <strong>". $row['user_email'] ."</strong><br/> 
                                </span> "; 
                                $cc1= $row['user_email'];
                                $ccname1 = $row['username']; 
                     }
                     else 
                     {
                         $html .= "<p class=' text-center'>This persion is my contact.</p>";
                         $cc1= '';
                         $ccname1 = '' ;
                    }
                    $html .= "</td>
                    <td>" . $row_treferedto['client_name'] ."<br/>" . $row_treferedto['client_email']  . 
                    "<span class='tooltip refsummary' data-tooltip-content='#rtooltip_content$i'><i class='fa fa-info-circle'></i></span>
                        <div class='tooltip_templates'>
                                <span id='rtooltip_content$i'>
                                     <strong>Name:".  $row_treferedto['client_name'] ."</strong> $ratingstr2 <br/>
                                     <strong>Profession:". $row_treferedto['client_profession'] ."</strong><br/>
                                     <strong>Phone:". $row_treferedto['client_phone'] ."</strong><br/>
                                     <strong>Email:". $row_treferedto['client_email'] ."</strong><br/>
                                     <strong>Location:". $row_treferedto['client_location'] ."</strong><br/>
                                     <strong>Client Note:". $row_treferedto['client_note'] ."</strong><br/>
                                </span> 
                        </div>
                    </td>
                    <td>
                        <span data-to='" . $row_treferedto['client_email']  .
                            "' data-introto='". $row_treferedto['client_name'] .    
                            "' data-clientid='". $row_treferedto['id'] . 	
                            "' data-introprofession='". $row_treferedto['client_profession'] . 	
                            "' data-introphone='". $row_treferedto['client_phone'] .
                            "' data-suggestid='". $row_referto['id'] .  						
                            "' data-suggestname='". $row_referto['client_name'] . 
                            "' data-suggestemail='" . $row_referto['client_email'] . 
                            "' data-profession='" . $row_referto['client_profession'] . 
                            "' data-phone='" . $row_referto['client_phone'] .
                            "' data-refintroid='" . $row['id'] .
                            "' data-cc1='" .  $cc1 . "' data-ccname1='" .  $ccname1  . "' class='btn-primary btn btn-xs btncallmailsender '   >Send Mail</span> 
                            <span data-refintroid='" . $row['id'] . "' class='btn-danger btn btn-xs btnremsuggestion'>Remove Suggestion</span> 
                            </td>
                        </tr>";
                        $i++;
                } 
            } 
        endif;
			
	  }  
    }
    
    $lastpage = $pages ;
    $prev = $goto == 1 ? 1 : $goto-1;
    $next = $goto == $pages ? $pages : $goto+1; 
    $html .= "<tr><td colspan='5'><ul class='pagination pagiknows '><li><a data-key='$key' data-func='prev' data-pg='$prev'>«</a></li>";
    if( $goto > 10)
        $html .=  "<li><a data-key='$key'  data-func='next' title='Show last few pages' data-pg='1'> ... </a></li>";
	 
        if($goto < 10)
		{
			 for($j= 1 ; $j  <=  10  ; $j++)
			 {
				 if($j > $pages)
				 {
                     break;
				 }
				 $active = $j == $goto ? 'active' : '';
				 $html .= "<li class='$active'><a data-key='$key'  data-pg='$j'>$j</a></li>";
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
			    $html .= "<li class='$active'><a data-key='$key'  data-pg='$i'>$i</a></li>";
			}
        }
        
	 if( $goto < ($lastpage - 10 ) )
        $html .= "<li><a data-key='$key'  data-func='next' title='Show last few pages' data-pg='$lastpage'> ... </a></li>";
        $html .= "<li> <input type='text' id='gotopageno' style='width: 120px; height: 32px; margin-top: 2px; margin-right: 5px; float: left; display: inline-block;' class= 'form-control' placeholder= 'Go to page ...' > </li>";
        $html .= "<li> <input type='button' id='gopage' value='Go' style='width: 50px; float: left; height: 32px; margin-top: 2px; display: inline-block;  background-color: #2e353d; color: #fff;' class= 'btn '  > </li>";
        $html .= "<li><a data-key='$key'  data-func='next' title='Next Page' data-pg='$next'>»</a></li>";  
        $html .= "</ul></td></tr>"; 
        $html .=   "</table>";

	if($i > 0)
	{
        echo $html;
	}
	else
	{
        echo $msg;
    }
}


// Scan Referrals and remove unwanted records  
if(isset($_POST['scanzipdistance']))
{
    $results = $link->query("SELECT  * FROM referralsuggestions  WHERE  isdeleted = '0' AND knowenteredby  = '$user_id' 
	and sourcezip <>'' and targetzip <> '' and distance = '0' and distancecalculated='0' LIMIT 0, 50 ");
    $connects = array();     
    if($results->num_rows > 0)
    { 
        $i=0;
        while($row = $results->fetch_array())
        {
          $connects[$i] = ['id'=> $row['id'], 'source'=>$row['sourcezip'], 'target'=> $row['targetzip']  ]; 
            
            $i++;
        }  
    }
    echo  json_encode($connects ); 
}
//api converted
// Scan Referrals and remove unwanted records 
if(isset($_POST['delreferrals']))
{
	$refid =  $_POST['refid'] ;
	$distance =  $_POST['distance'] ;
	$results = $link->query("UPDATE referralsuggestions SET isdeleted='1', distance='$distance' WHERE  id = '$refid' "); 
}

//api converted
// Scan Referrals and remove unwanted records 
if(isset($_POST['updatedistance']))
{
	$refid =  $_POST['refid'] ;
	$distance =  $_POST['distance'] ;
	$results = $link->query("UPDATE referralsuggestions SET  distance='$distance' , distancecalculated='1' WHERE  id = '$refid' "); 
}
 

//api converted
if(isset($_POST['checkdistancelocal']))
{
	$refid =  $_POST['refid'] ;
	$source =  $_POST['source'] ;
	$target =  $_POST['target'] ;
	$results = $link->query( " select max(distance) from ( (select distinct distance  from referralsuggestions where sourcezip='$source' and targetzip='$target'   ) UNION (select distinct distance    from referralsuggestions where sourcezip='$target' and targetzip='$source' ))  as d " );
   
    $distance =0;
    if($results->num_rows > 0)
    {
        $row = $results->fetch_array(); 
		$distance = $row['distance'];
		
		if( $distance  > 0 && $distance <= 30)
		{
			$results = $link->query("UPDATE referralsuggestions SET distance='$distance' WHERE  id = '$refid' "); 
			
		}			
		else  if( $distance > 30)
		{
			$results = $link->query("UPDATE referralsuggestions SET distance='$distance' , isdeleted='1'  WHERE  id = '$refid' "); 
		}  
    }      
   echo $row['distance'];


}



// Send Trigger Message
if(isset($_POST['sendtrigger']))
{
    $sendcode=0;
	$receipentemail =  $_POST['receipentemail'] ; 
    $receipent =  $_POST['receipent'] ; 
	$receipentid =  $_POST['receipentid'] ;
    $templateid =  $_POST['templateid'] ;

	
	$sender =  'Referrals MyCity';  ; 
    $sendermail = 'referrals@mycity.com';  
	
	// for mail variables
	$receipentname = $_POST['receipent'];
	$suggestname = $_POST['suggestname'];
	$profession = $_POST['profession'];
	$suggestemail = $_POST['suggestemail'];
	$phone = $_POST['phone'];
	
 
    $results = $link->query("select * from mc_mail_templates where id='$templateid'  ");
    
	if($results->num_rows > 0)
    {
         $row = $results->fetch_array(); 
         $subject =  $row['subject'];
		
		$mailbody = $row['mailbody'];	
		$mailbody = str_replace("{receipent}", $receipentname , $mailbody ) ;
		$mailbody = str_replace("{introducee}", $suggestname , $mailbody ) ;
		$mailbody = str_replace("{introducee_profession}", $profession , $mailbody ) ;
		$mailbody = str_replace("{introducee_email}", $suggestemail , $mailbody ) ;
		$mailbody = str_replace("{introducee_phone}", $phone , $mailbody ) ;
		 
       $body = "<!DOCTYPE html><html>
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
                    <td style='padding: 10px 10px 30px 10px;'> <div style='font-size: 16px;'> ". 
					
					"<p>Hi " . $receipent . "</p><div>" . $mailbody . 
		   "</div>
		   <p>
				 If you have any questions please contact me:<br/>
				 <br/>Name: " . $_username  . "<br/>
				 <br/>Phone: " . $_user_phone  ." <br/>
				 <br/>Email address: " . $_user_email  ."<br/>
			</p>
							 
				<p>
				<br/>
				Sincerely,<br/>
				Referrals@mycity.com<br/>
				</p>
				<p>
				If you would like more information,<br/>
				please email or call<br/>
				310-736-5787<br/>
				</p>
				</div>
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
                                    Copyright &copy; 2017 | All Rights Reserved.
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
</body></html>";
  
	//take log of the outgoing trigger mail 
	$link->query("INSERT INTO mailbox 
	(sender, receipent, subject, mailbody, senton, suggestedconnectid, email_type) 
	VALUES ('$user_id',$receipentid, '$subject', '" . $link->real_escape_string( $mailbody ) . "' ,  NOW(), '-1', 'trigger-mail')");
     $sendcode = sendmail($receipentemail,  $sendermail ,  $subject, $body, $altbody); 
    }
	echo $sendcode; 
}

// coded added on 23-42017 //
//saving help buttons
if(isset($_POST['savehelpbutton']))
{
	$helptitle =  $_POST['helptitle'] ; 
    $helpvideo =  $_POST['helpvideo'] ; 
    $id =  $_POST['id'] ;  

    if(isset($id ) && $id > 0)
    {
        $results = $link->query("update helpsbuttons set helptitle='$helptitle', helpvideo='$helpvideo' where id='$id' ");    
    }
    else
    {
         $results = $link->query("insert into helpsbuttons (helptitle, helpvideo) values ( '$helptitle', '$helpvideo') ");   
    } 
    echo "1"; 
}

//get help buttons
if(isset($_POST['gethelpvideos']))
{
	$id =  $_POST['id'] ;   
	 $q = $link->query("select * from helpsbuttons order by id ");
    while($q_row = $q->fetch_array()){
        $data[] = ["id" => $q_row['id'], "helptitle" => $q_row['helptitle'], "helpvideo" => $q_row['helpvideo']];
    }  
    
    echo json_encode($data);

}

//get help buttons
if(isset($_POST['gethelpvideo']))
{
	$id =  $_POST['id'] ;   
	 $q = $link->query("select * from helpsbuttons where id='$id' ");
    while($q_row = $q->fetch_array()){
        $data[] = ["id" => $q_row['id'], "helptitle" => $q_row['helptitle'], "helpvideo" => $q_row['helpvideo']];
    }  
    echo json_encode($data); 
}
/// code end

//saving mail template
if(isset($_POST['savemailtemplate']))
{
    $template = $link->real_escape_string($_POST['template']) ;
    $subject =  $link->real_escape_string($_POST['subject']) ; 
    $email = $link->real_escape_string($_POST['email'] ); 
    $id =  $_POST['id'] ;  
    $templatetype =  $_POST['templatetype']; 
    if($templatetype == 0) //for now we are saving in two different table. But merge later on
    {
        if(isset($id ) && $id > 0)
        {
            $results = $link->query("update mc_mail_templates set templatename='$template',
			subject='$subject', mailbody='$email' where id='$id' ");    
        }
        else
        {
            $results = $link->query("insert into mc_mail_templates (templatename, subject, mailbody) values ( '$template', '$subject' , '$email') ");   
        }
    } 
    
    if(isset($id ) && $id > 0)
    {
        $results = $link->query("update mc_mail_templates set templatename='$template', subject='$subject', mailbody='$email', mailtype='$templatetype' where id='$id' ");    
    }
    else
    {
        $results = $link->query("insert into mc_mail_templates (templatename, subject, mailbody, mailtype) values ( '$template', '$subject' , '$email', '$templatetype') ");   
    }     
     echo "1"; 
} 

//get mail template
if(isset($_POST['getmailtemplate']))
{
	$id =  $_POST['id'] ;   
	$results = $link->query("select * from mc_mail_templates where id='$id'");
    while($row = $results->fetch_array()){
        $data[] = ["id" => $row['id'], "template" => $row['templatename'], "subject" => $row['subject'] , "mailbody" => $row['mailbody'] ];
    }
    echo json_encode($data); 
}

//get mail templates
if(isset($_POST['gettriggermails']))
{
	$id =  $_POST['id'] ;   
	 $q = $link->query("( SELECT * FROM  mc_mail_templates  where status='0' order by templatename) UNION (select * from mc_mail_templates where status='0' order by templatename)");
    while($q_row = $q->fetch_array()){

        if($q_row['mailtype'] == 0)
        {
            $mailtype='Trigger Mail';
        }
        else 
        {
            $mailtype='Introduction Mail';
        }
        $data[] = ["id" => $q_row['id'], 
        "template" => $q_row['templatename'], 
        "subject" => $q_row['subject'] , 
        "mailbody" => $q_row['mailbody'],
        "mailtype" => $mailtype];
    }  
    echo json_encode($data); 
}




//reward point 
if(isset($_POST['udtcpage'] ))
{
    $cpage =  $_POST['cpage'] ; 

    $results  = $link->query("SELECT COUNT(*) as rcnt FROM activity_log WHERE  lkey='scgridpage' and uid='$user_id'");

    $currentpage = $results->fetch_array()['rcnt'];

    if($currentpage > 0)
    {
         $link->query("UPDATE activity_log SET lvalue='$cpage' WHERE lkey='scgridpage' AND uid='$user_id' ");      
    }
    else 
    {
         $link->query("INSERT INTO activity_log ( lkey,lvalue, uid ) 
    VALUES ('scgridpage', '$cpage', '$user_id')");  
    }   
} 

//reward point 
if(isset($_POST['remsuggestion'] ))
{
	$refid =  $_POST['refid'];
	$link->query("DELETE FROM referralsuggestions WHERE id='$refid' ");  
	echo $link->affected_rows;
} 
//reward point 
if(isset($_POST['loyaltypoint'] ))
{
	$point =  $_POST['point'] ;
	$description = $link->real_escape_string($_POST['description']);
	$link->query("INSERT INTO loyalty_point ( pointearned, earn_date, point_desc, user_id ) 
	VALUES ('$point', NOW() , '$description', '$user_id')");  
} 
// Suggest referrals 
if(isset($_POST['fetchknowstats']))
{
    $key = $_POST['key'];
    $pagesize = $_POST['pagesize'];
    $activepage = $_POST['activepage'] -1;
    
    //spot the starting position 
    $startfrom = $pagesize * $activepage  ;
    
    $results = $link->query("SELECT u.*, d.city, d.zip, d.country, d.groups, d.target_clients,d.target_referral_partners, d.vocations, d.createdon FROM mc_user as u INNER JOIN user_details as d ON u.id=d.user_id WHERE u.username LIKE '$key%' LIMIT $startfrom, $pagesize"); 
    //finding total pages 
    $totalrecs = $link->query("SELECT COUNT(*) as totalrec FROM mc_user as u INNER JOIN user_details as d ON u.id=d.user_id WHERE u.username LIKE '$key%'");
    
    $totalrecrow = $totalrecs->fetch_array()['totalrec'];
     
    $html  = '<p class="alert alert-info">These people are suggested as per your newly added contact. Connect with them to grow!</p>';
    $msg = "<p class='alert alert-info'>No matching suggestion!</p>";
    if($results->num_rows > 0)
    {
        $users = array();
         
        $html ='<table class="table table-condensed">
            <thead>
            <tr><th>User Details</th> 
            <th>Action</th> 
            </tr>
        </thead><tbody>';
        $i=0;
        while($row = $results->fetch_array())
        {  
            $html .= "<tr id='row-" . $row['id']  . "'>
                    <td>". $row['username'] ."<br/>" . $row['user_email'] . 
                    " <button class='btn btn-info btn-xs tooltip refsummary' data-tooltip-content='#tooltip_content$i'>More Info</button> 
                    
                    <div class='tooltip_templates'>
                        <span id='tooltip_content$i'> 
                             <strong>City:". $row['city'] ."</strong><br/>
                             <strong>Zip:". $row['zip'] ."</strong><br/>
                             <strong>Country:". $row['country'] ."</strong><br/>
                             <strong>Target clients:". $row['target_clients'] ."</strong><br/>
                              <strong>Target referral clients:". $row['target_referral_partners'] ."</strong><br/>
                             <strong>Package:". $row['user_pkg'] ."</strong><br/>
                        </span>
                    </div>
                    </td> 
                    ";
             $html .= "<td><a data-toggle='tab' data-pagesize='10' data-pageno='1'  href='#menu19' data-name='".  $row['username'] . "' data-id='" . $row['id'] . "' class='btn-primary btn btn-xs viewpklink'>View Suggested Knows</a>
                    </td>

                </tr>";
            $i++;
      } 
      $html .='</table>';
    }     
    //page logic
    $html .='<nav aria-label="Page navigation example">
  <ul class="pagination">
    <li class="page-item">
      <a class="page-link" href="#" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
        <span class="sr-only">Previous</span>
      </a>
    </li>
    ';
    
    if($totalrecrow % 10 > 0)
    {
        $totalpages = $totalrecrow / 10 + 1;
    }
    else
    {
        $totalpages = $totalrecrow / 10 ;
    }
    
    for($i=0;  $i < $totalpages -1 ; $i++)
    {
        $html .= '<li  ><a class="page-link showreferrals pagerlink" data-pagesize="10" data-pageno="'.  ($i+1) .'"  href="#">'.($i+1) .'</a></li>';
    }
    
    $html .= '<li class="page-item">
        <a class="page-link" href="#" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
        <span class="sr-only">Next</span>
      </a>
    </li>
  </ul>
</nav>'; 
    if($i > 0)
    {
        echo $html;
    }
    else
    {
        echo $msg;
    }
} 


if(isset($_POST['knowsuggesthistory']))
{
    $pid = $_POST['pid'];  // eg. 10
    $pagesize = $_POST['pagesize'];  // eg. 10
    $activepage = $_POST['activepage'] -1;   // eg. 5
    
    //spot the starting position 
    $startfrom = $pagesize * $activepage + 1; 
    $results = $link->query("SELECT * FROM referralsuggestions WHERE emailstatus='0'  AND   isdeleted<>'1'   AND knowenteredby  = '$pid' LIMIT $startfrom, $pagesize");
     
    //finding total pages 
    $totalrecs = $link->query("SELECT count(*) as totalrec FROM referralsuggestions WHERE emailstatus='0' AND   isdeleted <>'1' AND knowenteredby  = '$pid' ");
    
    $totalrecrow = $totalrecs->fetch_array()['totalrec'];
     
    $html  = '<p class="alert alert-info">These people are suggested as per your newly added contact. Connect with them to grow!</p>';
    $msg = "<p class='alert alert-info'>No matching suggestion!</p>";
    if($results->num_rows > 0)
    {
        $users = array();
         
        $html ='<table class="table table-condensed">
            <thead>
            <tr><th>Suggested Person</th>
            <th>New Know Added</th>  
            <th>Action</th> 
            </tr>
        </thead><tbody>';
        $i=0;
        while($row = $results->fetch_array())
        { 
            $str = "abcdefghijklmnopqrstuvwxyz";
            $treferto = $link->query("SELECT * FROM `user_people` WHERE `id` = '" . $row['knowtorefer'] ."'  ");
            $row_referto = $treferto->fetch_array() ;
            
            $treferedto = $link->query("SELECT * FROM `user_people` WHERE `id` = '" . $row['knowreferedto'] ."'  ");
            $row_treferedto = $treferedto->fetch_array() ;
            
            $html .= "<tr id='row-" . $row['id']  . "'>
                    <td>". $row_referto['client_name'] ."<br/>" . $row_referto['client_email'] . 
                    " <button class='btn btn-info btn-xs tooltip refsummary' data-tooltip-content='#tooltip_content$i'>More Info</button>
                    <div class='tooltip_templates'>
                        <span id='tooltip_content$i'>
                             <strong>Name:". $row_referto['client_name'] ."</strong><br/>
                             <strong>Profession:". $row_referto['client_profession'] ."</strong><br/>
                             <strong>Phone:". $row_referto['client_phone'] ."</strong><br/>
                             <strong>Email:". $row_referto['client_email'] ."</strong><br/>
                             <strong>Location:". $row_referto['client_location'] ."</strong><br/>
                             <strong>Client Note:". $row_referto['client_note'] ."</strong><br/>
                        </span>
                    </div>
                    </td>
                    <td>" . $row_treferedto['client_name'] ."<br/>" . $row_treferedto['client_email']  . "</td> 
                    <td><span data-to='" . $row_treferedto['client_email']  .
                    "' data-introto='". $row_treferedto['client_name'] .    
                    "' data-introprofession='". $row_treferedto['client_profession'] .  
                    "' data-introphone='". $row_treferedto['client_phone'] .                        
                    "' data-suggestname='". $row_referto['client_name'] . 
                    "' data-suggestemail='" . $row_referto['client_email'] . 
                    "' data-profession='" . $row_referto['client_profession'] . 
                    "' data-phone='" . $row_referto['client_phone'] .
                    "' data-refintroid='" . $row['id'] .
                    "' class='btn-primary btn btn-xs btncallmailsender'>Send Mail</span> 
                    <span data-refintroid='" . $row['id'] . "' class='btn-danger btn btn-xs btnremsuggestion'>Remove Suggestion</span>
                    </td>
                </tr>";
            $i++;
      } 
      $html .='</table>';
    }
	
	
	//page logic
    $html .='<nav aria-label="Page navigation example">
     <ul class="pagination">
      <li class="page-item">
		<a class="page-link" href="#" aria-label="Previous">
			<span aria-hidden="true">&laquo;</span>
			<span class="sr-only">Previous</span>
      </a>
    </li>
    ';
    
    if($totalrecrow % 10 > 0)
    {
        $totalpages = $totalrecrow / 10 + 1;
    }
    else
    {
        $totalpages = $totalrecrow / 10 ;
    }
    
    for($i=0;  $i < $totalpages -1 ; $i++)
    {
        $html .= '<li  ><a class="page-link showreferrals" data-pagesize="10" data-pageno="'.  ($i+1) .'"  href="#">'.($i+1) .'</a></li>';
    }
    
    $html .= '<li class="page-item">
        <a class="page-link" href="#" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
        <span class="sr-only">Next</span>
      </a>
    </li>
  </ul>
</nav>'; 

    if($i > 0)
    {
        echo $html;
    }
    else
    {
        echo $msg;
    }
}


if(isset($_POST['caldistance']))
{
    $source =  $_POST['source']; 
    $target = $_POST['target'];
    $url =  'http://mycity.com/api/calc_distance.php?source=' . $source . "&target=" . $target;
    $json = file_get_contents($url);
    echo  $json;
}
  

//reward point 
if(isset($_POST['updatezips'] ))
{
    $zip1 =  $_POST['zip1'] ;
    $zip2 =  $_POST['zip2'] ;
    $id =  $_POST['id'] ;
    if($zip1 !='' && $zip2 !='')
        $link->query("UPDATE referralsuggestions SET sourcezip='$zip1', targetzip='$zip2' WHERE id='$id'");   
}

//reward point 
if(isset($_POST['testmail'] ))
{
 $msg2 = "<!DOCTYPE html><html>
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
                    <td style='padding: 10px 10px 30px 10px;'>
                        <div style='font-size: 16px;'>Dear: <span style='font-weight: bold;'> Phan</span></div>
                        <br />
                        <div>You have opted to reset your password.</div>
                        <br />
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
 
echo sendmailfrommycityalert('xanayaima@gmail.com',  'Introduction/Referral from ' . $_username , $msg2, $msg2, 'heeyaistudio@gmail.com', 'HeeYai', 'egresscoin@gmail.com', 'Egress' );

}

 if(isset($_POST['fetchpoints'])) {
	 
	$html = "";
	$final = null; 
	$results = $link->query("SELECT  u.id, u.username, u.user_email, u.user_phone, SUM(p.pointearned) as points FROM mc_user as u inner join loyalty_point as p on u.id=p.user_id WHERE p.status='0' GROUP by p.user_id");
	 
	
	if($results->num_rows > 0) {
		while($row = $results->fetch_array()) {
			$target_clients = explode(",", $row["target_clients"]);
			$target_referral_partners = explode(",", $row["target_referral_partners"]);
			$user_picutre = !empty($row['image']) ? "images/" . $row['image'] : "images/no-photo.png"; 
			$str = "abcdefghijklmnopqrstuvwxyz";
			$rand = substr(str_shuffle($str),0,3);
			 
			 
		 $html .= '<div class="panel panel-default">
		<div class="panel-body">
		<div class="row">
		<div class="col-md-2">
		<img src=\''.$user_picutre.'\' alt="' .  $row["username"] . '" class="img-rounded" height="120" width="120">
		</div>
		<div class="col-md-4">
			<p><strong>Name:</strong>'.$row["username"].'</p>
			<p><strong>Email:</strong>'.$row["user_email"].'</p>
			<p><strong>Phone:</strong>'.$row["user_phone"].'</p>
		</div> <div class="col-md-3">
		<span id="pcircle' . $row['id'] . '" class="loyalty-sm">'.$row["points"].'</span>
		</div>
		<div class="col-md-3">
		<p>New Loyalty Point:</p> 
		 <div class="form-group"> 
				<input type="text" id="point' . $row['id'] . '"  class="form-control input-sm" value="'.$row["points"].'">
				<button data-cval="'.$row["points"].'" data-id="' . $row['id'] . '" class="btn btn-primary resetPoint" >Update</button>
		  </div>
		</div>  </div></div></div>';
	  }
	}
	else
	{
		$html = "No member has earned loyalty points so far now!";
	}
	echo $html; 
}

// ******** Reset Loyalty Point ********
if(isset($_POST['resetPoint'])){
    
	$id = $_POST['id'];
	$point = $_POST['point']; 
	$oldpoint = $_POST['oldpoint'];
	 
	//$reccount = intval( ($oldpoint - $point) / 10 );
 
	//update existing points 
	 $link->query("UPDATE  loyalty_point SET status='1' WHERE user_id = '$id'  ");  
	//update the new point
	 $link->query("INSERT INTO loyalty_point (user_id, pointearned , earn_date, point_desc, status) 
	  VALUES ('$id', '$point' , NOW(),  'Admin Reset', '0' )");   
}

//load members who entered new know recently
if(isset($_POST['knowentry']))
{ 
	$group = $_POST['group'];
	$vocation = $_POST['vocation'];
	 
	if($_user_role =='admin')
	{
		$userlist = $link->query("SELECT u.*, b.* FROM mc_user AS u INNER JOIN user_details as d 
		on u.id=d.user_id 
		inner join ( SELECT user_id, count(*) AS cnt FROM user_people GROUP BY user_id  ) AS b 
		on u.id=b.user_id  where FIND_IN_SET('$group',  groups )  > 0 and FIND_IN_SET('$vocation',  vocations )  > 0 order by cnt DESC"); 
		
	}
	else 
	{
		$userlist = $link->query("SELECT u.*, b.* FROM mc_user AS u INNER JOIN user_details as d 
		on u.id=d.user_id 
		inner join ( SELECT user_id, count(*) AS cnt FROM user_people GROUP BY user_id  ) AS b 
		on u.id=b.user_id  where u.id !='1'  and u.id !='$user_id' and  
		FIND_IN_SET('$group',  groups )  > 0 and FIND_IN_SET('$vocation',  vocations )  > 0 and user_pkg='Gold' order by cnt DESC"); 
		 
	}
	   
	
	if($userlist->num_rows > 0)
	{
		$users = array();
		$html ='<table class="table table-condensed">
			<thead>
			<tr>
				<th>Name</th>
				<th>Email</th> 
				<th>Total Connections</th> 
				<th></th>
			</tr>
		</thead><tbody>';
		while($row = $userlist->fetch_array())
		{
			$html .='<tr>
			<td>'. $row['username'] . '</td>
			<td >'. $row['user_email'] . '</td> 
			<td class="text-right"><strong>'.  $row['cnt'] . '</strong></td> 
			<td style="width: 140px">
				<button data-id="'. $row['id'] . '" class="btn btn-primary btn-sm listconnects">Show Connections</button>
			</td>
			</tr>';
		}
		$html .= "</tbody></table>";
		echo $html;
	}
	else
	{
		echo "<p class='alert alert-info'>No matching partners found!</p>";
	}
}


//load members who entered new know recently
if(isset($_POST['newsignups']))
{
    $goto = $_POST['page'];
    $startdate = trim($_POST['startdate']);
    $enddate = trim($_POST['enddate']); 
    $start = ($goto-1)*10;

    $startdateparts = explode('-', $startdate);
    $enddateparts = explode('-', $enddate);
 
       
    //$invaliddate=true;
    if( !checkdate ( $startdateparts[1], $startdateparts[2], $startdateparts[0] )  )
    {
        //$invaliddate = false;
		$startdate = date('Y-m-d');
    }

    if( !checkdate ( $enddateparts[1], $enddateparts[2], $enddateparts[0] )  )
    {
        //$invaliddate =  false;
		$enddate = date('Y-m-d');
    }

	
    if(isset($startdate) && isset($enddate))
    {
        $userlist = $link->query("SELECT u.*, d.user_id, d.city, d.zip FROM mc_user AS u LEFT JOIN user_details as d 
        on u.id=d.user_id   where  date(u.createdOn) >= '" . $startdate . "' and  date(u.createdOn) <= '" . $enddate . "' LIMIT $start,10 ");

        $pg = $link->query("select count(*) as recnt from mc_user AS u LEFT join user_details as d 
        on u.id=d.user_id   where  date(u.createdOn) >= '" . $startdate . "' and  date(u.createdOn) <= '" . $enddate . "'" );

        $pages = ceil($pg->fetch_array()['recnt'] /10);  
		
    } 
    else 
    {
        $createdDate =  date('Y-m-d');
        $userlist = $link->query("SELECT u.*, d.user_id, d.city, d.zip FROM mc_user AS u LEFT JOIN user_details as d 
        on u.id=d.user_id   where  date(u.createdOn) = '" . $createdDate . "' LIMIT $start,10 ");
        
        $pg = $link->query("select count(*) as recnt from mc_user AS u LEFT join user_details as d 
        on u.id=d.user_id  where  date(u.createdOn) = '" . $createdDate . "'  " );
 
        $pages = ceil($pg->fetch_array()['recnt'] /10); 
    }
    
    if($userlist->num_rows > 0)
	{

        $users = array();
        $html ='<table class="table table-condensed">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th> 
                    <th>Signup On</th> 
                    <th>Package</th>  
                </tr>
		    </thead>
        <tbody>';
		while($row = $userlist->fetch_array())
		{
			$html .='<tr>
			<td>'. $row['username'] . '</td>
			<td >'. $row['user_email'] . '</td> 
			 <td >'. date('Y-m-d', strtotime($row['createdOn'] )) . '</td> 
		    <td >'. $row['user_pkg'] . '</td> 
			</tr>';
		}
        
        $lastpage = $pages ;
        $prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1; 
        $html .= "<tr><td colspan='5'><ul class='pagination newsignpaginate'><li><a data-key='$key' data-func='prev' data-pg='$prev'>«</a></li>";
        if( $goto > 10) 
        $html .= "<li><a data-key='$key'  data-func='next' title='Show last few pages' data-pg='1'> ... </a></li>";
        
            if($goto < 10)
            { 
                for($j= 1 ; $j  <=  10  ; $j++)
                {
                    if($j > $pages)
                    {
                        break;
                    }
                    
                    $active = $j == $goto ? 'active' : '';
                    $html .= "<li class='$active'><a data-key='$key'  data-pg='$j'>$j</a></li>";
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
                    $html .= "<li class='$active'><a data-key='$key'  data-pg='$i'>$i</a></li>";
                }
            }
        if( $goto < ($lastpage - 10 ) )
            $html .= "<li><a data-key='$key'  data-func='next' title='Show last few pages' data-pg='$lastpage'> ... </a></li>";
            $html .= "<li><a data-key='$key'  data-func='next' title='Next Page' data-pg='$next'>»</a></li></ul></td></tr>"; 
            $html .=  "</table>";
        echo $html; //. "date:" . $invaliddate; 
	}
	else
	{
		echo "<p class='alert alert-info'>No matching partners found!</p>";
	} 
}


if(isset($_POST['listconnects']))
{
	$id = $_POST['id'];
	$userlist = $link->query(" SELECT * FROM  referralsuggestions WHERE partnerid ='$id' ORDER BY id desc ");
	
	if($userlist->num_rows > 0)
	{
		$users = array();
		$html =' ';
		while($row = $userlist->fetch_array())
		{
			//left member 
			$rsleftmember = $link->query(" select * from user_people WHERE id ='" . $row['knowtorefer'] . "'");
			$leftmember = $rsleftmember->fetch_array();
			//right  member 
			$rsrightmember = $link->query(" select * from user_people WHERE id ='" . $row['knowreferedto'] . "'");
			$rightmember = $rsrightmember->fetch_array();
			$html .='<tr>
			<td>'. $leftmember['client_name'] . '</td>
			<td >'. $rightmember['client_name'] . '</td>';
		
			if($row['email_status'] == 1)
			{
				$html .='<td>Sent</td>';
			}
			else
			{
				$html .='<td>No Sent</td>';	
			}
			 
			$html .=' </tr>';
		}  
		$html .= "</tbody></table>";
		echo $html;
	} 
	else
	{
		echo "<p class='alert alert-info'>No referral suggestion exists!</p>";
	}
}

//completed
if(isset($_POST['wizstepfetchmember']))
{  
	$professions = $_POST['profession'];
	$professionlist = explode(",",  $professions); 
	$where_group = " ( "; 
	for($i=0; $i < sizeof($professionlist); $i++ )
	{  
		$where_group .= " find_in_set (  '". $professionlist[$i] . "' , client_profession ) ";
		if( $i < sizeof($professionlist)-1 )
		{
			$where_group .=  " OR "; 
		} 
	}
	$where_group .= " ) ";
	$user = $link->query("SELECT * FROM user_people where user_id = '$user_id' AND $where_group ORDER by client_name");
	$users = array();
	while($row = $user->fetch_array())
	{
		$users[] = array('id' =>  $row['id']  ,  'username' =>  $row['client_name']   ); 
	}
	$json = json_encode($users);
	echo $json;
}



if(isset($_POST['wizstepintroreferrals']))
{
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
	
	//fetch profession and zip for selected members
	$referralid = $_POST['member'];  
	$user = $link->query("SELECT a.*, b.answer FROM user_people as a INNER JOIN user_answers as b ON a.id=b.user_id WHERE a.id = '$referralid' ");
	$users = array();
	$referall = $user->fetch_array();
	$referral_profession=$referall['answer'];
	$sourcezip = $referall['client_zip'];
	//create where clause for client profession 
	$professionlist = explode(",",  $referral_profession); 
	$where_prof = " ( "; 
	for($i=0; $i < sizeof($professionlist); $i++ )
	{  
		$where_prof .= " find_in_set (   '". $professionlist[$i] . "', p.client_profession ) "; 
		
		if( $i < sizeof($professionlist)-1 )
		{
			$where_prof .=  " OR "; 
		} 
    }  
	$where_prof .= " ) ";  
	//create main query to insert any new referrals
	$mainQry = "SELECT p.*,  SUM(r.ranking) as rank
				FROM user_people as p INNER JOIN user_rating as r on p.id=r.user_id INNER JOIN user_answers as a on p.id = a.user_id
				WHERE p.user_id IN  ( $qryInner )  AND " . $where_prof . " AND p.id <> '$referralid' GROUP BY p.id ORDER BY client_name" ;
	 		
	$userpeople = $link->query( $mainQry ); 
	$references = array();	 
	if($userpeople->num_rows > 0)
	{
		while($row = $userpeople->fetch_array())
		{
			$id = $row['id']; 
			$user_ranking = $row['rank']; 
			$userm = $link->query("SELECT user_email FROM mc_user where id = '".$row['user_id']."'");
			$userm = $userm->fetch_array();
			if($userm['user_email'] != $row['client_email']) { 
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
				$targetzip = $row['client_zip'];
				if($targetzip != "")
				{
					 if(checkExistingReferral( $row['user_id'] ,$row['id'] ,$referralid, $user_id  ) == 0 )
					 {
						 if($row['user_id'] != $user_id) {
						 $link->query("INSERT INTO referralsuggestions 
							( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby , sourcezip, targetzip, ranking) 
							VALUES ('".  $row['user_id'] . "', '". $row['id'] . "', 
							'$referralid' ,  NOW() ,  '$user_id' , '$sourcezip' , '$targetzip' ,   '$user_ranking' )");
						 }
						
					 } 
				} 
		}}
        }
	}
	
	//load suggestions where you want to send email
	$suggestedreferrals = $link->query("SELECT b.* FROM referralsuggestions as a INNER JOIN user_people as b on a.knowtorefer=b.id 
	WHERE a.knowreferedto = '$referralid' AND a.knowenteredby =  '$user_id'" );
	
	$users = array();
	while($row = $suggestedreferrals->fetch_array())
	{
		$users[] = array('id' =>  $row['id']  ,  'username' =>  $row['client_name']   ); 
	}
	$json = json_encode($users);
	echo $json;  	
}
if(isset($_POST['wizfinal']))
{
	$referralid = $_POST['member'];   
	$membertointroduce =  $_POST['membertointroduce']; 
	
	//Refer Left 
	$user = $link->query("SELECT * FROM user_people  WHERE id = '$membertointroduce' "); 
	$referleft = $user->fetch_array();
	 
	//Refer Right 
	$user = $link->query("SELECT * FROM user_people  WHERE id = '$referralid' "); 
	$referright = $user->fetch_array(); 
	
	
	$dataproperties =    
	" data-suggestid='" . $referleft['id']  .  "' " .
	" data-suggestemail='" . $referleft['client_email']  .  "' " .
	" data-suggestname='" . $referleft['client_name']  .  "' " . 
	" data-suggestphone='" . $referleft['client_phone']  .  "' " . 
	" data-suggestprof='" . $referleft['client_profession']  .  "' " .  
	" data-clientid='" . $referright['id']  .  "' " .
	" data-receipent='" . $referright['client_email']  .  "' " .
	" data-receipentname='" . $referright['client_name']  .  "' " .
	" data-receipentphone='" . $referright['client_phone']  .  "' " .
	" data-receipentprof='" . $referright['client_profession']  .  "' ";
	
	//find cc 
	if($referleft['user_id'] != $user_id)
	{
		$user = $link->query("select * from mc_user where id = '" . $referleft['user_id'] ."'"); 
		$ccrow = $user->fetch_array();
		$dataproperties .=  " data-cc1='" . $ccrow['client_email']  .  "' " . 
		" data-ccname1='" . $ccrow['username']  .  "' "   ;
	}
	else
	{
		$dataproperties .=  " data-cc1='' " . " data-ccname1='' "   ;
	}
	//set email log id
	$suggestedreferrals = $link->query("SELECT a.id  FROM referralsuggestions as a INNER JOIN user_people as b on a.knowtorefer=b.id 
	WHERE a.knowreferedto = '$referralid' AND a.knowtorefer =  '$membertointroduce'" );
	$maillogid = $suggestedreferrals->fetch_array()['id'];
	$dataproperties .=   " data-mailogid='" .  $maillogid . "' " ;
	  
	$html = '<div class="wizsummary-inner">
					<h2>Referral Suggestion Wizard Summary</h2>
					<hr/> 
			 <div class="col-md-6">
					 <div class="panel panel-primary">
					 <div class="panel-heading">
					  <h3>Person To Introduce</h3>
					  </div>
					  <div class="panel-body referpanel-left">
						<h3>' . $referleft['client_name'] . '</h3>
						<p>' . $referleft['client_email'] . '</p>
						<p><small>' . $referleft['client_profession'] . '</small></p>
					</div>
					</div>
					</div>
					<div class="col-md-6">
					 <div class="panel panel-primary">
					  <div class="panel-heading">
					  <h3>Person receiving introduction</h3>
					  </div>
					  <div class="panel-body referpanel-right">
						<h3>' . $referright['client_name'] . '</h3>
						<p>' . $referright['client_email'] . '</p>
						<p><small>' . $referright['client_profession'] . '</small></p>
					  </div>
					</div>
			 </div>
					
			 <div class="col-md-12 clearfix text-left">
				<button '. $dataproperties. ' class="btn btn-success btn-lg wizsendreferralmail">Send Email</button>
			</div>	
			 </div><div class="clearfix"></div> ';
	echo $html; 
}
 
// Suggest referrals 
if(isset($_POST['wizsendmail']))
{
	$suggestemail = $_POST['suggestemail']; 
	$suggestname = $_POST['suggestname'];
    $suggestid = $_POST['suggestid'];
	$email =  $_POST['to'];
	$profession = $_POST['profession']; 
	$phone = $_POST['phone'];
	
	$receipentname = $_POST['receipentname']; 
	$receipentprof = $_POST['receipentprof'];
	$receipentphone = $_POST['receipentphone'];
	$clientid = $_POST['clientid'];	
	
	$mailogid = $_POST['mailogid'];
    $cc1 = $_POST['cc1'];
    $ccname1 = $_POST['ccname1']; 


	$results = $link->query("SELECT * FROM referralsuggestions where id='$mailogid' "); 
	if($results->num_rows > 0)
	{
		$trow = $results->fetch_array();
	
		if($trow['emailstatus']==1)
		{
			echo "1";
			return;
		}
	} 
 

 $html ='<p>Hello ' . $link->real_escape_string( $receipentname) . ',</p>
	 <p>A friend of yours recently gave you high ratings on the following questions:<br/>
1 Do you want to grow your business?<br/>
2 Are you willing to network?<br/>
3 Are you willing to give referrals?<br/>
4 What is your expertise in your field?<br/>
</p>
<p>
They also mentioned that you are interested in meeting someone in the following vocation.
</p>';

 $html .= '
<div style="margin-bottom: 20px;
    background-color: #fff;
    border: 1px solid #efefef;padding:10px;
    border-radius: 4px; ">  
				<p><strong>Name:</strong> '. $suggestname .'</p>
					<p><strong>Profession:</strong> '.$profession .'</p>
				<p><strong>Email:</strong> '. $suggestemail .'</p>
				<p><strong>Phone:</strong> '.$phone .'</p>
			 ';
		 
 $html .='</div>';
	  
 $html .='<p>We matched an individual also with high ratings through your friend\'s partner network.<br/>';

/* If you are interested in this potential client or referral introduction, please respond with a yes
and your friend will create the introduction.<br/> */
$html .='<br/>
Sincerely,<br/>
Referrals@mycity.com<br/>
</p>
<p>
If you would like more information,<br/>
please email or call<br/>
310-736-5787<br/>
<a href=\'mailto:bob@mycity.com\' target=\'_blank\'>bob@mycity.com</a></p></body></html>';
 
	$to =   $email ;  
	$subject = 'Introduction/Referral from ' . $_username ; 
	$headers = "From: referrals@mycity.com\r\n";
	$headers .= "Reply-To: referrals@mycity.com\r\n"; 
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    //mail towards the new connect 

    $mailexistcheck= $link->query("SELECT COUNT(*) as ecnt FROM mailbox where sender='$user_id' AND receipent='$clientid' AND suggestedconnectid='$suggestid' ");

	$ecnt = $mailexistcheck->fetch_array()['ecnt'] ;
	//if( $ecnt )
	// if( $ecnt == 0)
		$link->query("INSERT INTO mailbox (sender, receipent, subject, mailbody, senton, suggestedconnectid) VALUES ('$user_id',$clientid, '$subject', '" . $link->real_escape_string($html) . "' ,  NOW(), '$suggestid' )");
	//mark as mail sent
	$link->query("UPDATE referralsuggestions SET emaillog='" .  $link->real_escape_string($html) . "' , emailstatus='1',  senton=NOW()  WHERE id='$mailogid' ");
 
     // if(  mail($to, $subject, $html, $headers) == TRUE  )
     $mailstatus = sendreferralmail(   $to, $subject , $html, $html ,  $_user_email, $_username ,  $cc1, $ccname1) ; 
 $notifiermail  ='<!DOCTYPE html><html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Email from mycity.com</title>
    <style type="text/css">
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
<body><p>Hello ' . $ccname1  . ',</p>
		<p>Your referral networking partner ' . $_username  . ' just sent an introduction/referral to one of your connection that you gave a high rating - ' .  $suggestname  . 
             '. You can count this as an introduction/referral for yourself. Thank you. <br/>Follow up with the person that you know would also be helpful. 
        <br/>
        Sincerely,<br/>
        Referrals@mycity.com<br/>
        </p>
        <p>
        If you would like more information,<br/>
        please email or call<br/>
        310-736-5787<br/>
        <a href=\'mailto:bob@mycity.com\' target=\'_blank\'>bob@mycity.com</a></p></body></html>'; 
        //mail towards partner
        sendemail( $cc1, 'Referral suggestion for one of your connection sent', $notifiermail , $notifiermail  );
		
	 if(  $mailstatus  == 1  )     
     {
        //$link->query("UPDATE referralsuggestions SET emaillog='" .  $link->real_escape_string($html) . "' , emailstatus='1',  senton=NOW()  WHERE id='$mailogid' ");
        //update log  
        echo "success"; 
        //send another email back to sender 
        $html  ='<p>Hello ' . $link->real_escape_string( $suggestname) . ',</p>
             <p>A friend of yours recently gave you high ratings on the following questions:<br/>
			1 Do you want to grow your business?<br/>
			2 Are you willing to network?<br/>
			3 Are you willing to give referrals?<br/>
			4 What is your expertise in your field?<br/>
        </p>
        <p>
        They also mentioned that you are interested in meeting someone in the following vocation.
    </p>';
		
	$html .= '
        <div style="margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #efefef;padding:10px;
            border-radius: 4px; ">  
                        <p><strong>Name:</strong> '. $receipentname .'</p>
                            <p><strong>Profession:</strong> '.$receipentprof .'</p>
                        <p><strong>Email:</strong> '. $to .'</p>
                        <p><strong>Phone:</strong> '.$receipentphone .'</p>
                     '; 
        $html .='</div>';
		$html .='<p>We matched an individual also with high ratings through your friend\'s partner network.<br/>';
		/* If you are interested in this potential client or referral introduction, please respond with a yes
        and your friend will create the introduction.<br/> */
        $html .='<br/>
        Sincerely,<br/>
        Referrals@mycity.com<br/>
        </p>
        <p>
        If you would like more information,<br/>
        please email or call<br/>
        310-736-5787<br/>
        <a href=\'mailto:bob@mycity.com\' target=\'_blank\'>bob@mycity.com</a></p></body></html>'; 
        sendemail(   $suggestemail, $subject , $html, $html); 
        }
        else
        {
            echo "fail";
        }  
   echo $html; 
}
 
if(isset($_POST['wiz_loadconnects']))
{
	//$userpeople= getConnections($user_id); 
	//echo ( $userpeople ) ;  	
}
 
if(isset($_POST['wiz_autocomplete']))
{
	  
}
 
 
//api
if(isset($_POST['wiz_summary']))
{
	$memberleft = $_POST['memberleft'];   
	$memberright =  $_POST['memberright']; 
	
	//Refer Left 
	$user = $link->query("SELECT * FROM user_people  WHERE id = '$memberleft' "); 
	$referleft = $user->fetch_array();
	 
	//Refer Right 
	$user = $link->query("SELECT * FROM user_people  WHERE id = '$memberright' "); 
	$referright = $user->fetch_array(); 
	
	$dataproperties   ='';
	$dataproperties =    
	" data-suggestid='" . $referleft['id']  .  "' " .
	" data-suggestemail='" . $referleft['client_email']  .  "' " .
	" data-suggestname='" . $referleft['client_name']  .  "' " . 
	" data-suggestphone='" . $referleft['client_phone']  .  "' " . 
	" data-suggestprof='" . $referleft['client_profession']  .  "' " .  
	" data-clientid='" . $referright['id']  .  "' " .
	" data-receipent='" . $referright['client_email']  .  "' " .
	" data-receipentname='" . $referright['client_name']  .  "' " .
	" data-receipentphone='" . $referright['client_phone']  .  "' " .
	" data-receipentprof='" . $referright['client_profession']  .  "' ";
	
	//find cc 
	if($referleft['user_id'] != $user_id)
	{
		$user = $link->query("select * from mc_user where id = '" . $referleft['user_id'] ."'"); 
		$ccrow = $user->fetch_array();
		$dataproperties .=  " data-cc1='" . $ccrow['client_email']  .  "' " . 
		" data-ccname1='" . $ccrow['username']  .  "' "   ;
	}
	else
	{
        $dataproperties .=  " data-cc1='' " . " data-ccname1='' "   ;
    }
    
    //set email log id
    $suggestedreferrals = $link->query("SELECT a.id  FROM referralsuggestions as a INNER JOIN user_people as b on a.knowtorefer=b.id 
	WHERE a.knowreferedto = '$referralid' AND a.knowtorefer =  '$membertointroduce'" );
	$maillogid = $suggestedreferrals->fetch_array()['id'];
	$dataproperties .=   " data-mailogid='" .  $maillogid . "' " ;

    $html = '';  
	$html = '<div class="wizsummary-inner">  
                    <h3 class="alertwide text-center">You are introducing <strong>' .  $referleft['client_name']. '</strong> to 
                    <strong>' . $referright['client_name'] .  '</strong>.<br/>
                    An email will be sent to <strong>' . $referright['client_name'] .  '</strong> about this introduction. </h3>
			 <div class="col-md-4">
					 <div class="panel panel-primary">
					 <div class="panel-heading">
					  <h4>Person To Introduce</h4>
					  </div>
					  <div class="panel-body referpanel-left">
						<h3>' . $referleft['client_name'] . '</h3>
						<p>' . $referleft['client_email'] . '</p>
						<p><small>' . $referleft['client_profession'] . '</small></p>
					</div>
					</div>
					</div>
					<div class="col-md-4"> 
					 <div class="panel panel-success">
						<div class="panel-heading">
					  <h4>Introducer</h4>
					  </div>
					  <div class="panel-body referpanel-center">
						<h3>' . $_SESSION['username']  . '</h3>
						<p>' . $_SESSION['user_email'] . '</p> 
					  </div></div>
					</div>
					<div class="col-md-4">
					 <div class="panel panel-primary">
					  <div class="panel-heading">
					  <h4>Person receiving introduction</h4>
					  </div>
					  <div class="panel-body referpanel-right">
						<h3>' . $referright['client_name'] . '</h3>
						<p>' . $referright['client_email'] . '</p>
						<p><small>' . $referright['client_profession'] . '</small></p>
					  </div>
					</div>
			 </div> 	
			 <div class="col-md-12 clearfix text-left">
				<button '. $dataproperties. ' class="btn btn-success btn-lg wiz_preview_mail_template"><i class="fa fa-envelope"></i> Preview Mail Template</button>
			</div>	
			 </div><div class="clearfix"></div> 
        ';
        $html .='<div class="modal fade intromailtemplate" tabindex="-1" role="dialog" aria-labelledby="intromailtemplate"
            id="intromailtemplate">
            <div class="modal-dialog "  >
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Sample of Email Message</h2> 
                    </div>
                    <div class="modal-body text-left " style="height: 360px; overflow-y:scroll"  > 
                        <div id="mailbody"></div>
                    </div>
                    <div class="modal-footer clearfix" >
                    <button   class="btn btn-primary wiz_send_referral_mail" >Send Mail</button>
			<button data-dismiss="modal"  class="btn btn-danger" >Cancel</button>
		</div>
                    </div>
                </div>
            </div>
        </div>'; 
 
	echo $html; 
} 


//api
if($_POST['loadmail'] )
{  

    $fileid = $_POST['loadmail'];
    $receipentname =$_POST['receipentname'];
    $receipentemail =$_POST['to'];

    $ccname1= $_POST['ccname1'];
    $suggestname = $_POST['suggestname'];
    $profession  = $_POST['profession'];
    $suggestemail = $_POST['suggestemail'];
    $phone = $_POST['phone'];
    $clientid = $_POST['clientid'];
 
    
    $partnerrs = $link->query("select * from  mc_user  where id = (select user_id from  user_people where id='$clientid') " );
    if( $partnerrs->num_rows  > 0 )
    {
        $ratedby = $partnerrs->fetch_array()['username'];
        $ds = DIRECTORY_SEPARATOR;
        $apppath = '';
        $path =  $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath . $ds  ;  
        if($_username == $ratedby)
        {
            if(  file_exists( $path . "templates/mailsamemember" . $fileid  .   ".txt" ) )
            {
                $mailbody = file_get_contents( $path . "templates/mailsamemember" . $fileid  .   ".txt" ) ; 
                $mailbody = str_replace("{receipent}", $receipentname , $mailbody ) ;  
                $mailbody = str_replace("{rated_by}", $ratedby  , $mailbody ) ;
                $mailbody = str_replace("{introducee}", $suggestname , $mailbody ) ;
                $mailbody = str_replace("{introducee_profession}", $profession , $mailbody ) ;
                $mailbody = str_replace("{introducee_email}", $suggestemail , $mailbody ) ;
                $mailbody = str_replace("{introducee_phone}", $phone , $mailbody ) ; 
                echo $mailbody; 
            }
        }
        else 
        {
            if(  file_exists( $path . "templates/mail" . $fileid  .   ".txt" ) )
            {
                $mailbody = file_get_contents( $path . "templates/mail" . $fileid  .   ".txt" ) ; 
                $mailbody = str_replace("{receipent}", $receipentname , $mailbody ) ; 
                $mailbody = str_replace("{user}", $_username  , $mailbody ) ;
                $mailbody = str_replace("{rated_by}", $ratedby  , $mailbody ) ;
                $mailbody = str_replace("{introducee}", $suggestname , $mailbody ) ;
                $mailbody = str_replace("{introducee_profession}", $profession , $mailbody ) ;
                $mailbody = str_replace("{introducee_email}", $suggestemail , $mailbody ) ;
                $mailbody = str_replace("{introducee_phone}", $phone , $mailbody ) ; 
                echo $mailbody; 
            }
        }  
    }
    else
    {
        echo '0';
    }
}


//api
// Suggest referrals 
if(isset($_POST['wiz_sendmail']))
{
    $fileid = $_POST['wiz_sendmail']; 
	$suggestemail = $_POST['suggestemail']; 
	$suggestname = $_POST['suggestname'];
    $suggestid = $_POST['suggestid'];
	$email =  $_POST['to'];
	$profession = $_POST['profession']; 
	$phone = $_POST['phone'];
	
	$receipentname = $_POST['receipentname']; 
	$receipentprof = $_POST['receipentprof'];
	$receipentphone = $_POST['receipentphone'];
	$clientid = $_POST['clientid'];	
	
	$mailogid = $_POST['mailogid'];
    $cc1 = $_POST['cc1'];
    $ccname1 = $_POST['ccname1']; 


	$results = $link->query("SELECT * FROM referralsuggestions where id='$mailogid' "); 
	if($results->num_rows > 0)
	{
		$trow = $results->fetch_array(); 
		if($trow['emailstatus']==1)
		{
			echo "1";
			return;
		}
	} 

    $partnerrs = $link->query("select * from  mc_user  where id = (select user_id from  user_people where id='$clientid') " );
    if( $partnerrs->num_rows  > 0 )
    {
        $ratedby = $partnerrs->fetch_array()['username'];
        $ds = DIRECTORY_SEPARATOR; 
        $apppath = '';
        $path =  $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath . $ds  ; 
         //check if a refferalsuggestion entry exists. If not make an entry 
        $refcounter = $link->query("select count(*) as refcnt from  referralsuggestions 
        where   knowtorefer='$suggestid' and knowreferedto='$clientid' and knowenteredby='$user_id'")->fetch_array(); 
        if($refcounter['refcnt'] == 0)
        { 
            $refcounter = $link->query("select user_id from  user_people  where id='$suggestid'");
            if($refcounter->num_rows  > 0)
            { 
                $link->query ("insert into referralsuggestions 
                    ( partnerid, knowtorefer,knowreferedto, entrydate,   knowenteredby) 
                        VALUES ('". $refcounter->fetch_array()['user_id'] . "', '$suggestid', 
                        '$clientid' ,  NOW() ,  '$user_id') "); 
            } 
        }

         if($_username == $ratedby)
        {
            if(  file_exists( $path . "templates/mailsamemember" . $fileid  .   ".txt" ) )
            {
                $mailbody = file_get_contents( $path . "templates/mailsamemember" . $fileid  .   ".txt" ) ; 
                $mailbody = str_replace("{receipent}", $receipentname , $mailbody ) ;  
                $mailbody = str_replace("{rated_by}", $ratedby  , $mailbody ) ;
                $mailbody = str_replace("{introducee}", $suggestname , $mailbody ) ;
                $mailbody = str_replace("{introducee_profession}", $profession , $mailbody ) ;
                $mailbody = str_replace("{introducee_email}", $suggestemail , $mailbody ) ;
                $mailbody = str_replace("{introducee_phone}", $phone , $mailbody ) ; 
                $html = $mailbody; 
            }
        }
        else 
        {
            if(  file_exists( $path . "templates/mail" . $fileid  .   ".txt" ) )
            {
                $mailbody = file_get_contents( $path . "templates/mail" . $fileid  .   ".txt" ) ; 
                $mailbody = str_replace("{receipent}", $receipentname , $mailbody ) ; 
                $mailbody = str_replace("{user}", $_username  , $mailbody ) ;
                $mailbody = str_replace("{rated_by}", $ratedby  , $mailbody ) ;
                $mailbody = str_replace("{introducee}", $suggestname , $mailbody ) ;
                $mailbody = str_replace("{introducee_profession}", $profession , $mailbody ) ;
                $mailbody = str_replace("{introducee_email}", $suggestemail , $mailbody ) ;
                $mailbody = str_replace("{introducee_phone}", $phone , $mailbody ) ; 
                $html = $mailbody; 
            }
        } 
  

        $to =   $email ;  
        $subject = 'Introduction/Referral from ' . $_username ; 
        $headers = "From: referrals@mycity.com\r\n";
        $headers .= "Reply-To: referrals@mycity.com\r\n"; 
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
     
        //mail towards the new connect 
        $mailexistcheck= $link->query("SELECT COUNT(*) as ecnt FROM mailbox where sender='$user_id' AND receipent='$clientid' AND suggestedconnectid='$suggestid' "); 
        $ecnt = $mailexistcheck->fetch_array()['ecnt'] ;
        //if( $ecnt )
        // if( $ecnt == 0)
        $link->query("INSERT INTO mailbox (sender, receipent, subject, mailbody, senton, suggestedconnectid) VALUES ('$user_id',$clientid, '$subject', '" . $link->real_escape_string($html) . "' ,  NOW(), '$suggestid' )");
        //mark as mail sent
        //$link->query("UPDATE referralsuggestions SET emaillog='" .  $link->real_escape_string($html) . "' , emailstatus='1',  senton=NOW()  WHERE id='$mailogid' "); 
        // if(  mail($to, $subject, $html, $headers) == TRUE  )
        $mailstatus = sendreferralmail(   $to, $subject , $html, $html ,  $_user_email, $_username ,  $cc1, $ccname1) ; 
    
        $notifiermail  ='<!DOCTYPE html><html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Email from mycity.com</title>
        <style type="text/css">
            body {margin: 10px 0; padding: 0 10px; background: #f3f3f3; font-size: 14px;}
            table {border-collapse: collapse;}
            td {font-family: arial, sans-serif; color: #333333;} 
            @media only screen and (max-width: 480px) {
                body,table,td,p,a,li,blockquote {`
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
            <p>Hello ' . $_username  . ',</p>
            <p>Your referral networking partner ' . $link->real_escape_string( $suggestname)  . 
            ' is introduced to one of your connection that you gave a high rating. 
            You can count this as an introduction/referral for yourself.' . 
            '<div style="margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #efefef;padding:10px;
            border-radius: 4px; "><h3>Person Introduced</h3>
            <p><strong>Name:</strong> '. $suggestname .'</p>
            <p><strong>Profession:</strong> '.$profession .'</p>
            <p><strong>Email:</strong> '. $suggestemail .'</p>
            <p><strong>Phone:</strong> '.$phone .'</p></div>' .
            '<div style="margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #efefef;padding:10px;
            border-radius: 4px; "><h3>Connection Who Received the Introduction</h3> 
                    <p><strong>Name:</strong> '. $receipentname .'</p>
                        <p><strong>Profession:</strong> '.$receipentprof .'</p>
                    <p><strong>Email:</strong> '. $email .'</p>
                    <p><strong>Phone:</strong> '.$receipentphone .'</p></div><br/>
                    Follow up with the person that you know would also be helpful. 
            <br/>
            Sincerely,<br/>
            Referrals@mycity.com<br/>
            </p>
            <p>
            If you would like more information,<br/>
            please email or call<br/>
            310-736-5787<br/>
            <a href=\'mailto:bob@mycity.com\' target=\'_blank\'>bob@mycity.com</a></p></body></html>'; 
            //mail towards partner
            

        $mailstatus = sendemail( $cc1, 'Referral suggestion for one of your connection sent', $notifiermail , $notifiermail  ); 
        $mailstatus=1;
        if(  $mailstatus  == 1  )     
        {   
            //send another email back to sender 
            $html  ='<p>Hello ' . $link->real_escape_string( $suggestname) . ',</p>
                <p>A friend of yours recently gave you high ratings on the following questions:<br/>
            1 Do you want to grow your business?<br/>
            2 Are you willing to network?<br/>
            3 Are you willing to give referrals?<br/>
            4 What is your expertise in your field?<br/>
            </p>
            <p>
            They also mentioned that you are interested in meeting someone in the following vocation.
            </p>';  
            $html .= '
            <div style="margin-bottom: 20px;
                background-color: #fff;
                border: 1px solid #efefef;padding:10px;
                border-radius: 4px; ">  
                            <p><strong>Name:</strong> '. $receipentname .'</p>
                                <p><strong>Profession:</strong> '.$receipentprof .'</p>
                            <p><strong>Email:</strong> '. $to .'</p>
                            <p><strong>Phone:</strong> '.$receipentphone .'</p>
               ';
                     
            $html .='</div>'; 
            $html .='<p>We matched an individual also with high ratings through your friend\'s partner network.<br/>'; 
            /* If you are interested in this potential client or referral introduction, please respond with a yes
            and your friend will create the introduction.<br/> */
            $html .='<br/>
            Sincerely,<br/>
            Referrals@mycity.com<br/>
            </p>
            <p>
            If you would like more information,<br/>
            please email or call<br/>
            310-736-5787<br/>
            <a href=\'mailto:bob@mycity.com\' target=\'_blank\'>bob@mycity.com</a></p></body></html>'; 
            sendemail(   $suggestemail, $subject , $html, $html) ; 
            echo "success";
        }
        else
        {
                echo "fail";
        }   
        
    }
    }
/*
if(isset($_POST['wizstepfetchmember']))
{  
	$professions = $_POST['profession'];
	$professionlist = explode(",",  $professions); 
	$where_group = " ( "; 
	for($i=0; $i < sizeof($professionlist); $i++ )
	{  
		$where_group .= " find_in_set (  '". $professionlist[$i] . "' , client_profession ) ";
		if( $i < sizeof($professionlist)-1 )
		{
			$where_group .=  " OR "; 
		} 
	}
	$where_group .= " ) ";
	
	
	$user = $link->query("SELECT * FROM user_people where user_id = '$user_id' AND $where_group ORDER by client_name");
 
	$users = array();
	while($row = $user->fetch_array())
	{ 
		$users[] = array('id' =>  $row['id']  ,  'username' =>  $row['client_name']   ); 
	}
	echo  json_encode($users);  
}
*/

if(isset($_POST['importknows']))
{
	include_once("lib/excel_reader.php");
	$ds = DIRECTORY_SEPARATOR;  
	$apppath = ''; 
	$storeFolder =  'assets/uploads';    
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $ds. $apppath . $ds. $storeFolder . $ds .  'knowlist_' . $user_id . ".xls" ; 
	if ( !file_exists($targetPath)) 
	{ 
	  echo "nofile";  
	  return;
	} 
	$new =0; 
	$excel = new PhpExcelReader;
	$excel->read(  $targetPath );
	$sheet = $excel->sheets[0];
 
	$nr_sheets = count($excel->sheets);    
 
	if( $sheet['numRows'] <= 1)
	{
		 echo "<p class='alert'>There are no data to import.</p>";
	}	
	else
	{ 
		$x=2;
		$voc='';
		$comvoc = $link->query(" select * from mc_settings where  skey= 'common_vocation'  " );
		if($comvoc->num_rows  > 0)
		{
			$voc = $comvoc->fetch_array()['svalue'] ;
		} 
		while($x <= $sheet['numRows'])  //cycle every row 
		{
			$cname =  $sheet['cells'][$x][1] . " " . $sheet['cells'][$x][2];
			$email = $sheet['cells'][$x][3];
			$company = $sheet['cells'][$x][4];
			$profession = $sheet['cells'][$x][5];
			$livestyle = $sheet['cells'][$x][6];
			
			if( trim($cname) == ""   ) break;
			 
			$insnewknow = "INSERT INTO user_people (user_id, client_name, client_email, client_profession, company , isimport, entrydate  ) 
				VALUES ('$user_id','$cname','$email', '$profession', '$company', '1', NOW() )";
			 
			$insQ = $link->query($insnewknow); 
			$knowid = $link->insert_id; 
			$link->query("insert into user_answers ( question_id,  user_id, answer  ) values ('9', '$knowid',  '$voc' )"); 
			
			$new++;
			$x++; 
		}  
	} 
	echo $new ;
}
if(isset($_POST['editimportedcontacts']))
{
	$page = $_POST['editimportedcontacts'];
    getImportedKnows($user_id, $page);
}


if(isset($_POST['getImportedKnows']))
{
	$page = $_POST['getImportedKnows'];
    getImportedKnows($user_id, $page);
}
 

if(isset($_POST['linkedinimport']))
{
    include_once("lib/excel_reader.php");
    $ds = DIRECTORY_SEPARATOR;  
    $apppath = ''; 
    $storeFolder =  'assets/uploads';    
    $targetPath = $_SERVER['DOCUMENT_ROOT'] . $ds. $apppath . $ds. $storeFolder . $ds .  'linkeden_' . $user_id . ".xls" ;
	 
    if ( !file_exists($targetPath)) 
    { 
      echo "nofile";  
      return;
    }
    
    $new =0; 
    $excel = new PhpExcelReader;
    $excel->read(  $targetPath );
    $sheet = $excel->sheets[0];
 
    $nr_sheets = count($excel->sheets);    
 
    if( $sheet['numRows'] <= 1)
    {
         echo "<p class='alert'>There are no data to import.</p>";
    }   
    else
    {
        
        $x=2;
        $existingcount=0;
		$voc='';
		$comvoc = $link->query(" select * from mc_settings where  skey= 'common_vocation'  " );
		if($comvoc->num_rows  > 0)
		{
			$voc = $comvoc->fetch_array()['svalue'] ;
		}  
        while($x <= $sheet['numRows'])  //cycle every row 
        {
            $cname =  $sheet['cells'][$x][1] . " " . $sheet['cells'][$x][2] ;  
            $email = $sheet['cells'][$x][3];
            $company = $sheet['cells'][$x][4];
            $profession = $sheet['cells'][$x][5];
            if(trim($cname) != '' && trim( $email)  != ''  )  
            {
                $userpeople = $link->query("select count(*) as existscount from user_people where user_id='$user_id' and  client_email='$email'"); 
                if($userpeople->num_rows  > 0)
                {
                    if(  $userpeople->fetch_array()['existscount']  ==0)
                    {
                        $insnewknow = "INSERT INTO mc_linkedin_import 
                        (userid, fullname, email, company, profession, tag, entrydate )
                        VALUES ('$user_id','$cname','$email', '$company', '$profession', '$tag' , NOW())";
                        $insQ = $link->query($insnewknow);
                        
                        $insnewknow = "INSERT INTO user_people (user_id, client_name, client_email, client_profession, company , isimport, entrydate, lcid  ) 
                        VALUES ('$user_id','$cname','$email', '$profession', '$company', '1', NOW(), '$insQ' )";
                        
                        $insQ = $link->query($insnewknow); 
                        $knowid = $link->insert_id; 
                        $link->query("insert into user_answers ( question_id,  user_id, answer  ) values ('9', '$knowid',  '$voc' )");  
                        $new++;
                          
                    }
                }
            }
            else
            {
                break;
            }
            $x++;
        } 
    }  
    echo  $new ; 
}

if(isset($_POST['linkedinimportlist']))
{
	$goto = $_POST['linkedinimportlist'];
	$key = $_POST['key'];
	if($key != '')
		$and_where_clause = " and client_name like '%$key%'";
	else 
		$and_where_clause = "";
	
	$start = ($goto-1)*10; 
		
	//check if these records are already imported
	$imports  = $link->query("select  * from user_people 
    where  user_id='$user_id' and isimport='1' $and_where_clause order by id desc LIMIT $start,10");
	$pg = $link->query("SELECT count(*) as recnt  FROM user_people 
	WHERE  user_id = '$user_id' and isimport='1'  $and_where_clause " );
	$pages = ceil($pg->fetch_array()['recnt'] /10); 
	echo "<table class='table table-colored table-alternate table-bordered'>";
	echo "<tr><th>Name</th><th>Email</th><th>Profession</th><th>Company</th> <th>Action</th></tr>" ; 
    while( $row = $imports->fetch_array() ) 
    {
          echo "<tr><td>" . $row['client_name'] . '</td><td>'  . $row['client_email'] . 
          '</td><td>'  . $row['client_profession'] . '</td><td>'  . $row['company'] . 
          '</td>';
		 // if($row['mailsent'] == 1)
		  //{
			  //echo "<td><span class='badge badge-red'>Invited</span></td>";
		  //}
		 // else
		  //{
		  	//echo "<td><span class='badge badge-green'>Not Invited</span></td>";
		  //}
		  echo '<td>
		  <button data-email="' . $row['client_email'] . '"  data-receipent="' . $row['client_name'] . '" data-mailsent="'  . $row['mailsent'] . '" data-id="'  . $row['id'] . '" class="btn btn-primary btn-small btnshownmailtemplates"><i class="fa fa-envelope"></i></button></td></tr>' ;   
     } 
     
     $lastpage = $pages ;
     $prev = $goto == 1 ? 1 : $goto-1;
     $next = $goto == $pages ? $pages : $goto+1; 
     echo "<tr><td colspan='5'><ul class='pagination pagilinkedin'><li><a data-key='$key' data-func='prev' data-pg='$prev'>«</a></li>";
     if( $goto > 10) 	 echo "<li><a data-key='$key'  data-func='next' title='Show last few pages' data-pg='1'> ... </a></li>";
	 
		if($goto < 10)
		{ 
			 for($j= 1 ; $j  <=  10  ; $j++)
			 {
				 if($j > $pages)
				 {
					 break;
				 }
				 $active = $j == $goto ? 'active' : '';
				 echo "<li class='$active'><a data-key='$key'  data-pg='$j'>$j</a></li>";
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
			    echo "<li class='$active'><a data-key='$key'  data-pg='$i'>$i</a></li>";
			 }
		}
     
     if( $goto < ($lastpage - 10 ) )
	 echo "<li><a data-key='$key'  data-func='next' title='Show last few pages' data-pg='$lastpage'> ... </a></li>";
     echo "<li><a data-key='$key'  data-func='next' title='Next Page' data-pg='$next'>»</a></li></ul></td></tr>"; 
     echo   "</table>"; 
	
	
	 echo '<div class="modal mine-modal fade" id="selectlinkedinmail" tabindex="-1" role="dialog">
		 <div class="modal-dialog">
			<div class="modal-content"> 
				<div class="modal-header ">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Select Mail Template</h4>
				</div>
				<div class="modal-body text-left" style="height: 450px; overflow-y: scroll">
					<div id="linkedinmails"></div>
				</div>
				<div class="modal-footer"> 
					<div class="col-xs-12"> 
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div> 
			</div>
		</div>
	</div>
	';
} 
 

if(isset($_POST['linkedinsignups']))
{
	$goto = $_POST['linkedinsignups'];
	$key = $_POST['key'];
	if($key != '')
		$and_where_clause = " and u.username like '%$key%'";
	else 
		$and_where_clause = "";
	
	$start = ($goto-1)*10; 
		
	//check if these records are already imported
	$imports  = $link->query("select u.id, u.user_email, u.username, u.user_pkg, user_phone,  d.city, d.zip, d.country,
     d.groups, d.target_clients, d.target_referral_partners, d.vocations, d.about_your_self 
     from mc_user as u inner join user_details as d on u.id=d.user_id inner join mc_linkedin_import as l 
     on l.id=d.lcid where d.lcid > '0'  $and_where_clause order by u.id desc LIMIT $start,10"); 
     
    $pg = $link->query("select count(*) as recnt  from user_details as d inner join mc_linkedin_import as l 
	 on l.id=d.lcid where d.lcid >  '0'  $and_where_clause " );
   
	$pages = ceil($pg->fetch_array()['recnt'] /10); 
	echo "<table class='table table-colored table-alternate table-bordered'>";
	echo "<tr><th>Name</th><th>Email</th><th>Package</th><th>Phone</th><th>Vocations</th><th>Action</th></tr>" ; 
    while( $row = $imports->fetch_array() ) 
    {
          echo "<tr><td>" . $row['username'] . '</td><td>'  . $row['user_email'] . 
          '</td><td>'  . $row['user_pkg'] . '</td><td>'  . $row['user_phone'] . 
          '</td><td>'  . $row['vocations'] . 
          '</td>'  ; 
		  echo '<td>
		  <button data-toggle="modal"  id="' . $row['id'] . '"   data-target="#myModal" class="btn btn-primary btn-small leaveMsg "><i class="fa fa-envelope"></i></button></td></tr>' ;   
     }

	 $lastpage = $pages ;
     $prev = $goto == 1 ? 1 : $goto-1;
     $next = $goto == $pages ? $pages : $goto+1; 
     echo "<tr><td colspan='5'><ul class='pagination pagilinkedin'><li><a data-key='$key' data-func='prev' data-pg='$prev'>«</a></li>";
     if( $goto > 10) 
	 echo "<li><a data-key='$key'  data-func='next' title='Show last few pages' data-pg='1'> ... </a></li>";
	 
		if($goto < 10)
		{ 
			 for($j= 1 ; $j  <=  10  ; $j++)
			 {
				 if($j > $pages)
				 {
					 break;
				 }
				
				 $active = $j == $goto ? 'active' : '';
				 echo "<li class='$active'><a data-key='$key'  data-pg='$j'>$j</a></li>";
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
			    echo "<li class='$active'><a data-key='$key'  data-pg='$i'>$i</a></li>";
			 }
		}
	 if( $goto < ($lastpage - 10 ) )
	 echo "<li><a data-key='$key'  data-func='next' title='Show last few pages' data-pg='$lastpage'> ... </a></li>";
     echo "<li><a data-key='$key'  data-func='next' title='Next Page' data-pg='$next'>»</a></li></ul></td></tr>"; 
     echo   "</table>";

	 echo '<div class="modal mine-modal fade" id="selectlinkedinmail" tabindex="-1" role="dialog">
		 <div class="modal-dialog">
			<div class="modal-content"> 
				<div class="modal-header ">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Select Mail Template</h4>
				</div>
				<div class="modal-body text-left" style="height: 450px; overflow-y: scroll">
					<div id="linkedinmails"></div>
				</div>
				<div class="modal-footer"> 
					<div class="col-xs-12"> 
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div> 
			</div>
		</div>
	</div>
	';
}
 
if(isset($_POST['loadmailtemplates'])  )
{
	$receipent  = $_POST['name'];
	$email = $_POST['email'];
	$contact = $_POST['contact'];
	$id =$_POST['loadmailtemplates'];
	
	//check if mail was sent earlier 
	
	$maillog  = $link->query("select mailsent from  mc_linkedin_import where  id='$id'");
	$mailsent = 0;
	if($maillog->num_rows > 0)
	{
		$mailsent = $maillog->fetch_array()['mailsent'];
	} 
	if($mailsent ==  0 )
	{
        $mailtemplates  = $link->query("select * from  mc_mail_templates order by templatename");  
		$html =  '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">'; 
		$counter=1 ; 
		if( sizeof($mailtemplates)  > 0)
		{
			$html .=  '<h4 class="text-center">Below are the available email templates. Select the one email</h4>';
			foreach ($mailtemplates as $item )
			{
				if(  $item['mailtype'] == 0 )
				{
					$html .= '<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading' . $counter .'">
					<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $counter .'" aria-expanded="true" aria-controls="collapse' . $counter .'">' .
						$item['templatename'] .'
					</a>
					</h4>
					</div>
					<div id="collapse' . $counter .'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading' . $counter .'">
					<div class="panel-body">
					'. $item['mailbody'] .'
					<button data-contactid="' . $contact  . '" data-email="' . $email  . '" data-name="' . $receipent  . '" data-tid="' . $item['id'] . '" type="button" data-dismiss="modal"  class="btn btn-success btnsendlinkedininvite">Send Mail</button>
					</div>
					</div>
					</div>'; 
				} 
				$counter++;
			}
		}  
		else 
	        $html .= '<h4 class="text-center">No email template has been configured yet. Please contact admin!</h4>';
		 
	$html .=  "</div>"; 
 	echo $html;
	} 
}
  
if(isset($_POST['invitelinkedincontact']))
{
	$receipent  = $_POST['receipent'];
	$email = $_POST['email'];
	$tid = $_POST['templateid'];  
	$id = $_POST['invitelinkedincontact']; 
	
	//check if these records are already imported
    $maillog  = $link->query("select * from  mc_linkedin_import as li inner join mc_user as u on u.id=li.userid 
	where li.id='$id'"); 
 	$mailsent = 0; 
	if($maillog->num_rows > 0)
	{
		$maillogrow  = $maillog->fetch_array()['mailsent'];
		$mailsent = $maillogrow['mailsent'];
	}  
	if($mailsent == 1)
	{
		echo "0";	
	}
	else 
	{
		$mailcontent   = $link->query("select mailbody, subject from  mc_mail_templates  where id='$tid'"); 
		
		 
		if($mailcontent->num_rows > 0)
		{
			$row = $mailcontent->fetch_array();	
			$subject =  $row['subject'];
			$mailbody = $row['mailbody']; 
			 //creating dynamic and hashed URL  
			$len = strlen(md5( $id )) . 's' .  strlen(   (string)$id ); 
			$hid = md5( $id ) . $id . md5( $id );
 			$lurl = $siteurl.'/landing.php?id=' . md5( $id ) . '&l=' . $len  .  '&hid='  .  $hid ;
			$landingpage = "<a target='_blank' href='$lurl'>here</a>"; 
    		
			$results = $link->query("select * from mc_mail_templates where id='$templateid'  "); 
			$mailbody = str_replace("{linkedin_contact_landing_page_url}", $landingpage , $mailbody ) ;
			 
			$body ="<!DOCTYPE html><html>
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
								<td style='padding: 10px 10px 30px 10px;'>
								<p>Hi " . $receipent  .",</p>
								 "
								 . $mailbody  .
								"</td>
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
            
			 sendmailusersigned( $email  ,  $_user_email ,  $_username,  $subject, $body, $body) ;
			//$link->query("update  mc_linkedin_import 
			//set  subject='$subject', mailbody='" . $link->real_escape_string($body) . "', senddate=NOW(),
			//mailsent='1' where id='$id'"); 
			 $link->query("insert into mailbox 
			 (sender, receipent, subject, mailbody, senton, suggestedconnectid, email_type) 
			 VALUES ('$user_id', $id , '$subject', '" . $link->real_escape_string( $body ) . "' ,  NOW(), '-1', 'linkedin-invite')");
			echo '1';  
		}
		else 
		{
			echo '0';
		}
	}
} 


if(isset($_POST['loadlinvitemailtemplate'])  )
{
	$receipent  = $_POST['name'];
	$email = $_POST['email'];
	$contact = $_POST['contact'];
	$id =$_POST['loadmailtemplates'];
	
	//check if mail was sent earlier  
	$maillog  = $link->query("select mailsent from   mc_linkedin_import where  id='$id'");
	$mailsent = 0;
	if($maillog->num_rows > 0)
	{
		$mailsent = $maillog->fetch_array()['mailsent'];
	} 
	if($mailsent ==  0 )
	{
		$mailtemplates  = $link->query("select * from  mc_mail_templates order by templatename");  
		$html =  '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">'; 
		$counter=1 ;

		if( sizeof($mailtemplates)  > 0)
		{
			$html .=  '<h4 class="text-center">Below are the available email templates. Select the one email</h4>';
			foreach ($mailtemplates as $item )
			{
				if(  $item['mailtype'] == 2 )
				{
					$html .= '<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading' . $counter .'">
					<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $counter .'" aria-expanded="true" aria-controls="collapse' . $counter .'">' .
						$item['templatename'] .'
					</a>
					</h4>
					</div>
					<div id="collapse' . $counter .'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading' . $counter .'">
					<div class="panel-body">
					'. $item['mailbody'] .'
                    <p style="line-height: 1.755"><br>Sincerely<br>
                    ' . $_username . '<br> 
                    ' . $_user_email . '<br> 
                    ' . $_user_phone . '<br> 
                    </p> 
  

<button data-contactid="' . $contact  . '" data-email="' . $email  . '" data-name="' . $receipent  . '" data-tid="' . $item['id'] . '" type="button" data-dismiss="modal"  class="btn btn-success btnsendlinkedininvite">Send Mail</button>
					</div>
					</div>
					</div>'; 
				} 
				$counter++;
			}
		}  
		else 
	        $html .= '<h4 class="text-center">No email template has been configured yet. Please contact admin!</h4>';
		 
	$html .=  "</div>"; 
 	echo $html;
    
    }
}




if(isset($_POST['set_reminder']))
{
	$title = $_POST['title'];
    $type = $_POST['type'];
	$text = $_POST['text']; 
	$assignedto = $_POST['assignedto']; 
	$reminderdate = $_POST['reminderdate'];
	$hr = $_POST['hr'];
	$min = $_POST['min'];
	$hrformat = $_POST['hrformat']; 
	
	$err = 0;
	$dateparts= explode ('-', $reminderdate);
	if( sizeof($dateparts) ==3)
	{
		$reminderdatetime = date('Y-m-d H:i:s', strtotime($dateparts[2] . "-" . $dateparts[1] . "-" . $dateparts[0] . 
		" " . $hr . ":" . $min . " " . $hrformat) ); 
	}
	else 
	{
		$err = 1; 
		$errlog = array( 'err'  =>   '1', 'msg'  =>   'Invalid date provided!' ); 
	}
	
	if ( !isset($title) || $title == "")
	{
		$err = 1; 
		$errlog = array( 'err'  =>   '1', 'msg'  =>   'Missing reminder title!' ); 
	}
	
	if ( !isset($text) || $text == "")
	{
		$err = 1; 
		$errlog = array( 'err'  =>   '1', 'msg'  =>   'Missing reminder body!' ); 
	}
	
	if ( !isset($type) || $type == "")
	{
		$err = 1; 
		$errlog = array( 'err'  =>   '1', 'msg'  =>   'You need to specify reminder type!' ); 
	} 
	if($err == 0)
	{
		$insQstmnt = "insert into mc_reminder 
		(type,subject,reminderbody,assignedto, emailreminderon, entrydate , enteredby) 
		VALUES ('$type','$title','$text',  $assignedto,  '$reminderdatetime'  , NOW() , '$user_id')";
		$insQ = $link->query($insQstmnt);
		$insID = $link->insert_id; 	  
		$errlog = array( 'err'  =>   '0', 'msg' => $insQstmnt. 'Reminder saved successfully!'  );  
	}
	
	echo json_encode($errlog);
}
 

// ******** Selecting User   ********

//api
if(isset($_POST['selectUserID'])){
    $assignno = $_POST['assignno'];
	$username ='';
    $q = $link->query(" SELECT  id, client_name  FROM user_people  WHERE id  = '$assignno'  ");
    if($q->num_rows > 0)
	{
		$row = $q->fetch_array();
		$username= $row['client_name']; 
    }
	echo $username;
}

// ******** Delete Reminder  User ********
if(isset($_POST['delReminder'])){
    $id = $_POST['delReminder'];
    $q = $link->query("SELECT id FROM user_people WHERE user_id = '$user_id' ORDER BY client_name ASC");
    if($q->num_rows > 0){
        $link->query("delete from mc_reminder where id = '$id'");
    }
}


 // Get Profile 
if(isset($_POST['searchpartner']))
{
    $partnername = $_POST['partnername'] ;
    $results = $link->query("SELECT * FROM user_details as a inner join mc_user as b on b.id = a.user_id  
    where  username like '%". $partnername . "%'  AND  b.id != 1");
    
 
    if($results->num_rows > 0)
    {
        $users = array();
          while($row = $results->fetch_array()) 
          {
            $refcnt =0;  
            $revrefcnt =0;
            $str = "abcdefghijklmnopqrstuvwxyz";
            $rand = substr(str_shuffle($str),0,3);
            $partnerinreferrals  = $link->query("select * from  user_people  where  client_email='". $row["user_email"] ."'");
 
 			if($partnerinreferrals->num_rows > 0)
            {
                $partnerref = $partnerinreferrals->fetch_array();
                $refpartcount  = $link->query("select count(*) as refcnt  from  referralsuggestions 
                    where knowenteredby='$user_id' and emailstatus='1' and 
                    knowreferedto IN (SELECT id FROM `user_people` where client_email='". $row["user_email"] ."' )")->fetch_array();
                $refcnt = $refpartcount['refcnt']; 
            }

        //Counting referrals sent back
        $reversesender = $link->query("select  sum(s.total)  as totalreceived from user_people as u 
            inner join ( SELECT receipent, count(*) as total FROM  mailbox as m inner join user_people as p on 
            m.receipent=p.id  where sender='" . $row['user_id']  . "'  group by receipent ) as s on u.id=s.receipent
where u.client_email='" . $_SESSION['user_email'] . "'");    
         
        $revrefcnt = 0;
        if($reversesender->num_rows > 0)
        { $revrefcnt =  $reversesender->fetch_array()['totalreceived'];  
            if( $revrefcnt  == '') $revrefcnt= 0;
            
        }
                 
        $html .=   '<div class="panel panel-default">
        <div class="panel-body">
        <div class="row">
        <div class="col-sm-2">
        <img src=\''.$user_picutre.'\' alt="' .  $row["username"] . '" class="img-rounded" height="120" width="120">


        </div>
        <div class="col-sm-4">
        <p><strong>Name:</strong> '.$row["id"].'</p>
        <p><strong>Email:</strong> '.$row["user_email"].'</p>
        <p><strong>Phone:</strong> '.$row["user_phone"].'</p>';

    $html .='</div>
        <div class="col-sm-3">
            <div class="hero-widget well well-sm">
                <div class="icon">
                     <i class="fa fa-user fa-3x green"></i>
                </div>
                <div class="text">
                    <var>'.  $refcnt . '</var>
                    <label class="text-muted">Referrals Sent</label>
                </div>
                <div class="options">
                    <button class="btn btn-primary btn-md"><i class="fa fa-search"></i> View Referrals</button>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="hero-widget well well-sm">
                <div class="icon">
                     <i class="fa fa-user fa-3x orange"></i>
                </div>
                <div class="text">
                    <var>'.  $revrefcnt . '</var>
                    <label class="text-muted">Referrals Received</label>
                </div>
                <div class="options">
                    <button class="btn btn-primary btn-md"><i class="fa fa-search"></i> View Referrals</button>
                </div>
            </div>
        </div>
 
        </div></div></div>';
      } 
      echo $html; 
    }     
} 
 
if(isset($_POST['checksession']))
{
    if($user_id == '')
        echo 'session_out'; 
} 

//for admin
if(isset($_POST['showmyknows'])  )
{ 

    $page = $_POST['page'] ;
  
    if($_user_role == 'admin')
    {
        getMyCityUsers($page);
    }
    else
    {
        getReferences($user_id, $page);
    } 
}



if(isset($_POST['generateknowstates']))
{  

    $page = $_POST['page']; 
    $role = $_POST['role']; 

    $pagesize = 10;
    $start = ($page - 1) * $pagesize; 

    if ($role ==1) //admin
    {
        $knowresult = $link->query("select u.id, u.user_email, u.username, u.user_phone,count(*) as knowcount 
        from user_people  as p inner join mc_user as u on p.user_id=u.id 
        group by p.user_id order by knowcount desc limit " . $start . ", " .  $pagesize );
    }
    else 
    {
        $user = $link->query("select * from user_details where user_id = '$user_id'");
        $user = $user->fetch_array();
        $groups = explode(",", $user['groups']);

        $whereGroup = "(FIND_IN_SET('".implode("', `groups`) OR FIND_IN_SET('", $groups)."', `groups`))"; 
        
        $subquery = "select user_id from user_details where user_id <> '$user_id' and user_id <> '1' and " . $whereGroup  ; 

        $knowresult = $link->query( "select u.id, u.user_email, u.username, u.user_phone,count(*) as knowcount 
        from user_people  as p inner join mc_user as u on p.user_id=u.id 
        where u.id in (" . $subquery  . ") group by p.user_id" );
    }  
    $html ='<table class="table table-bordered table-striped">';
    $html .='<tr><th>ID</th><th>Email</th><th>Name</th><th>Phone</th>
    <th>Total Knows Added</th></tr>'; 
    $i=1; 
    if($knowresult->num_rows > 0)
    {
        if ($role ==1) //admin
        {
            $pg = $link->query("select u.id from user_people  as p inner join mc_user as u on p.user_id=u.id  
                group by p.user_id order by p.id" );
            $pages = ceil($pg->num_rows/10);
        }
        else 
        {
            $pg = $link->query( "select u.id from user_people  as p inner join mc_user as u on p.user_id=u.id 
                where u.id in (" . $subquery  . ") group by p.user_id" );
            $pages = ceil($pg->num_rows/10);
        }

        while($row = $knowresult->fetch_array())
        {
            $html .= '<tr id="' . $row['id'] . '">
            <td>' . $i . '</td>' . 
            '<td >' .$row['user_email'] . '</td>' . 
            '<td >' .$row['username'] . '</td>' . 
            '<td >' .$row['user_phone'] . '</td>' . 
            '<td >' .$row['knowcount'] . '</td>' .
            '</tr>';
            $i++; 
        }

        $html .='</table>';
        $prev = $page == 1 ? 1 : $page-1;
        $next = $page == $pages ? $pages : $page+1;

        $html .= "<ul class='pagination pagiAd'><li><a data-role='" .  $role.  "'  data-func='prev' class='btnknowreport' data-page='$prev'>«</a></li>";
        for($i=1; $i<=$pages; $i++){
            $active = $i == $page ? 'active' : '';
            $html .= "<li class='$active'><a data-role='" .  $role.  "' class='btnknowreport' data-page='$i'>$i</a></li>";
        }

        $html .= "<li><a data-role='" .  $role.  "' class='btnknowreport' data-func='next' data-page='$next'>»</a></li></ul>"; 

}
echo $html ;

}


if(isset($_POST['savetestimonial']))
{
    $id =  $_POST['testimonialid'] ; 
    $summary =  $link->real_escape_string ( $_POST['summary'] ) ; 
    $video =  $link->real_escape_string($_POST['video'] ) ;  

    if(isset($id ) && $id > 0)
    {
       $results = $link->query("update mc_testimonial set videolink='$video', summary='$summary' where id='$id' ");    
    }
    else
    { 
         $results = $link->query("insert into mc_testimonial (videolink, summary) values ( '$video', '$summary') ");   
    }  
    echo "Testimonial updated!";  
}

if(isset($_POST['reloadtestimonial']))
{
    $video_testimonials = getTestimonials($link);
    $rowindex=1;
    foreach ($video_testimonials as $item )
    {
        echo "<tr><td id='tbody-" . $item['id'] . "'><span class='videolink" . $item['id'] . "'>" . $item['videolink'] ."</span>" ;
        echo "</td><td class='videosummary" . $item['id'] . "'>" .  $item['summary']  .  "</td><td> 
        <button class='btn-primary btn btn-xs edittestimonial' data-id='" . $item['id'] . "'><i class='fa fa-pencil'></i></button>
        <button class='btn-danger btn btn-xs deletestimonial' data-id='" . $item['id'] . "'><i class='fa fa-trash'></i></button>
        ";
        $rowindex++;
    } 
    if($rowindex == 1)
    {
        echo '<tr><td colspan="3">No testimonial exists!</td></tr>'; 
    }
} 
 
if(isset($_POST['delTestimonial']))
{
    $id =  $_POST['delTestimonial'] ;
    $results = $link->query("delete from mc_testimonial where id='$id' ");
    echo "success";  
}

if(isset($_POST['getnewcontacts']))
{
    $id  = $_POST['userid'];
	$date = date('Y-m-d', strtotime("-2 week"))  ;
	$goto = $_POST['page']; 
	$start = ($goto-1)*10;  
	
	$key = $_POST['key'];
	if($key != '')
		$and_where_clause = " and client_name like '%$key%'";
	else 
		$and_where_clause = "";
	
	
	$pg = $link->query(" select count(*) as reccnt from user_people where date(entrydate) > '$date' and user_id='$id' $and_where_clause order by id desc " );
	$pages = ceil($pg->fetch_array()['reccnt'] /10);
	 
	$knowresult = $link->query(
	"select * from user_people where date(entrydate) > '$date' and user_id='$id' $and_where_clause order by id desc limit $start,10 " );
	
	echo "<table class='table table-striped'><tr><td>Name</td><td>Email</td><td>Profession</td><td>Action</td></tr>";
	$rowindex =0;
    foreach ($knowresult as $item )
    {
        echo "<tr><td id='tbody-" . $item['id'] . "'>" . $item['client_name'] ."</td>" ;
        echo "<td>" .  $item['client_email']  .  "</td>";
		echo "<td>" .  $item['client_profession']  .  "</td>";
        echo "<td>
		<button data-id='" .  $item['id']  .  "' data-receipent='" .  $item['client_name']  .  "' data-email='" .  $item['client_email']  .  "' title='Send Testimonial Video' class='btn-primary btn btn-xs btnsendvideolink' data-id='" . $item['id'] . "'><i class='fa fa-envelope'></i></button>
        </td></tr>";
        $rowindex++;
    }
	
	$lastpage = $pages ;
	$prev = $goto == 1 ? 1 : $goto-1;
    $next = $goto == $pages ? $pages : $goto+1; 
	echo "<tr><td colspan='5'><ul class='pagination paginewcontacts'><li><a data-key='$key' data-mid='$id' data-func='prev' data-pg='$prev'>«</a></li>";
    
	if( $goto > 10)
		echo "<li><a data-key='$key' data-mid='$id' data-func='next' title='Show last few pages' data-pg='1'> ... </a></li>";
		if($goto < 10)
		{ 
			for($j= 1 ; $j  <=  10  ; $j++)
			{
				if($j > $pages)
				{
					break;
				}
				
				$active = $j == $goto ? 'active' : '';
				echo "<li class='$active'><a data-mid='$id' data-key='$key'  data-pg='$j'>$j</a></li>";
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
			    echo "<li class='$active'><a data-key='$key' data-mid='$id' data-pg='$i'>$i</a></li>";
			 }
		}
	
	if( $goto < ($lastpage - 10 ) )
	 echo "<li><a data-key='$key' data-mid='$id' data-func='next' title='Show last few pages' data-pg='$lastpage'> ... </a></li>";
     echo "<li><a data-key='$key' data-mid='$id' data-func='next' title='Next Page' data-pg='$next'>»</a></li></ul></td></tr>"; 
     echo   "</table>";  
	
	echo '
	 	<div class="modal mine-modal fade" id="selectvideomail" tabindex="-1" role="dialog">
		 <div class="modal-dialog">
			<div class="modal-content"> 
				<div class="modal-header ">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Select Mail Template</h4>
				</div>
				<div class="modal-body text-left" style="height: 450px; overflow-y: scroll">
					<div id="videomailtemplates"></div>
				</div>
				<div class="modal-footer"> 
					<div class="col-xs-12"> 
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div> 
			</div>
		</div>
	</div>
	'; 
}


if(isset($_POST['loadvideomailtemplates'])  )
{
	$receipent  = $_POST['name'];
	$email = $_POST['email'];
	$contact = $_POST['contact'];
	$mailtype = $_POST['mailtype'];
	 
	$mailtemplates  = $link->query("select * from  mc_mail_templates where mailtype='$mailtype' order by templatename"); 
	$html =  '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">'; 
		$counter=1 ; 
		if( sizeof($mailtemplates)  > 0)
		{
			$html .=  '<p class="text-center">Specify the Video URL you want to share in the box below and from among the
			mail templates select one to send an email with the Video URL you specified.</p>';
			
			$html .= '<input type="text" id="tbvideourl" placeholder="Youtube Testimonial Video URL" class="form-control"/>';
			
			foreach ($mailtemplates as $item )
			{
				 
					$html .= '<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading' . $counter .'">
					<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $counter .'" aria-expanded="true" aria-controls="collapse' . $counter .'">' .
						$item['templatename'] .'
					</a>
					</h4>
					</div>
					<div id="collapse' . $counter .'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading' . $counter .'">
					<div class="panel-body">
					
					'. $item['mailbody'] .'
					<button data-contactid="' . $contact  . '" data-email="' . $email  . '" data-name="' . $receipent  . '" data-tid="' . $item['id'] . '" type="button" data-dismiss="modal"  class="btn btn-success btnsendvideourl">Send Mail</button>
					</div>
					</div>
					</div>'; 
				 
				$counter++;
			}
		}  
		else 
	        $html .= '<h4 class="text-center">No email template has been configured yet. Please contact admin!</h4>';
		 
	$html .=  "</div>"; 
 	echo $html;
	 
}


if(isset($_POST['sendvideourl']))
{
	 
	
    $sendcode=0;
	$receipentemail =  $_POST['email'] ;  
	$receipentid =  $_POST['userid'] ;
    $templateid =  $_POST['templateid'] ;
	$sender =  'Referrals MyCity';  ; 
    $sendermail = 'referrals@mycity.com';     
	
	// for mail variables
	$receipentname = $_POST['receipent'];
	$videourl = $_POST['url'];
	$videourlparts = explode('/',  $videourl );
 
    $videoid = $videourlparts[sizeof($videourlparts) -1 ];
	 
	
	$videolink = "<a target='_blank' href='http://mycity.com/testimonial-video.php?id=" . $videoid .  "'>http://mycity.com/testimonial-video.php?id=" . $videoid .  "</a>"; 
    $results = $link->query("select * from mc_mail_templates where id='$templateid'  ");
    
	if($results->num_rows > 0)
    {
         $row = $results->fetch_array(); 
         $subject =  $row['subject'];
		
		$mailbody = $row['mailbody'];	
		$mailbody = str_replace("{receipent}", $receipentname , $mailbody ) ;
		$mailbody = str_replace("{link_url}", $videolink , $mailbody ) ;
		  
       $body = "<!DOCTYPE html><html>
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
                    <td style='padding: 10px 10px 30px 10px;'> <div style='font-size: 16px;'> ".
		   		"<div>" . $mailbody . 
		   	   "</div> 
				<p>
				<br/>
				Sincerely,<br/>
				Referrals@mycity.com<br/>
				</p>
				<p>
				If you would like more information,<br/>
				please email or call<br/>
				310-736-5787<br/>
				</p>
				</div>
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
                                    Copyright &copy; 2017 | All Rights Reserved.
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
</body></html>";
 
	//take log of the outgoing trigger mail 
	//$link->query("INSERT INTO mailbox 
	//(sender, receipent, subject, mailbody, senton, suggestedconnectid, email_type) 
	//VALUES ('$user_id',$receipentid, '$subject', '" . $link->real_escape_string( $mailbody ) . "' ,  NOW(), '-1', 'trigger-mail')");
       $sendcode = sendmail($receipentemail,  $sendermail ,  $subject, $body, $body); 
    }
	echo $sendcode; 
}

	
// Add/Update Packages
if(isset($_POST['settings'] )){
    $settinid  =0;
	if($_user_role == 'admin') {
        $vocation = $_POST['vocation'] ;
        $settings = $_POST['settings'] ;
	if($settings ==1)
	{
		$reccount = $link->query(" select * from mc_settings where  skey= 'common_vocation'  " ); 
		if($reccount->num_rows  > 0)
		{ 
			$link->query("update mc_settings set svalue='$vocation'  where skey='common_vocation' ");
			$settinid = $link->affected_rows;
		}
		else 
		{
			$link->query("insert into mc_settings ( skey , svalue  ) values ('common_vocation', '$vocation' )");
			$settinid = $link->insert_id; 
		} 
		
	}      
    }
	echo $settinid ; 
}
 

if(isset($_POST['loadsettings'] ))
{
	if($_user_role == 'admin')
	{ 
		$settings = $_POST['loadsettings'] ;
		$id = $_POST['id'] ;
		if($settings ==1)
		{
			$comvoc = $link->query(" select * from mc_settings where  skey= 'common_vocation'  " );
			if($comvoc->num_rows  > 0)
			{
				$voc = $comvoc->fetch_array()['svalue'] ;
				
				$html  =  "<p><strong>Common Vocations for imported contacts:</strong> </p>
				<select name='common_vocations[]' data-placeholder='Common vocations' class='form-control chosen-selec editcommon_vocations' multiple  >";
 
                $commonvocations = explode(",",  $voc); 
				for($v=0; $v < sizeof($commonvocations); $v++)
				{
                    $html .= "<option value='" . $commonvocations[$v] . "'>" .  $commonvocations[$v]  . "</option>";
                } 
                
                $html .=  "</select>";
				echo $voc;
				 
			}
		}      
    } 
} 

if(isset($_POST['addcommonvocation'] ))
{
    $updatecount =0;
	if($_user_role == 'admin')
	{ 
		$vocation = $_POST['vocation'] ;
		$knowid = $_POST['knowid'] ;
		$result = $link->query(" select * from user_answers where  question_id='9' and user_id='$knowid'   " );
		if($result->num_rows > 0)
		{
			$newvoc = $result->fetch_array()['answer'] . "," .  $vocation;  
			$newvocarr = explode(',',$newvoc);
			$newvocationsarr = array_unique($newvocarr);
			$nwevocstr = implode(', ',  $newvocationsarr );
			
			$link->query("update user_answers set  answer ='$nwevocstr'  where  question_id='9' and user_id='$knowid'     ");
			$updatecount = $link->affected_rows;
		}
		else 
		{
			$link->query("insert into user_answers ( question_id,  user_id, answer  ) values ('9', '$knowid',  '$vocation' )");
			$updatecount = $link->insert_id; 
		}        
    }  
	echo $updatecount;
}


if(isset($_POST['updateLinkedContact']))
{

	$id = $_SESSION['linkedincid'] ;  
 	$ques_rate = $_POST['updateLinkedContact']['ques_rate'];
    $ques_text = $_POST['updateLinkedContact']['ques_text'];
    $ques = $_POST['updateLinkedContact']['ques'];
	
	$link->query("DELETE FROM user_rating WHERE user_id = '$id'");
	$link->query("DELETE FROM user_answers WHERE user_id = '$id'");
	
	for($i=0; $i<count($ques_rate); $i++)
	{
		$link->query("INSERT INTO user_rating (user_id, question_id, ranking) VALUES ('$id', '".$ques[$i]."', '".$ques_rate[$i]."')");
	}
	
	for($i=0; $i<count($ques_text); $i++) 
	{
		$q_id   = $ques_text[$i][id];
		$answer = $ques_text[$i][answer];
		if( $answer ) 
		{
			$link->query("INSERT INTO user_answers (user_id, question_id,  answer) values('$id', '".$q_id."', '".$answer."')");
		}
	}
    //saving userid for use later in signup  
    $_SESSION['linkeduserid']  =  $id  ; 
 
}
 
//api
if(isset($_POST['fetchallpartners']))
{
	$goto = $_POST['fetchallpartners']; 
    $start = ($goto-1)*10;
    
    $user = $link->query("select * from user_details where user_id = '$user_id'");
    $user = $user->fetch_array();
    $groups = explode(",", $user['groups']);  
	$where_in_set = " (  " ;
    
    for($i=0; $i < sizeof($groups); $i++ )
    {
        $groupid = $groups[$i];
		$where_in_set .= " find_in_set('$groupid', groups) "; 
		if( $i < sizeof($groups)-1 )
		{
			$where_in_set .= " OR "; 
		}
	}
    $where_in_set .=" ) " ;
    
	$mainQry = " select a.user_id, b.username,b.user_email, a.vocations  from user_details as a inner join mc_user as b on b.id = a.user_id 
	where $where_in_set and b.id != '1' and user_pkg='Gold'  order by username limit $start,10 " ; 
  
    $mainQryCnt = "select count(*) as cnt from user_details as a inner join mc_user as b on b.id = a.user_id 
	where $where_in_set and b.id != '1' and user_pkg='Gold'  " ;
 
    $q = $link->query( $mainQry );
    $html = "No records found!";
    if($q->num_rows > 0)
    {
        
        $pg = $link->query(  $mainQryCnt );
        $pages =  ceil( $pg->fetch_array()['cnt'] /10);
 
 
        $html = "<table class='table table-alternate'><tr><td>Name</td><td>Email</td><td>Vocation</td><td>Action</td></tr>";
        while($row = $q->fetch_array())
        {
				$id = $row['user_id'];
				$partner_name = $row['username'];
				$partner_email = $row['user_email'];
				$partner_vocations = $row['vocations']; 
 
				$str = "abcdefghijklmnopqrstuvwxyz";
				$rand = substr(str_shuffle($str),0,3); 
				$html .= "<tr id='$rand-$id'> 
					<td>$partner_name</td>
					<td>$partner_email</td>
					<td>$partner_vocations</td> 
					<td> 
					 <button title='Edit Before Sending Mail' data-name='$partner_name' data-email='$partner_email' class='btn-primary btn btn-xs showmailpreviewmodal'><i class='fa fa-pencil'></i></button>
					 
					</td>
				</tr>";
        } 
        $prev = $goto == 1 ? 1 : $goto-1;
        $next = $goto == $pages ? $pages : $goto+1;
        $html .= "<tr><td colspan='8'><ul class='pagination allpartners'><li><a data-func='prev' data-pg='$prev'>«</a></li>";
			for($i=1; $i<=$pages; $i++)
			{
				$active = $i == $goto ? 'active' : '';
				$html .= "<li class='$active'><a data-pg='$i'>$i</a></li>";
			}
			$html .= "<li><a data-func='next' data-pg='$next'>»</a></li></ul></td></tr>";
        }
    
    echo $html . '<div class="modal   " id="previewinviteemail" tabindex="-1" 
    role="dialog" aria-labelledby="previewinviteemail" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Mail Preview</h4>
            </div>
            <div class="modal-body text-left mailpreview">
            <div class="row">
                <div class="col-md-4">
                    <label>Receipent:</label> 
                    <input class="form-control" readonly id="eptbreceipent"   />  
                    <label>Receipent Email:</label> 
                    <input class="form-control" readonly id="eptbreceipentemail"   />   
                </div> 
                <div class="col-md-8">  
                    <textarea name="mailpreview" id="mailpreview"></textarea>
                </div>
            </div>  
            </div>
            <div class="modal-footer ">
                <button class="btn btn-success" id="btnsendinvitemail">Send</button>
            </div> 
          </dov>
        </div>
    </div> '; 
} 


if(isset($_POST['showinvitemaimpreview']))
{
    $name = $_POST['name'];
    $email =$_POST['email']; 
    $ds = DIRECTORY_SEPARATOR; 
    $apppath = '';
    $path =  $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath    ;  
    if(  file_exists( $path . "templates/invitemail.txt" ) )
    {
        $mailbody = file_get_contents( $path . "templates/invitemail.txt" ) ;
        $mailbody = str_replace("{receipent}", $name , $mailbody ) ; 
        echo $mailbody;
    }
    else 
    {
        echo "0";
    }       
} 

if(isset($_POST['sendinvitemail']))
{  
    $name = $_POST['name'];
    $email =$_POST['email']; 
    $ds = DIRECTORY_SEPARATOR; 
    $apppath = '';
    $path =  $_SERVER['DOCUMENT_ROOT'] . $ds .$apppath   ;  
    if(  file_exists( $path . "templates/invitemail.txt" ) )
    {
        $mailbody = file_get_contents( $path . "templates/invitemail.txt" ) ;
        $mailbody = str_replace("{receipent}", $name , $mailbody ) ; 
        sendmail(   $email , 'bob@mycity.com',  "Want to grow your business? MyCity.com Referral Program can help you.",  $mailbody ,  $mailbody);
        echo 1;
    }
    else 
    {
        echo "0";
    }     
}

if(isset($_POST['editinvitemailpreview']))
{ 
    $ds = DIRECTORY_SEPARATOR; 
    $apppath = '';
    $path =  $_SERVER['DOCUMENT_ROOT'] . $ds . $apppath   ;  
    if(  file_exists( $path . "templates/invitemail.txt" ) )
    {
        $mailbody = file_get_contents( $path . "templates/invitemail.txt" ) ; 
        echo $mailbody;
    }
    else 
    {
        echo "0";
    }       
}

if(isset($_POST['saveinvitemail']))
{ 
    $content = $_POST['emailcontent']; 
    $ds = DIRECTORY_SEPARATOR; 
    $apppath = '';
    $path =  $_SERVER['DOCUMENT_ROOT'] . $ds .$apppath  . "templates/invitemail.txt" ;   
    $fp = fopen( $path ,"w" );
    fwrite($fp,$content);
    fclose($fp); 
}



if(isset($_POST['makeprofilepublic']))
{
        $profileurl = 'http://www.mycity.com/profile/?l=' . $user_id;
        $query = "update mc_user set  profileisvisible='1', 
        publicprofile='$profileurl' WHERE id='$user_id' ";
        $link->query($query);  

}




if(isset($_POST['claimprofile']))
{
    $pass = md5($_POST['pass']);
    $id =  $_POST['id'] ;
 
    $result = $link->query(" select * from mc_user where  id='$id'   " );
 
    if($result->num_rows > 0)
    { 
        $row = $result->fetch_array();
        if($row['signup_type'] != "10")
        {
            echo "You are not allowed to claim this profile! Please contact us for further assistance.";
            return;
        }
 
        if($row['user_status'] == "1")
        {
            echo "Your account is already active. Please login instead.";
            return;
        }
        
        if($row['user_status'] == "0" && $row['signup_type'] == "10")
        {
            $query = "update mc_user set user_pass='$pass', createdOn=NOW(), user_status='1' where id='$id' and signup_type='10'";
            $link->query($query); 

            $mailbody = "<p>Hi " . $row['username'] . ",<br/><br/>
            Thank you for signing up in MyCity.com by claiming your profile. 
            Please login to our website using your email and the password you provided in the profile claiming page. </p> 
            ";


            $body = "<!DOCTYPE html><html>
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
                                <td style='padding: 10px 10px 30px 10px;'> <div style='font-size: 16px;'> ".
                               "<div>" . $mailbody . 
                              "</div> 
                            <p>
                            <br/>
                            Sincerely,<br/>
                            Referrals@mycity.com<br/>
                            </p>
                            <p>
                            If you would like more information,<br/>
                            please email or call<br/>
                            310-736-5787<br/>
                            </p>
                            </div>
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
                                                Copyright &copy; 2017 | All Rights Reserved.
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
            </body></html>";

            
            sendemail(  $row['reg_email'] ,  'Thanks for claiming your profile' , $body, $body );
 
            echo "1";
            return;
        } 
    }
    else 
    {
        echo "No matching profile found!";
        return;
    } 
     

}
 

if(isset($_POST['minimalprofile']))
{
    $username =  $_POST['username'] ;
    $email =  $_POST['email'] ;
 
    $query = "insert into mc_user (username, user_email, user_pass,user_phone, user_status, signup_type) value
     ( '$username', '$email', '', '', '0','10') ";
 
            $link->query($query); 
            $insID = $link->insert_id; 

            $mailbody = "<p>Hi " . $username . ",<br/><br/>
            Your profile is ready to be claimed and start using our referral network. 
            We already have people to introduce you to who are highly rated and are interested in meeting you.
            </p>
            <p>
            Please claim your profile by visiting the link below and start a team or join an existing team in your area.</p> 
            <p><a target='_blank' href='http://mycity.com/claim-profile.php?l=$insID'>http://mycity.com/claim-profile.php?l=".  $insID . "</a></p>
            ";


            $body = "<!DOCTYPE html><html>
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
                                <td style='padding: 10px 10px 30px 10px;'> <div style='font-size: 16px;'> ".
                               "<div>" . $mailbody . 
                              "</div> 
                            <p>
                            <br/>
                            Sincerely,<br/>
                            Referrals@mycity.com<br/>
                            </p>
                            <p>
                            If you would like more information,<br/>
                            please email or call<br/>
                            310-736-5787<br/>
                            </p>
                            </div>
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
                                                Copyright &copy; 2017 | All Rights Reserved.
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
            </body></html>";
 
            sendemail(  $email ,  'You are invited to claim your profile in MyCity.com' , $body, $body ); 
            echo "1";
            return; 
}
 

$link->close();