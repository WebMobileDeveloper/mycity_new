<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?> 
<div class='col-md-9'>
	 <div class='profile-item'> 
				<h2>User Preferences</h2>
				<div class='hr-sm'></div>
				<div class='row marg4'>
				<?php echo form_open(); ?>
				<div class='col-xs-7'>
					<label>Who can see your connections:</label>
				</div>
				<div class="col-xs-4">
					<div class='form-group'  >  
						<select class='form-control sm_control' id='whocanview' name='config_privacy'> 
							<option value='10'>Your connections</option> 
							<option value='0'>Only You</option>
						</select>
					</div>
				</div> 
				<div class="col-xs-1"> 
					<button type='submit' name='btn_save_privacy' value='save_privacy' class='btn btn-primary updateprivacy'>Save</button> 
				</div>
				<?php echo form_close(); ?>
				 </div> 
	  </div> 
	</div>  
	</div><!-- row -->
</div><!-- container -->