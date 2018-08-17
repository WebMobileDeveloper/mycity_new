<?php
include_once 'includes/functions.php';
session_start();
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
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>MyCity - Where business people come to network</title> 
    <link href="assets/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="assets/fa/css/font-awesome.min.css" rel="stylesheet">  
    
    <link href="assets/css/animate.min.css" rel="stylesheet"/>  
    <link href="assets/css/admin.css" rel="stylesheet"/>
	<link rel="stylesheet" href="assets/css/chosen.css">
	<link rel="stylesheet" href="assets/css/easy-autocomplete.min.css">


  </head>
