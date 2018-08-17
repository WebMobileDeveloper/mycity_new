<?php 

$id= 0;
$mem_voc = '';
$com_voc = array();



if($edit_comvoc != null)
{
	  
	if($edit_comvoc->num_rows() > 0)
	{
		$row = $edit_comvoc->row();	 
		 
		$id = $row->id;
		$mem_voc = $row->member_voc;
		$com_voc = explode(',', $row->know_common_voc);
	}
	
	
}

?>
<div class='col-md-9'>   
	<div class='profile-item'>  
	   <h2>Common Vocations For Imported Contacts</h2>
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
	   <div class='row marg1'>
			<div class="col-md-12">
			<label class="custom-label">Select Member Vocation:</label>  
			<select data-placeholder='Choose vocations ...' class="form-control chosen-select " name="member_voc" id="member_voc"  > 
                  <?php
                       foreach ($vocations->result_array() as $vocation)
					   {
						   if($mem_voc == $vocation['voc_name'])
							   $selected ='selected';
						   else 
							   $selected ='';
						   echo "<option $selected value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
                       }
                   ?>
             </select> 
            </div>   
		
			<div class="col-md-12">
			  <label class="custom-label">Common Vocations for knows:</label>  
              <select data-placeholder='Choose vocations ...' multiple class="form-control chosen-select  common_vocations" name="common_vocations[]" id="common_vocations"  > 
                  <?php
                       foreach ($vocations->result_array() as $vocation) 
					   {
						   
						   for($i=0; $i < sizeof($com_voc); $i++)
						   {
							   if($com_voc[$i] == $vocation['voc_name'])
							   {
								   $selected ='selected'; break;
							   }
							   else 
								   $selected ='';
						   }
						  
                          echo "<option  $selected value='" . $vocation['voc_name'] . "'>" . $vocation['voc_name'] . "</option>";
                       }
                   ?>
             </select>
            </div>   
		 <div class="col-md-12 marg1">
			<input type='hidden' name='edit_id' value='<?php echo $id;?>' />
			 <button type='submit' name='save_com_voc' value='save' class='btn btn-primary savesettingscv'>Save Settings</button> 
			  <a href='<?php echo $base;?>manage-vocations/common-vocation/' class='btn btn-danger'>Cancel</a>
		</div>
		</div> 
		 
	<?php echo form_close() ;?>
	</div>  
	
	
	 
	<div class='profile-item'>  
	   <h2>Existing Common Vocations</h2>
	   <div class='hr-sm'></div>
	<?php
			echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";
			if($this->session->msg_error_add  )
			{
				echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error_add . "</p>";
				$this->session->unset_userdata('msg_error_add');
			} 
			echo "</div></div>";
		?>
		
		<table class='table table-alternate'>
			<tr><th>Member Vocation</th><th>Common Vocations for Knows</th><th>Action</th></tr>
			<?php 
			 
			foreach( $all_comvocs->result_array() as $row) 
			{
				echo  "<tr><td>" .  $row['member_voc']  . "</td><td>" .  $row['know_common_voc']  . "</td> ";
				echo  "<td><a href='". $base . "manage-vocations/common-vocation/change/" . $row['id'] . "' class='btn btn-xs btn-primary btneditcomvoc'>Edit</button></td></tr>";
            } 
			?>	
		</table>
		
	   
	</div>
	
	
	</div>  
</div> <!-- row -->
</div> <!-- container -->  