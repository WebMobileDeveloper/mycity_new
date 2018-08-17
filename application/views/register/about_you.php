<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>  
	 <section id="sec_three" class="next-sections form-large" style="display: block; pointer-events: auto;">
        <div class="container">
            <div class="row sec_two">
                <div class="col-md-12">
                    <h1 class="description">Tell us about yourself</h1>
                    <p class="description">Create Your Account. You know the drill...</p>
					<?php  echo '<div class="col-md-12"><p class="description">' .  $error_msg . '</p></div>';  ?>
                </div>
                 
	 <?php 
				   $attributes = array('class' => 'reg_form' );
				   echo form_open('', $attributes); 
				    
				   ?>
	 <div class="col-md-4 col-md-offset-2  "> 
                    <div class="form-group">
                        <input name="first_name" type="text" class="form-control" placeholder="First name" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
             </div>
	 <div class="col-md-4 ">       
                    <div class="form-group ">
                        <input name="last_name" type="text" class="form-control" placeholder="Last name" value="">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div>
</div>
	 <div class="col-md-4 col-md-offset-2  ">
        <div class="form-group ">
                        <input name="email2" type="email" class="form-control" placeholder="Email address" readonly value="<?php echo $new_reg_email; ?>">
                        <label class="message"></label><span class="required">*</span>
                    </div>
                   </div>
	 <div class="col-md-4   "> 
                    <div class="form-group">
                        <input name="password" type="password" class="form-control" placeholder="Create password">
                        <label class="message"></label>
                        <span class="required">*</span>
                    </div> 
	</div>
	   
	  <div class="col-md-8 col-md-offset-2  "> 
			<div class="form-group">
				<button type="submit" name='btn_updatename' value='create_account' class="btn btn-block button green submit regUser">Create account</button>
			</div>
	</div>
<?php	echo form_close(); ?>
 <div class="col-md-10 col-md-offset-1  "> 	
			<div class="form-group">
				<p>By clicking "Create Account" you agree to the Mycity.com
                                <a href="/terms-of-service.php" target="_blank">Terms of Services</a> and
                                <a href="/privacy-policy.php" target="_blank">Privacy Policy</a>.
				</p>
			</div>
        </div>
	 </div>
  </div>
</section>
	
 
	</div> <!-- row -->
</div> <!-- container -->
