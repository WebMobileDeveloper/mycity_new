<?php

$allvocations ='';
foreach ($vocations->result() as $vocation)
{
	$allvocations .= "<option value='" . $vocation->voc_name  . "'>" . $vocation->voc_name  . "</option>";
}
									

?>
<div class='col-md-9'> 
<div class='profile-item'>
<h2>Compose Email for member</h2>
		<div class='hr-sm'></div>
		<?php 
	if($this->session->msg_error  )
	{ 
		echo "<div class='row'><div class='col-md-10 col-md-offset-1'>"; 
		echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error . "</p>";
		$this->session->unset_userdata('msg_error'); 
		echo "</div></div>"; 
	} 
	else 
	{
		$receipent = $member_selected->row(); 
		echo form_open();
	?> 
		<div class='row'>
			<div class="col-xs-12 col-md-12 marg1 ">
				<label class="custom-label">To:</label>
				<input type="text" value='<?php echo $receipent->username;?>' class="form-control compose_name" id='compose_name' name="compose_name"   required="">
				<input type="hidden" value='<?php echo $receipent->user_email;?>'  name="receipent_email"  >
			</div>
			<div class="col-xs-12 col-md-12  ">
				<label class="custom-label">CC: (Adding CC is optional)</label>
				<input type="text" class="form-control compose_cc" id="compose_cc" name="compose_cc" >
			</div>
			
			<div class="col-xs-12 col-md-12  ">
				<label class="custom-label">Subject:</label>
				<input type="text" class="form-control compose_subject" id="compose_subject" name="compose_subject" required="">
			</div> 
			<div class="col-xs-12 col-md-12  "> 
				<label class="custom-label">Email Body:</label>
				<textarea name='emailbody' class="form-control emailbody"  id='emailbody' rows='5'></textarea>
			</div> 
			<div class="col-xs-12 col-md-12 pad10"> 
			
				<button type="submit" class="btn btn-primary btn-lg" name="btn_send_email" value='send' >Sent</button>
				
				<a  data-toggle="tab" href="#menu2" class="btn btn-link btn-lg"   >Cancel</a>
				
			</div>  
	  </div>
	  <?php 
	  echo form_close();
	}
	
	?> 
	</div>
</div>  
 
</div> <!-- row -->
</div> <!-- container -->
