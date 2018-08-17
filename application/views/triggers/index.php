<?php 
?>
<div class='col-md-9'> 
<?php

	if($this->session->tmsg)
	{
		echo '<div class="alertinfofix text-center">' . $this->session->tmsg . '</div>'; 
		$this->session->unset_userdata( 'tmsg'); 
	}
 
 echo form_open();?>
<div class='profile-item'> 
				<h2>Add New Trigger Question</h2>
				<div class='hr-sm'></div>
				
				 <div class='row padt1'>  
					<div class="col-sm-9 col-xs-12 padd-8">
					<input type="text" name='triggername' placeholder="Add New Trigger" class="form-control">
					</div>
					<div class="col-sm-2 col-xs-12 padd-8 text-center">
					<button type='submit' name='btn_savetrigger' value='save_trigger' class="btn btn-primary ">Save</button>
					<input type='hidden' value='0' name='triggerid'/>	 
					</div>
				</div>

</div>
<?php echo form_close();?>
<div class='profile-item'> 
	<table class="table table-responsive">
		<thead>
			<tr>
				<th>Question #</th>
				<th>Trigger Question</th>
				<th>Action</th>
				</tr>
				</thead>
			<tbody >
			<?php
			$rowindex=1;
			foreach ($alltriggers->result() as $trigger )
			{
				echo "<tr><td>" . $rowindex. "</td><td id='tbody-" . $trigger->id  . "'><span id='trigbody-" . $trigger->id  . "'>" . $trigger->trigger_question  ."</span>" ;
				echo "</td><td>
				<button class='btn-primary btn btn-xs edittrigger' data-id='" . $trigger->id  . "'><i class='fa fa-pencil'></i></button>
				<button class='btn-danger btn btn-xs removetrigger' data-id='" . $trigger->id  . "'><i class='fa fa-times-circle'></i></button></td></tr>";
				$rowindex++;
				}
				?>
			</tbody>
		</table>

</div>  
</div>  
</div><!-- row -->
</div><!-- container -->

 