<div class='col-md-9'> 

<div class='globalsearch'> 
	<?php echo form_open($base. 'my-network'  ); ?>
				<div class="row">
					 <div class="col-xs-12 col-md-12">
					<?php
					if ($this->session->role == 'admin')
					{
						echo  '<h3>Search Registered Member</h3>';
					}
					else
					{
						echo  '<h3>Search your existing contacts</h3>';
					}
					?> <hr/>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Specify Name" name='srchRefName' class="form-control srchRefName">
					</div>
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Entry Date" name='srchentryDate' class="form-control srchentryDate">
					</div>
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Email" name='srchemail' class="form-control srchemail">
					</div>
					
					<div class="col-xs-12 col-md-3">
						<input type="text"  placeholder="Phone number" name='srchPhone' class="form-control srchPhone">
					</div> 
				</div> 
				<div class="row">
				
				<div class="col-xs-12 col-md-3 padt10">
					<select data-placeholder="Specify Cities" name='filtercity[]'  id="filtercity" class='chosen-select' multiple > 
						<?php
							foreach ($groups->result() as $group)
								{
									echo "<option value='" . $group->grp_name  . "'>" . $group->grp_name . "</option>";
                                }
						?>
				</select>  
				</div> 
					<div class="col-xs-12 col-md-3 padt10">
						<input type="text"  placeholder="Specify Zip Code" class="form-control" name="srchZipCode">
					</div>
					
					<div class="col-xs-12 col-md-6 padt10">
						<select data-placeholder="Select Tags" name='filterTags[]' id="filterTags" class='chosen-select srchTags' multiple >
							<?php
								foreach ($tags->result() as $tag)
								{
									echo "<option value='" . $tag->tagname  . "'>" . $tag->tagname . "</option>";
                                }
							?>
						</select>
						<small class="pull-right">(Multiple tags can be selected)</small>
					</div> 

					
					</div> 
					<div class="row  "> 
					<div class="col-xs-12 col-md-5"> 
						<select data-placeholder="Select Lifestyles" name="filterLifestyle[]" class='chosen-select user_ques_text_add' multiple >
							 <?php
								foreach ($lifestyles->result() as $lifestyle)
								{
									echo "<option value='" . $lifestyle->ls_name  . "'>" . $lifestyle->ls_name  . "</option>";
                                }
							?>
						</select>
						<small class="pull-right">(Multiple lifestyle can be selected)</small>
					</div>
					
					<div class="col-xs-12 col-md-5">
                    <select data-placeholder="Select Vocations" name="locateVoc[]" class='chosen-select user_ques_text_add' multiple  >
						 
							<?php
								foreach ($vocations->result() as $vocation)
								{
									echo "<option value='" . $vocation->voc_name  . "'>" . $vocation->voc_name  . "</option>";
                                }
							?>
						</select><small class="pull-right">(Multiple vocations can be selected)</small>
					</div>
					<div class="col-xs-12 col-md-2 " style='padding-top: 10px;'>
					<button type='submit' value='search' name='btn_search' class="btn btn-primary btnblock srchRef">Search</button></div>
				</div>	 
<?php echo form_close(); ?>
</div>
 
<div class='profile-item marg1'> 
<a class='btn btn-primary' href='<?php echo $base; ?>my-network/add/'>Add New</a>
<table id="tbl_clients" class="display" style="width:100%">
	<thead>
		<tr>
			<th>Reference</th>
			<th>Vocation</th>
			<th>Phone</th>
			<th>Email</th>
			<th>Location</th>
			<th>Group</th>
			<th>Ratings</th>
			<th>Action</th>
		</tr>
	</thead>
<tbody> 
<?php

if(  isset($knows['results']->num_rows ) ):
foreach($knows['results']->result() as $item )
{
	$id = $item->id;
	$client_name = $item->client_name;
	$client_profession = $item->client_profession;
	$client_phone = $item->client_phone;
	$client_email = $item->client_email;
	$client_location = $item->client_location;
	$user_group = $item->user_group;
	$userGrpName = '';
	$userVocName = '';
	$user_ranking=$item->rank;
	$introducee='';
	$str = "abcdefghijklmnopqrstuvwxyz";
	$rand = substr(str_shuffle($str),0,3);
	
	echo "<tr id='$rand-$id'>
		<td>$client_name</td>
		<td>$client_profession</td>
		<td>$client_phone</td>
		<td>$client_email</td>
		<td>$client_location</td>
		<td>$userGrpName</td>
		<td>$user_ranking</td>
		<td>
			<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>
				<ul class='dropdown-menu'>
					<li class=close_drop'><a data-toggle='modal' data-target='#edit_people_details' class='editPeopleDetails'><i class='fa fa-pencil'></i> Edit Details</a></li>  
					<li><a class='btnselecttrigger' data-rname='$client_name' 
					data-introducee='' data-remid='$client_email' data-phone='$client_phone' 
					data-rpt='$id' ><i class='fa fa-envelope'></i> Message</a></li>  
					<li><a class='delUserClient' data-id='$id'><i class='fa fa-times-circle'></i> Remove Know</a></li>  
					<li><a class='view_comm_vocation' data-user='$id' ><i class='fa fa-link' data-toggle='tooltip' title='Common Vocations'></i> Check Vocations</a></li>     
				</ul>
			</div>
		</td>
		</tr>"; 	
	} 
	endif;
	?> 
	</tbody>
		<tfoot>
			<tr>
			   <th>Reference</th>
			   <th>Vocation</th>
			   <th>Phone</th>
			   <th>Email</th>
			   <th>Location</th>
			   <th>Group</th>
			   <th>Ratings</th>
			   <th>Action</th>
            </tr>
        </tfoot>
    </table>
<?php 
	 $pager_config['base_url'] = $this->config->item('base_url') . 'my-network';				
	 $pager_config['total_rows'] = $knows['num_rows'];
	 $choice = $knows["num_rows"] ;
	 
	 
	 $pager_config["num_links"] = 20; 
	 
	 $this->pagination->initialize($pager_config); 
	 echo $this->pagination->create_links();	

?>
<hr/>

<a class='btn btn-primary' href='<?php echo $base; ?>my-network/add/'>Add New</a>
</div>

</div>  
</div> <!-- row -->
</div> <!-- container --> 
 	
<div class="modal fade suggestconnectmodal" tabindex="-1" role="dialog" aria-labelledby="suggestwizard" id="suggestwizard">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Referral Suggestion Wizard</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style='height: 450px; overflow-y:scroll;text-align:left'>
					<div class='bs-wizard'>
						<div id='wizstep1' class="col-xs-4 bs-wizard-step disabled">
						  <div class="text-center bs-wizard-stepnum">Step 1</div>
						  <div class="progress"><div class="progress-bar"></div></div>
						  <a href="#" class="bs-wizard-dot"></a>
						  <div class="bs-wizard-info text-center">Search member by vocations</div>
						  <div class="form-group pad10  ">
							<select data-placeholder='Vocation' class="form-control chosen-select wiz_profession" name="wiz_profession" id="wiz_profession" >
                                <?php
                                foreach ($vocations->result() as $vocation)
                                {
									echo "<option value='" . $vocation->voc_name  . "'>" . $vocation->voc_name  . "</option>";
                                }
                                ?>
							</select>
						</div>
			<div class="form-group pad10  ">
			   <button class='btn btn-success btn-sm wiz_step1_show_member'>Show Connections</button>
		    </div>
		</div> 
        <div id='wizstep2' class="col-xs-4 bs-wizard-step disabled"><!-- complete -->
			<div class="text-center bs-wizard-stepnum">Step 2</div>
				<div class="progress"><div class="progress-bar"></div></div>
                    <a href="#" class="bs-wizard-dot"></a>
                    <div class="bs-wizard-info text-center">Select person to introduce</div>
                    <div class="form-group pad10">
				<select   class="form-control wiz_memberleft" id='wiz_memberleft'  name="wiz_memberleft">
				</select>
			</div>
		</div> 
			<div id='wizstep3' class="col-xs-4 bs-wizard-step disabled">
				<div class="text-center bs-wizard-stepnum">Step 3</div>
				<div class="progress"><div class="progress-bar"></div></div>
					<a href="#" class="bs-wizard-dot"></a>
				<div class="bs-wizard-info text-center">Select member who will receive introduction</div>
				<div class="form-group pad10"> 
					<input class="form-control wiz_memberright" id="provider-remote" />  
				</div>
				<div class="form-group pad10  ">
					<input type='hidden' id="rmid" /> 
					<input type='hidden' class="refereruid" value='<?php echo $this->session->id ;?>'/>  
					<input type='hidden' class="referername" value='<?php echo $this->session->name ;?>'/>  
					<input type='hidden' class="refereremail" value='<?php echo $this->session->email ;?>'/>  
					<input type='hidden' class="refererrole" value='<?php echo $this->session->role ;?>'/>  					
					<button data-rightid='' class='btn btn-success btn-sm wiz_step_show_summary'>Show Referral Summary</button>
				</div>
			</div> 
		</div> 
		<div class='text-center'>
			<span class='alertinfofix ref_directtodirectwizard'>If you know the person to introduce, switch to Direct to Direct Referral Wizard</span> 
		</div>
	   <div id='wiz_summary'></div> 
	</div> 
	<div class="modal-footer clearfix" >
			<button data-dismiss="modal"  class='btn btn-primary'>Cancel</button>
		</div> 
	   </div>
    </div>
</div> 
 

<div class="modal fade onetooneintroduction" tabindex="-1" role="dialog" aria-labelledby="onetooneintroduction" id="onetooneintroduction">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Direct to Direct Introduction Wizard</h2> 
                </div>
                <div class="modal-body modal-body-no-pad" style='height: 450px; overflow-y:scroll; text-align:left'>
					<div class='bs-wizard'>
						<div id='wizstep1' class="col-xs-6 bs-wizard-step disabled">
						 
						  <div class="bs-wizard-info text-center"><h5>Search member to introduce</h5></div>
						  <div class="form-group pad10  "> 
						  <input class="form-control dwiz_memberleft" id="dtdleftmember" />  
						</div> 
		</div>  
			<div id='wizstep3' class="col-xs-6 bs-wizard-step disabled">  
				<div class="bs-wizard-info text-center"><h5>Select member who will receive introduction</h5></div>
				<div class="form-group pad10">  
					<input class="form-control dwiz_memberright" id="dtdrightmember" />  
				</div>
				<div class="form-group pad10  ">
				<input type='hidden' id="lmid" /> 
					<input type='hidden' id="dtdlmid" /> 
					<input type='hidden' id="dtdrmid" /> 
					
				</div>
			</div> 
			
	   <div class='text-center'>
				<button data-rightid='' class='btn btn-success btn-sm dwiz_step_show_summary'>Show Referral Summary</button><br/>
		</div> 
		</div> 
		<div class='text-center'>
			<span class='alertinfofix  ref_wizard'  >If you want to search know by vocation first, switch to Referral Suggestion Wizard</span>
			 
		</div>
	   <div id='dtdwiz_summary'></div> 
	</div>
		<div class="modal-footer clearfix" >
			<button data-dismiss="modal"  class='btn btn-primary'>Cancel</button>
		</div> 
	   </div>
    </div>
</div> 


<div class="modal fade intromailtemplate" tabindex="-1" role="dialog" aria-labelledby="intromailtemplate"
            id="intromailtemplate">
            <div class="modal-dialog "  >
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" >Sample of Email Message</h2> 
                    </div>
                    <div class="modal-body text-left " style="height: 360px; overflow-y:scroll">
						<div id="intromailbody"></div>
                    </div>
                    <div class="modal-footer clearfix" >
                    <button   class="btn btn-primary wiz_send_referral_mail" >Send Mail</button>
			<button data-dismiss="modal"  class="btn btn-danger" >Cancel</button>
		</div>
     </div>
    </div>
</div>
 
 <div class="modal fade mine-modal" id="modaltriggermailselect" tabindex="-1" role="dialog" aria-labelledby="triggermailselect">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="suggestedref">Select A Trigger Mail</h4>
                            </div>
                            <div class="modal-body text-left" id='triggermailselect' style='height: 450px; overflow-y: scroll'>
                         <?php 
                            $rowindex=1;
                            echo '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">'; 
                            $counter=1 ; 
                            if( sizeof($mailtemplates)  > 0)
                            {
                                echo '<h4 class="text-center">Below are the available trigger mails. Select the one email</h4>';
                                foreach ($mailtemplates as $item )
                                {
                                   if( strcasecmp($item['mailtype'] , 'Introduction Mail' ) != 0 )
                                   {
                                          echo '<div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="heading' . $counter .'">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse' . $counter .'" aria-expanded="true" aria-controls="collapse' . $counter .'">
                                                '. $item['template'] .'
                                            </a>
                                        </h4>
                                        </div>
                                        <div id="collapse' . $counter .'" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading' . $counter .'">
                                        <div class="panel-body">
                                            '. html_entity_decode(  $item['mailbody'] ) .' 
                                            <button data-tid="' . $item['id'] . '" type="button" data-dismiss="modal"  class="btn btn-success btnsendtrigger">Send Mail</button>
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
                                <button type="button" class="btn btn-danger pull-right"    data-dismiss="modal" >Close</button>
                            </div> 
                            </div>
                        </div>
                        </div> 
						

   
		