<div class='col-md-9'> 
<div class='profile-item'> 
	<h4>Client Tracking</h4>
	  
	 <ul class="nav nav-tabs" role="tablist">
		 <li role="presentation" class='<?php echo ($status==1? "active" : ''); ?>' >
		 <a href="<?php echo $base;?>dashboard/client-tracking/active/<?php echo $this->session->p1c; ?>"  > Active Members</a></li>
		 <li role="presentation"  class='<?php echo ($status==0? "active" : ''); ?>'>
		 <a href="<?php echo $base;?>dashboard/client-tracking/inactive/<?php echo $this->session->p2c; ?>" > Members</a></li>
		 <li role="presentation"  class='<?php echo ($status==10? "active" : ''); ?>' >
		 <a  href="<?php echo $base;?>dashboard/client-tracking/ex/<?php echo $this->session->p3c; ?>" > Ex-clients</a></li>
	 </ul> 
			  <div class="tab-content">
				 
					<?php 
					echo form_open(); 
		$html = "<table class='table table-responsive'>";
		$html .=  "<tr ><th></th><th>Name</th><th>Email</th> <th>Select</th><th>Action</th></tr>"  ;  
			
	   foreach($members['results']->result() as $item)
	   {
		   $user_picture = ( $item->image !='' && (  file_exists($site_path . $profile_img .$item->image  ))? $base. $profile_img .$item->image : $base . $image .   "no-photo.png"); 
		   $html .=  "<tr id='row'>" . 
		   "<td rowspan='2'><img src='"  . $user_picture  .  "' alt='"  .  $item->d   . "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" .
		   "<td>" . $item->d . "</td>" .
		   "<td>" . $item->b . "</td>" .  
		   "<td><input type='checkbox'  name='cb_actmembers[]' value='" . $item->a  . "'> </td>" . 
		   "<td>";
		   $html .= "<div class='dropdown '><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" .
		   "<ul class='dropdown-menu pull-right'> " .
		   "<li><a  href='" . $base . "dashboard/client-tracking/"  . 
		   ( $this->uri->segment(3) == '' ? "active" : $this->uri->segment(3) ) . "/" . ( $this->uri->segment(4) =='' ? "0" : $this->uri->segment(4) ) . "/" . $item->a . "#timeline' data-id='" . $item->a . "'    >Email Timeline</a></li>" . 
		   "<li><a  href='#menu71' data-toggle='tab' class='btn_slcvmclient'  data-id='" . $item->a. "' data-name='" . $item->d . "'  >Client Management</a></li>" .
		   "<li><a  href='#'  class='btn_3tinvite'  data-id='" . $item->a . "' data-name='" . $item->d . "'  >Invite to 3 Touch Program</a></li>" .
		   "</ul> " ;
		   $html .=  "</div></td></tr>";
		   $html .= "<tr><td><strong>Snapshot:</strong></td><td colspan='5'>" . $item->sh . "</td></tr>" ;  
		}
		 
	   if( $members['num_rows']  > 0)
	   {
		   if($status == 0)
		   {
			  $html  .= "<tr><td colspan='3'></td><td colspan='2'> " . 
			   "<button type='submit' name='move_user' value='1' class='btn btn-primary btn_deac_acclient'   >Move to Active Member</button>" .
			   " <button type='submit' name='move_user' value='10' class='btn btn-danger btn_deac_acclient' >Move to Ex-client</button> " .
			   "</td></tr>"; 
		   }
		   else  if($status == 1)
		   {
			   $html  .= "<tr><td colspan='3'></td><td colspan='2'> " . 
			   "<button type='submit' name='move_user' value='0' class='btn btn-success btn_deac_acclient'    >Move to Member</button>" .
			   " <button type='submit' name='move_user' value='10' class='btn btn-danger btn_deac_acclient'    >Move to Ex-client</button> " .
			   "</td></tr>";
		   }
		   else if($status == 10)
		   {
			   $html  .= "<tr><td colspan='3'></td><td colspan='2'> " . 
			   "<button type='submit' name='move_user' value='1' class='btn btn-primary btn_deac_acclient'   >Move to Active Member</button>" .
			   " <button type='submit' name='move_user' value='0' class='btn btn-success btn_deac_acclient' data-s='0' >Move to Member</button> " .
			   "</td></tr>";
		   }
		   
		}
		$html .='</table>';	
	   echo $html;   
	echo form_close(); 
 			
	$pager_config['total_rows'] = $members['num_rows'];
	$choice = $members["num_rows"] / 10;
	$pager_config["num_links"] = 20;
	$this->pagination->initialize($pager_config); 
	echo $this->pagination->create_links();	
  
		 	 				
	?> 		 
		</div>
</div>

<?php 
if($timeline != null):
$timelineuserdet= $timeline_user->row();

?>
<div class='profile-item' id='timeline'> 
<h4  >Email Sequence Assigned <span id="sp_nameselected"> to <?php echo $timelineuserdet->username ; ?></span></h4>
<hr/>	

 <div class="row   clearfix" id='tl_box'> 
<div class='col-md-12'>
<div class="tl-box" > 
<ul id="events-tl">
<?php 
 
	$index= 0 ;
	foreach($timeline->result() as $item)
	{
		if($item->status == 0)
		{
			$nulitem = "<li ><span></span>" ;
			$buttons  = "<br/><button data-mid='" . $timelineuserdet->id . "' data-mname='" .  $timelineuserdet->username  . "' data-schdate='" .  $item->d . "' data-id='" .  $item->seqid . "' class='btn btn-primary btn-xs btnupdateschedule'>Change Schedule</button>" ;
			$buttons .= " <button data-mid='" .  $timelineuserdet->id . "' data-mname='" .  $timelineuserdet->username . "' data-schdate='" .  $item->d . "' data-id='" . $item->seqid . "' class='btn btn-success btn-xs btnprocessseq'>Process Now</button>" ;
		}
		else 
		{
			$buttons  = "";
			$nulitem = "<li class='processed'><span></span>" ;
		}
		
		$nulitem .= "<div class='title'>Sequence #" . ( $index + 1 ) . "</div>" . 
		"<div class='info'>". $item->mail_heading ;
		$nulitem .= $buttons . "</div>" . 
		"<div class='time' >" .
		"<span>" . $item->d . "</span>" . 
		"</div>" .
		"</li>" ;
		$index++;
		echo $nulitem;  
	}
 
 if($index == 0)
 {
	 echo "<li><span></span>"  .
	 "<div class='title'>Sequence #0</div>" .
	 "<div class='info'>No Email Assigned Yet</div>" .
	 "<div class='time' >" .
	 "<span>" . date('m-d-Y H:i:s', time()) . "</span>"  .
	 "</div>" .
	 "</li>" ; 
 }

?>
</ul>


</div>
		 
</div> 
</div>
	<div class="pad10 "> 
<hr/>	<a href="javascript:void(0);" class="btn btn-primary btnassignemail" id="add">Add New Email</a> 
</div>	 
</div>
<?php 
endif; 

?>
<div class="modal fade mod_changeschedule" tabindex="-1" role="dialog" aria-labelledby="mod_changeschedule" id="mod_changeschedule">
        <div class="modal-dialog modal-md"   >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Change Email Schedule</h4>
				</div>
                <div class="modal-body  " style='height:90px; overflow-y: scroll'  > 
					
				<div class="col-xs-12 col-sm-6">
					<div class="form-group">
						<label for="aemschedule">Email Sending Scheduled On</label>
						<input type="text" class="form-control" id="rescheduledate" placeholder="Scheduled On">
						 
					  </div> 
				</div>
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="aemschedulehr">Hour</label>
						<select class="form-control" id="aemreschedulehr">
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
						<label for="aemschedulehr">Minute</label>
						<select class="form-control" id="aemreschedulemin">
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
						<label for="aemscheduleper">Period</label> 
						<select class="form-control" id="aemrescheduleper">
							<option>AM</option>
							<option>PM</option> 
						</select> 
					  </div> 
				</div> 
				  
				</div> 
			<div class="modal-footer ">
			 
					<button class="btn btn-danger email_schupdate"  data-dismiss="modal" aria-label="Close" >Update</button>
			 </div> 
			 </div>
        </div>
</div> 




<div class="modal fade mod_assignemail" tabindex="-1" role="dialog" aria-labelledby="mod_assignemail" id="mod_assignemail">
        <div class="modal-dialog modal-lg" >
            <div class="modal-content">
               <div class="modal-header">
					<button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title">Configure Email </h4>
				</div>
                <div class="modal-body  " style='height: 450px; overflow-y: scroll'  > 
					
				<div class="col-xs-12 col-sm-6">
					<div class="form-group">
						<label for="aemschedule">Scheduled On</label>
						<input type="text" class="form-control" id="aemschedule" placeholder="Scheduled On">
					  </div> 
				</div>
				<div class="col-xs-12 col-sm-2">
					<div class="form-group">
						<label for="aemschedulehr">Hour</label> 
						<select class="form-control" id="aemschedulehr">
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
						<label for="aemschedulemin">Minute</label> 
						<select class="form-control" id="aemschedulemin">
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
						<label for="aemscheduleper">Period</label> 
						<select class="form-control" id="aemscheduleper">
							<option>AM</option>
							<option>PM</option> 
						</select> 
					  </div> 
				</div>  
				<div class="col-xs-12 col-sm-12">
					<div id='assignemailgrid'></div>
				</div> 
				</div> 
			<div class="modal-footer ">
					<button class="btn btn-danger email_select" data-dismiss="modal" aria-label="Close" >Select</button>
			 </div> 
			 </div>
        </div>
</div>



  </div>
</div> <!-- row -->
</div> <!-- container -->  