<?php 
  
?>
<div class='col-md-9'>
	<div class='profile-item'>  
		<h2>Connections Requests Received</h2>
				<div class='hr-sm '></div>
				<div class='marg2'></div>
	<a href="<?php echo $base; ?>my-network/my_connections"  class='btn btn-primary btn-sm vuconcount' >View Requests Sent</a>	
		 
		<hr/>
		
					<div id="conreqlist0 marg2">
					
					<?php 
					
					$html = "";
				 
					foreach($connections2['results']->result() as $item)
						{
							 
							$html .= '<div class="box-border-bottom"><div class="row"><div class="col-xs-4 col-md-2">' ;
							$user_picture = ( $item->image !='' &&  file_exists(  $profile_img . $item->image   ) ? $base. $profile_img . $item->image : $base . $image .  "no-photo.png");  
							$html .=  '<img src="' .  $user_picture  . '" alt="' . $item->username . '" class="img-rounded"  height="120" width="120" />';
							
							$html .= '</div>';							
							$html .= '<div class="col-xs-8 col-md-8"><strong>' . $item->username  .'</strong>' .'<br/>' ;

							if($item->city != '' &&   $item->city !==  null )	
								$html .=	$item->city . " " . $item->zip  . '<br/>' ; 	
							
							if($item->country != ''  &&  $item->country !==  null )
								$html .=	 $item->country  .'<br/>'   ;
							
							
							if( $direction2 == 0)
							{ 
								if($item->status == 0)
								{
									$html .= "<p><strong>Phone:</strong> " . $item->user_phone  . "</p>";  
									$html .=	   '</div>' ; 
									$html .= '<div class="col-xs-12 col-md-2">'     ; 
									$html .="<button data-st='1' data-id='" . $item->id  .  "' class='btn-primary btn btn-block btnchangedirectmailstatus'><i class='fa fa-check'></i> Accept</button>";
									$html .=   '</div> ' ; 
								}
								else 
								{
									$html .= "<p><strong>Phone:</strong> <i class='fa fa-lock' title='Phone not display as member has not approve connection'></i></p>";
									$html .= '</div>' ;
									$html .= '<div class="col-xs-12 col-md-2">' ;
									$html .= '<button type="button" data-id="' . $item->id . '" class="btn btn-primary btn-block btncomposedirectmail" ><i class="fa fa-envelope"></i> Message</button>'; 
									$html .= " <button data-st='0' data-id='" . $item->id  .  "' class='btn-warning btn btn-block btnchangedirectmailstatus'><i class='fa fa-close'></i> Reject</button>";
									$html .=  '</div> ' ; 
									 
								}
							}
							else 
							{
								if($item->status == 1)
								{
									$html .= "<p><strong>Phone:</strong> " . $item->user_phone  . "</p>";  
									$html .=	   '</div>' ; 
									$html .= '<div class="col-xs-12 col-md-2">'     ; 
									$html .= '<button type="button" data-id="' . $item->id . '" class="btn btn-primary btn-block btncomposedirectmail" ><i class="fa fa-envelope"></i> Message</button>'; 
									$html .=   '</div> ' ; 
								}
								else 
								{
									$html .= "<p><strong>Phone:</strong> <i class='fa fa-lock' title='Phone not display as member has not approve connection'></i></p>";
									$html .= '</div>' ; 
									$html .= '<div class="col-xs-12 col-md-2">' ;
									$html .= '</div> ' ;  
								}
							}				
								 
						 
							$html .= "</div></div> ";
						} 
						
				echo $html;
				$pager_config['base_url'] = $this->config->item('base_url') . 'connection/requests_received/';				
				$pager_config['total_rows'] = $connections2['num_rows'];
				
				$pager_config["num_links"] = $connections2['num_rows']/10;
				 
				$this->pagination->initialize($pager_config); 
				echo $this->pagination->create_links();	
				 
		 ?>
				  
		</div> 	 
	</div>  
</div> <!-- row -->
</div> <!-- container --> 
 