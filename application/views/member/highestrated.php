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
			<select class="form-control " name='db_group'>
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
			 <select id='memberVocation' name='db_voc' class="form-control memberVocation"> 	 
										<option value=''>Select Vocation</option> 
										<?php 
											foreach($vocations->result() as $vocitem)
											{
												 echo "<option value='" .$vocitem->voc_name   . "'>" . $vocitem->voc_name  . "</option>";
											}
										?>
			 </select> 
			 
			 
		</div>
		<div class="col-sm-2 col-xs-12 padd-8 text-center">
			<button type='submit' name='btn_view_rated' value='view_rated' style="margin-top: 0 !important" class="btn btn-primary btnblock showselectedProfile">VIEW PROFILE</button>
		</div>
		<?php echo form_close(); ?>
	</div>
</div> 

<?php
	 
	if(isset($profiles['result']) && $profiles['result'] != null && $profiles['result']->num_rows() > 0) : 
	foreach( $profiles['result']->result() as $row): 
	?>
	
 <div  class="profile-summary marg1"> 
	<div class='row text'>
	<div class="col-md-12 col-sm-12 col-xs-12 text-left"> 
	 <h3><?php echo $row->client_name;?> <?php echo  $row->rate  ; ?></h3> 
	 </div>
	<div class="col-md-6 col-sm-12 col-xs-12 text-left"> 
		<p><?php echo ($row->client_profession ==''? 'Not Specified':  $row->client_profession  ) ; ?></p>
		<p class='medium'><?php echo  $row->client_location  ; ?> </p>   
	 </div> 
	<div class='col-md-4'>  
		   
		<p class='text-lg'><i class='fa fa-mobile fa-2x'></i> <?php echo  $row->client_phone ; ?></p> 
	 
		<p class='text-lg'><i class='fa fa-envelope  '></i> <?php echo  $row->client_email ; ?></p>  
	</div>  

	<div class="col-md-12 col-sm-12 col-xs-12 text-left"> 
	
	<div class='hr-sm'></div>
<h4><b>Know Group</b></h4>

		<p><?php echo ($row->user_group ==''? 'Not Specified':  $row->user_group  ) ; ?></p>
		  
	 </div> 	
	</div>
  </div>  
   
  <?php 
  endforeach; 
  endif; 
  ?>
 
 
</div>
  
</div> <!-- row -->
</div> <!-- container -->  