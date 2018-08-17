<div   class='container '>
	<div class='row '>
  <div id="contact" class="about" style="pointer-events: auto;">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h4 style="margin-bottom: 65px;  font-weight: bold;">Testimonials</h4>
				</div> 
			</div>
		</div>
	</div>
    
    <?php 
        
        $rowindex=1;
        foreach ($testimonials->result() as $item )
        {
        	echo '<div class="col-md-4 marg4"> 
        	<div class="embed-responsive embed-responsive-16by9 tmvideo">
				  <iframe class="embed-responsive-item" frameborder="0" 
				  src="' . $item->videolink . '" ></iframe> 
			</div> 
			<div class="quote">' . $item->summary  . '</div> 
        	</div> '; 
          $rowindex++; 
        } 
		?>
		
	</div><!-- row -->	
</div><!-- container -->

 