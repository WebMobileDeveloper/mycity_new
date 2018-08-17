<div class='col-md-9'> 
	
<div class='profile-item'>  
		<h2>Manage Incomplete Signup</h2>
		<div class='hr-sm'></div>
		<table class='table table-bordered table-colored'><tr><td>Sl. No.</td><td>Signup Email</td><td>Signup Date</td><td>Action</td></tr> 
		<?php 
			$i=1;
			if($members['results'] != null)
			{
			foreach($members['results']->result() as $item)
			{
				echo '<tr><td>' .  $i  . "</td><td>" .  $item->user_email . "</td><td>". $item->createdon . "</td>"; 
				echo "<td><button data-id='" . $item->id . "'  data-email='" . $item->user_email . "' class='btn btn-primary btn-sm btncontactsignup'><i class='fa fa-envelope'></i></button></td></tr>";
				$i++;
			} 
			echo "</table>"; 
			}
			  
		 $pager_config['base_url'] = $this->config->item('base_url') . 'member/incomplete-signup';				
		 $pager_config['total_rows'] = $members['num_rows'];
		 $choice = $members["num_rows"] ; 
		 $pager_config["num_links"] = 20;  
		 $this->pagination->initialize($pager_config); 
		 echo $this->pagination->create_links();	
 
  
		?> 
	</div> 
</div> 
</div>  
</div> <!-- row -->
</div> <!-- container -->   
<div class="modal fade intromailtemplate" tabindex="-1" role="dialog" aria-labelledby="emailunifinishsignup" id="emailunifinishsignup"> 
                  <div class="modal-dialog "  > 
                  <div class="modal-content"> 
                 <div class="modal-header"> 
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button> 
                 <h2 class="modal-title" >Unfinished Signup Email Template</h2>  
                 </div> 
                 <div class="modal-body text-left "  >
                  <div id="mailbody"><?php
                     
                     $ds = DIRECTORY_SEPARATOR;
                     $apppath = '';
                     $path =  $_SERVER['DOCUMENT_ROOT'] . $ds  ;  
                    
                         if(  file_exists( $path . "templates/unfinishsignuptemplate01.txt" ) )
                         {
							$mailbody = file_get_contents( $path . "templates/unfinishsignuptemplate01.txt"  ) ;  
                            echo $mailbody; 
                         }
                     ?></div> 
                 </div> 
                 <div class="modal-footer clearfix" > 
                 <button   class="btn btn-primary sendemailforunifinishsignup" >Send Mail</button> 
                 <button data-dismiss="modal"  class="btn btn-danger" >Cancel</button> 
                 </div> 
                 </div> 
                 </div> 
            </div> 
			

  