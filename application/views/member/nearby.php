<div class='col-md-9'> 
<?php   

	if($this->session->maillog)
	{
		echo '<div class="alertinfofix text-center">' . $this->session->maillog . '</div>';
		$this->session->unset_userdata( 'maillog'); 
	}
	
	$html =''; 
	$user_picture='';
	
	if ( $connected_members['num_rows']  > 0 ) 
	{
		foreach($connected_members['results'] as $row)
		{
			if(   $row->image  !='' &&  file_exists(  $site_path . $profile_img   .  $row->image ))
				$user_picture =  $base. $profile_img   .  $row->image;
			else
				$user_picture = $base. $image .  "no-photo.png"  ; 
			?>
			<div  class="profile-summary marg1">
			<div id="profile" class="profile-blank">
				<h1 ><?php echo $row->username;?></h1> 
			</div>   
			<div class='row text'>
				<div class="col-md-2 col-sm-12 col-xs-12 text-center"> 
				<img src="<?php echo $user_picture ;?>" alt="" class="img-rounded"  height="120" width="120" />
			</div> 
			 
			<div class="col-md-6 col-sm-12 col-xs-12 text-left"> 
				<p><?php echo ($row->vocations ==''? 'Not Specified':  $row->vocations  ) ; ?></p>
				<p class='medium'><?php echo  $row->city . " - " .  $row->zip ; ?>, <strong><?php echo  $row->country ; ?></strong></p>   
				<p><button data-toggle="modal" data-id="<?php echo $row->id ;?>" data-target="#myModal" class="btn-primary btn btncomposedirectmail"><i class="fa fa-envelope"></i> Send Email</button></p> 
			 
			</div>  
			<div class='col-md-4'>   
				<p class='text-lg'><i class='fa fa-map'></i> <?php echo  $row->city . " - " .  $row->zip ; ?></p> 
				<div class='hr-sm'></div>
				<p class='text-lg'><i class='fa fa-mobile fa-2x'></i> <?php echo  $row->user_phone ; ?></p> 
				<div class='hr-sm'></div>
				<p class='text-lg'><i class='fa fa-mobile fa-2x'></i> <?php echo  $row->country ; ?></p>  
			</div> 
			</div>
  </div>
  
  <?php 
  }
  }
  echo $html; 
?>
 
</div>
  
</div> <!-- row -->
</div> <!-- container -->  