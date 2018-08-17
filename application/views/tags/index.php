<div class='col-md-9'>   
	<div class='profile-item'>  
	   <h2>Manage Tags</h2>
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
	   <div class="row marg1">
		<div class="col-md-5">
			 <select class="form-control fetchTag" name='tb_tag_id' >
				<option value="null">-select tag-</option>
				<?php
				foreach ($tags->result() as $tag)
				{
					echo "<option value='" . $tag->id  . "'>" . $tag->tagname  . "</option>";
				}
				?>
			</select>
		</div>
		<div class="col-md-4">
			<input name='tb_tag_name' type="text" class="form-control editTag"/>
			<input name='hid_tag_id' type="hidden" id='hid_tag_id' />
		</div> 
		<div class="col-md-3">
			<button name='btn_save' value='update'  class="btn btn-primary btn-sm  ">UPDATE</button> 
			<button name='btn_cancel' value='cancel' class="btn btn-danger  btn-sm  ">CANCEL</button>
		</div>
	</div>
	<?php echo form_close() ;?>
	</div>  
	 
	<div class='profile-item'>  
	   <h2>Add Lifestyle</h2>
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
	   <?php echo form_open() ;?>
	   <div class="row marg1">
		<div class="col-md-5">
			 <input type="text" name='tb_tag_name' placeholder="Add New Tag" class="form-control vocationName">
		</div> 
		<div class="col-md-3">
			<button  name='btn_save' value='save'   class="btn btn-primary btn-sm ">ADD NEW</button>
		</div>
	</div>
	<?php echo form_close() ;?>
	</div>
	 
	</div>  
</div> <!-- row -->
</div> <!-- container -->  