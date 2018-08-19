<?php 
	defined('BASEPATH') OR exit('No direct script access allowed');
 
	if($member->num_rows() > 0):
		$row = $member->row() ;
		$id =  $row->id; 
		$username = $row->username;
		$user_id =  $row->id;
		$_user_role	= $row->user_role;
	?>
	   	 
<div class='col-md-9'> 
<?php  if( isset($error['error']) ) echo "<div class='alertinfofix'>" .   $error['error'] . "</div>";?>
	<div class='row'>
		<div class="col-md-6"> 
			<div class="panel panel-default panelhome">
                <div class="panel-heading">
				<div class='pull-left'>
                    <h4>Profile </h4>
					</div>
					<div class='pull-right'>
					
					<a href="#" data-toggle='modal' data-target='#changeAccSett' 
					class="changeAccSett btn btn-primary btn-sm" 
                    data-id="<?php echo $row->id ?>" title='Click to edit profile'>
                        <i class="fa fa-pencil"></i> Update Profile </a>
					</div>
					<div class="clearfix"></div>
                </div>
            <div class="panel-body">
                    
						<div class="row">
						<div class="col-md-4"> 
							 <img src="<?php echo $asset;?>uploads/profiles/<?php echo $row->image;?>" alt="" class="img-rounded"   height="120" width="120" />
							 <br/><a href="#" data-toggle='modal' data-target='#changepicture' class="btn-primary btn btn-xs changepic_btn marg1" data-id="<?php echo $row->id ?>">
							 <i class="fa fa-pencil"></i> Update Picture
							 </a>
						</div>
						<div class="col-md-6"> 
						<p id='profilep'>
                        <?php
                         echo "<strong>" . $row->username  . "<br/>" . $row->user_email  . 
                         "<br/>Phone: <a href='tel:" . $row->user_phone  ."'>" . $row->user_phone . "</a>" .
                         "<br/>Package Name:" . $row->user_pkg . "</strong>";
                         ?></p> 			
						</div>
                        <div class="col-md-12">
                        <?php 
                            if( $row->profileisvisible == '1' )
                            {
                                ?>
								<?php echo form_open(); ?>
                                <p><br/><strong>Your Publicly Visible Profile:</strong></p>
								<?php  if($row->user_shortcode !='') { ?>
								<a target='_blank' href="<?php echo $base . "profile/". $row->user_shortcode?>"><?php echo $base . "profile/". $row->user_shortcode; ?></a>
								<?php } ?>
								<!--input class='form-control' type='text' disabled 
                            value='<?php echo $row->publicprofile ?>'/-->  
							<br/>
							<button type='submit' name='btn_upd_url' value='update_url' class='btn btn-primary btnmakeprofilepublic'>Update Profile Link</button>
							<?php echo form_close(); ?>
						   <?php
                            }
                            else 
                            {
								$username_parts  =  explode(' ', $username);
								$combined_name  = implode('',  $username_parts );
								$combined_name = strtolower($combined_name); 
							?>
							<?php echo form_open(); ?>
                            <p><br/><strong>Your Public Profile:</strong></p>
							<?php if($row->user_shortcode !='') { ?>
								<a target='_blank' href="<?php echo $base . "profile/". $row->user_shortcode?>"><?php echo $base . "profile/". $row->user_shortcode; ?></a>
								<?php } ?>
							<br>
							<input value='<?php echo  $user_id; ?>' type='hidden' name='hidid'/>
							<button type='submit' name='btn_upd_url' value='update_url' class='btn btn-primary btnmakeprofilepublic'>Make My Profile Public</button>
							<?php echo form_close(); ?>
							<?php
							}
                        ?>
						</div> 	
					</div> 
			 </div>
			<?php if($row->linkedin_profile !=''):?>
			<div class='panel-footer'>
			 <a href='<?php echo $row->linkedin_profile; ?>' target='_blank'><i class='fa fa-linkedin-square fa-2x'></i> </a>
			</div>
			<?php endif; ?>	
         </div>
		 <?php if(  $row->user_type == 1 ) :?>
         <div class="panel panel-default panelhome-sm">
            <div class="panel-heading">
                <h4>Business Information</h4>
            </div>
            <div class="panel-body" id="groupnames">
            <p id='profilep'>
                        <?php
                         echo "<strong>" . $row->busi_name   ."</strong>" .
                         "<br/><strong>Business Type: </strong>" . $row->busi_type . 
                         "<br/><strong>Location: </strong>" . $row->busi_location  . 
                         "<br/><strong>Business Hours: </strong>" .$row->busi_hours  ;
                         ?></p> 
             </div>
         </div> 
        <?php endif; ?>
		
		 <div class="panel panel-default panelhome-sm">
            <div class="panel-heading">
                <h4>My Cities</h4>
            </div> 
			<div class="panel-body" id="groupnames" style='height: 340px; overflow-y:scroll'> 
			 <?php 
				 $mygroups = explode(',', $row->group_names );
				 foreach($mygroups as $gitem)
				 {
					 echo "<span class='grpitem'>" . $gitem . "</span>"; 
				 } 
             ?> 
             </div>
         </div>
		</div> <!-- first column --> 
		<div class='col-md-6'>
		<?php  
		
		 
		 if ($row->user_role == 'user') :
		?> 
	<div class="panel panel-default panelhome"> 
		<div class="panel-body  ">
			<h3 class='text-center txt-head-md'>Get Referrals From Your LinkedIn Connections</h3>
			<p class='text-center'> <i class='fa fa-linkedin fa-5x blue-o'></i> </p>
             <p>
             <strong>Step 1:</strong> Go to <a target='_blank' class='blue' href='https://www.linkedin.com/psettings/member-data'>https://www.linkedin.com/psettings/member-data</a> <br/>
             <strong>Step 2:</strong> Click <strong class='blue'>Request Archive</strong><br/>
             <strong>Step 3:</strong> Email the .zip file or Connections.csv to support@edgeupnetwork.com.  
			</p>  
	    </div>
    </div>
	<div class="panel panel-default panelhome-sm">
		<div class="panel-heading">
		 <h4>My Preferences</h4>
		</div>
			  <div class="panel-body pscroll"  style='height: 240px; overflow-y:scroll'>
             <div id='memberdetails'>
             <?php  
             echo "<p><strong>Target Clients:</strong> <br/>" ; 
			 $targetclients = explode(',', $row->target_clients );
			 foreach($targetclients as $tcitem)
			 {
				 echo "<span class='grpitem'>" . $tcitem . "</span>"; 
			 } 
			 echo "</p>"; 
			 echo "<p><strong>Target Referral Partners:</strong><br/>";
			 
			 $targetreferralpartners = explode(',', $row->target_referral_partners );
			 foreach($targetreferralpartners as $trpitem)
			 {
				echo "<span class='grpitem'>" . $trpitem . "</span>"; 
			 } 
             echo "</p>"; 
			 echo  "<p><strong>Vocation:</strong><br/>";
			 $myvocations = explode(',', $row->vocations );
			 foreach($myvocations as $vitem)
			 {
				echo "<span class='grpitem'>" . $vitem . "</span>"; 
			 } 
             echo "</p>";  
            ?></div> 
	    </div>
    </div> 
	
	<?php 
		if($profileviewlog->num_rows() > 0 )
		{
	?>
	
	<div class="panel panel-default panelhome-sm">
		<div class="panel-heading">
		 <h4>Recently Viewed Profiles</h4>
		</div>
			  <div class="panel-body pscroll"  style='min-height: 120px; overflow-y:scroll'>
             <div id='memberdetails'>
             <?php
				
				foreach( $profileviewlog->result() as $item )
				{
					echo "<a href='".$base ."profile/". $item->user_shortcode . "' target='_blank' class='grpitem'>" . ucwords($item->username) . "</a>"; 
				}    
            ?></div> 
	    </div>
    </div> 
	<?php 
		}
	?>
	
	
	<?php  
		if ( $row->is_employee == '1') 
		{  
	 ?>
	 <div class="panel panel-default panelhome-sm">
		<div class="panel-heading">
			<h4>My Tasks</h4>
		</div>
				<div class="panel-body pscroll"  style='height: 150px; overflow-y:scroll'>
				<div id='memberdetails'>
				 <?php
					/*
					echo "<ul>"; 
					while($row = $mytasks->fetch_array() )
					{
						if($row['task_desc'] !='')
							echo "<li > " . $row['task_desc'] . "</li>";  
					}
					echo "</ul>"; 
				  */
				?>
				</div> 
			</div>
		</div>
	<?php  
	}  
	?> 
<?php 

if($all_excels->num_rows() > 0 )
{
	?>

<div class="panel panel-default panelhome-sm">
		<div class="panel-heading">
		 <h4>LinkedIn File Import Status</h4>
		</div>
			<div class="panel-body pscroll"  style='height: 240px; overflow-y:scroll'>
             <div id='memberdetails'>
             <?php  
             echo "<p>" ; 
			 $targetclients = explode(',', $row->target_clients );
			 foreach($all_excels->result() as $file_item)
			 {
				echo "<strong>File Name:</strong> <span >" . $file_item->filepath . "</span><br/>"; 
				echo "<strong>Upload Date:</strong> <span >" . $file_item->upload_date . "</span><br/>";
				echo "<strong>Total Row Processed:</strong> <span>" . $file_item->last_row_processed . "</span><br/>"; 
				echo "<strong>Total Row Imported:</strong> <span>" . $file_item->total_imported . "</span><br/>";
				echo "<strong>Total number of rows:</strong> <span>" . $file_item->total_row . "</span><br/>"; 
				echo "<strong>Current Status:</strong> <span class='badge'>" . ($file_item->status == '1' ? "Completed" :  "Processing" ) . "</span>"; 
				echo "<br/>-------------------------------------------------------------------<br/>"; 
			 } 
			 echo "</p>"; 
			   
            ?></div> 
	    </div>
    </div> 
<?php  
}
 ?>
 <?php 

else:

if($row->user_role =='admin') :
	
?>


<div class="panel panel-default  ">
	<div class="panel-heading">
		<div class='pull-left'>
		<h4>Front Page Statement</h4>
		</div>
		<div class='pull-right'>
			<button class='btn btn-primary btnshownoteedit' title='Click to add new statement' ><i class='fa fa-plus'></i></button>
		</div> 
		<div class='clearfix'></div>
		
		</div>
	    <div class="panel-body text-left pscroll panelnote">
		<div class="form-group noteshow">
			<?php  
				if(  $statements->num_rows() > 0 ): 
					$row = $statements->row_array();
					$notetext = $row['note'];
						echo "<div class='inforow text-left'>";
						echo  "<div id='fp_note'>".$row['note'] . "</div><br/><small> Note entered on: ";
						echo  $row['enteredon'] ."</small></p>";
						echo "</div> ";  
					else:
					$notetext='';
					echo "<p class='infoalert pad10 text-center'>You haven't created any note!</p>";
				endif;  
			?>
			</div>
			<div class="form-group notearea" style="display: none;">
				<?php echo form_open('/dashboard'); ?>
				<label for="exampleInputEmail1">Compose Note:</label>
				<textarea class="form-control" id='instantnote' name='instantnote' rows="4"><?php echo $notetext;?></textarea> 
			
			<hr/>
			 <button type="submit" id='btnsavenote' name='save_note' value='save' class="btn btn-primary">Save</button> 
			<?php echo form_close(); ?>	
			</div>
					 
					 
	    </div>
    </div> 
	
	 
 
 <div class="panel panel-default panelhome">
	 <div class="panel-heading">
		 <h4>Recent Lifestyle Updated Knows</h4>
	</div>
   <div class="panel-body "  style='height: 240px; overflow-y:scroll'>
	<?php
	
	?>
   </div>
 </div>	
 
<?php 
  endif;	
  endif;
?>
</div>
</div> 
</div>
</div><!-- row -->
</div><!-- container -->

<div class="modal fade" id="changeAccSett" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
<?php echo form_open(); ?>       
	   <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title text-center">Action / Changes</h3>
            </div>
            <div class="modal-body text-left fixheight">
                
				<div id="action"> 
				<div class='row'>
                    <div class="col-xs-12 col-sm-12"> 
						<div id='profilemsg'></div> 
					</div>
			 <div class="col-xs-12 col-sm-12 col-md-6"> 	
			 <h5>Personal Options</h5>			 
				<div class="form-group">
					  <input type="text" <?php if($_user_role != 'admin' ) echo "readonly"; ?> value='<?php echo $row->username ;?>' class="form-control" name="upd_username" placeholder="Full name">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" value='<?php echo $row->user_phone ;?>' name="upd_phone" placeholder="Phone">
				</div>
				<div class="form-group">
				 
					<select name="upd_country" class="form-control">
						<option selected disabled="disabled" value="null">-select your country-</option>
						<?php 
							foreach($allcountry->result() as $countryitem)
							{
								if($countryitem->name == $row->country )
									echo "<option selected value='" . $countryitem->name  . "'>" . $countryitem->name . "</option>";
								else 
									echo "<option value='" . $countryitem->name  . "'>" . $countryitem->name . "</option>";
							}
						
						?> 
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" value='<?php echo $row->street ;?>'  name="upd_street" placeholder="Street Address">
				</div>
				<div class="form-group"> 
					<select  name="upd_city" placeholder="City"  class="form-control" placeholder="Business Location">
					<?php
								foreach ($groups->result() as $item) 
								{
									if($row->city == $item->grp_name )
										echo "<option selected>" . $item->grp_name  . "</option>";
									else 
										echo "<option  >" . $item->grp_name  . "</option>";
								}
								?>
								
					</select>  
				</div>
				<div class="form-group">
					 <input type="text" value='<?php echo $row->zip; ?>' class="form-control" name="upd_zip" placeholder="Zip">
				</div>
				  
				
			</div>		
			<div class="col-xs-12 col-sm-12 col-md-6"> 
				<h5>Account Management</h5>
				<div class='row'>
				<div class="col-xs-12 col-sm-12 col-md-8"> 
					<div class="form-group">
						<input type="text" class="form-control" value='<?php echo $row->user_email ;?>' name="upd_email" placeholder="Email">  
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-4"> 
					<div class="form-group"> 
						<button type='button' class="btn btn-primary btn-sm btnblock changePass">Change Password</button>
					</div>
				</div>
				</div>
				<?php echo form_open('/dashboard') ;?>
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <input type="password" class="form-control" name="old_pass" placeholder="Old password">
				 </div>
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <input type="password" class="form-control" name="new_pass" placeholder="New password">
				 </div>
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <button type='submit' name='btn_save' value='password' class="btn btn-primary savePass">Update password</button>
				 </div>
				 <?php echo form_close( ) ;?>
				
				<h5>LinkedIn URL</h5>
				<div class="form-group">
					<input type="text" class="form-control" value='<?php echo $row->linkedin_profile ;?>' name="linkedin_profile" placeholder="LinkedIn URL">  
				</div>
			</div> 
			</div>
			
			<div class='row'> 
			<div class="col-xs-12 col-sm-12 col-md-6"> 
				<h5>Make Profile</h5>
				<div class="form-group">
					Public <input type="radio" id="upd_public" style="display:inline" name="upd_public_private"  value="1" > 
					Private <input type="radio" id="upd_private" style="display:inline" name="upd_public_private" value="0" >
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-6"> 
				<h5>Reminder Email</h5>
				<div class="form-group">
					Yes <input type="radio" id="upd_reminder_yes" style="display:inline" name="upd_reminder_email"  value="yes" > 
					No <input type="radio" id="upd_reminder_no" style="display:inline" name="upd_reminder_email" value="no" >
				</div>
			</div>
			</div>
			<div class='row'>  
			<div class="col-xs-12 col-sm-12 col-md-12"> 
			 <?php  
				 if ($_user_role == 'admin') :
			 ?>
				<h5>Add Tags</h5>
				<div class="form-group">
					<select data-placeholder='Specify Tags ...'  multiple  name='member_tags'  class='form-control chosen-select member_tags'>
					<?php  
						foreach ($alltags as $tagitem)
						{
							echo "<option  value='" . $tagitem['tagname'] . "'>" . $tagitem['tagname'] . "</option>"; 
						} 
					  ?>
					 </select>
				</div>
			<?php
				endif;
			 ?>
			 <h5>About YourSelf</h5>
			 <div class="form-group">
				<textarea type="text" style="height: 150px!important;" class="form-control" name="about_your_self" placeholder="Please start writing.."><?php echo $row->about_your_self;?></textarea>
			 </div>
				<h5>Professional Settings</h5> 
				<div class="form-group">
					<label for="city_names">Your City(s)</label> 
					 
					<div class="form-control hidedthead" style="height: 250px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="city_names" style="width:100%">
							<thead>
								<tr><th>City</th> </tr>
							</thead>
							<tbody>
								<?php
								$row_groups = explode(',', $row->groups); 
								foreach ($groups->result() as $item)
								{
									$checked ='';
									for($i=0 ; $i < sizeof($row_groups) ; $i++)
									{
										if($item->id == $row_groups[$i])
											$checked ='checked';
									}
									
									echo "<tr><td><input  $checked type='checkbox' name='upd_usergrp[]' value='" . $item->id  . "'/> " . $item->grp_name  . "</td></tr>";
								}
								?>
							</tbody> 
						</table> 
					</div> 
				</div>
				<div class="form-group">
					<label for="vocation_names">Your Vocation(s)</label>
					<div class="form-control hidedthead" style="height: 250px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="vocation_names"  style="width:100%">
							<thead>
								<tr><th>Your vocations</th> </tr>
							</thead>
							<tbody>
								<?php
								$row_vocations = explode(',', $row->vocations); 
								foreach ($vocations->result() as $vocation) 
								{
									$checked ='';
									for($i=0 ; $i < sizeof($row_vocations) ; $i++)
									{
										if($vocation->voc_name == $row_vocations[$i])
											$checked ='checked';
									}
									echo "<tr><td><input $checked type='checkbox' name='upd_uservoc[]' value='" . $vocation->voc_name   . "'/> " . $vocation->voc_name   . "</td></tr>";
								}
								?>  
							</tbody> 
						</table> 
					</div> 
				</div>
				<div class="form-group">
					<label for="targetclient_names">Target Client(s)</label>
					<div class="form-control hidedthead" style="height: 250px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="targetclient_names"  style="width:100%">
							<thead>
								<tr><th>Target Client</th> </tr>
							</thead>
							<tbody>
								<?php
								$row_target_clients = explode(',', $row->target_clients); 
								foreach ($vocations->result() as $vocation) 
								{
									$checked ='';
									for($i=0 ; $i < sizeof($row_target_clients) ; $i++)
									{
										if($vocation->voc_name == $row_target_clients[$i])
											$checked ='checked';
									}
									echo "<tr><td><input $checked type='checkbox' name='upd_usertarget[]' value='" . $vocation->voc_name   . "'/> " . $vocation->voc_name   . "</td></tr>";
								} 
								?>  
							</tbody> 
						</table> 
					</div>  
				</div>
				<div class="form-group">
					<label for="targetref_names">Target Referral Partner(s)</label>
					<div class="form-control hidedthead" style="height: 250px !important; overflow-y: auto; overflow-x:hidden">
                        <table id="targetref_names"  style="width:100%">
							<thead>
								<tr><th>Target Referral Partner(s)</th></tr>
							</thead>
							<tbody>
								<?php
								$row_target_referral_partners = explode(',', $row->target_referral_partners); 
								foreach ($vocations->result() as $vocation) 
								{
									$checked ='';
									for($i=0 ; $i < sizeof($row_target_referral_partners) ; $i++)
									{
										if($vocation->voc_name == $row_target_referral_partners[$i])
											$checked ='checked';
									}
									echo "<tr><td><input $checked  type='checkbox' name='upd_usertargetreferral[]' value='" . $vocation->voc_name   . "'/> " . $vocation->voc_name   . "</td></tr>";
								} 
								?>  
							</tbody> 
						</table> 
					</div> 
				</div> 
			</div>
			
			<div class="col-xs-12 col-sm-12 col-md-12"> 
				<h5>Business Information</h5>
				<div class="form-group">
					<select name="membertype_edit" 
					class="form-control"
					data-placeholder="Business" 
					data-class="form-large" 
					tabindex="-1" aria-hidden="true">
                            <option selected disabled="disabled" value="0">- Select User Type -</option>
                            <option value="1">Business Information</option> 
                        </select> 
				</div>
				<div class="form-group">
					<input value='<?php echo $row->busi_name;?>' name="busi_name_edit" disabled="disabled" type="text" class="form-control" placeholder="Business Name" value=""> 
				</div>
				<div class="form-group">
					<input value='<?php echo $row->busi_location;?>' name="busi_location_street_edit" disabled="disabled" type="text" class="form-control" placeholder="Street Address" value="">  
				</div>
				<div class="form-group">
					<select name="busi_location_edit" disabled="disabled"  class="form-control" placeholder="Business Location">
					<?php
						foreach ($groups->result() as $item)
						{
							echo "<option>" . $item->grp_name  . "</option>";
						}
					 ?> 
					</select> 
				</div>
				<div class="form-group">
					<select  name="busi_type_edit" disabled="disabled"  class="form-control" placeholder="Business Type">
						<?php
						foreach ($vocations->result() as $vocation)
						{
							echo "<option>" . $vocation->voc_name   . "</option>";
						}
					    ?>
					</select>
				</div>
				<div class="form-group">
					<input value='<?php echo $row->busi_hours;?>' name="busi_hours_edit" disabled="disabled" type="text" class="form-control" placeholder="Business Hours" value=""> 
				</div>
				<div class="form-group">
					<input value='<?php echo $row->busi_website;?>' name="busi_website_edit"  disabled="disabled" type="text" class="form-control" placeholder="Website" value=""> 
				</div> 
			  </div>
			</div>
			</div> 
            </div>
            <div class="modal-footer">
                <div class="col-xs-12">
                    <button type='submit' name='btn_update' value='update_profile' class="btn btn-primary btnblock">Save changes</button>
                </div>
            </div>
        </div>
		 <?php echo form_close(); ?>
    </div>
</div> 
<div class="modal fade" id="changepicture" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $base; ?>dashboard" method="post" enctype="multipart/form-data">
			<div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Update Profile Picture</h4>
            </div>
            <div class="modal-body text-left">
				  <small>Select Image:</small>
                <input type="file" name="prof_img" required="" >
            </div>
            <div class="modal-footer">
                
                <div class="col-xs-12">
                    <button class="btn btn-default btnblock" type="submit" value='upload' name="upload_btn">Upload Now</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
			</form>
        </div>
    </div>
</div>
<?php
	endif;
?> 