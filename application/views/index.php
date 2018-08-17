<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>  
	<section id="main-section" class="welcome-sec next-sections">
        <div class="container">
            <div class="row"> 
			
			<?php
			
				$statement = $allstatements->row();
				if(  $allstatements->num_rows() > 0 ): 
					$row = $allstatements->row();
					echo '<div class="col-md-8 col-md-offset-2 text-center">';
					echo "<div class='animated homeentice  bounce text-center '>";
					echo  "<p>".$row->note  . " </p>";
					echo "</div></div>"; 
				endif; 
			  
			?>
                <div class="col-md-12 col-xs-12 col-sm-12 text-center">
                    <h3>Welcome to MyCity</h3>
                    <h4>Join for FREE to see people rated in your area</h4>

                    <div class="col-md-8 col-md-offset-2">
                        <p>Work with your networking partners and proactively seek new introductions through our database and
                            rating system. Experience 21st century networking. And we'll help you with your LinkedIn account
                            and turn your LinkedIn into Revenue.</p>
                    </div>
                    <div class="col-md-6 col-md-offset-3" style="background-color: rgba(0,0,0,.6) ;color:#000;padding-bottom:15px">
                   <?php 
				   $attributes = array('class' => 'reg_form' );
				   echo form_open('', $attributes); ?>
				 <?php echo validation_errors('<p class="val_error">', '</p>'); ?>
				 
				 <?php if( isset($errmsg) && $errmsg != '' ) echo '<p style="margin-top:10px "class="alertdangerfix">'. $errmsg . '</p>';   ?>
				 
				 
						<div class="input-group">
                            <input id="email1" name="email1" type="text" class="form-control user_email" placeholder="Email Address...."
                                   aria-describedby="basic-addon2" style="padding-left: 20px;">
                            <span class="input-group-addon button green submit-type" id="basic-addon2">
                                <button name='reg_step1' class="nextBtn" data-sec="#sec_three" id="nextBtn1" value='signup'>GET STARTED</button>
                            </span>
                        </div>
		 		 </form>
	<div class="   panel-search-home"> 
		<div class="panel-body">
			<p class='txt-lg'>Search Business</p> 
                 <div class="form-group">
					<select data-placeholder="City" id="tbsearchbycity" name="tbsearchbycity"  class="form-control  "  > 
					<option value=''>Select City</option> 
                  
        </select>
  </div>
  <div class="form-group">   
    <select data-placeholder='Vocations ...' class="form-control    " name="tbsearchbyvoc" id="tbsearchbyvoc"  > 
       <?php
	   echo $vocaoptions;
	  ?>
	</select>  
  </div>
  <button type="submit" id="form_search_business" class="flatbutton">Search</button>
  </div>
  </div>   
  </div>
  </div>
  </div>
  </div>
</section>
	
 
	</div> <!-- row -->
</div> <!-- container -->
