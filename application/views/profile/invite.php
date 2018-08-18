<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	$targetvoc =''; 
?> 
  
<div class="container">
 <div class='row'>
 <div class='col-md-8 col-md-offset-2'>
	
		<?php 
			
			if($invitelog->num_rows() > 0)
			{
				$row = $invitelog->row_array(); 
				$hash_id = $row['hash_id']; 
				 
				if($answer_rs->num_rows() > 0)
				{
					$targetvocs = $answer_rs->row_array();
					$targetvoc = $targetvocs['answer']; 
				} 
				
				if($msg =='')
				{ 
				?>
				
				<div class=" col-xs-12 col-sm-12 col-md-6 col-md-offset-3 text-center" style='min-height: 80vh; padding-top: 60px; padding-bottom: 40px;'>    
				<h1>Join MyCity</h1>
				<p class='text-md'>Turn prospects into Relationships!</p>
				<?php 
				
				if($targetvoc !='')
				{
					echo "<p class='text-md'>Signup now and start connection with people whose vocations are $targetvoc.</p>"; 
				}
				
				?>
				<br/>
				<?php echo form_open(); ?>
				<input type='password' name='password' placeholder='Your Password' class='form-control'></input>
				<br/>
				<input type='hidden'  value='<?php echo $hash_id ; ?>' name='hashid''></input>
				<button type='submit' name='btn_signup' value='signup' class='btn btn-primary'>Join Now</button>
				<?php echo form_close(); ?>
				</div> 
				
				<?php } else { ?>
				<div class=" col-xs-12 col-sm-12 col-md-6 col-md-offset-3 text-center" style='height: 80vh; padding-top: 60px;'>    
				<h1>MyCity Invite Account Status!</h1>
				<?php 
				echo "<p class='text-md'>" . $msg . "</p>"; 	
				?>
				</div>
				<?php  } ?> 
	<?php  } 
	else 
			{
				?> 
				<div class=" col-xs-12 col-sm-12 text-center" style='height: 80vh; padding-top: 60px;'>    
				  <h1>Invalid Page Access</h1>
				  <p class='into-text'>Seems like you have reached the page wrongly.</p> 
					<p><br/><a href='<?php echo BASE_URL;?>' class='btn btn-success'>Back to Home</a></p>
				</div>
			
				<?php 
			} 
			
			?> 
 </div>
	</div><!-- row -->
</div><!-- container -->

  
	 
 
	

	
	