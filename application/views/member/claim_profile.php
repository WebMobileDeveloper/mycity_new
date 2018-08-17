<div class='' style='background-color:#fff; min-height: 490px;padding-top: 60px'>
	<div   class='container '>
		<div class='row marg4'>
			<div class="col-md-10 col-md-offset-1">
				
				
				<div class='profile-item'>
				<?php echo form_open(); ?>
				<h2>Claim Your Profile and Connect to Other Professionals</h2> 
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
				<div class="col-xs-12 col-sm-6  "> 
					<label class="custom-label">Email:</label> 
					<input type="email" readonly class="form-control" name="e_email" required="" 
					value='<?php echo $cmail?>'>
				</div>
				<div class="col-xs-12 col-sm-6  "> 
					<label class="custom-label">Password:</label> 
					<input type="password" class="form-control" name="e_password" required="">
				</div>
				</div>
				<div class='row marg1'>
				<div class="col-xs-12 col-sm-6  "> 
					<input type='hidden' value='<?php echo $cname; ?>' name='e_name' />
					<button type="submit" class="btn btn-primary" name="btn_save" value="save">Save &amp; Login</button>
				</div>
				</div> 
				<?php } ?>
				
				<?php echo form_close(); ?>
				</div> 
				
			</div><!-- row -->
		</div><!-- container -->
	</div>
</div>

 