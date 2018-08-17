<?php

include_once 'includes/db.php';
global $link;
$groups = $link->query("SELECT * FROM  groups order by grp_name ");


$id = $_GET['l']; 

 $q = $link->query("select * from mc_user  where id = '$id'");
 
 $linkedin_profile = '';
 $current_company = '';
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

 //loading user account info 
 if ($q->num_rows > 0)
 {
     $html = "";
     $row = $q->fetch_array();
     $username = $row['username'];
     $user_email = $row['user_email'];
     $user_phone = $row['user_phone'];  
     $user_pkg = $row['user_pkg'];
     $user_picture = ((file_exists("../images/" .$row['image'] ))? "../images/".  $row['image'] :"../images/no-photo.png");  
     //loading account profile
     $q = $link->query("select * from user_details  where user_id = '$id'");



     if ($q->num_rows > 0)
     {
         $row = $q->fetch_array();
         $linkedin_profile = $row['linkedin_profile'];
         $current_company = $row['current_company'];
         $country = $row['country'];
         $city = $row['city'];
         $zip = $row['zip']; 
         $target_clients = $row['target_clients'];
         $target_referral_partners = $row['target_referral_partners'];
         $voc = $row['vocations'];
         $about_your_self = $row['about_your_self'];
         $upd_public_private = $row['upd_public_private'];
         $upd_reminder_email = $row['upd_reminder_email'];
         $profileincomplete = '0';
         
         $grp = $row['groups'];
         $groupwhere .= "select group_concat(grp_name) as goupnames from groups where id in (" .$grp . ")"; 
         $groups = $link->query($groupwhere  );   
         if($groups->num_rows  > 0)
         {
             $grouplist = $groups->fetch_array()['goupnames'];
         }
         else 
         {
             $grouplist = 'Not Available';
         } 

         $show = true;
     }
     else
     {
         $show = false;
     }

 }
 else 
 {
    header("Location: http://mycity.com");
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


    <link rel="stylesheet" href="css/claim_profile.css"/> 
    
    <script src="js/custom.js?r=1.<?php echo mt_rand(1,1000) ?>" type="text/javascript"></script>
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

<div id="profile" class="profile">
		<div class="container">
			<div class="row"> 
				<div class="col-md-12 text-center"> 
					<h1 class='large'>Claim your profile and meet people</h1> 
				 </div>
			</div>
		</div>
	</div> 
<article>

    <div class="container">   
        <div class='col-md-8 col-md-offset-2'> 
        <h2></h2> 
        <p class='large'>We already have referral partners or potential clients for you.  
        As we say it there is life after linkedin. Linkedin gives you the opportunity to meet more people. 
        Join us and we will introduce you to centers of influence and potential clients in your area.</p>
        </div>
      <div class="col-md-4 col-md-offset-2">
        <div class='profile-summary'>
            <h2>Contact Information</h2>
            <p class='large'><strong>Name:</strong> <?php echo $username;?></p>
                <p class='large'>
                <strong>Email:</strong> <a href='mailto:<?php echo $user_email; ?>'><?php echo $user_email; ?></a></p>
                <p class='large'><strong>Phone: </strong><?php echo $user_phone; ?></p> 
             </div> 
          </div> 
        <div class="col-md-4">
            <div class='profile-summary'>
                <h2>Contact Address:</h2>
                <p class='large'><strong>City: </strong><?php echo  $city . " - " .  $zip ; ?></p>
                <p class='large'><strong>Country: </strong><?php echo  $country ; ?></p>   
        </div> 
       </div>  
    </div>
</article>
  
<section id="callin-section" >
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3 class='text-large'>
                        To claim your profile, send this form!
                    </h3>
                </div>
                  
                        <div class="col-md-3 col-md-offset-2">
                        <input class='form-control form-control2' type='password' placeholder='Specify Password' id='password'  name='password'/>
                        </div>
                        <div class="col-md-3">
                            <input class='form-control form-control2' type='password' placeholder='Confirm Password'  id='cpassword'  name='cpassword'/>
                        </div>
                    <div class="col-md-2 text-left">
                    <input type='button' data-claim='<?php echo $id;?>' name='btnclaimnow' id='btnclaimnow' class='btn btn-blue' value='Claim Now'></input>
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
    <link rel="stylesheet" href="css/tooltipster.bundle.min.css">
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.js"></script>
    <script src="js/scroll.js"></script>
    <script src="js/jquery.scrollme.min.js"></script>
	<script src="js/core.js"></script>
	<script src="js/dropdown.js"></script>
	<script src="js/myscript.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script src="ckeditor/ckeditor.js"></script>	   
	<script src="js/chosen.jquery.min.js" type="text/javascript"></script>
	<script src="js/tooltipster.bundle.min.js" type="text/javascript"></script>
	<script src="js/jquery.easy-autocomplete.min.js"></script>
	<script src="js/dropzone.js"></script>

	<script src="js/bootstrap-tour.min.js"></script>
    </body>
</html>


