<?php 


if(isset($_GET['token']) && isset( $_GET['l'] ) && isset(  $_GET['hval'] ))
{
	$token = $_GET['token'];
	$tokenlength = $_GET['l'];
	$tlhash = $_GET['hval'];
	
	
	$id = substr($token, 0, $tokenlength);
	
	$hashid  = substr($token, $tokenlength, strlen($token)-1  );
	 
	 
	 if(md5($id) !=  $hashid )
	 {
		header('location: index.php'); 
	 }
	 
}
else
{
  header('location: index.php'); 
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
<article>
  
  <div class="content next-sections">
  <h1>Start your profile on mycity.com</h1>
  <br/>
    <div class='msg'></div> 
  <p class='into-text'>Participate in a group in your area. 
                            Create relationships and refer business based on our rating system to each other with our algorithm. <br/>
                            People are calling us the match.com in the business world. </p>
                   
                            <p class='text-md'>We'll help you build your team!</p> 
  <p class='text-md'>
  <button class='btn btn-white btn-primary btn-lg btnclaim' data-ci='<?php echo $id; ?>'>Claim Now</button>
  </p> 
  </div><div class="launchpad"></div>
</article>

<section id="callin-section" >
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-xs-12 col-sm-12 text-center">
                    <h3 class='text-lg'>
                        Signup to start a team in your city.
                    </h3>
                </div>
                <div class="col-md-4 col-xs-12 col-sm-12 text-center">
                    <a href='http://mycity.com#signup' class='btn btn-white'>Signup</a>
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
  </body>
  
<script>
   
$(document).on('click', '.btnclaim', function()
{
	var i = $(this).attr('data-ci');
	var aurl = "//" + window.location.hostname + "/api/api.php/"; 
	 
    $.ajax({
        type: 'post',
        url: aurl + 'member/claimprofile/',
        data: {  i : i },
        success: function(data) 
		{
			data = $.parseJSON(data); 
			$('.msg').html('<p class="alert alert-info">' + data.errmsg   +"</p>");
        },
        error: function( ) {
           
           $('.msg').html('<p class="alert alert-info">' + data.errmsg   +"</p>");
        }
    });
	
})


</script>
  
</html>
