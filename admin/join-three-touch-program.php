<?php
ob_start();
include_once("template/head.php"); 
include_once 'includes/db.php';
global $link;

$userid = $_SESSION['user_id'];

$participantrs = $link->query("SELECT * FROM  mc_program_client where client_id='$userid'");
 
 
?>
 
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
			if (!isset($_SESSION['user_id']))
			{
				?>
				
			<div class=" col-md-12 col-md-6 col-md-offset-3 text-center" style='height: 70vh; padding-top: 60px;'>    
			  <h1>Member Only Page!</h1>
			 <br/>
			
			 <h3>You need to be a registered member of MyCity and login to view this page!</h3> <br/>
				<a href='login.php' class='btn btn-blue'>Go to login page.</a>
			</div>
		
		
				<?php 
			}
			else 
			{
				if($participantrs->num_rows  > 0)
				{
					?> 
			<div class=" col-md-12 col-md-8 col-md-offset-2 text-center" style='height: 70vh; padding-top: 60px;'>    
			  <h1>You have already join in the program!</h1>
			 <br/> 
			 <h3>Please go to dashboard and manage your 3 touch relationship!</h3> <br/>
				<a href='dashboard.php' class='btn btn-blue'>Go to Dashboard.</a>
			</div>
		
		
				<?php 
					 
				}
				else
				{
					
				?>
				<div class=" col-xs-12 col-sm-12" style='height: 70vh; padding-top: 60px;'>    
					  <h1>Join MyCity Three Touch Program</h1>
					  <p class='into-text'>Convert connection into a relationship over 30 day period with our unique 3 Touch Program.</p>
					  <p class='text-md'>Turn prospects into Relationships!</p>
					   
						<button type='button'  class='btn btn-blue join3tprogram'>Join Now</button>
				</div>
				<?php
				
				}
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
	 <link rel="stylesheet" href="css/style_landing.css"/> 
<?php 

include_once("template/footerjs.php");

?>

 