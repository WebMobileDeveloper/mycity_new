<?php 
?>
<div class='col-md-9'> 
  <?php 
  
  $html = "<div class='panel-group' id='accordion' role='tablist' aria-multiselectable='true'>";
  
  $html .= "<div  class='panel panel-default'>" .
		"<div class='panel-heading' role='tab' id='head0'>"   .
		"<h2 class='panel-title'>" .
		"<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col0' aria-expanded='true' aria-controls='collapseOne'>MyCity Calling System</a></h2></div>" .
		"<div id='col0' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head0'>" .
		"<div class='panel-body'><img width='100%' src='" . $base . "assets/img/edgeup_network_success_system.jpg' alt='MyCity Calling System' /></div></div></div>";
 $html .= "<div  class='panel panel-default'>" .
 "<div class='panel-heading' role='tab' id='head1'>"   .
 "<h2 class='panel-title'>" .
 "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col1' aria-expanded='true' aria-controls='collapseOne'>MyCity Business Growth</a></h2></div>" .
 "<div id='col1' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head1'>" .
 "<div class='panel-body'><img width='100%' src='" . $base . "assets/img/mycity_business_growth.jpg' alt='Edgeup Network Success System' /></div></div></div>";
				
$html .= "<div  class='panel panel-default'>" .
 "<div class='panel-heading' role='tab' id='head2'>"   .
 "<h2 class='panel-title'>" .
 "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col2' aria-expanded='true' aria-controls='collapseOne'>Voice Mail Drops and Permission Texting System</a></h2></div>" .
 "<div id='col2' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head2'>" .
 "<div class='panel-body'><img width='100%' src='" . $base . "assets/img/voice-mail-drops-and-permission-texting-system.jpg' alt='Voice Mail Drops &amp; Permission Texting System' /></div></div></div>";
			 
				
 $html .= "<div  class='panel panel-default'>" .
 "<div class='panel-heading' role='tab' id='head3'>" .
 "<h2 class='panel-title'>" .
 "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col3' aria-expanded='true' aria-controls='collapseOne'>Interview Training Video</a></h2></div>" .
 "<div id='col3' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head3'>" .
 "<div class='panel-body'><div class='embed-responsive embed-responsive-16by9 tmvideo'>" .
 "<iframe class='embed-responsive-item' frameborder='0' width='100' height='315' " .
 "src='https://www.youtube.com/embed/KYmyrMQ0ucw' ></iframe> </div> </div></div></div>";
 
 $idx = 4;
 foreach( $faqs->result() as $item ) 
 {
	 $html .= "<div  class='panel panel-default'>" . 
	 "<div class='panel-heading' role='tab' id='head" . $idx . "'>"  .
	 "<h2 class='panel-title'>" .
	 "<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col" . $idx . 
	 "' aria-expanded='true' aria-controls='collapseOne'>" . $item->helptitle .  "</a></h2></div>" .
	 "<div id='col" .$idx . "' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head" . $idx . "'>" .
	 "<div class='panel-body'>"  . $item->helptext  ."</div></div></div>";
 
 $idx++;
 }    
 $html .='</div>';
 
 echo $html;
 
?>
 					
</div>  
</div><!-- row -->
</div><!-- container -->

 