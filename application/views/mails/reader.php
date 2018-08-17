<div class='col-md-9'> 
<a href='<?php echo $this->input->get("return") ; ?>' class='btn btn-primary' ><< Back to Mailbox</a>	 
 
<div class='profile-item marg1'> 
 <?php if($mail_details->num_rows() > 0  ):  
	$row = $mail_details->row() ;
	
	if($type =='in')
	{
		$target_email = $row->receipent;
		$target_id = $row->receipent_id;
	}
	else if($type =='out')
	{
		$target_email = $row->sender;
		$target_id = $row->sender_id;
	}
	else 
	{
		$target_email = $target_id = '';
	}
	
	if( $target_email  == $this->session->email || $target_id == $this->session->id )
	{ 
			echo "<h2>" . $row->subject .  "</h2>";
			echo "<hr/>"; 
			echo "<p>From:" . $row->sender .  "  Sent on: " .   $row->senton   .  "</p>";
			echo "<hr/>"; 
			echo $row->emailbody;
	}
	else 
	{
			echo  "<p class='alertinfofix '>No mail was found!</p>";
	}
	?>
	  
  <?php else: ?> 
        <p class='alertinfofix'>No mail was found!</p>
	  
  <?php  endif; ?>
</div> 
</div>  
</div> <!-- row -->
</div> <!-- container --> 
 	 
  
 
		