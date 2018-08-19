<?php
if(!isset($_SESSION))session_start();
date_default_timezone_set('America/Los_Angeles');
include_once '../includes/db.php';
 
    //~4mcx9c3b1830513cc3b11138fc4b76635d32e692
    //~2mcx1f0e3dad9990834195f7439f8ffabdffc4
	
	$sid = $_GET['cts'];  //connection token
	$rid = $_GET['ctr'];  
	
	
    $id = $_GET['c']; 
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
         $show = false;

    } 
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="MyCity"/> 
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $username . " - MyCity Registered Member" ;?></title>
    <link rel="stylesheet" href="../css/default.css"/>
    <link rel="stylesheet" href="../css/style.css"/> 
    <link rel="stylesheet" href="../css/font-awesome.min.css"> 
    <link rel="stylesheet" href="../css/bootstrap.min.css"/>    
    <script src="../js/jquery.js" type="text/javascript"></script>
    <script src="../js/bootstrap.min.js" type="text/javascript"></script>
     
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
<div class="modal fade bs-example-modal-sm" id="signin" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <h2 class="title">Sign in</h2>
                <div class="form-group">
                    <input id="login_username" name="username" class="form-control" placeholder="Your email">
                </div>
                <div class="form-group">
                    <input id="login_password" type="password" class="form-control" name="password" placeholder="Password">
                </div>
                <button type="submit" id="sign_in_button" class="flatbutton">Let’s go</button>
                <!--<p class="forgot_password"><a href="javascript:void(0)">Forgot your password?</a></p>
                <p class="strikey">or</p>
                <button id="log_in_facebook" class="facebook_button flatbutton">Sign in</button>-->
				 <p class="forgot_password"><span data-toggle="modal" data-target="#forgetPW" style="cursor:pointer;">Forgot your password?</span></p>
            </div>
        </div>
    </div>
</div>


<div class="modal fade bs-example-modal-sm" id="forgetPW" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <h2 class="title">Forgot Password</h2>
                <div class="form-group">
                    <input id="forgPWEmail" type="email" class="form-control" name="forgPWEmail" placeholder="Type your email">
                </div>
                <button type="button" id="resPWBtn" class="flatbutton">Reset Password</button>
            </div>
        </div>
    </div>
</div>

<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-xs-4"> 
				<?php
					if(!isset($_SESSION['user_id'])) {
						echo '<a href="http://mycity.com"><img src="../images/logo.png" alt="logo"></a>';
					} else {
						echo '<a href="http://mycity.com/dashboard.php"><img src="../images/logo.png" alt="logo"></a>';
					}
				?> 
				<a id="play-video" href='#watch-mycity-video' 
				class='noborder watchvideo' data-toggle="modal" data-target="#videomodal" 
				data-video='zUzISiLmqMw' class='play-video-home'>
				<img src='../images/bob-profile.png' class='profile'  />
				</a>
				   
			</div>
			<div class="col-xs-6 text-center">
				<h5 class="siteTagline"><?php echo $tagline[0]["page_content"] ?></h5>
			</div>
            <div class="col-xs-2 text-right">
                <?php
                if(!isset($_SESSION['user_id'])){
                    echo "<ul><li><a data-toggle=\"modal\" data-target=\"#signin\">Sign in </a></li></ul>";
                }else{
                    echo "<ul>
								<li><a href='../dashboard.php'><i style='font-size: 36px;' class='fa fa-home' title='Home'></i></a></li>
								<li><a href='../message.php'><i style='font-size: 36px;' class='fa fa-envelope' title='Messages'></i></a></li>
								<li><a href='../logout.php'><i style='font-size: 36px;' class='fa fa-sign-out' title='Logout'></i></a></li>
							</ul>";
                }
                ?>
            </div>
        </div>
    </div>
</section> 
<div id="fb-root"></div> 
<?php  

if($show)
{
    ?> 
	<div id="profile" class="profile">
		<div class="container">
			<div class="row">
				<div class="col-md-2 col-sm-12 col-xs-12 text-center"> 
					<img src="<?php echo $user_picture ;?>" alt="" class="img-rounded"   height="120" width="120" />
				</div> 
				<div class="col-md-10 col-sm-12 col-xs-12 text-left"> 
					<h1 ><?php echo $username;?></h1> 
					<br/> 
					<?php  if($sid !='' && $rid !=''): ?>
					<p>
					 <button class="btn btn-orange btn-lg btnacceptconnection"  data-rid='<?php echo $rid;?>' data-sid='<?php echo $sid;?>' >Click to Connect</button> 
					</p>
<?php endif;?>					
				 </div>
			</div>
		</div>
	</div>  
	<div class="container">
        <div class="row"> 
        <div class="col-md-8 col-md-offset-2"> 
        <div class="row"> 
			 <div class="col-md-6">
			 <div class='profile-summary'>
                <h2>Contact Information</h2>
                
                <?php if(isset(  $_SESSION['user_id'] )) 
                {
                    ?>
                    <p class='medium'> 
					<strong>Email: <a href='mailto:<?php echo $user_email; ?>'><?php echo $user_email; ?></a></p>
                <p class='medium'><strong>Phone: <?php echo $user_phone; ?></p>
                <br/>
				    
                <?php 

                }else 
                {
                    ?>
                    <p class='medium'> 
					<strong>Email: <span class='lock'><span class='fa fa-lock'></span> Click Here</span></strong></p>
                <p class='medium'><strong>Phone: <span class='lock'><span class='fa fa-lock'></span> Click Here</span></strong></p>  
                    <?php  
                }
                ?> 
             </div> 
          </div> 
        <div class="col-md-6">
            <div class='profile-summary'>
                <h2>Contact Address:</h2>
                <p class='medium'><?php echo  $city . " - " .  $zip ; ?></p>
                <p class='medium'><strong><?php echo  $country ; ?></p>   
    </div> 
    </div> 
         <div class="col-md-12">
            <div class='profile-summary'>
                 
                <h2>External Links:</h2>    

                <?php
				 if (filter_var($current_company, FILTER_VALIDATE_URL) === false) {
					?>
					<p class='medium'><strong>Current Company: </strong><?php echo ($current_company !='' ? $current_company: "Not Specified"); ?></p>
					<?php
				 } else {
				 ?>
                 <a class="icon-link" title="Company Website" href="<?php echo $current_company; ?>"><i class="fa fa-briefcase dark" aria-hidden="true"></i></a> 
				 
                 <?php }  
                 
				 if (filter_var($linkedin_profile, FILTER_VALIDATE_URL) === false) {
					?>
					<p class='medium'><strong>Linkedin Profile: </strong><?php echo ( $linkedin_profile !='' ? $linkedin_profile : "Not Specified");  ?></p>
					<?php
				 } else {
				 ?>
				 <a class="icon-link" title="Linkedin Profile" href="<?php echo $linkedin_profile; ?>"><i class="fa fa-linkedin-square dark" aria-hidden="true"></i></a> 
				 
				 <?php } ?>

                 <h2>My Bio:</h2>
                <p class='medium'>
                <?php echo ($about_your_self ==''? 'Not Specified':  $about_your_self) ; ?></p>

				<h2>Target Clients:</h2>
                <p class='medium'>
                <?php echo ($target_clients ==''? 'Not Specified':  $target_clients) ; ?></p>
                <h2>Target Referral Partners:</h2> 
				 <p class='medium'>
				 <?php echo ($target_referral_partners  ==''? 'Not Specified':  $target_referral_partners  ) ; ?> 
                 <h2>Target Referral Partners:</h2> 
				 <p class='medium'>
				 <?php echo ($voc ==''? 'Not Specified':  $voc  ) ; ?> 
			 </p>

             </div>	

<?php  if($sid !='' && $rid !=''): ?>
<p>
	 <button  data-rid='<?php echo $rid;?>' data-sid='<?php echo $sid;?>' class="btn btn-orange btn-lg btnacceptconnection"  >Click to Connect</button> 
					</p> 
					<?php endif;  ?>

					
         </div>			
  
            </div>				
			</div>  
        </div> 
</div>   
<?php 
}
else 
{
   ?>
   <div id="profile" class="profile">
		<div class="container">
			<div class="row"> 
				<div class="col-md-12 col-sm-12 col-xs-12 text-center"> 
					<h1 >No Matching Profile Found!</h1> 
				 </div>
			</div>
		</div>
	</div> 
    <div class="container">
        <div class="row"> 
        <div class="col-md-8 col-md-offset-2"> 
            <p class='medium'> There is no member whose profile match the link you have visited.
             Please contact administrator or ask your contact who shared this profile!</p>
        </div> 
        </div>
    </div> 
   <?php 
   }
?>
<section class="footer">
        <div class="container">
            <div class="row">
                <div class=" col-xs-12 col-sm-4">
                    <img src="../images/logo.png" alt="logo">
                </div>
                <div class=" col-xs-12 col-sm-8 text-right">
                    <ul>
						<?php
							if(!isset($_SESSION['user_id'])) {
								echo '<li><a href="index.php">Home </a></li>';
							} else {
								echo '<li><a href="dashboard.php">Home </a></li>';
							}
						?>
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
   <div class="modal fade" id="modallock" tabindex="-1" 
    role="dialog" aria-labelledby="modallock" style='top: 120px' >
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Signup to View Complete Profile</h4>
            </div>
            <div class="modal-body text-center">
              <a class='btn btn-orange btn-lg ' href='http://www.mycity.com'>GET STARTED</a> 
            </div> 
          </dov>
        </div>
</div>
</div>

<div class="modalbl modal fade" id="constate" tabindex="-1" 
    role="dialog" aria-labelledby="constate" style='top: 120px' >
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header  "><i class="fa fa-warning yellow"></i> Connection Status</div>
            <div class="modal-body text-center">
                <p class="txtbg" id='cstate'>Search for potential clients or referral partners, rated by other members.<br> You must be a member to search.</p> 
			   
            </div> 
          </div>
        </div>
</div>


    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.min.js"></script> 
    <script src="../js/main.min.js?v=1.<?php echo mt_rand(1,1000) ?>" type="text/javascript"></script> 
     <script>
	 
	$(document).on('click', '.btnacceptconnection', function()
{
	var sid= $(this).attr('data-sid');
	var rid= $(this).attr('data-rid');
	 
	 
    $.ajax({
        type: 'post',
        url: "//" + window.location.hostname + "/api/api.php/member/getconnect/",
        data: { sid : sid, rid:rid },
        success: function(data) 
		{
			data = $.parseJSON(data);
			 
			$('#cstate').html(data.errmsg);
			$('#constate').modal('show');
			 
        }
    });
}) 
	
	</script>
</body>
</html>
