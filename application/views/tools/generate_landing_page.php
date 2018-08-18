<?php 
?>
<div class='col-md-9'>  
 <div class='profile-item'> 
	<h2>Generate MyCity Invite Landing Page</h2>
	<hr> 
	 
	<div class='row'>
	<div class='col-md-12'>
	<?php 
	if($url =='')
	{
		echo "<p class='alertinfofix'>No Landing Page URL generated!</p>";
	}
	else 
	{
		echo "<p class='alertdangerfix'  >" .  $url . "</p>";
	}  
	?> 
	</div>
	<div class='col-md-6 marg1'>
		 <div class="form-group">
		 <label>Select Member</label>
			<select type="text" placeholder="Member Name"  class="form-control chosen-select" name="memberid" id='memberid'>
			<?php 
			foreach($allmembers->result() as $item)
			{
				echo "<option value='" . $item->id ."'>" . $item->username ."</item>";
			}
			?>
			</select>
		 </div>
		</div>
		<div class='col-md-6'>
		 <div class="form-group">
		 <label>Select Know to be invited</label>
			<input class="form-control " id="provider-remote-know" name='knowname' placeholder='Start typing a know name'/>
			<input type='hidden' id="knowid" name='knowid' />  
			<input type='hidden' id="partnerid" name='partnerid' />  
			<input type='hidden' id="partnername" name='partnername' />  
			<br/>
			<button name='btn_prepare' value='prepare_url'  class="btn btn-primary btnblock btnrequestcity">Generate Landing Page</button> 
	 		 </div>
		</div> 
	 </div>		   
  </div>  
<div class='profile-item'> 
	<h2>Landing Page URLs</h2>
	<hr> 
	<?php 
	 
	if($all_urls == null  || $all_urls->num_rows() ==0 )
	{
		echo "<p class='alertinfofix'>No Landing Page URL generated!</p>";
	}
	else 
	{
		 
		echo "<table class='table table-responsive table-colored table-bordered'>";
		echo "<thead><tr><th>MyCity User Name</th><th>Know Invited</th><th>Landing Page URL</th></tr></thead>";
		foreach($all_urls->result() as $item)
		{
			if($item->partner_name !='' && $item->know_name != '')
			{
				echo "<tr>";
				echo "<td>";
				echo "<a href='<?php echo BASE_URL;?>/profile/" .    $item->partner_id . "' target='_blank'>" . $item->username . "</a>";
				echo "</td>";
				echo "<td>";
				echo $item->client_name;
				echo "</td>";
				echo "<td>";
				echo  $url =  "<?php echo BASE_URL;?>/profile/invite/" .  $item->partner_name . "/" . $item->know_name;
				echo "</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
	}
	?>

	
<div class="modal fade rated6_intromail" tabindex="-1" role="dialog" 
	aria-labelledby="rated6_intromail" id="rated6_intromail">
	<div class="modal-dialog modal-lg"  >
			 <div class="modal-content">
			 <div class="modal-header">
			 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			 <span aria-hidden="true">&times;</span></button>
			 <h2 class="modal-title" >Sample of Email Message</h2> 
			 </div>
			 <div class="modal-body text-left " style="height: 360px; overflow-y:scroll"  >
			<div style="visibility: hidden; display: none;" id="rated6_mailbody"></div>
			<div id="rated6_wiz_emailbody" class="rated6_wiz_emailbody"></div> 
			</div>
			<div class="modal-footer clearfix" >
			<button   class="btn btn-primary wiz_rated6_send_referral_mail" >Send Mail</button>
			<button data-dismiss="modal"  class="btn btn-danger" >Cancel</button>
			</div>
			</div>
			</div>
	 </div>
	 
	 
	 
</div>	
</div>  
</div><!-- row -->
</div><!-- container -->

