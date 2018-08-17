<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" /> 
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<title><?php if($title !='') echo $title; else echo "Turn your Social Media into Relationships & Referrals" ; ?> - MyCity</title>
    <meta name="author" content="MyCity"/>  
	
	
	<?php if(isset($noindex) && $noindex != '')
	{
		?>
		 <META NAME="robots" CONTENT="noindex">
		<?php
	} 
	?>
	
	<?php if(isset($keyword) && $keyword != '')
	{
		?>
		<meta name="keywords" content="<?php echo $keyword; ?>"/>
		<?php
	} 
	?>
	
	<?php if(isset($meta_desc) && $meta_desc !='')
	{
		?>
		<meta name="description" content="<?php echo $meta_desc; ?>"/>
		<?php
	}
	else 
	{
		?>
		<meta name="description" content="A proactive system to grow relationships, grow referral partners, grow sales."/>
		<?php
	}
	?>	
	
    <link href="<?php echo $base.$asset ;?>css/bootstrap.min.css" rel="stylesheet" /> 
	<link rel="stylesheet" href="<?php echo $base.$asset ;?>css/jquery-ui.min.css">
    <link href="<?php echo $base.$asset ;?>css/animate.min.css" rel="stylesheet"/>  
    <link href="<?php echo $base.$asset ;?>css/style.css?v=<?php echo mt_rand(1,100000);?>" rel="stylesheet"/>   
    <link href="<?php echo $base.$asset ;?>fa/css/font-awesome.min.css" rel="stylesheet">   
	<link rel="stylesheet" href="<?php echo $base.$asset ;?>css/chosen.css">    
	<link rel="stylesheet" href="<?php echo $base.$asset ;?>css/easy-autocomplete.min.css"> 
	<link rel="stylesheet" href="<?php echo $base.$asset ;?>dt/datatables.min.css"> 
	<link href="<?php echo $base.$asset ;?>css/index.css" rel="stylesheet"/>    
	<link rel="stylesheet" href="<?php echo $base.$asset ;?>css/tooltipster.bundle.min.css"> 
	
	<?php
	
	$pos = strpos(current_url() , "mycity.com");
	if ($pos !== false)
	{
		?>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-26668236-1"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date()); 
		  gtag('config', 'UA-26668236-1');
		</script>  
		<?php 
	}
	?>
	
	
	
	 
</head>
<body >