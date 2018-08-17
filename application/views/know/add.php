<?php

$allvocations ='';
foreach ($vocations->result() as $vocation)
{
	$allvocations .= "<option value='" . $vocation->voc_name  . "'>" . $vocation->voc_name  . "</option>";
}

$com_vocs =array();
if($comvoc !='')
{
	$com_vocs = explode(',', $comvoc); 
}	

?>
<div class='col-md-9'> 
<div class='profile-item'>
<h2>Add New Know</h2>
		<div class='hr-sm'></div>
   <?php echo form_open() ;?>
	 <div class='row marg2'> 
		<div class="col-xs-12 col-sm-6  "> 
			<label class="custom-label">Name:</label> 
			<input type="text" class="form-control client_name" name="e_name" required="">
		</div>
			<div class="col-xs-12 col-sm-6 ">
				<label class="custom-label">Vocation(s):</label>   
				<select autocomplete="off" data-placeholder='Choose vocations ...' multiple class="form-control chosen-select client_pro" name="e_profession[]" id="e_prof"  >
				<?php
					foreach($vocations->result() as $voc)
					{
						echo "<option  >" . $voc->voc_name ."</option>"; 
					}
                                ?>
							</select>
							<small class="pull-right">(Enter comma seperated)</small> 
                        </div>
                    </div>
					
	<div class='row'> 
                        <div class="col-xs-12 col-sm-6  ">
							<label class="custom-label">Phone:</label> 
                            <input type="text" class="form-control client_ph" name="e_phone" required="">
                           
                        </div> 
                        <div class="col-xs-12 col-sm-6 form-group">
                            <label class="custom-label">Email:</label> 
                            <input type="text" class="form-control client_email newcontactemail" name="e_email" required="">
                            
                        </div> 
						</div>					
		<div class='row'> 
							<div class="col-xs-12 col-sm-6  ">
							 <label class="custom-label">Lifestyle: </label> 
                             <select data-placeholder='Specify lifestyles ...'  multiple  name="e_lifestyle[]" class="form-control chosen-select  client_lifestyle" id="">
                                <?php
                                foreach ($lifestyles->result() as $lifestyle) { 
                                    echo "<option value='" . $lifestyle->ls_name  . "'>" . $lifestyle->ls_name  . "</option>";
                                }
                                    ?>
							 </select>								
                  
							 <label class="custom-label">City: <strong >( In case your city is not listed, request to list it <a data-toggle='tab' data-dismiss='modal' aria-label='Close' href='#menu65'>here</a>.)</strong>
                    </label> 
							<select autocomplete="off" data-placeholder='Specify Cities' multiple  name="e_location[]"  class='form-control chosen-select client_location' > 
							<?php	 foreach ($groups->result() as $group) { 
                                    echo "<option value='" . $group->grp_name  . "'>" . $group->grp_name  . "</option>";
                                }
								?>
						   </select> 
							 <small class="pull-right">(Enter comma separated)</small>
                      
						<label class="custom-label">Zip:<br></label> 
                        <input type="text" name="e_zip" class="form-control client_zip" id=""> 
								
						<label class="custom-label">Note(s):</label>
						<input type="text" class="form-control client_note" name="e_note"  >
						<small class="pull-right">(Enter comma separated)</small>
	</div>
	<div class="col-xs-12 col-sm-6  ">
	<?php
		
		$i = 1;
		$textquestion =''; 
		$ratequestions ='';
		$vocaquestions ='';
		foreach ($questions->result() as $item) 
		{
			$name = "rating" .$i  ; 
			$q_id = $item->id ;
			$question = $item->question ;
			$q_type = $item->question_type ;
			if($q_type == "rating"):
				$ratequestions .= " <div class='col-xs-12 col-sm-12 '>
					<label class='custom-label'>$question</label> 
					<div class='col-sm-6 col-xs-12 form-group'>
					<span class='starRating main user_ques_main' data-ques='$q_id'>";
				$ratequestions .= "<input id='rating01$i' type='radio' class='user_ques' name='$name' value='5' checked><label for='rating01$i'><span></span></label><label for='rating01$i'>5</label>
							<input id='rating02$i' type='radio' class='user_ques' name='$name' value='4'><label for='rating02$i'><span></span></label>
							<label for='rating02$i'>4</label>
							<input id='rating03$i' type='radio'  class='user_ques' name='$name' value='3'><label for='rating03$i'><span></span></label>
							<label for='rating03$i'>3</label>
							<input id='rating04$i' type='radio'  class='user_ques' name='$name' value='2'><label for='rating04$i'><span></span></label>
							<label for='rating04$i'>2</label>
							<input id='rating05$i' type='radio' class='user_ques' name='$name' value='1'><label for='rating05$i'><span></span></label>
							<label for='rating05$i'>1</label>"; 
				$ratequestions .= "</span> </div> </div> "; 
			else:
				$vocaquestions .= $question ;
			endif;
			$i++; 
		}
	echo $ratequestions;
	?> 
	</div> 
</div>			
<div class='row'> 
	<div class="col-xs-12 col-sm-12">
		<label class='custom-label'><?php echo $vocaquestions; ?></label> 
		<input type='hidden' value='<?php echo $q_id;?>' name='questionid' />
		<select id='answer$q_id'  autocomplete="off" name='voc_answer' 
		data-placeholder='Choose vocations ...' class='user_ques_text_add user_target_voc chosen-select form-control' multiple  > 
		<?php echo $allvocations; ?> 
		</select> 	 						
	</div>
</div>
<div class='row'>
	<div class="col-xs-12 col-sm-12 padd-5">
		<label class="custom-label">Tags:</label> 
			<select data-placeholder='Specify Tags ...' autocomplete="off" multiple  name="knowtags[]"  class="form-control chosen-select  client_tags" id=""> 
				<?php
					foreach ($tags->result() as $tag)
					{
						echo "<option value='" . $tag->tagname . "'>" . $tag->tagname  . "</option>";
					}
				?>
				</select>  
			</div>   
		</div>	  
	<hr/>
	<div class="col-sm-12  ">
		<input name='add_know' type="submit" value="Submit" class="btn btn-primary btnblock   addnewknow">   
		</div><div class='clearfix'></div>
	</div>
</div>  
<?php echo form_close(); ?>
</div> <!-- row -->
</div> <!-- container -->
