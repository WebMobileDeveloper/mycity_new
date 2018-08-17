<div class='col-md-9'> 
	
<div class='profile-item'> 
	<?php echo form_open($base. 'member'  ); ?>
				<div class="row">
					 <div class="col-xs-12 col-md-12">
					 <h3>Search Registered Member</h3> <hr/>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Specify Name" name='srchRefName' class="form-control srchRefName">
					</div>
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Entry Date" name='srchentryDate' class="form-control srchentryDate">
					</div>
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Email" name='srchemail' class="form-control srchemail">
					</div>
					
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Phone number" name='srchPhone' class="form-control srchPhone">
					</div> 
				</div> 
				<div class="row"> 
				<div class="col-xs-12 col-md-3 padt10">
					<select data-placeholder="Specify Cities" name='filtercity[]'  id="filtercity" class='chosen-select' multiple > 
						<?php
							foreach ($groups->result() as $group)
							{
								echo "<option value='" . $group->grp_name  . "'>" . $group->grp_name . "</option>";
                            }
						?>
				</select>  
				</div> 
					<div class="col-xs-12 col-md-3 padt10">
						<input type="text"  placeholder="Specify Zip Code" class="form-control" name="srchZipCode">
					</div> 
					<div class="col-xs-12 col-md-6 padt10">
						<select data-placeholder="Select Tags" name='filterTags[]' id="filterTags" class='chosen-select srchTags' multiple >
							<?php
								foreach ($tags->result() as $tag)
								{
									echo "<option value='" . $tag->tagname  . "'>" . $tag->tagname . "</option>";
                                }
							?>
						</select>
						<small class="pull-right">(Multiple tags can be selected)</small>
					</div>  
					</div> 
					<div class="row  ">  
					<div class="col-xs-12 col-md-5">
                    <select data-placeholder="Select Vocations" name="locateVoc[]" class='chosen-select user_ques_text_add' multiple  >
						 
							<?php
								foreach ($vocations->result() as $vocation)
								{
									echo "<option value='" . $vocation->voc_name  . "'>" . $vocation->voc_name  . "</option>";
                                }
							?>
						</select><small class="pull-right">(Multiple vocations can be selected)</small>
					</div>
					<div class="col-xs-12 col-md-2 " style='padding-top: 10px;'>
					<button type='submit' value='search' name='btn_search' class="btn btn-primary btnblock srchRef">Search</button></div>
				</div>	 
<?php echo form_close(); ?>
</div>
<div class='profile-item marg1' style="overflow-x: scroll"> 
	<h2>MyCity Members</h2>
	<div class='hr-sm'></div> 
	<a class='btn btn-primary marg1' href='<?php echo $base; ?>member/add/'>Add New</a>
	<table id="tbl_clients" class="display" style="font-size: 14px;width:100%">
		<thead>
			<tr>
				<th>Member Info</th>
				<th>Vocation</th> 
				<th>Location</th>
				<th>Group</th> 
				<th>Packages</th> 
				<th>Joined On</th>
				<th>Action</th>
			</tr>
		</thead>
	<tbody> 
	
	<?php 
	if(  $allmembers['results']->num_rows()  > 0 ):
	foreach($allmembers['results']->result() as $item )
	{
		$id = $item->id;
		$username = $item->username;
		$profession = $item->vocations;
		$location = '';
		$user_phone = $item->user_phone;
		$user_email = $item->user_email;
		$package= $item->user_pkg;
		$createdon = $item->createdOn;
		$usergroup =  $item->groups;
		$userrole =  $item->user_role;
		$user_status =  $item->user_status;
		$path =  $item->image;
		$user_package =  $item->user_pkg;
		$reference_count =  0; 
		 
		$str = "abcdefghijklmnopqrstuvwxyz";
		$rand = substr(str_shuffle($str),0,3);
		
		$tr = "";
		$ico = "fa-eye";
		$sts = "deactivate";
		if($user_status == 0)
		{
			$tr = "danger";
			$ico = "fa-eye-slash";
			$sts = "activate";
			$menu_text  = "Activate User";
		}
		else 
		{
			$menu_text  = "Disable User";
		}
		
		
		echo "<tr id='$rand-$id'>
			<td><strong>$username</strong> <br/>Email: $user_email <br/>Phone: $user_phone<br/>
			Package: $package<br/>
			References: $reference_count 
			</td>
			<td>$profession</td>  
			<td>$location</td>
			<td>$usergroup</td> 
			<td>$user_package </td>			
			<td >$createdon</td>
			<td>
				<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i></a>
				<ul class='dropdown-menu'>   
					<li class=close_drop'><a class='changeAccSett' data-id='$id' data-toggle='modal' data-target='#changeAccSett'>
                        <i class='fa fa-user' data-toggle='tooltip' title='View client'></i> View client
						</a></li>  
					<li><a class='changeProfilePhoto' data-path='$path' data-id='$id' >
							<i class='fa fa-file-photo-o' data-toggle='tooltip' title='Upload Profile Photo'></i> Upload Photo
						</a></li>  
					<li><a class='viewUser' data-user='$id' data-toggle='modal' data-target='#userModal'>
							<i class='fa fa-users' data-toggle='tooltip' title='View references'></i> View references
						</a></li>  
					<li><a class='importmemberknows' data-user='$id'><i class='fa fa-cloud-upload' data-toggle='tooltip' title='Import Knows from CSV'></i> Import Knows</a></li>  
					<li><a  href='" . $base   . "member/compose-email/$id'  data-i='$id'><i class='fa fa-envelope' title='Compose Email'></i> Compose Email</a></li>  
					<li><a href='"  . $base . "member/switch-account/$id' ><i class='fa fa-exchange' data-toggle='tooltip' title='Switch to Member Account'></i> Switch to Member Account</a>
					</li>  
					<li><a class='showreferrals' data-pagesize='10' data-pageno='1' data-user='$id' ><i class='fa fa-link' data-toggle='tooltip' title='Introduction/Referral'></i> Introduction/Referral</a>
					</li>  
					
					<li><a href='".$base."member/check-duplicate-referrals/$id'  ><i class='fa fa-link' data-toggle='tooltip' title='Check Duplicate Knows'></i> Check Duplicate Connections</a>
					</li>  
					
					<li><a class='ref_wizard_byadmin' data-refname='$username' data-refemail='$user_email' data-user='$id' data-role='$userrole'><i class='fa fa-link' data-toggle='tooltip' title='Referral Wizard'></i> Referral Wizard</a>
			</li>";
			
			if($user_status == 0)
			{
				echo "<li><a class='btn_statechange' data-s='1' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
				echo "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>";
				echo "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
			}
			else if($user_status == 1)
			{
				echo "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>";
				echo "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
			}
			else if($user_status == 10)
			{
				echo "<li><a class='btn_statechange' data-s='1' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
				echo "<li><a class='btn_statechange' data-s='100' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Ex-client</a></li>"; 
			}
			else if($user_status == 100)
			{
				echo "<li><a class='btn_statechange' data-s='1'  data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Active Member</a></li>";
				echo "<li><a class='btn_statechange' data-s='10' data-user='$id'><i class='fa $ico' data-toggle='tooltip' title='$sts'></i> Move to Member</a></li>"; 
			}
				 
			
			echo "  <li><a class='delUser' data-user='$id'><i class='fa fa-trash text-danger' data-toggle='tooltip' title='Delete'></i> Delete User</a>
					</li>    
					 </ul>
				 </div> 
			</td>
			</tr>"; 	
		} 
		endif;
		?> 
		</tbody>
			<tfoot>
				<tr>
				<th>Member Info</th>
				<th>Vocation</th> 
				<th>Location</th>
				<th>Group</th> 
				<th >Joined On</th>
				<th>Action</th>
				</tr>
			</tfoot>
		</table>
<?php
	$pager_config['base_url'] = $this->config->item('base_url') . 'member';	
	$pager_config['total_rows'] = $allmembers['num_rows'];
	$this->pagination->initialize($pager_config);
	echo $this->pagination->create_links(); 
?>
<hr/> 
<a class='btn btn-primary marg1' href='<?php echo $base; ?>member/add/'>Add New</a>

</div> 

</div> 
  
</div> <!-- row -->
</div> <!-- container -->  

<div class="modal fade" id="changeAccSett" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
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
					  <input type="text" <?php if($this->session->role != 'admin' ) echo "readonly"; ?> class="form-control" name="upd_username" placeholder="Full name">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="upd_phone" placeholder="Phone">
				</div>
				<div class="form-group">
					<select name="upd_country" class="form-control">
						<option selected disabled="disabled" value="null">-select your country-</option>
						<?php 
						foreach($country->result() as $item)
						{
							echo "<option value='" .  $item->name. "'>" .$item->name ."</option>";
						}
						
						?>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" name="upd_street" placeholder="Street Address">
				</div>
				<div class="form-group">
					<select  name="upd_city" placeholder="City"  class="form-control" placeholder="Business Location">
						<?php
						
						foreach($groups->result() as $item)
						{
							echo "<option value='" .  $item->grp_name. "'>" .$item->grp_name ."</option>";
						}
						
						?>
					</select>  
				</div>
				<div class="form-group">
					 <input type="text" class="form-control" name="upd_zip" placeholder="Zip">
				</div>
				 
				
				
			</div>		
			<div class="col-xs-12 col-sm-12 col-md-6"> 
				<h5>Account Management</h5>
				<div class='row'>
				<div class="col-xs-12 col-sm-12 col-md-8"> 
					<div class="form-group">
						<input type="text" class="form-control" name="upd_email" placeholder="Email">  
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-4"> 
					<div class="form-group"> 
						<button class="btn btn-primary btn-sm btnblock changePass">Change Password</button>
					</div>
				</div>
				</div>
				
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <input type="password" class="form-control" name="old_pass" placeholder="Old password">
				 </div>
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <input type="password" class="form-control" name="new_pass" placeholder="New password">
				 </div>
				 <div class="form-group" style="display: none;" data-type="changePass">
				 <button class="btn btn-primary savePass">Update password</button>
				 </div>
			  
			  
				<h5>LinkedIn URL</h5>
				<div class="form-group">
					<input type="text" class="form-control" value='' name="linkedin_profile" placeholder="LinkedIn URL">  
				</div>
				
				
			</div> 
			</div>
			
			<div class='row'> 
			<div class="col-xs-12 col-sm-12 col-md-6"> 
				<h5>Make Profile</h5>
				<div class="form-group">
					Public <input type="radio" id="upd_public" style="display:inline" name="upd_public_private"  value="public" > 
					Private <input type="radio" id="upd_private" style="display:inline" name="upd_public_private" value="private" >
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
				 if ($this->session->role == 'admin') :
			 ?>
				<h5>Add Tags</h5>
				<div class="form-group">
					<select data-placeholder='Specify Tags ...'  multiple  name='member_tags'  class='form-control chosen-select member_tags'>
					<?php  
						foreach ($tags->result() as $tagitem)
						{
							echo "<option  value='" . $tagitem->tagname  . "'>" . $tagitem->tagname  . "</option>"; 
						} 
					  ?>
					 </select>
				</div>
			<?php  
				endif;
			 ?>	
				<h5>About YourSelf</h5>
				<div class="form-group">
					<textarea type="text" style="height: 150px!important;" class="form-control" name="about_your_self" placeholder="Please start writing.."></textarea>
				</div>
				<h5>Professional Settings</h5> 
				<div class="form-group">
					<label for="city_names">Your City(s)</label> 
					<div class="form-control hidedthead" style="height: 150px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="city_names"  style="width:100%">
							<thead>
								<tr><th>City</th> </tr>
							</thead>
							<tbody>
								<?php
								foreach ($groups->result() as $item)
								{
									echo "<tr><td><input type='checkbox' name='upd_usergrp' value='" . $item->id  . "'/> " . $item->grp_name  . "</td></tr>";
								}
								?>  
							</tbody> 
						</table> 
					</div> 
				</div>
				<div class="form-group">
					<label for="vocation_names">Your Vocation(s)</label>
					<div class="form-control hidedthead" style="height: 150px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="vocation_names"  style="width:100%">
							<thead>
								<tr><th>Your vocations</th> </tr>
							</thead>
							<tbody>
								<?php
								foreach ($vocations->result() as $vocation) {
									echo "<tr><td><input type='checkbox' name='upd_uservoc' value='" . $vocation->voc_name  . "'/> " . $vocation->voc_name . "</td></tr>";
								}
								?>  
							</tbody> 
						</table> 
					</div> 
				</div>
				<div class="form-group">
					<label for="targetclient_names">Target Client(s)</label>
					<div class="form-control hidedthead" style="height: 150px !important; overflow-y: auto; overflow-x: hidden;"> 
						<table id="targetclient_names"  style="width:100%">
							<thead>
								<tr><th>Target Client</th> </tr>
							</thead>
							<tbody>
								<?php
								foreach ($vocations->result() as $vocation) {
									echo "<tr><td><input type='checkbox' name='upd_usertarget' value='" . $vocation->voc_name  . "'/> " . $vocation->voc_name  . "</td></tr>";
								}
								?>  
							</tbody> 
						</table> 
					</div> 
					 
				</div>
				<div class="form-group">
					<label for="targetref_names">Target Referral Partner(s)</label>
					<div class="form-control hidedthead" style="height: 150px !important; overflow-y: auto; overflow-x:hidden">
                        <table id="targetref_names"  style="width:100%">
							<thead>
								<tr><th>Target Referral Partner(s)</th></tr>
							</thead>
							<tbody>
								<?php 
								foreach ($vocations->result() as $vocation)
								{
									echo "<tr><td><input type='checkbox' name='upd_usertargetreferral' value='" . $vocation->voc_name  . "'/> " .  $vocation->voc_name .   "</td></tr>";
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
					<select name="membertype_edit" class="form-control" data-placeholder="Business" data-class="form-large"
                                tabindex="-1" aria-hidden="true">
                            <option selected disabled="disabled" value="null">- Select User Type -</option>
                            <option value="1">Business Information</option> 
                        </select> 
				</div>
				<div class="form-group">
					<input name="busi_name_edit" disabled="disabled" type="text" class="form-control" placeholder="Business Name" value=""> 
				</div>
				<div class="form-group">
					<input name="busi_location_street_edit" disabled="disabled" type="text" class="form-control" placeholder="Street Address" value="">  
				</div>
				<div class="form-group">
					<select name="busi_location_edit" disabled="disabled"  class="form-control" placeholder="Business Location">
					<?php
					echo $citynames;
					?></select> 
				</div>
				<div class="form-group">
					<select  name="busi_type_edit" disabled="disabled"  class="form-control" placeholder="Business Type">
						<?php echo $vocaoptions; ?>
						</select>
				</div>
				<div class="form-group">
					<input name="busi_hours_edit" disabled="disabled" type="text" class="form-control" placeholder="Business Hours" value=""> 
				</div>
				<div class="form-group">
					<input name="busi_website_edit"  disabled="disabled" type="text" class="form-control" placeholder="Website" value=""> 
				</div>
			 
				
				  </div>
			</div>
			</div> 
            </div>
            <div class="modal-footer">
                <div class="col-xs-12">
                    <button class="btn btn-primary btnblock updateUserProf">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changememberpicture" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            
			<div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Update Profile Picture</h4>
            </div>
            <div class="modal-body ">
				<div class='row'>
				<div class="col-xs-12 col-md-4"> 
					<h4>Member Photo</h4>
					<div id='curmemphoto'></div>
				 </div>
				<div class="col-xs-12 col-md-8"> 
					<h4>Drag &amp; Drop New Member Photo</h4>
					 <form  action="includes/profilephotoupload.php" class="dropzone" id="memberprofileimage">
					 <div class="dz-message" data-dz-message><span>Upload Profile Picture</span></div>
					 
					 <input type="hidden" id='hidmid' name='hidmid' />  

					 </form> 
				</div>	 </div>
            </div>
            <div class="modal-footer">
				<div class="col-xs-12">
					<button class="btn btn-primary btnblock" type="submit" id="btnupdatememphoto">Update Profile Photo</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div> 
<div class="modal fade liimportmodal" tabindex="-1" role="dialog" aria-labelledby="liimportmodal" id="liimportmodal">
        <div class="modal-dialog " >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Import LinkedIn Connections</h4>
				</div>
                <div class="modal-body  "  > 
					 <form action="<?php echo $base; ?>member/"
								  class="dropzone"
								  id="linkimport">
						<input type="hidden" id='hidliuserid' name='hidliuserid' value='' /> 
						<input type="hidden" value='upload' name='hid_upload_excel' /> 
					</form> 
					<?php echo form_open('member/'); ?> 
					<div class='form-group pad10 text-center'>
						<button name='linkedin_import' value='import' class='btn btn-primary btn-lg linkedinimportba'>Start Import</button>
						<a data-toggle="tab" href="#menu40"  class='btn btn-danger btn-lg linkedinimportbalist'> View LinkedIn Import List</a> 
					</div>
					<?php echo form_close(); ?>
				</div>
            </div>
        </div>
</div>
<div class="modal fade suggestconnectmodal" tabindex="-1" role="dialog" aria-labelledby="suggestwizard" id="suggestwizard">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Referral Suggestion Wizard</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style='height: 450px; overflow-y:scroll;text-align:left'>
					<div class='bs-wizard'>
						<div id='wizstep1' class="col-xs-4 bs-wizard-step disabled">
						  <div class="text-center bs-wizard-stepnum">Step 1</div>
						  <div class="progress"><div class="progress-bar"></div></div>
						  <a href="#" class="bs-wizard-dot"></a>
						  <div class="bs-wizard-info text-center">Search member by vocations</div>
						  <div class="form-group pad10  ">
							<select data-placeholder='Vocation' class="form-control wiz_profession" name="wiz_profession" id="wiz_profession" >
                                <?php
								foreach ($vocations->result() as $vocation)
								{
									echo "<option value='" . $vocation->voc_name  . "'>" . $vocation->voc_name  . "</option>";
                                }
							?>
							</select>
						</div>
			<div class="form-group pad10  ">
			   <button class='btn btn-success btn-sm wiz_step1_show_member'>Show Connections</button>
		    </div>
		</div> 
        <div id='wizstep2' class="col-xs-4 bs-wizard-step disabled"><!-- complete -->
			<div class="text-center bs-wizard-stepnum">Step 2</div>
				<div class="progress"><div class="progress-bar"></div></div>
                    <a href="#" class="bs-wizard-dot"></a>
                    <div class="bs-wizard-info text-center">Select person to introduce</div>
                    <div class="form-group pad10">
				<select   class="form-control wiz_memberleft" id='wiz_memberleft'  name="wiz_memberleft">
				</select>
			</div>
		</div> 
			<div id='wizstep3' class="col-xs-4 bs-wizard-step disabled">
				<div class="text-center bs-wizard-stepnum">Step 3</div>
				<div class="progress"><div class="progress-bar"></div></div>
					<a href="#" class="bs-wizard-dot"></a>
				<div class="bs-wizard-info text-center">Select member who will receive introduction</div>
				<div class="form-group pad10"> 
					<input class="form-control wiz_memberright" id="provider-remote" />  
				</div>
				<div class="form-group pad10  ">
					<input type='hidden' id="rmid" /> 
					<input type='hidden' class="refereruid" />  
					<input type='hidden' class="referername" />  
					<input type='hidden' class="refereremail" />  
					<input type='hidden' class="refererrole" />  					
					<button data-rightid='' class='btn btn-success btn-sm wiz_step_show_summary'>Show Referral Summary</button>
				</div>
			</div> 
		</div> 
		<div class='text-center'>
			<span class='alertinfofix ref_directtodirectwizard'>If you know the person to introduce, switch to Direct to Direct Referral Wizard</span> 
		</div>
	   <div id='wiz_summary'></div> 
	</div> 
	<div class="modal-footer clearfix" >
			<button data-dismiss="modal"  class='btn btn-primary'>Cancel</button>
		</div> 
	   </div>
    </div>
</div> 