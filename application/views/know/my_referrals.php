<div class='col-md-9'> 
 <div class='profile-item'> 
<?php

$uid= $this->session->id;
$html = '';
if( !empty($referrals['records']  )  ) :

  $start = 0; 
  $startfrom =  0 ;
  $html  =  '<p class="alert alert-info">These people are suggested as per your newly added contact. Connect with them to grow!</p>';
  $msg = "<p class='alert alert-info'>No matching suggestion!</p>";
  
  $html .='<table class="table table-condensed">
			<thead>
			<tr><th>Connect to suggest </th>
            <th>Partner Info </th>  
			<th>Introduced to</th>  
			<th>Action</th> 
			</tr>
 </thead><tbody>';
 $i=0; 
foreach($referrals['records']  as $row )
{ 
			//$treferedto = $link->query(" SELECT u.*, sum(r.ranking) as ranking  FROM user_people as u inner join user_rating as r on u.id=r.user_id where u.id='" . $row['knowreferedto'] ."' group by u.id   ");
			//$row_treferedto = $treferedto->fetch_array() ;
			//$treferto = $link->query("SELECT * FROM `user_people` WHERE `id` = '" . $row['knowtorefer'] ."'  ");
			//$row_referto = $treferto->fetch_array() ;
		
	$treferedto = $this->db->query(" SELECT u.*, sum(r.ranking) as ranking  FROM user_people as u inner join user_rating as r on u.id=r.user_id where u.id='" . $row['knowreferedto'] ."' group by u.id   ");
	$row_treferedto = $treferedto->row() ;
	$treferto = $this->db->query("SELECT * FROM  user_people  WHERE `id` = '" . $row['knowtorefer'] ."'  ");
	  
	$row_referto = $treferto->row() ; 
	$rate = $row['ranking']; 
	$starcount =  intval ( $rate/5 ) ;  
	$ratingstr ='';
	for($sc=0; $sc < $starcount; $sc++)
	{
		$ratingstr .="<i class='fa fa-star'></i>";
	}
	$rate2 = $row_treferedto->ranking ; 
	$starcount2 =  intval ( $rate2/5 ) ;  
	$ratingstr2 ='';
	for($sc=0; $sc < $starcount2; $sc++)
	{
		$ratingstr2 .="<i class='fa fa-star'></i>";
	}
	if( $rate < 5 )
	{
		$ratingstr2 ="";
    } 		
		if($row_referto != null)
		 {
			$html .= "<tr id='row-" . $row['id']  . "'>
                            <td>". $row_referto->client_name  .  "<br/>" . $row_referto->client_email  . 
                            " <span class='tooltip refsummary' data-tooltip-content='#tooltip_content$i'><i class='fa fa-info-circle '></i></span>  
                            <div class='tooltip_templates'>
                                <span id='tooltip_content$i'>
                                     <strong>Name:".  $row_referto->client_name  ."</strong><br/> $ratingstr <br/>
                                     <strong>Profession:<br/>". ($row_referto->client_profession  != '' ?$row_referto->client_profession : 'Not specified') ."</strong><br/>
                                     <strong>Phone:". $row_referto->client_phone  ."</strong><br/>
                                     <strong>Email:". $row_referto->client_email   ."</strong><br/>
                                     <strong>Location:". $row_referto->client_location  ."</strong><br/>
                                     <strong>Client Note:". $row_referto->client_note  ."</strong><br/>
                                </span>
                            </div>
                            </td>";
                     $html .= "<td>";
                             
                    if($row['partnerid'] != $uid)
                    {  
                        $html .= "<span id='tooltip_partner$i'>
									<strong>". $row['username'] ."</strong><br/> 
                                    <strong>". $row['user_phone'] ."</strong><br/>
                                    <strong>". $row['user_email'] ."</strong><br/> 
                                </span> ";
								$cc1= $row['user_email'];
								$ccname1 = $row['username']; 
                     }
                     else 
                     {
                         $html .= "<p class=' text-center'>This person is my contact.</p>";
                         $cc1= '';
                         $ccname1 = '' ;
                    }
                    $html .= "</td>
                    <td>" . $row_treferedto->client_name  ."<br/>" . $row_treferedto->client_email   . 
                    "<span class='tooltip refsummary' data-tooltip-content='#rtooltip_content$i'><i class='fa fa-info-circle  '></i></span>
                        <div class='tooltip_templates'>
                                <span id='rtooltip_content$i'>
                                     <strong>Name:".  $row_treferedto->client_name     ."</strong><br/> $ratingstr2 <br/>
                                     <strong>Profession:<br/>". $row_treferedto->client_profession  ."</strong><br/>
                                     <strong>Phone:". $row_treferedto->client_phone  ."</strong><br/>
                                     <strong>Email:". $row_treferedto->client_email  ."</strong><br/>
                                     <strong>Location:". $row_treferedto->client_location  ."</strong><br/>
                                     <strong>Client Note:". $row_treferedto->client_note  ."</strong><br/>
                                </span> 
                        </div>
                    </td>
                    <td>
                        <span data-to='" . $row_treferedto->client_email   .
                            "' data-introto='". $row_treferedto->client_name  .    
                            "' data-clientid='". $row_treferedto->id  . 	
                            "' data-introprofession='". $row_treferedto->client_profession  . 	
                            "' data-introphone='". $row_treferedto->client_phone  .
                            "' data-suggestid='". $row_referto->id  .  						
                            "' data-suggestname='". $row_referto->client_name  . 
                            "' data-suggestemail='" . $row_referto->client_email  . 
                            "' data-profession='" . $row_referto->client_profession  . 
                            "' data-phone='" . $row_referto->client_phone  .
							"' data-zip='" . $row_referto->client_zip .
                            "' data-refintroid='" . $row['id'] .
                            "' data-cc1='" .  $cc1 . "' data-ccname1='" .  $ccname1  . "' data-uid='" .  $uid  . "' class='btn-primary btn btn-xs btncallmailsender '   >Send Mail</span> 
                            <span data-refintroid='" . $row['id'] . "' data-uid='" .  $uid  . "' class='btn-danger btn btn-xs btnremsuggestion'>Remove</span> 
							<br/><br/><input name='refintro[]' data-introid='" . $row['id'] . "'  type='checkbox' > Mark For Removal
                            </td>
                        </tr>";
         }  
 	
      $i++; 
} 

 
 $html .=   "<tr><td colspan='3'></td><td>" .
 " <input   data-uid='19' type='submit' class='btn btn-remove showreferrals' id='remintrosuggest' value='Delete Selected'></td></tr>";
 $html .=   "</table>";
 
 echo $html; 

/*
	  
		if( $offset < ($lastpage - 10 ) )
        $html .= "<li><a data-key='$key' data-uid='$uid'  data-func='next' title='Show last few pages' data-pg='$lastpage'> ... </a></li>";
        $html .= "<li> <input type='text' id='gotopageno' style='width: 120px; height: 32px; margin-top: 2px; margin-right: 5px; float: left; display: inline-block;' class= 'form-control' placeholder= 'Go to page ...' > </li>";
        $html .= "<li> <input type='button' data-uid='$uid'  id='gopage' value='Go' style='width: 50px; float: left; height: 32px; margin-top: 2px; display: inline-block;  background-color: #2e353d; color: #fff;' class= 'btn '  > </li>";
        $html .= "<li><a data-key='$key' data-uid='$uid'  data-func='next' title='Next Page' data-pg='$next'>»</a></li>";  
        $html .= "</ul></td></tr>"; 
       
		*/
		  
	$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/referrals/'  ;	
	$pager_config['total_rows'] = $referrals['totalpage'];
	
	$choice = $referrals["totalpage"] ;
	$pager_config["num_links"] = round($choice); 
				  
	$this->pagination->initialize($pager_config);
	
	
	
	echo $this->pagination->create_links();	

		 
		
else: 
  ?>
  
				<h2>No Referral Suggestion Found!</h2>
				<div class='hr-sm'></div>
                <p class='content medium'>So far you have no  matching referral suggestions!</p>
	  
  <?php
endif;

	
	?> 
	 
	
	<div class="modal fade " id="suggestedreferral" tabindex="-1" role="dialog" aria-labelledby="suggestedref">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="suggestedref">Referral Introduction</h4>
      </div>
      <div class="modal-body" id='suggreff' style='padding: 15px!important; height: 540px; overflow-y: scroll; '> 
		  <div class="form-group">
			 <h2>Contact introduction details</h2>
		  </div>
          <div class='row'>
          <div class='col-md-6'>
                <div class="panel panel-primary">
					<div class="panel-heading"><strong>Introducee</strong></div>
					<div class="panel-body">
						<p><i class='fa fa-user dark'></i> <span id="spconnectname" ></span><br/>
						 <i class='fa fa-envelope dark'></i> <span id="spconnectemail" ></span><br/>
						 <i class='fa fa-phone dark'></i> <span id="spconnectphone" ></span><br/>
						 <i class='fa fa-briefcase  dark'></i> <span id="spconnectprofession" ></span>
						 </p> 
						<input type='hidden' id="connectname"  />
						<input type='hidden' id="connectemail" />
						<input type='hidden' id="connectphone" />
						<input type="hidden"  id="connectprofession" value="">
				   </div>
                </div> 
            </div> 
            <div class='col-md-6'>
                <div class="panel panel-success">
                <div class="panel-heading"><strong>Contact Receipent</strong></div>
                <div class="panel-body">
                    <p id='introduceto' ></p>
                </div>
                </div> 
            </div> 
          </div>
          <div class='row'>
          <div class='col-md-12'>
            <h3 class=" text-center">Below are the available trigger mails. Select the one email</h3> 
            <br/><br/>
         </div></div>
          
            <?php
            
                $rowindex=1;
                echo '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';
                $counter=1 ;
                if( sizeof($mailtemplates)  > 0)
                {
					foreach ($mailtemplates as $item )
                    { 
                        $mailbody = html_entity_decode(  $item['mailbody'] )  ;
                        if( strcasecmp($item['mailtype'] , 'Introduction Mail' ) == 0 )
                        {
                            echo '<div class="panel panel-default panel-emailtemplates">
                                    <div class="panel-heading" role="tab" id="heading' . $counter .'">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $counter .'" aria-expanded="true" aria-controls="collapse' . $counter .'">
                                                '. $item['template'] .'
                                            </a>
                                        </h4>
                                        </div>
                                        <div id="collapse' . $counter .'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading' . $counter .'">
                                        <div class="panel-body">';
                                        $mailbody = str_replace("{receipent}","<span class='tplvar_receipent'>name</span>", $mailbody ) ;
                                        $mailbody = str_replace("{user}","<span class='tplvar_user'>" . $this->session->name   ."</span>", $mailbody ) ;
										$mailbody = str_replace("{user_email}","<span class='tplvar_user_email'>" . $this->session->email   ."</span>", $mailbody ) ;
                                        $mailbody = str_replace("{sender_phone}","<span class='tplvar_user_phone'>" . $this->session->phone   ."</span>", $mailbody ) ;
										$mailbody = str_replace("{rated_by}","<span class='tplvar_rated_by'>name</span>", $mailbody ) ;
                                        $mailbody = str_replace("{introducee}","<span class='tplvar_introducee'>introducee</span>", $mailbody ) ;
                                        $mailbody = str_replace("{introducee_profession}","<span class='tplvar_introducee_profession'></span>", $mailbody ) ;
                                        $mailbody = str_replace("{introducee_email}","<span class='tplvar_introducee_email'></span>", $mailbody ) ;
                                        $mailbody = str_replace("{introducee_phone}","<span class='tplvar_introducee_phone'></span>", $mailbody ) ;
										$mailbody = str_replace("{introducee_zip}","<span class='tplvar_introducee_zip'></span>", $mailbody ) ;
                                         echo "<div style='visibility: hidden; display: none;' class='email_editor_text' id='email_editor_text'>" . $mailbody. "</div>";
										echo "<div class='email_editor' name='email_editor' id='email_editor'></div>" . '<button data-tid="' . $item['id'] . '" type="button" data-dismiss="modal"  class="btn btn-success sendintromail">Send Mail</button>
                                        </div>
                                        </div>
                                    </div>'; 
                                   } 
                                    $counter++;
                                }
                            }  
                            else 
                                echo '<h4 class="text-center">No email template has been configured yet. Please contact admin!</h4>';  
                            
                                echo "</div>"; 
                         ?>  
      </div>
      <div class="modal-footer"> 
		<input type="hidden"  id="receipent" value=""/> 
		<input type="hidden"  id="receipentname" value=""/> 
		<input type="hidden"  id="receipentphone" value=""/> 
		<input type="hidden"  id="receipentprof" value=""/> 
		<input type="hidden"  id="suggestid" value=""/> 
		<input type='hidden' id='mailogid' value=''/>
		<input type='hidden' id='clientid' value=''/>
		<input type='hidden' id='cc1' value=''/>
		<input type='hidden' id='ccname1' value=''/> 
      </div>
    </div>
  </div>
</div> 
</div>    
</div>  
</div> <!-- row -->
</div> <!-- container -->