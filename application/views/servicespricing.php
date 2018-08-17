<div   class='container-fluid'>
	<div class='row '>
	  <div id="contact" class="about" style="pointer-events: auto;">
			<div class="container">
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<h4 style="margin-bottom: 65px;  font-weight: bold;">Packages</h4>
					</div> 
				</div>
			</div>
		</div>
		</div>
</div>
<div id='contact' class='packages'>	
<div  class='container'>	
<div class='row '>
	<?php
	
	if ($packages['packages']->num_rows() > 0)
		{
			foreach($packages['packages']->result() as $userpkg )
			{
				?>
					<div class="col-md-4 col-sm-4 col-xs-12">
                        <div class="box">
                            <h4 class="bg"><?php echo $userpkg->package_title ; ?></h4>
                            <h4><span>$<?php echo (($userpkg->package_price ==0.00)? "Free" : $userpkg->package_price ); ?>/mo</span></h4>
							<h3><?php echo $userpkg->package_limit ; ?> months minimum</h3>
                            <ul>
							 <?php  
								foreach($packages['pkg_services']->result() as $pkgservice )
								{
										if($pkgservice->pkg_id == $userpkg->id)
										{
										?>
										<li><?php echo $pkgservice->services ; ?></li>
								<?php }
								}								?>
                            </ul>
							<div class='btn-area'  >
                            <span style="font-size: 25px; line-height: 1.7555;"><i class="fa fa-phone" ></i> Call 310 736-5787</span>
                            <a href="http://www.edgeupnetworks.com/get-started" target="_blank" class="bg">JOIN</a>
							</div>
                        </div>
                    </div>
					<?php } 
					
					} ?>
					
	 
		
	</div><!-- row -->	
	</div>
</div><!-- container -->

 