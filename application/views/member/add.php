<?php

$allvocations ='';
foreach ($vocations->result() as $vocation)
{
	$allvocations .= "<option value='" . $vocation->voc_name  . "'>" . $vocation->voc_name  . "</option>";
}
									

?>
<div class='col-md-9'> 
<div class='profile-item'>
	<h2>Add New Know</h2>
	<div class='hr-sm'></div>
	<?php
		echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";
		if($this->session->msg_error  )
		{
			echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error . "</p>";
			$this->session->unset_userdata('msg_error');
		} 
		echo "</div></div>";
	?> 
   <?php echo form_open() ;?>
	 <div class='row marg2'> 
		<div class="col-xs-12 col-sm-6  "> 
			<label class="custom-label">Name:</label> 
			<input type="text" autocomplete="off"  class="form-control client_name" name="e_name" required="">
		</div>
                        <div class="col-xs-12 col-sm-6 ">
                            <label class="custom-label">Vocation(s):</label> 
                            <select autocomplete="off" data-placeholder='Choose vocations ...' multiple class="form-control chosen-select " name="e_profession[]" id="e_prof"  > 
                                    <?php 
                                    foreach($vocations->result() as $voc)
									{
										echo "<option>" . $voc->voc_name ."</option>";
									}
                                    ?>
                                </select>
                                <small class="pull-right">(Enter comma seperated)</small> 
                        </div>
                        </div> 
						
	<div class='row'> 
                        <div class="col-xs-12 col-sm-6  ">
							<label class="custom-label">Phone:</label> 
                            <input type="text"  autocomplete="off"  class="form-control client_ph" name="e_phone" required="">
                           
                        </div> 
                        <div class="col-xs-12 col-sm-6 form-group">
                            <label class="custom-label">Email:</label> 
                            <input type="text" autocomplete="off"  class="form-control client_email newcontactemail" name="e_email" required="">
                            
                        </div> 
 </div>					
					<div class='row'> 
						<div class='col-md-6'>
		<label class="custom-label">Street:</label> 
		<input type="text"  autocomplete="off"  class="form-control" name="e_street" required="">
                              
	</div>
	
							<div class="col-xs-12 col-sm-6  ">
							 		
 
	
							 <label class="custom-label">City: <strong >( In case your city is not listed, request to list it <a data-toggle='tab' data-dismiss='modal' aria-label='Close' href='#menu65'>here</a>.)</strong>
                    </label> 
							<select autocomplete="off"  autocomplete="off" data-placeholder='Specify Cities'    name="e_location"  class='form-control chosen-select client_location' > 
							<?php	 foreach ($groups->result() as $group) { 
                                    echo "<option value='" . $group->grp_name  . "'>" . $group->grp_name  . "</option>";
                                }
								?>
						   </select> 
							 <small class="pull-right">(Enter comma separated)</small>
                      </div>
					  <div class='col-md-6'>
						<label class="custom-label">Zip:<br></label> 
                        <input autocomplete="off"  type="text" name="e_zip" class="form-control client_zip" id=""> 
							 
	</div> 
	</div>
	
	<div class='row'> 	
 <div class='col-md-6'>

<label class="custom-label">Groups:<br></label> 
							<select autocomplete="off"  class="form-control chosen-select user_grp" multiple  name="e_group[]">
								<?php
									  foreach ($groups->result() as $group) { 
                                    echo "<option value='" . $group->grp_name  . "'>" . $group->grp_name  . "</option>";
                                }
                                ?>
							</select>
							
	</div>
</div>	
							
 
<div class='row'>
	<div class='col-md-6'>
		<label class="custom-label">Tags:</label> 
			<select autocomplete="off"  data-placeholder='Specify Tags ...' autocomplete="off" multiple  name="knowtags[]"  class="form-control chosen-select  client_tags" id=""> 
				<?php
					foreach ($tags->result() as $tag)
					{
						echo "<option value='" . $tag->tagname . "'>" . $tag->tagname  . "</option>";
					}
				?>
				</select>  
			</div>   
	<div class='col-md-6'>
		<?php
		
		$i = 1; 
		$vocaquestions ='';
		foreach ($questions->result() as $item) 
		{
			$q_id = $item->id ;
			$question = $item->question ;
			$q_type = $item->question_type ;
			if($q_type != "rating"): 
				$vocaquestions .= $question ;
				break;
			endif; 
		} 
?> 
	 
		<label class='custom-label'><?php echo $vocaquestions; ?></label> 
		<input type='hidden' value='<?php echo $q_id;?>' name='questionid' />
		<select id='answer$q_id'  autocomplete="off"  name='target_voc[]' 
		data-placeholder='Choose vocations ...' class='user_ques_text_add chosen-select form-control' multiple  > 
		<?php echo $allvocations; ?> 
		</select> 	 						
	 

	</div>
 
 <div class="col-sm-12  ">
 <label class="custom-label">About(s):</label>
	<textarea type="text" class="form-control client_note" name="e_about"  ></textarea>
	 <small class="pull-right">(Enter comma separated)</small>

 </div>	
  </div>	 
 <div class='row'>
	<hr/>
	<div class="col-sm-12  ">
		<input name='add_member' type="submit" value="Submit" class="btn btn-primary btnblock   addnewknow">   
		</div><div class='clearfix'></div>
	</div></div>
</div>  
<?php echo form_close(); ?>
</div> <!-- row -->
</div> <!-- container -->
