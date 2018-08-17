<?php
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php'; 
?>
<div id="fb-root"></div>
	<div id="contact" class="about">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h4 style="margin-bottom: 65px; margin-top: 150px; font-weight: bold;">Testimonial Video</h4>
				</div> 
			</div>
		</div>
	</div>
	 <div class="container">
        <div class="row"> 
       		<div class='col-md-6 col-md-offset-3 text-center'>
				<div class='loading'>
					<p class='loadingtext'>
					 <img src='images/loading.gif' alt='Loading Video' />
					Redirecting to video</p>
				</div>
			</div>
        </div> 
    </div> 

<script>
	 
	var delay = 3600; 
	setTimeout(function(){ window.location = 'https://www.youtube.com/watch?v=<?php echo $_GET['id'] ; ?>'; }, delay);
	
	
</script>
<?php include("footer.php") ?>
