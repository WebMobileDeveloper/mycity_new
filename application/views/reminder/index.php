<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?> 
<div class='col-md-9'>
	<?php
		  
		 $html ='';
		foreach($allreminders->result()  as $item)
		{
			switch($item->type )
                { 
                  case 'CALL':
                    $icon  ='<i class="fa fa-phone dark"></i> ';
                    break;  
                    case 'NOTE':
                        $icon  ='<i class="fa fa-pencil dark"></i> ';
                        break;
                   case 'TASK':
                    $icon  ='<i class="fa fa-tasks dark"></i> ';
                        break;
                     case 'EMAIL':
                     $icon  ='<i class="fa fa-envelope dark"></i> ';
                        break;
                     case 'MEETING':
                     $icon  ='<i class="fa fa-users dark"></i> ';
                        break;
                     case 'PHONE':
                     $icon  ='<i class="fa fa-phone dark"></i> ';
                        break;
                        
                }
                $html .= '<div class="panel panel-default  panel-success"><div class="panel-body"><p>' . $icon . '<strong>' . 
                $item->subject .  '</strong></p><hr/>' ;
                $html .=    $item->reminderbody  ;
                
                $html .= '<hr/><p>Reminder set on: <span class="badge badge-remindate">' .  $item->emailreminderon  . '</span> </p>' ;
                $html .= '</div></div>';  
		}
		
		echo $html;
		 
	?>	
	</div>  
	</div><!-- row -->
</div><!-- container -->