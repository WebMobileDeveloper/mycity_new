<?php

include_once 'includes/db.php';
global $link;

$code = $_GET['c'];
$groups = $link->query("SELECT * FROM  groups order by grp_name "); 
$targetvoc ='';

$msg ='';
if($_POST['btn_signup'] == 'signup')
{
	$hashid = $_POST['hashid'];
	$password = md5( $_POST['password'] ); 
	 
	$invknow = $link->query("select a.*, b.answer from user_people as a inner join user_answers as b on a.id=b.user_id where a.id = 
	( select know_id from  mc_invite_know_log where hash_id='$code' ) "); 
	if($invknow->num_rows > 0)
	{
		$knowrow = $invknow->fetch_array();
		$client_name = $knowrow['client_name']; 
		$client_profession = $knowrow['client_profession']; 
		$client_phone = $knowrow['client_phone']; 
		$client_email = $knowrow['client_email']; 
		$client_location = $knowrow['client_location']; 
		$client_zip = $knowrow['client_zip'];  
		$targetvocation  = $knowrow['answer'];  
		  
		$memberrs = $link->query("select count(*) as cnt from  mc_user where user_email = '$client_email' "); 
		$memcnt_row = $memberrs->fetch_array();
		if( $memcnt_row['cnt'] == 0 )
		{
			$link->query("insert into mc_user 
			(user_email, user_pass,  username,   user_phone, tags  ) values 
			('$client_email', '$password', '$client_name','$client_phone', 'Know Signup' )");
			$insID = $link->insert_id;
			$link->query("insert into user_details 
			(user_id, zip,  city,   vocations, target_clients  ) values 
			('$insID', '$client_zip', '$client_location','$client_profession', '$targetvocation' )"); 
			
			$msg = 'Signup Complete!';
		}
		else 
		{
			$msg = "Your account already exists. <br/>Please <a href='https://mycity.com/login'>login</a> instead!";
		} 
	} 	 
}
 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="MyCity"/> 
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title></title>
    <link rel="stylesheet" href="css/default.css"/> 
    <link rel="stylesheet" href="css/style_landing.css"/> 
    <link rel="stylesheet" href="css/font-awesome.min.css"> 
    <link rel="stylesheet" href="css/bootstrap.min.css"/> 
	<link rel="stylesheet" href="css/easy-autocomplete.min.css">
	<link href="css/bootstrap-tour.min.css" rel="stylesheet">
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script> 
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga'); 
        ga('create', 'UA-26668236-1', 'auto');
        ga('send', 'pageview');
    </script>

</head>
<body class="no-padd" style="padding: 0 !important; "> 
<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-xs-4"> 
				<?php
					if(!isset($_SESSION['user_id'])) {
						echo '<a href="index.php"><img src="images/logo.png" alt="logo"></a>';
					} else {
						echo '<a href="dashboard.php"><img src="images/logo.png" alt="logo"></a>';
					}
                ?> 
                <a id="play-video" href='#watch-mycity-video' 
				class='noborder watchvideo' data-toggle="modal" data-target="#videomodal" 
				data-video='zUzISiLmqMw' class='play-video-home'>
				<img src='images/bob-profile.png' class='profile'  />
				</a>  
            </div>
			<div class="col-xs-6 text-center">
                <h5 class="siteTagline"><?php echo $tagline[0]["page_content"] ?></h5>
			</div>
            <div class="col-xs-2 text-right"> 
            </div>
        </div>
    </div>
</section>
 
<section >
        <div class="container"> 
            <div class="row">
			<?php 
			
			$invitedknow = $link->query("select * from mc_invite_know_log where hash_id='$code' "); 
			if($invitedknow->num_rows > 0)
			{
				$row = $invitedknow->fetch_array();
				$knowid = $row['know_id']; 
				$useranswers = $link->query("select * from user_answers where user_id='$knowid' "); 
				if($useranswers->num_rows > 0)
				{
					$targetvocs = $useranswers->fetch_array();
					$targetvoc = $targetvocs['answer'];
					 
				}	

				if($msg =='')
				{					
				?>
				<div class=" col-xs-12 col-sm-12 col-md-6 col-md-offset-3 text-center" style='height: 80vh; padding-top: 60px;'>    
				<h1>Join MyCity</h1>
				<p class='text-md'>Turn prospects into Relationships!</p>
				<?php 
				
				if($targetvoc !='')
				{
					echo "<p class='text-md'>Signup now and start connection with people whose vocations are $targetvoc.</p>"; 
				}
				
				?>
				<br/>
				<form method='post' >
				<input type='password' name='password' placeholder='Your Password' class='form-control'></input>
				<br/>
				<input type='hidden'  value='<?php echo $row['hash_id'] ; ?>' name='hashid''></input>
				<button type='submit' name='btn_signup' value='signup' class='btn btn-blue'>Join Now</button>
				</form>
				</div> 
				<? 
				}
				else
				{
					?>
					<div class=" col-xs-12 col-sm-12 col-md-6 col-md-offset-3 text-center" style='height: 80vh; padding-top: 60px;'>    
				<h1>Account exists!</h1>
				<?php 
				echo "<p class='text-md'>" . $msg . "</p>";
					
				?>
				</div>
					
					<?php 
					 
				}
			}
			else 
			{
				?> 
				<div class=" col-xs-12 col-sm-12 text-center" style='height: 80vh; padding-top: 60px;'>    
				  <h1>Invalid Page Access</h1>
				  <p class='into-text'>Seems like you have reached the page wrongly.</p> 
					<p><br/><a href='' class='btn btn-success'>Back to Home</a></p>
				</div>
			
				<?php 
			}  
			?>
			    
 </div> 
  </div> 
 
        </div>
    </section> 
    <section class="footer">
        <div class="container">
            <div class="row">
                 <div class=" col-xs-12 col-sm-4">
                    <img src="images/logo.png" alt="logo">
                </div>
                <div class=" col-xs-12 col-sm-8 text-right">
                    <ul>
						 <li><a href="index.php">Home </a></li> 
                        <li>|</li>
                        <li><a href="about.php">About</a></li>
                        <li>|</li>
						<li><a href="blog.php">Blog</a></li>
                        <li>|</li>
                        <li><a href="http://edgeupnetworks.com/">Find Partners </a></li>
                        <li>|</li>
                        <li><a href="packages.php">Services & Pricing </a></li>
                        <li>|</li>
                        <li><a href="testimonial.php"> Testimonials </a></li>
                        <li>|</li>
                        <li><a href="contact.php">Contact us</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>