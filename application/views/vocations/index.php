<div class='col-md-9'>   
	<div class='profile-item'>  
	   <h2>Manage Vocation</h2>
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
			 <select class="form-control fetVocations" name='tb_voc_id' >
				<option value="null">-select-</option>
				<?php
				foreach ($vocations->result() as $vocation)
				{
					echo "<option value='" . $vocation->id  . "'>" . $vocation->voc_name  . "</option>";
				}
				?>
			</select>
		</div>
		<div class="col-md-4">
			<input name='tb_voc_name' type="text" class="form-control editVocation"/>
			<input name='hid_voc_id' type="hidden" id='hid_voc_id' />
		</div> 
		<div class="col-md-3">
			<button name='btn_save' value='update'  class="btn btn-primary btn-sm  ">UPDATE</button> 
			<button name='btn_cancel' value='cancel' class="btn btn-danger  btn-sm  ">DELETE</button>
		</div>
	</div>
	<?php echo form_close() ;?>
	</div>  
	
	
	
	
	<div class='profile-item'>  
	   <h2>Add Vocation</h2>
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
			 <input type="text" name='tb_voc_name' placeholder="Add New Vocation" class="form-control vocationName">
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