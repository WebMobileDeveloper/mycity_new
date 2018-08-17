<div class='col-md-9'> 
<div class='profile-item'> 
	<h4>Setup Email Template</h4>
	<div class='hr-sm '></div>
	<div class='row'>
	   <div class="col-xs-12 col-sm-6">
	<div class='globalsearch'>
	<label for="em_client">Search Client:</label> 
	<div class='row'>
	<div class="col-xs-12 col-sm-8">
	
		<div class="form-group"> 
			<input type="text" class="form-control " id="vm_client" placeholder="Client Name"> 
		</div>
		
	</div>
	<div class="col-xs-12 col-sm-2">
	
		<div class="form-group">  
			<input type="button" class="btn btn-primary " id="btn_srhvmclient" value='Search'  >
		</div>
		
	</div>	
	 </div> 
	</div> 
	</div>
	
	<div class="col-xs-12 col-sm-12 marg40"> 
		 <ul class="nav nav-tabs navactionlog" role="tablist">
				<li role="presentation" class="<?php echo $tab1; ?>">
				<a href="<?php echo $base;?>dashboard/clients-voice-mails/<?php echo $this->session->pg_cvc; ?>" > Clients With Voice Mail</a></li>
				<li role="presentation" class="<?php echo $tab2; ?>">
				<a href="<?php echo $base;?>dashboard/client-without-voice-mails/<?php echo $this->session->pg_cnvc; ?>" > Clients Without Voice Mail</a></li>
				 </ul> 
			  
					<?php  
					$html = "<table class='table table-responsive'>";
					$html .= "<tr ><th></th><th>Name</th><th>Package</th><th>Last Action</th><th>Next Action</th><th>Action Snapshot</th><th>Action</th></tr>"  ;  
					  
					foreach($voicemails['results']->result() as $item) 
					{
						$user_picture = ($item->h  !='' && (file_exists($site_path . $profile_img .$item->h  ))? $base. $profile_img .$item->h : $base . $image .   "no-photo.png"); 
						 $html .= "<tr  >" . 
						"<td><img src='"  . $user_picture  .  "' alt='"  .  $item->username   . "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" .
						"<td>" . $item->username . "</td><td>" .  $item->f . "</td>" .
						"<td>" . $item->lastbroadcast . "</td>" . 
						"<td>" . $item->nextbroadcast . "</td>" . 
						"<td>" . $item->da . "</td>" .
						"<td>";
						
						if($tab1 =='active')
						{
							$html .= "<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" .
							"<ul class='dropdown-menu'> " .
							"<li><a  href='".$base. "dashboard/" .    $this->uri->segment(2) . "/"  . ($this->uri->segment(3) > 0 ? $this->uri->segment(3) :0) . "/"  .  $item->a   .  "' >New Voicemail</a></li>" .
							"</ul> " ;
						}
						else  if($tab2 =='active')
						{
							$html .= "<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" .
							"<ul class='dropdown-menu'> " .
							"<li><a  href='".$base. "dashboard/" .    $this->uri->segment(2) . "/"  . ($this->uri->segment(3) > 0 ? $this->uri->segment(3) :0) . "/"  .  $item->a   .  "' >Client Management</a></li>" .
							"</ul> " ;
						}
						 
						 
						
						$html .=  "</div></td></tr>";
					} 
					$html .= '</table>'; 
					echo $html; 
					echo $this->pagination->create_links();	
			?> 
		</div>	
		</div>
	</div> 
	
	
		<?php 
		if( $voice_timeline != null  &&  $voicemail_user != null):
		  
		$vmuser = $voicemail_user->row(); 
	?>


	 <div class='profile-item' id='voicemailform'> 
		<h4>Voice Mail Entry</h4>
		<div class='row'>  
			<div class="col-md-12"> 
				<?php echo form_open(); ?>	
				<div class='row'>
					<div class="col-xs-12 col-sm-6">
							 <div class="form-group">
								<label>Voicemail Assign Date:</label> 
								<input name="vm_assigndate" type="text" placeholder="Assign Date" id='vm_assigndate'class="form-control"> 
							</div> 
					</div>  
					<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="vm_schedulehr">Hour</label>
						<select  name="vm_schedulehr" class="form-control" id="vm_schedulehr">
							<option>00</option>
							<option>01</option>
							<option>02</option>
							<option>03</option>
							<option>04</option>
							<option>05</option>
							<option>06</option>
							<option>07</option>
							<option>08</option>
							<option>09</option>
							<option>10</option>
							<option>11</option>
							<option>12</option>
						</select>
						
					  </div> 
				</div> 
				
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="vm_schedulemin">Minute</label>
						<select name="vm_schedulemin" class="form-control" id="vm_schedulemin">
							<option>00</option>
							<option>05</option>
							<option>10</option>
							<option>15</option>
							<option>20</option>
							<option>25</option>
							<option>30</option>
							<option>35</option>
							<option>40</option>
							<option>45</option>
							<option>50</option>
							<option>55</option>
						</select>
						
					  </div> 
				</div> 
				
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="vm_scheduleper">Period</label> 
						<select class="form-control" name="vm_scheduleper" id="vm_scheduleper">
							<option>AM</option>
							<option>PM</option> 
						</select> 
					  </div> 
				</div> 
					 
				</div> 
				 <div class='row'>
						<div class="col-xs-12 col-md-12">
							 <div class="form-group"> 
								<label>Voice Mail Description:</label> 
								<textarea id="vm_description" name="vm_description" rows='10' class="form-control" ></textarea> 
							</div> 
						</div>  
				</div>
				<input type='hidden' value='<?php  echo current_url(); ?>' name='rurl'/>
				<input type='hidden' value='<?php echo $vmuser->id; ?>' name='clientid'/>
				<input type='hidden'  name='vmid'/>
				<button type='submit' name='add_voicemail' value='save' class="btn btn-primary btnblock cfg_save_voicemail">Save</button>       
<a type='button' href='<?php echo $base; ?>dashboard/clients-voice-mails/<?php echo   $this->session->pg_cvc; ?>' class="btn btn-danger ">Cancel</a>  				
			<?php echo form_close(); ?>	
			
		</div>
	</div>
</div> 
	<div class='profile-item'> 
		<h4>Voicemail Timeline for <span style='color:#ff6644' id="vm_nameselected"><a href='<?php echo $base; ?>profile/<?php echo $vmuser->id;?>' target='_blank'><?php echo $vmuser->username; ?></a></span></h4>
		<div class='row'>  
		
 <div class='col-md-12'> 
<div id='vmevent-loading'></div>  
	<div class="tl-box" > 
		<ul id="vmevent-tl">
		<?php 
			$index=0;
			 $k=0;
			foreach($voice_timeline['results']->result() as $item )
			{
				
				if($item->c == 0)
				{
						$nulitem = "<li ><span></span>" ;
						$buttons  = "<br/><hr/><button data-desc='" .  $item->b . "'   data-adate='" .  $item->a . "' data-id='" .  $item->id . "' class='btn btn-primary btn-xs btnvm_edit'>Edit</button>" ;
						$buttons .= " <button data-desc='" .  $item->b . "'   data-adate='" .  $item->a . "' data-id='" .  $item->id . "' class='btn btn-success btn-xs btnvm_completed'>Mark As Complete</button>" ;
						$buttons .= " <button data-name='" .  $vmuser->username . "'  data-id='" .  $item->id . "'  data-mid='" .  $item->mid . "'  class='btn btn-info btn-xs btncreatetaskform'>Set Employee Task</button>" ;
						$buttons .= " <button data-tid='" .  $item->id . "' class='btn btn-danger btn-xs btn_rem_task'>Delete</button>" ;
						
				}
				else 
				{
					$buttons  = "";
					$nulitem = "<li class='processed'><span></span>" ;
				}
				
				$nulitem .= "<div class='title'>Voicemail #" . ($index + 1) . "</div>" . 
				 "<div class='info'>". $item->b ;
				$nulitem .=  $buttons . "</div>" . 
                    "<div class='time' >" .
					"<span>" . $item->a ."</span>" . 
                    "</div>" .
					" </li>" ;
			echo $nulitem;	 
				$k++;
				$index++;
			}
				
		?>
		</ul>  
			<div class="pad10 "> 
			<hr/> <a href="#voicemailform"  style='color: #fff'>To add new voicemail scroll above to voicemail entry block.</a> 
			</div> 
	</div> 
</div> 
 
	</div>
	</div>
		<?php 
		
		endif;
		
		?>
	
<div class="modal fade modalsetemptask" tabindex="-1" role="dialog" aria-labelledby="modalsetemptask"
         id="modalsetemptask">
        <div class="modal-dialog  ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Assign Task to Employee</h4>
                </div>
                <div class="modal-body">
				<div class="col-xs-12 col-sm-6">
				<div class='form-group'  >  
					<label for="taskdate">Notification Time:</label> 
				 <input class='form-control ' id='taskdate' placeholder='Task Notification Date'> 
				 </div>
				
				</div>
				<div class="col-xs-12 col-sm-6">
				<div class='form-group'  > 
				 <label for="empname">Employee Assigned:</label> 
				 <select class='form-control ' id='empname' placeholder='Task Notification Date'>
				 <?php 
					foreach($staffs->result() as $sitem)
					{
						echo "<option value='" .  $sitem->id . "'>" . $sitem->username . "</option>";
					}						
				 ?>
				 </select>				 
				 </div> 
				</div>
			 
				<div class="col-xs-12 col-sm-12">
				<div class='form-group'  > <label for="taskdesc">Task Details:</label> 
				  <textarea row='5' class='form-control ' id='taskdesc' placeholder='Task Details'></textarea> 
				 </div>
				
				</div>
				<div class="col-xs-12 col-sm-12"> 
				  <div class='form-group'  > 
				  <button   class='btn btn-primary btn-xs btn-xs btn_assignemployee'>Save Task</button> 
				  </div>  </div> 
				  
				  <div class='clearfix'></div>
                </div>
            </div>
        </div>
    </div>

	
  </div>
</div> <!-- row -->
</div> <!-- container -->  