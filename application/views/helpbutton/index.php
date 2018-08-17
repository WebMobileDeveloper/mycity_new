<?php 
$id = 0;
$helptitle = $helpvideo = '';
	
if($helpbutton_edit != null)
{
	$item = $helpbutton_edit->row();
	$id = $item->id;
	$helptitle = $item->helptitle;
	$helpvideo = $item->helpvideo;
	 
}

?>
<div class='col-md-9'>   
	<div class='profile-item'>  
	   <h2>Manage Help Buttons</h2>
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
		 <div class="col-xs-12 col-sm-12 col-sm-offset-0  "> 
			<div class="form-group">
				<label for="exampleInputEmail1">Help Title:</label>
				<input type='text' class="form-control" name='helptitle' id='helptitle' required value='<?php echo $helptitle;?>'/>
			</div>
			<div class="form-group">
				<label for="exampleInputEmail1">Help Video:</label>
				<input type='text' class="form-control"  name='helpvideo' id='helpvideo' value='<?php echo $helpvideo;?>' />
				</div>
				<div class="form-group">
					<input type='hidden' name='editid' value='<?php echo $id;?>'/>
					<button type='submit' name='btn_save' value='save' class='btn btn-primary' id='btnsavehelp' >Save</button> 
					<a    class='btn btn-danger' href='<?php echo $base;?>manage-helpbutton' >Cancel</a>
				</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<?php echo form_close() ;?>
	</div>   
	
	<div class='profile-item'>  
	   <h2>Help Button List</h2>
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
	   <table class="table table-responsive">
			<thead>
				<tr>
					<th>Help Title</th> 
					<th>Help Video</th> 
					<th>Action</th>
				</tr>
			</thead>
			<tbody id='divhelpvideos'>
				<?php
					$rowindex=1;
					foreach ($helpbuttons->result_array() as $item )
					{
						echo "<tr><td id='tbody-" . $item['id'] . "'><span id='trigbody-" . $item['id'] . "'>" . $item['helptitle'] ."</span>" ;
						echo "</td><td>" .  $item['helpvideo']  .  "</td><td>
						<a class='btn-primary btn btn-xs'  href='". $base. "manage-helpbutton/change/" . $item['id'] . "'><i class='fa fa-pencil'></i></a>";
						$rowindex++;
					}
				?>
			</tbody>
		</table>
	</div>
	 
	</div>  
</div> <!-- row -->
</div> <!-- container -->  