<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(  $this->session->has_userdata('loginmail') )
{
	$email = $this->session->loginmail;
}
else 
{
	$email ='';
}	
 
?> 
<section id="main-section" class="welcome-sec  login">
        <div class="container">
            <div class="row">
            <div class="col-xs-6 col-sm-12 col-md-4 pull-left  hidden-xs"  > 
            <div class="panel panel-default panel-search">
            <div class="panel-heading text-left"><h2 class='htxt-md'>Search Rated Businesses / Individuals</h2></div>
            <div class="panel-body">
                 <div class="form-group">   
 <select data-placeholder="City" id="tbsearchbycity" name="tbsearchbycity"  class="form-control  "  > 
                            <option value=''>Select City</option> 
                            <?php
                            echo $citynames; 
                            ?>
                        </select>
  </div>
  <div class="form-group">   
   <select data-placeholder='Vocations ...' class="form-control   " name="tbsearchbyvoc" id="tbsearchbyvoc"  > 
       <?php
	   foreach ($vocations as $vocation) 
	   {
		   echo "<option value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
		}
	  ?>
  </select> 
                </div>
                <button type="submit" id="form_search_business" class="flatbutton">Search</button>  
            </div>
         </div>
 <div id='tempblock'></div>
            </div>
            <div class="col-xs-6 col-sm-12 col-md-4 pull-right text-center hidden-xs"  >
                <p class='login-intro'><i class='fa fa-support'></i><br/>
                   <span>When you refer others you’re actually helping Yourself. 
                   <br/>Learn who appreciates you for your efforts.</span></p>
            </div> 
			<?php if($partnerid>0 ) echo    form_open('login?p=' .  $partnerid) ; else echo form_open('login') ;?> 
            <div class="col-xs-12 col-sm-12 col-md-4 text-center"  >
                    <div class='formarea'>
                        <h1 class="login-title"><img width='50' height='50' class='img   img-circle' src='<?php echo $image;?>no-photo.png' /> MyCity Login</h1>   
                      <hr/> 
					  
					  <?php 

						if($log_err !='')
						{
							echo "<p class='alertdangerfix'>" . $log_err . "</p>"; 

						} 
						?>


                <div class="form-group"> 
                    <input id="form_login_username" name="username" class="form-control" placeholder="Your email" value='<?php echo $email; ?>'>
                </div>
                <div class="form-group">
                    <input id="form_login_password" type="password" class="form-control" name="password" placeholder="Password">
                </div>  
				<div class="form-group text-left"> 
				  <div class="checkbox">
					<label>
					  <input id="form_login_remember_me" name='remember_me' type="checkbox"> Remember me
					</label>
				  </div> 
			  </div> 
			  <input type='hidden' name='partnerid' value='<?php echo $partnerid;?>'/>
			  <button type="submit" name='btnlogin' id="form_sign_in_button" value='submit' class="flatbutton">Sign In</button>
                <!--<p class="forgot_password"><a href="javascript:void(0)">Forgot your password?</a></p>
                <p class="strikey">or</p>
                <button id="log_in_facebook" class="facebook_button flatbutton">Sign in</button>-->
				 <p class="forgot_password"><span data-toggle="modal" data-target="#forgetPW" style="cursor:pointer;">Forgot your password?</span></p>
                </div>
                </div>
				<?php echo form_close() ;?>
            </div>
        </div>
</section>
<div class="modal fade bs-example-modal-sm" id="forgetPW" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <h2 class="title">Forgot Password</h2>
                <div class="form-group">
                    <input id="forgPWEmail" type="email" class="form-control" name="forgPWEmail" placeholder="Type your email">
                </div>
                <button type="button" id="resPWBtn" class="flatbutton">Reset Password</button>
            </div>
        </div>
    </div>
</div>
