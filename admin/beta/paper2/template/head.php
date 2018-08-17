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
        if($logintoken != '')
        {
            $loginlogrs = $link->query("select * from mc_user where id = (select userid from  mc_login_log where token='$logintoken') ");
            if($loginlogrs->num_rows == 1 )
            {
                $loginlogrow = $loginlogrs->fetch_array();
                
                $_SESSION['user_id'] =  $loginlogrow['id']; 
                $_SESSION['username'] =  $loginlogrow['username']; 
                $_SESSION['user_email'] =  $loginlogrow['user_email']; ;
                $_SESSION['user_phone'] =  $loginlogrow['user_phone']; 
                $_SESSION['user_role'] =  $loginlogrow['user_role']; 
                $_SESSION['user_pic'] =     ("images/".((!empty( $loginlogrow['image'] ))?  $loginlogrow['image'] :"no-photo.png"));
    
            }
        }   
    } 
?>


<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
	<link rel="icon" type="image/png" sizes="96x96" href="assets/img/favicon.png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    
	<title>Turn your Social Media into Relationships & Referrals - MyCity</title>
    <meta name="author" content="MyCity"/> 
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />  
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" /> 
    <link href="assets/css/animate.min.css" rel="stylesheet"/>  
    <link href="assets/css/style.css" rel="stylesheet"/>   
    <link href="assets/fa/css/font-awesome.min.css" rel="stylesheet">   
	<link rel="stylesheet" href="css/chosen.css">    
	
    <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-26668236-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-26668236-1');
</script>

</head>

