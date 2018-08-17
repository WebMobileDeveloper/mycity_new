<?php 
 

?>
<div class='col-md-9'>
	 
	<div class='profile-item'> 
		<h2>My connections</h2>
			<div class='hr-sm '></div>
				<div class='marg2'></div>
	   
		
		<div class="tab-content marg2">
					<div role="tabpanel" class="tab-pane active tabcontent" id="conreqsent">
					 
					<div id="conreqlist1">
					  <?php 
					 
					$html = ""; 
					if($connections['results'] !=null && $connections['results']->num_rows()  > 0  ):
					foreach($connections['results']->result() as $item)
					{
						$html .= '<div class="box-border-bottom"><div class="row"><div class="col-xs-4 col-md-2">' ;
						$user_picture = (  $item->image !='' &&  file_exists( $profile_img .$item->image   )? $base. $profile_img .$item->image : $base . $image .   "no-photo.png");  
						$html .=  '<img src="' .  $user_picture  . '" alt="' . $item->username . '" class="img-rounded"  height="120" width="120" />';
							$html .= '</div>';							
							$html .= '<div class="col-xs-8 col-md-8"><strong><a href="'. $base . 'profile/' .$item->user_shortcode .  '" >' . $item->username  .'</a></strong>' .'<br/>' ;
							$html .=	($item->vocations ==''? 'Not Specified':  $item->vocations  )  . '<br/>' ; 	
							if($item->city != '' &&   $item->city !==  null )	
								$html .=	$item->city . " " . $item->zip  . '<br/>' ; 	
							
							if($item->country != ''  &&  $item->country !==  null )
								$html .=	 $item->country  .'<br/>'   ;
							
							
							$html .= "<p><strong>Phone:</strong> " .$item->user_phone  . " </p>";
							$html .= '</div>' ;
							$html .= '<div class="col-xs-12 col-md-2">' ;
							$html .= '<button type="button" data-id="' . $item->id . '" class="btn btn-primary btn-block btncomposedirectmail" ><i class="fa fa-envelope"></i> Message</button>'; 
							$html .= " <button data-st='0' data-id='" . $item->id  .  "' class='btn-warning btn btn-block btnchangedirectmailstatus'><i class='fa fa-close'></i> Reject</button>";
							$html .=  '</div> ' ; 
							$html .=   '</div></div> ' ;
							
						}  
						echo $html; 
						$pager_config['total_rows'] = $connections['num_rows'];
						$choice = $connections["num_rows"] / 10;
						$pager_config["num_links"] = round($choice);
						$this->pagination->initialize($pager_config);
					 
						echo $this->pagination->create_links();
						else:

							echo "No Connection Request Found!";
						
						endif;
					?>
					</div>
					</div>
					 
		    </div>  
			</div>  
</div>  
</div> <!-- row -->
</div> <!-- container --> 
 