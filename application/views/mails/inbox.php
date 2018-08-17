<div class='col-md-9'> 
	  <div class='profile-item'> 
		<h3>Inbox</h3> 
	  <hr/>
	  
 <ul class="nav nav-tabs" role="tablist"> 
    <li role="presentation"  class="<?php echo ( $mailtype == 0 ?  "active" :  " " ); ?>"><a href="<?php echo $base; ?>mails/inbox/0/"  > Messages</a></li>
    <li role="presentation"  class="<?php echo ( $mailtype == 10 ?  "active" :  "" ) ; ?>" ><a  href="<?php echo $base; ?>mails/inbox/10/"   > Connection Requests</a></li>
 </ul>

 <?php if( !empty($inbox['result'])  ): ?>
  <!-- Nav tabs -->
  
  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="homein">
		<div id='myinboxdc0'>
		<?php 
		 
		echo "<table class='table table-condensed'><thead><tr><th></th><th></th><th>Sender</th><th>Subject</th><th>Date</th><th>Action</th></tr></thead><tbody> " ;
        
		foreach(  $inbox['result']->result() as $item   )
		{ 
            if($item->emailstatus == 0)
			{
				$estate ='<span class="badge badge-primary">New</span>';
				$bt ='strong';
			} 
			else 
			{
				$estate = ' ';
				$bt =' ';
			} 
		  echo "<tr  data-id='"  . $item->id   . "'   ><td><input type='checkbox' class='delmail' data-id='" . $item->id  . "'></td><td>" .  $estate . "</td><td class='" . $bt ."' data-id='"  .  $item->id  . "' class='readinmail'>" . 
		  $item->username ."</td><td class='" . $bt . "' data-id='"  . $item->id  . "' class='readinmail'>" .  $item->subject   .  "</td><td class='" . $bt . "'>" .  $item->senton  .  "</td>" ;
          echo 		  "<td>";
		  echo "<a href='" . $base  . 'mails/read/?type=in&mail=' . $item->id  .  "&return=".   current_url()  . "' class='btn-primary btn'>Read</a> "; 
		  if( $mailtype == 10  && $item->connect_status == 0)
		  {
			 echo  "<button data-id='" . $item->partnerid. "' data-st='1' data-e='" .  $item->sender .  "' class='btn-primary btn   btnchangedirectmailstatus'>Accept</button> "; 
		  }
		  echo  "<button data-id='" . $item->partnerid .  "' class='btn btn-primary   btncomposedirectmail'> Message</button>"   ;
		  echo  " <button data-id='" . $item->id .  "' class='btn btn-danger btn_rem_mail'> Delete</button>"   ;
		  echo  "</td> ";
		  echo "</tr>";      
        }  
		echo '</table>';  
		$pager_config['base_url'] = $this->config->item('base_url') . 'mails/inbox/' . $mailtype . "/" ;	 
		$pager_config['total_rows'] = $inbox['num_rows'];
		$this->pagination->initialize($pager_config); 
		echo $this->pagination->create_links();
		 
		?>
		</div>  
	</div>  
	<div role="tabpanel" class="tab-pane  " id="conreqin">
		<div id='myinboxdc10'></div>  
	</div>  
  </div>  
  </div>
  <?php else: ?> 
        <p class='content medium'>Inbox is empty! </p>
	  
  <?php  endif; ?>
</div> 
  
</div> <!-- row -->
</div> <!-- container --> 
 	 
  <div class="modal fade mine-modal" id="refmailreader" tabindex="-1" role="dialog" aria-labelledby="refmailreaderbox">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="suggestedref">Mail Content</h4>
      </div>
      <div class="modal-body text-left" id='refmailreaderbox'>
       
      </div>
      <div class="modal-footer">  
        <button type="button" data-dismiss="modal"  class="btn btn-danger">Close</button> 
      </div>
    </div>
  </div>
</div> 
 
		