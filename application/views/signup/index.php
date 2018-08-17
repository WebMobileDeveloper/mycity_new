<div class='' style='background-color:#fff; min-height: 490px;padding-top: 60px'>
	<div   class='container '>
		<div class='row marg4'>
			<div class="col-md-6 col-md-offset-3">
				
				
				<div class='profile-item'>
				<?php echo form_open(); ?>
				<h2>Signup</h2> 
				<div class='hr-sm'></div>
				<?php  
				if($msg != '')
				{
					echo "<div class='alertinfofix marg1'> " . $msg . "</div>";
					echo "<div class='marg2'></div>";
				} 
				else 
				{
					?> 
				<div class='row marg1'>
				<div class="col-xs-12 col-sm-12"> 
					<label class="custom-label">Name:</label> 
					<input type="text"   class="form-control" name="e_name" required=""  >
				</div>
				<div class="col-xs-12 col-sm-12"> 
					<label class="custom-label">Email:</label> 
					<input type="email"   class="form-control" name="e_email" required=""  >
				</div>
				<div class="col-xs-12 col-sm-12"> 
					<label class="custom-label">Password:</label> 
					<input type="password" class="form-control" name="e_password" required="">
				</div>
				</div>
				<div class='row marg1'>
				<div class="col-xs-12 col-sm-12"> 
					<input type='hidden' value='<?php echo  ( isset($partnerid) ? $partnerid : 0 ) ?>' name='e_partnerid' />
					
					   
					<input type='hidden' value='<?php echo $cname; ?>' name='e_knid' />
					<button type="submit" class="btn btn-primary" name="btn_save" value="save">Join mycity</button>
				</div>
				</div> 
				<?php } ?>
				
				<?php echo form_close(); ?>
				</div> 
			 
			</div><!-- row -->
		</div><!-- container -->
	</div>
</div>
 