<div class='col-md-9'>  
 <?php 
 
	if( $this->session->msg_error  )
	{
		if($this->session->error_code == 0)
		{ 
			$classname = 'alertinfofix';
		}
		else 
		{
			$classname = 'alertdangerfix';
		}
		echo "<p class='" . $classname. " text-center'> " . $this->session->msg_error . "</p>";
		$this->session->unset_userdata('msg_error');
	}
	
	if( !empty($nearest_knows['result'])  ): ?>
	
   <?php 
	if($offset2 > 0)
	{
		$active2='active';
		$active1='';	
	}
	else 
	{
		$active1='active';
		$active2='';
	}
	 
	
 ?>
  
<div class='profile-item'>  
		<h2>Member Search Result</h2>
				<div class='hr-sm '></div>
				<div class='marg2'></div>
				<form method='post' style='display:inline' action='<?php echo $base; ?>business/search/<?php echo $offset ;?>'><button type="submit" name='vu_member' value='1'  class='btn btn-primary btn-sm vuconcount' >Members</button></form>	
		  
			    <form  method='post' style='display:inline' action='<?php echo $base; ?>business/search/knows/<?php echo $offset2 ;?>'><button  type="submit"  name='vu_know' value='1' class='btn btn btn-sm vuconcount' style='color:#000' >Their Knows</button></form>		
				<hr/>
				<div role="tabpanel" class="tab-pane <?php echo $active1; ?>" id="membergrid">
					<div class="memberlist"> 
					<?php 
					$html ='';
					$user_picture='';
					$mrate= '';
					$rowcount=0;
					
					foreach($nearest_knows['result']->result() as $item )
					{
						 
						if(   $item->f !='' &&  file_exists(  $site_path . $profile_img   .  $item->f))
							 $user_picture =  $base. $profile_img   .  $item->f;
						else 
							$user_picture = $base. $image .  "no-photo.png"  ;
					 
					$html .= '<div class="search-box"><div class="row"><div class="col-xs-4 col-md-2">' ;
					$html .= "<img src='"  . $user_picture  .  "' alt='"  .  $item->b   . "'   class='img-rounded'  width='80'> " .'</div>' ;
					$html .= '<div class="col-xs-8 col-md-7"><strong><a target="_blank" href="' . $base  . 'profile/' .  $item->ui   . '">' . $item->b  .'</a></strong>'  .   
					'<input type="hidden" value="' . $item->b . '" id="bcname"><br/>' . $item->w  .'<br/>' ;
					
					if($item->p != '' &&   $item->p !=   null )	
						$html .= $item->p .'<br/>' ;
					if($item->q != '' &&  $item->q !=   null )
						$html .=	 $item->q . " "  . $item->r . '<br/>'  ;
					
					$html .= $item->s ; 
					$mrate =  ceil( $item->rating / 5 ) ;
					$star ='';
					
					for(  $sc =0; $sc < 5; $sc++)
					{
						if($sc < $mrate)
							$star .= "<i class='fa fa-star orange'></i>";
						else 
							$star  .= "<i class='fa fa-star lgray'></i>";
					} 
					if( $mrate ==  5)
					{
						$html .= "<br/><span  class='badge badge-green pointer'><i class='fa fa-sun-o'></i> Top Rated Member</span>"    ;
						$html .= '<br/><a href="#"  data-id="' . $item->ui  . '" class="btn btn-primary btn-sm showratingdetails">Click to view rating details</a>';
					}
					else if($mrate > 0)
					{
						$html .= "<br/>" .	$star    ;
						$html .= '<br/><a href="#"  data-id="' . $item->ui  . '" class="btn btn-primary btn-sm showratingdetails">Click to view rating details</a>';
					
					}
					else 
						$html .= "<br/><span  class='badge badge-blue pointer '>Non Rated Member</span>"  ;
					
					 
					
					
					$html .= '</div> ';
					$html .='<div class="col-xs-4 col-md-3">';
					
					if($item->isconnected == 1 )
					{
						$html .= '<button type="button" data-id="' . $item->ui . '" class="btn btn-primary btn-block btncomposedirectmail" ><i class="fa fa-envelope"></i> Message</button>'; 
					}
					else
					{
						$html .= '<button type="button" data-i="' .$item->ui . '" data-pg="' . $offset  . '" data-tgt="" class="btn btn-primary btn-solid btn-block btnconnect" ><i class="fa fa-envelope"></i> Connect</button>' ; 
					}
					$html .= '<button type="button" data-id="' . $item->ui . '" class="btn btn-primary btn-block btnratemembers" ><i class="fa fa-star"></i> Rate Now</button>';
					$html .=	'</div></div></div>' ;

					$rowcount++;
					} 
					?>  
					<?php 
					if(isset($req_result))
					{
						echo "<p class='alert alertinfofix text-center'> " . $req_result['errmsg'] . "</p>";
					} 
					echo $html;   
					$pager_config['total_rows'] = $nearest_knows['pages']; 
					$choice = ( $nearest_knows["pages"] / 10 > 20 ? 20 : $nearest_knows["pages"] / 10 ); 
					$pager_config["num_links"] = 30;   
					$this->pagination->initialize($pager_config); 
					echo $this->pagination->create_links(); 
				?>
				</div>
			</div> 
		</div>
  <?php else: ?>
  <div class='profile-item'> 
				<h2>Search Result</h2>
				<div class='hr-sm'></div>
                <p class='content medium'>No matching member found! </p>
	  </div>
  <?php  endif; ?>
</div> 
  
 <div class='modal' id='memberratingmodal' tabindex='-1' role='dialog' aria-labelledby='memberratingmodal' >
		<div class='modal-dialog '>
		<div class='modal-content'>
		<div class='modal-header'>
		<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
			<span aria-hidden='true'>&times;</span></button>
			<h4 class='modal-title'>Rate Member</h4>
        </div>
		<div class='modal-body text-left  '>
		    <div id='member_rating_box'> 
			</div>  
           </div>
           <div class='modal-footer'>
				<button class='btn btn-success' data-mid='0' id='btnsavememberrating'>Save</button>
           </div> 
         </div>
       </div>
</div> 
 
<div class='modal' id='ratingdetails' tabindex='-1' role='dialog' aria-labelledby='ratingdetails' >
		<div class='modal-dialog modal-md'>
		<div class='modal-content'>
		<div class='modal-header'>
		<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
			<span aria-hidden='true'>&times;</span></button>
			<h4 class='modal-title'>Rating Details</h4>
        </div>
			<div class='modal-body text-left  ' style='max-height: 360px; overflow-y: scroll'> 
				<div class='pzone'></div> 
           </div>
           <div class='modal-footer'>
				<button class='btn btn-success' data-dismiss='modal'  >Close</button>
           </div> 
         </div>
       </div>
</div> 


</div> <!-- row -->
</div> <!-- container -->  