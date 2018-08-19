<?php
if(!isset($_SESSION))session_start();
include_once 'includes/db.php';
include_once 'includes/functions.php';
date_default_timezone_set('America/Los_Angeles');
$tagline = getPageDetails("tagline");



if( isset( $_COOKIE['_mcu'] ) )
{  
    $mcu =  json_decode( "[" .     $_COOKIE['_mcu']  . "]", true )  ;  
    
    
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
else
{ 
} 


if (isset($_SESSION['user_id']))
{
    header('location: dashboard.php');
}

$groups = getGroups($link);
$vocations = getVocations($link);


if(isset($_POST['btnlandingsignup']))
{
    $landingzip = $_POST['landingzip'];
    $landingcity = $_POST['landingcity']; 
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>MyCity - Where business people come to network</title> 
   
    <link rel="stylesheet" href="css/default.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="stylesheet" href="css/style_2.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/custom.css"/>
	<link rel="stylesheet" href="css/dropdown.css"/>
	<link rel="stylesheet" href="css/light.css"/>
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="css/chosen.css">
	<link rel="stylesheet" href="css/easy-autocomplete.min.css">
	<link href="css/bootstrap-tour.min.css" rel="stylesheet">
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    
	<script src="js/custom.js?v=1.449" type="text/javascript"></script> 
  </head>
