<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?> 
<div class='col-md-9'>
<div class='profile-item'> 
				<h2>Existing reminders</h2>
				<div class='hr-sm'></div>
		<div class='marg1'></div>		
	<?php
	  
	$html ='<table  class="display " style="width:100%" id="tbl-reminders"><thead>';
         $html .='<tr><th>Sl. No.</th><th>Type</th><th>Reminder Title</th><th>Created On</th>' .
         '<th>Reminder Date and Time</th><th>Action</th></tr></thead><tbody> ';
 
		$i=1;
		foreach( $allreminders['result']->result() as $obj)
		{
			$html .= 
			'<tr><td>' . $i  .  '</td>' . 
			'<td >'  . $obj->type .   '</td>'  . 
			'<td >'  . $obj->subject .   '</td>'  .
			'<td >'  . $obj->entrydate .  '</td>'  . 
			'<td >'  . $obj->emailreminderon .   '</td>'  .
			'<td >';
			$html .= '<button data-id="' . $obj->id .  '"  data-remdate="' . 
			$obj->emailreminderon . '" data-title="' . $obj->subject .  
			'" data-reminder="' .  $obj->reminderbody  .  
			'"  class="btn btn-primary btn-xs btnvu">View</button> ' . 
			'<a class="btn btn-primary btn-xs btnvu"  href="' .$base . 'reminders/edit/' .  $obj->id .  '" >Edit</a>' .
            ' <a data-id="'  . $obj->id .   '" class="btn btn-danger btn-xs btnrem">Remove</a>';
			$html .= '</td></tr>';  
			$i++;
         }
		$html .='</tbody> </table>';
		echo $html;
		
		$pager_config['base_url'] = $this->config->item('base_url') . 'reminders/manage/'  ;				
		$pager_config['total_rows'] = $allreminders['num_rows'];
		$this->pagination->initialize($pager_config); 
		echo $this->pagination->create_links();	
		
		
	?>
 <div class="modal fade reminderview" tabindex="-1" role="dialog" aria-labelledby="reminderview" id="reminderview"> 
                  <div class="modal-dialog "> 
                      <div class="modal-content"> 
                          <div class="modal-header"> 
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                              <span aria-hidden="true">&times;</span></button> 
                              <h2 id="remindtitle" class="modal-title" >Reminder Summary</h2> 
                             <small   id="cprofession"></small> 
                         </div> 
                         <div class="modal-body modal-body-no-pad"  style="max-height: 520px; overflow-y:scroll; text-align:left">  
                             <div id="remisummary"> 
                            </div> 
                        </div> 
                         <div class="modal-footer" > 
                      <button class="btn btn-danger btn-lg" data-dismiss="modal" aria-label="Close" >Close</button> 
                  </div> 
              </div> 
        </div> 
     </div> 
	</div>  	 
	</div>  
	</div><!-- row -->
</div><!-- container -->