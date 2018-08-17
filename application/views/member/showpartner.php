<div class='col-md-9'> 
	  
  <div class='profile-item'> 
	<h2>My Partners</h2>
	<div class='hr-sm'></div>
	<div class="row marg1"> 
		<?php echo form_open(); ?>
		<div class="col-sm-12 col-xs-12 ">
			<h4><b>Your Group</b></h4>
			</div>
		<div class="col-sm-5 col-xs-12 ">
			<select class="form-control fetGroupMembers" name='db_group'>
				<option value="null">--- Select Group ---</option>
				<?php
					foreach ($groups->result() as $group )
					{
						echo "<option value='" . $group->id  . "'>" . $group->grp_name  . "</option>";
					}
				?>
			</select>
		</div>
		<div class="col-sm-5 col-xs-12 padd-8">
			<select id='groupMembers' class="form-control groupMembers" name='db_member'>
			</select>
		</div>
		<div class="col-sm-2 col-xs-12 padd-8 text-center">
			<button type='submit' name='btn_view_profile' value='view_profile' style="margin-top: 0 !important" class="btn btn-primary btnblock showselectedProfile">VIEW PROFILE</button>
		</div>
		<?php echo form_close(); ?>
	</div>
</div> 

<?php 
 if(isset($profile) && $profile != null && $profile->num_rows() > 0) : 
	$row = $profile->row();
	$user_picture = ((file_exists(  $image .$row->image  ))? $base. $image .$row->image : $base . $image .   "no-photo.png");  

	?>
           
 <div  class="profile-summary marg1">
	 <div id="profile" class="profile">
		<h1 ><?php echo $row->username;?></h1> 
	  </div> 
  
	<div class='row text'>
		<div class="col-md-2 col-sm-12 col-xs-12 text-center"> 
		<img src="<?php echo $user_picture ;?>" alt="" class="img-rounded"  height="120" width="120" />
	</div> 
	<div class="col-md-6 col-sm-12 col-xs-12 text-left"> 
		<p><?php echo ($row->vocations ==''? 'Not Specified':  $row->vocations  ) ; ?></p>
		<p class='medium'><?php echo  $row->city . " - " .  $row->zip ; ?>, <strong><?php echo  $row->country ; ?></strong></p>   
		<p><button data-toggle="modal" id="<?php echo $row->id ;?>" data-target="#myModal" class="btn-primary btn leaveMsg"><i class="fa fa-envelope"></i> Send Email</button></p> 
	</div>
	
	<div class='col-md-4'>  
		 
		<p class='text-lg'><i class='fa fa-map'></i> <?php echo  $row->city . " - " .  $row->zip ; ?></p> 
		<div class='hr-sm'></div>
		<p class='text-lg'><i class='fa fa-mobile fa-2x'></i> <?php echo  $row->user_phone ; ?></p> 
		<div class='hr-sm'></div>
		<p class='text-lg'><i class='fa fa-mobile fa-2x'></i> <?php echo  $row->country ; ?></p>  
	</div>   
	</div>
  </div>  
  <div class='row'> 
	<div class="col-md-12">
	  <div class='profile-item'> 
		<h2>Biodata:</h2>
				<div class='hr-sm'></div>
                <p class='content medium'>
                <?php echo ($row->about_your_self ==''? 'Not Specified':  $row->about_your_self) ; ?></p>
	  </div>
	  
	  <div class='profile-item'> 
		<h2>Target Clients:</h2>
		<div class='hr-sm'></div>
			<p class='content medium'>
			<?php echo ($row->target_clients ==''? 'Not Specified':  $row->target_clients) ; ?></p> 
	  </div> 
	  <div class='profile-item last-nd'> 
		<h2>Target Referral Partners:</h2> 
		<div class='hr-sm'></div>
		<p class='content medium'>
		<?php echo ($row->target_referral_partners  ==''? 'Not Specified':  $row->target_referral_partners  ) ; ?>         
	  </div> 
    </div>	 
	</div> 
  <?php  endif; ?>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Name: </h4>
            </div>
            <div class="modal-body text-left">
                <small>Your name</small>
                <input type="text" class="form-control" id="sender_name" placeholder="" readonly="readonly" required="" value="Bob Friedenthal">
                <small>Your email</small>
                <input type="email" class="form-control" id="sender_email" placeholder="" readonly="readonly" required="" value="bob@edgeupnetwork.com">
                <small>Message</small>
                <textarea id="sender_msg" class="form-control" cols="30" rows="4"
                          placeholder="The vocation and the rating of their person you would like an  introduction.."></textarea>
            </div>
            <div class="modal-footer"> 
                <div class="col-xs-12">
                    <button class="btn btn-primary leaveUserMsg">SUBMIT NOW</button>
                    <button type="btn btn-danger" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
 
</div>
  
</div> <!-- row -->
</div> <!-- container -->  