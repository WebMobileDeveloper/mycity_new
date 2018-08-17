<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
?> 
<?php 
	$memberid=0;
	$qr = array(); 
	$qr[0] = $qr[1] = $qr[2] = $qr[3] =$qr[4] = 0; 
	if($member->num_rows() > 0):
		$row = $member->row(); 
		$memberid= $row->id; 
		$user_picture = ((file_exists($site_path . $profile_img .$row->image  ))? $base. $profile_img .$row->image : $base . $image .   "no-photo.png");  
 ?> 
<div class="container">
 <div class='row'> 
 <?php  
	echo "<div class='col-md-10 col-md-offset-1'>";
	if($this->session->msg_error  )
	{
		echo "<p class='  alertinfofix text-center'> " . $this->session->msg_error . "</p>";
		$this->session->unset_userdata('msg_error');
	} 
	echo "</div>";
 ?>
 <div class='col-md-9'>   
   <div  class="profile-summary">
		<div id="profile" class="profile">
		<div class='row'>
			<div class='col-md-8'>  		
			<h1 ><?php echo ucwords($row->username);?></h1> 
			</div>
			 <div class='col-md-4 text-right'>   
			 </div>
		 </div> 
	  </div>  
	<div class='row text'>
		<div class="col-md-2 col-sm-12 col-xs-12 text-center"> 
		<img src="<?php echo $user_picture ;?>" alt="" class="img-rounded" 
		height="120" width="120" /> 
	</div> 
	<div class="col-md-7 col-sm-12 col-xs-12 text-left"> 
		<p><?php echo ($row->vocations ==''? 'Not Specified':  $row->vocations  ) ; ?></p>
		<p class='medium'><?php echo  $row->city . " - " .  $row->zip ; ?></p>
		<p class='medium'><strong><?php echo  $row->country ; ?></strong></p>
		<?php
			if($this->session->id )
			{
				if($this->session->id != $row->id) 
				{ 
			?> 
				<form action="<?php echo $base; ?>profile/<?php echo $urlsegment; ?>" method="post">
				<input type="hidden" name="partnerid" value="<?php echo $row->id; ?>" /> 
				<input type="hidden" name="useremail" value="<?php echo $this->session->email; ?>" /> 
				<button type="submit" class='btn btn-success' name="connect_req" value="send"><i class='fa fa-plus'></i> Connect</button> 
				</form>  
			<?php
				}
			}
		?> 
	</div>
	
	<div class="col-md-3 col-sm-12 col-xs-12 text-center"> 
		 
		<?php 
			$rank = 0;
			$count =  $ratings->row()->rated_by;
			$rated_by = 0;
			
			$i=0;
			foreach($ratings->result() as $item)
			{
				$rank += $item->rank;  
				$qr[$i] = $item->rank; 
				$i++;
			} 
			$rank = ceil($rank/$count) ;
			if(  $rank > 0)
			{
				echo "<p class='rating_head'><strong>Overall Rating</strong></p>";
				echo "<span class='rating_text'> " .  $rank  . 
				" </span> ";
				for($ri=0; $ri < ($rank/5) ; $ri++)
				{
					echo "<i class='fa fa-star orange'></i>";
				}
				 
				for(  ; $ri <  5; $ri++)
				{
					echo "<i class='fa fa-star gray'></i>";
				} 
				echo "<p style='margin-top: 10px;' class='show_review pointer'><strong>View Details</strong></p>";				
			}
		?> 
	</div>  
	</div>
  </div>  
  <div class='row'> 
	<div class="col-md-12">
	  <div class='profile-item'> 
				<h2>My Bio:</h2>
				<div class='hr-sm'></div>
                <p class='content medium'>
                <?php echo ($row->about_your_self ==''? 'Not Specified':  $row->about_your_self) ; ?></p>
	  </div> 
	  <div class='profile-item'> 
		<h2>Target Clients:</h2>
		<div class='hr-sm'></div>
			<p class='content medium'>
			<?php echo ($row->target_clients ==''? 'Not Specified':  $row->target_clients) ; ?></p> 
	  </div> 
	  <div class='profile-item last-nd'> 
		<h2>Target Referral Partners:</h2> 
		<div class='hr-sm'></div>
		<p class='content medium'>
		<?php echo ($row->target_referral_partners  ==''? 'Not Specified':  $row->target_referral_partners  ) ; ?>         
	  </div> 
    </div>	 
	</div>  
	</div>
	<div class='col-md-3'>  
	
	<?php if($invite_details != null ):?> 
	<?php
		$invite_row = $invite_details->row(); 
	?>
	<div class='profile-item'> 
		<h2>Join mycity.com</h2> 
		<?php 	
		if($row->username !='')
		echo "<p class='text-center'>to connect with <br/>". $row->username . "</p>";
		
		?>
		<p class='text-center'>
		<a href='https://mycity.com/profile/invite/<?php echo strtolower(  $invite_row->partner_name ."/". $invite_row->know_name ); ?>' class='btn btn-primary btn-lg'>Join Now</a> 
		</p>
	</div> 
<?php else: ?>
	<?php if( !$this->session->has_userdata('id')   ):?> 
	<div class='profile-item'> 
		<h2 style='font-size: 1.6em'>Are you a member?</h2>  
		<form method='post'>
		<p class='text-center'>
		<button class='btn btn-success btn-md' type='submit' name='btn_signup' value='signup' >Yes</button>
		<input type='hidden' value='<?php echo $memberid;?>' name='hidpid'/>
		<button type='submit' name='btn_join' value='join' class='btn btn-primary  btn-md'>No</button> 
		 
		</p>
		</form>
		 
	</div> 
	<?php endif; ?> 
	<?php endif; ?> 
	<?php if($row->linkedin_profile !=''):?> 
	<div class='profile-item'> 
	  <h2>Social Links</h2> 
		<div class='hr-sm'></div> 
		<br/> 
		<a href='<?php echo $row->linkedin_profile; ?>' target='_blank'><i class='fa fa-linkedin-square fa-2x'></i> </a>  
	</div>  
	<?php endif; ?>

	
	  <div class='profile-item'> 
	  <h2>Our Business</h2> 
		<div class='hr-sm'></div> 
		<p class='text-lg marg1'><?php echo  $row->busi_location_street ; ?></p> 
		<p class='text-lg'><?php echo  $row->busi_location ; ?></p>  
		<p class='text-lg'><a target='_blank' href='<?php echo $row->busi_website;?>'><?php echo  $row->busi_website ; ?></a></p> 
		<p class='text-lg'>Opens: <?php echo  $row->busi_hours ; ?></p>  
	  </div>   
	</div> 
<?php
   //echo $memberid; 
if($this->session->id && $memberid == $this->session->id ): 
?>
<div class='col-md-12'> 
  <div class='profile-item'> 
	  <h2>Connections</h2> 
		<div class='hr-sm'></div> 
		<table id="tbl_clients" class="display" style="width:100%">
		<thead>
			<tr>
				<th>Reference</th>
				<th>Vocation</th>
				<th>Phone</th>
				<th>Email</th>
				<th>Location</th>
				<th>Group</th>
				<th>Ratings</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		
		<?php  
			if(  isset($connections['results']->num_rows ) ):
			foreach($connections['results']->result() as $item )
			{
				$id = $item->id;
				$client_name = $item->client_name;
				$client_profession = $item->client_profession;
				$client_phone = $item->client_phone;
				$client_email = $item->client_email;
				$client_location = $item->client_location;
				$user_group = $item->user_group;
				$userGrpName = '';
				$userVocName = '';
				$user_ranking=$item->rank;
				$introducee='';
				$str = "abcdefghijklmnopqrstuvwxyz";
				$rand = substr(str_shuffle($str),0,3);
				echo "<tr id='$rand-$id'>
					<td>$client_name</td>
					<td>$client_profession</td>
					<td>$client_phone</td>
					<td>$client_email</td>
					<td>$client_location</td>
					<td>$userGrpName</td>
					<td>$user_ranking</td>
					<td>
						<button type='button' data-pg='$offset' 
						name='btn_send_connect_req' 
						data-name='$client_name' 
						data-id='$id' 
						data-voc='$client_profession' data-email='$client_email' data-phone='$client_phone' data-param='$urlsegment'   class='btn btn-primary btn-block btnsendinvite' ><i class='fa fa-envelope'></i> Invite</button> 
					</td>
					</tr>"; 	
				} 
				endif;
				?> 
				  
				</tbody>
					<tfoot>
						<tr>
						   <th>Reference</th>
						   <th>Vocation</th>
						   <th>Phone</th>
						   <th>Email</th>
						   <th>Location</th>
						   <th>Group</th>
						   <th>Ratings</th>
						   <th>Action</th>
						</tr>
					</tfoot>
				</table>
			<?php 
				 $pager_config['base_url'] = $this->config->item('base_url') . 'profile/' . $urlsegment;				
				 $pager_config['total_rows'] = $connections['num_rows'];
				 $this->pagination->initialize($pager_config); 
				 echo $this->pagination->create_links();	

			?>
	  </div> 
</div>	
 
<?php
endif;
?>
</div><!-- row -->
</div><!-- container -->
<?php else: ?>
	<section id="sec_sucess_msg" class="next-sections form-large" style="display: block;  height: 500px;pointer-events: auto;">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center pad8">
					<h1 class="description">Oooops ... wrong profile!</h1> 
					<h2>There is no member profile to show!</h2>
                    <p class="description"></p>
                </div> 
            </div>
        </div>
</section>
<?php endif; ?> 
<div class="modal fade" id="review_det" tabindex="-1" role="dialog" aria-labelledby="review_det" >
	<div class="modal-dialog ">
        <div class="modal-content">
		 <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span></button>
			<h2 class="modal-title" >Review Details</h2> 
		</div>
		<div class="modal-body modal-body-no-pad" >
			<table class='table'>
				<tr><td>Wants more business</td><td><?php echo ceil($qr[0] /$count ); ?></td></tr>
				<tr><td>Willing to Give Referrals</td><td><?php echo ceil($qr[1]/$count ); ?></td></tr>
				<tr><td>Expert Level in Their field</td><td><?php echo ceil($qr[2]/$count ); ?></td></tr>
				<tr><td>Would you refer</td><td><?php echo ceil($qr[3]/$count ); ?></td></tr>
				<tr><td>Willing to Network</td><td><?php echo ceil($qr[4]/$count ); ?></td></tr>
			</table> 
		 </div> 
		 <div class="modal-footer clearfix" >
			<button data-dismiss="modal" class='btn btn-primary'>Close</button>
		</div> 
		
		
          </div>
        </div>
</div>