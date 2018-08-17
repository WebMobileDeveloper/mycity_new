       <div id="menu2" class="tab-pane fade">
                        <div class="top-head">
                            <div class="col-xs-12 col-sm-8">
                                <h4>ENTER PEOPLE YOU KNOW DETAILS 
								<?php //if($help_data[9]['helpvideo']!=""){ ?>
								<a href="#<?php //echo $help_data[9]['helpvideo']; ?>" target="_blank" ><i class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;"> Help</span></a>
								<?php //} ?>
								</h4>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="col-xs-12 col-sm-6 padd-5"> 
                            <div class="col-xs-12"><label class="custom-label">Name:</label></div>
                            <div class="col-xs-12"> 
								<input type="text" class="form-control client_name" name="e_name" required="">
							</div>
                        </div>
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <div class="col-sm-12 col-xs-12"><label class="custom-label">Vocation(s):</label></div>
                            <div class="col-sm-12 col-xs-12">
                                <select data-placeholder='Choose vocations ...' multiple class="form-control client_pro" name="e_profession[]" id="e_prof"  > 
                                    <?php
                                    foreach ($vocations as $vocation) {
                                        echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                                <small class="pull-right">(Enter comma seperated)</small>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <div class="col-sm-12 col-xs-12"><label class="custom-label">Phone:</label></div>
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" class="form-control client_ph" name="e_phone" required="">
                            </div>
                        </div> 
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <div class="col-sm-12 col-xs-12"><label class="custom-label">Email:</label></div>
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" class="form-control client_email newcontactemail" name="e_email" required="">
                            </div>
                        </div> 
                        <div class="col-xs-12 col-sm-6 padd-5">
                            <div class="col-sm-12 col-xs-12">
								<label class="custom-label">Lifestyle:<br></label>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <select  name="e_lifestyle" class="form-control client_lifestyle" id=""> 
								<option value='0'>Select Lifestyle</option>
									<?php
                            foreach ($lifestyles as $lifestyle) {
                                                echo "<option value='" . $lifestyle['id'] . "'>" . $lifestyle['name'] . "</option>";
                                            }
                                    ?>
								</select>								
                            </div> 
							<div class="col-sm-12 col-xs-12">
								<label class="custom-label">Location(s):<br></label>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" name="e_location" class="form-control client_location" id="">
                                <small class="pull-right">(Enter comma separated)</small>
                            </div>
							<div class="col-sm-12 col-xs-12">
								<label class="custom-label">Zip:<br></label>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <input type="text" name="e_zip" class="form-control client_zip" id=""> 
                            </div>
							<div class="col-xs-12 col-sm-12 padd-5">
							<div class="col-sm-12 col-xs-12"><label class="custom-label">Note(s):</label></div>
                            <div class="col-sm-12 col-xs-12">
								<input type="text" class="form-control client_note" name="e_note" required="">
								<small class="pull-right">(Enter comma separated)</small>
                            </div>
							</div>
						<div class="col-sm-12 col-xs-12 <?php echo $hideClass; ?> ">
							<label class="custom-label">Groups:<br></label>
						</div>
						<div class="col-sm-12 col-xs-12 <?php echo $hideClass; ?> ">
							<select class="form-control user_grp">
								<?php
									foreach ($getGroups as $item)
									{
										$sel = $userGrp == $item['id'] ? "selected='selected' " : "";
                                        if ($_user_role == 'admin')
										{
                                            $dis = "";
                                        }
										else
										{
                                            //$dis = $userGrp == $item['id'] ? "" : "disabled='disabled' ";
                                        }
                                        echo "<option value='" . $item['id'] . "' " . $sel . $dis . ">" . $item['name'] . "</option>";
                                    }
                                    ?>
							</select>
                        </div>
                    </div>
					<?php
                        //if ($_user_role == 'admin' OR $user_pkg != 'free') {
                        echo '<div class="col-xs-12 col-sm-6  padd-5">';
                        $i = 1;
						$textquestion ='';
                        foreach ($ques_data as $item) {
                            $name = "rating0" . $i;
                            $q_id = $item['id'];
                            $question = $item['question'];
                            $q_type = $item['question_type'];
							if($q_type == "rating"):
							 
                            echo "
                                <div class='col-xs-12 col-sm-12 padd-5 pad-bor'>
                                <div class='col-sm-6 padd-5 col-xs-12'><label class='custom-label'>$question</label></div>
                                <div class='col-sm-6 col-xs-12 padd-5'>
                                <span class='starRating main user_ques_main' data-ques='$q_id'>";
                                  echo "<input id='rating01$i' type='radio' class='user_ques' name='$name' value='5' checked><label for='rating01$i'><span></span></label><label for='rating01$i'>5</label>
                                        <input id='rating02$i' type='radio' class='user_ques' name='$name' value='4'><label for='rating02$i'><span></span></label>
                                        <label for='rating02$i'>4</label>
                                        <input id='rating03$i' type='radio'  class='user_ques' name='$name' value='3'><label for='rating03$i'><span></span></label>
                                        <label for='rating03$i'>3</label>
                                        <input id='rating04$i' type='radio'  class='user_ques' name='$name' value='2'><label for='rating04$i'><span></span></label>
                                        <label for='rating04$i'>2</label>
                                        <input id='rating05$i' type='radio' class='user_ques' name='$name' value='1'><label for='rating05$i'><span></span></label>
                                        <label for='rating05$i'>1</label>"; 
                                echo "</span>
                                </div>
                            </div>
                                </span>";
							else: 
								$textquestion = "<div class='col-xs-12 col-sm-12  padd-5'>
								 <div class=col-sm-12 col-xs-12>
									<label class='custom-label'>$question</label> 
								</div>
								<div class=col-sm-12 col-xs-12> 
                                <span class='starRating main user_ques_main' data-ques='$q_id'>";
								 
								$textquestion .= 
								"<select id='answer$q_id' data-ques='$q_id'  name='$name' 
								data-placeholder='Choose vocations ...' class='chosen-select user_ques_text_add' multiple  >
								<option value=''></option>";
								 
								foreach($vocations as $vocitem)
								{
									$textquestion .= "<option value='" .$vocitem['name']  . "'>" . $vocitem['name'] . "</option>";
								}
								$textquestion .= "</select>
							<label for='rating01$i'><span></span></label> 
                            </span></div></div>";
							endif;
                            $i++;
                        }
                        echo '</div>';
                        //}
                    echo $textquestion;    
						?> 
				<div class="col-sm-12">
					<div class="col-sm-11 col-xs-12 "> 
					<input type="button" value="Submit" class="btnblock pull-right addClientUser"></div>
				</div>
				<?php // people you know details ?>
				<div class="col-xs-12 people-know">
				<hr/>
				</div>
				<div class="col-xs-12 people-know">
					<div class="col-xs-4">
						<input type="text"  placeholder="Search By Name" class="form-control srchRefName">
					</div>
					<div class="col-xs-4">
						<select class="form-control" id="locateVoc" size="1">
							<option value="">Search by Vocation</option>
							<?php
								foreach ($vocations as $vocation)
								{
									echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
                                }
							?>
						</select>
					</div>
					<div class="col-xs-4"><button class="btnblock srchRef">Search</button></div>
				</div>
				<div class="col-xs-12 people-know">
					<div class="col-xs-12">
						<h4><?php echo $text ?></h4>
						</div>
						<div class="col-xs-12" style="overflow-x: auto;">
							<table class="table table-responsive">
								<thead>
                                    <tr>
                                        <th>Reference Name</th>
                                        <th>Vocation</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Group</th>
                                        <?php if ($_user_role == 'admin') { ?>
                                            <th>Package</th>
                                            <th>References</th>
                                            <th>Joined On</th>
                                        <?php } else { ?>
                                            <th>Ratings</th>
                                        <?php } ?>
                                        <th>Action  
			<?php //if($help_data[9]['helpvideo']!=""){ ?>
			   <a href="#<?php //echo $help_data[9]['helpvideo']; ?>" target="_blank" ><i class='glyphicon glyphicon-arrow-right' ></i><span style="color:red;"> Help</span></a>
			<?php //} ?></th>
         </tr>
        </thead>
          <tbody class="clientsUsers"></tbody>
             </table> 
         <div class="modal fade mine-modal" id="modaltriggermailselect" tabindex="-1" role="dialog" aria-labelledby="triggermailselect">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="suggestedref">Select A Trigger Mail</h4>
                            </div>
                            <div class="modal-body text-left" id='triggermailselect'>
                         <?php 
                            $rowindex=1;
                            echo '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">'; 
                            $counter=1 ; 
                            if( sizeof($mailtemplates)  > 0)
                            {
                                echo '<h4 class="text-center">Below are the available trigger mails. Select the one email</h4>';
                                foreach ($mailtemplates as $item )
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
                                            '. $item['mailbody'] .' 
                                            <button data-tid="' . $item['id'] . '" type="button" data-dismiss="modal"  class="btn btn-success btnsendtrigger">Send Mail</button>
                                        </div>
                                        </div>
                                    </div>';
                                    $counter++;
                                }
                            }
                               	
                            else 
                                echo '<h4 class="text-center">No email template has been configured yet. Please contact admin!</h4>';  
                            
                                echo "</div>"; 
                         ?>
                            </div> 
                            </div>
                        </div>
                        </div>  

                            </div>
                        </div>
                    </div>

