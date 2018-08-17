<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?> 
<div class='col-md-9'>


<?php 
$id= 0;
$type =  $subject=  $reminderbody=  $emailreminderon= '';

if( $reminder_det[0]['result'] != null ):

	if ($remid > 0)
	{
		echo "<div class='row'>";
		echo "<div class='col-md-12'>";
		echo "<p class=''>Reminder saved successfully!</p>";
		echo "</div>";
		echo "</div>"; 
	} 
  
	$row = $reminder_det[0]['result']->row();
	$id= $row->id;
	$type= $row->type;
	$subject= $row->subject;
	$reminderbody= $row->reminderbody;
	$emailreminderon= $row->emailreminderon; 
	$rhr =  date('H', strtotime($emailreminderon) ) ;  
	$rper =  ($rhr > 12 ? "PM"  : "AM") ;
	$rhr = ($rhr > 12 ?  $rhr - 12  :   $rhr ) ; 
	$rmin =  date('i', strtotime($emailreminderon) ); 
 
 
?>
	<div class='profile-item'> 
				<h2>Your reminders</h2>
				<div class='hr-sm'></div>
<?php echo form_open() ;?> 
		<div class='row marg4'>
		<div class="col-sm-12"> 
			<div class="form-group">
				<label for="title">Reminder Type:</label> 
			  </div>
			 </div>  
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" name="type" value="TASK" <?php echo ($type == "TASK" ? "checked" : "" ); ?>>
					<span class="cr"><i class="cr-icon fa fa-tasks"></i></span>
					Task
				</label>
			</div> 
         </div>
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" checked name="type" value="NOTE" <?php echo ($type == "NOTE" ? "checked" : "" ); ?>>
					<span class="cr"><i class="cr-icon fa fa-pencil-square-o"></i></span>
					Note
				</label>
			</div> 
        </div>
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" name="type" value="EMAIL" <?php echo ($type == "EMAIL" ? "checked" : "" ); ?>>
					<span class="cr"><i class="cr-icon fa fa-envelope"></i></span>
					Email
				</label>
			</div> 
        </div>
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" name="type" value="CALL" <?php echo ($type == "CALL" ? "checked" : "" ); ?>>
					<span class="cr"><i class="cr-icon fa fa-phone"></i></span>
					Call
				</label>
			</div> 
        </div>
		<div class="col-sm-2"> 
			<div class="radio">
				<label style="font-size: 1em">
					<input type="radio" name="type" value="MEETING" <?php echo ($type == "MEETING" ? "checked" : "" ); ?>>
					<span class="cr"><i class="cr-icon fa fa-users"></i></span>
					Meeting
				</label>
			</div> 
         </div> 
		 </div>
		 <div class="form-group">
				<label for="title">Reminder Title:</label>
				<input type="text" class="form-control" name="rem_title" placeholder="Reminder Title" value='<?php echo $subject;?>' > 
			  </div>
			  <div class="form-group">
				<label for="text">Reminder Text:</label>
				<textarea class="form-control" rows='10' name="rem_text" placeholder="Reminder Body"><?php echo $reminderbody;?></textarea>
			  </div>
			<hr/>  
		    <div class='row'><input type="hidden" class="form-control" name="hid_assignno" value='0' >
			 <?php if($this->session->role == 'admin'  ) { ?>	
				<div class='col-md-3'>
					<div class="form-group">
					<label for="title">Assigned To:</label>
					<input type="text" class="form-control" name="assignno" placeholder="Assign reminder to ...">
					
					</div> 
				</div>
			 <?php } ?>
				<div class='col-md-4'>
					<div class="form-group">
					<label for="remindermailday">Email Reminder on the day of:</label>
					<input type="text" class="form-control" name="remindermail_day" placeholder="Reminder date" value='<?php echo date('d/m/Y', strtotime($emailreminderon) )  ;?>'> 
					</div> 
				</div>
			<div class='col-md-4'>
				<div class="form-group">
					<label for="remindermailday">Reminder Time:</label><br/>
					<select  class="form-control form-control-sm" name="rem_hour" style='width: 90px;display:inline-block'>
					<?php 
					for($hr=1; $hr <=12; $hr++)
					{ 
				
						if($hr < 10)
							echo "<option " . ( $hr == $rhr ? "selected" : ""  )    .   " >0" . $hr . "</option>";
						else
							echo "<option " . ( $hr == $rhr ? "selected" : ""  )    .   " >" . $hr . "</option>";
					}
					?> 
					</select> : 
					<select  class="form-control form-control-sm" name="rem_min" style='width: 90px;display:inline-block'>
					<?php 
					for($hr=1; $hr <=60; $hr++)
					{
						if($hr < 10)
							echo "<option " . ( $hr == $rmin ? "selected" : ""  )    .   "  >0" . $hr . "</option>";
						else
							echo "<option " . ( $hr == $rmin ? "selected" : ""  )    .   ">" . $hr . "</option>";
					} 				
					?> 
					</select>
					
					<select  class="form-control form-control-xs" name="rem_format" style='width: 80px;display:inline-block'>
						<option <?php echo ( $rper == "AM" ? "selected" : ""  );  ?> >AM</option>  
						<option <?php echo ( $rper == "PM" ? "selected" : ""  );  ?>>PM</option>  
					</select> 
					
					</div> 
				</div>
			 </div>
			 <input type='hidden' value='<?php echo   $id ; ?>' name='rem_id'/>
			 <button type="submit" name='btn_savereminder' value='save_reminder' class="btn btn-primary ">Submit</button>
			 <button type="button" id='btnclearreminder' class="btn btn-danger  ">Cancel</button> 
			</div>
			
			</div>
	 <?php 
	 
	 echo form_close() ; 
	
	else:
	 
	?>
	<div class='profile-item'> 
				<h2>Your reminders</h2>
				<div class='hr-sm'></div>
                <p class='content medium'>No Reminders found!</p>
	  </div>
	
	<?php
	endif;
	
	?>		
	</div>  
	</div><!-- row -->
</div><!-- container -->