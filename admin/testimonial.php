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
					<h4 style="margin-bottom: 65px; margin-top: 150px; font-weight: bold;">Testimonials </h4>
				</div> 
			</div>
		</div>
	</div>
	 <div class="container">
        <div class="row">

        <?php 
        $video_testimonials = getTestimonials($link);
        $rowindex=1;
        foreach ($video_testimonials as $item )
        {
        	echo '<div class="col-md-4"> 
        	<div class="embed-responsive embed-responsive-16by9 tmvideo">
				  <iframe class="embed-responsive-item" frameborder="0" 
				  src="' . $item['videolink'] . '" ></iframe> 
			</div> 
			<div class="quote">' . $item['summary'] . '</div> 
        	</div> '; 
          $rowindex++; 
        }

	 ?>
       
        </div>
      
    </div>
			
<?php include("footer.php") ?>
