<div class='col-md-9'> 
<div class='profile-item'> 
	   <h2>Reverse Tracking</h2>
				<div class='hr-sm'></div>
		<?php echo form_open(); ?>	
	   <div class='row'> 
			<div class="col-xs-12 col-md-3">
				<div class="form-group">
					<label>Phone &amp; Name:</label> 
					<input type="text" placeholder="Contact Name or Phone ..."  
					class="form-control search-control" name="keyword"> 
				</div> 
            </div> 
			<div class="col-xs-12 col-md-3 ">
						 <div class="form-group">
							<label>Location:</label>   
                            <select data-placeholder="Specify Cities" name="city" class='form-control search-control revtracklocation ' > 
                            <option value=''>--Select Location---</option> 
                            <?php 
                                foreach($groups->result() as $item)
								{
									echo "<option value='" .$item->grp_name    . "'>" . $item->grp_name . "</option>";
								}
                            ?>
                        </select>
                    </div> 
                </div> 
               
                <div class="col-xs-12 col-md-6  ">
                     <div class="form-group">
							<label>Select Vocations:</label> 
                            <select data-placeholder="Select vocations"   name='vocations[]' multiple class="form-control chosen-select"> 	 
										<option value=''>Vocation</option> 
										<?php 
										foreach($vocations->result() as $vocitem)
										{
											 echo "<option value='" .$vocitem->voc_name    . "'>" . $vocitem->voc_name . "</option>";
										}
										?>
							 </select> 
						</div> 
                 </div>  
            </div> 
		
	<div class='row'>
					<div class="col-xs-12 col-md-6">
						 <div class="form-group"> 
							<label>Know Tags:</label> 
                            <select data-placeholder='Specify Tags ...'  multiple  name="tags[]"  class="form-control chosen-select  " id="reversetracktags"> 
                            <?php
                               foreach($tags->result() as $item)
								{
									echo "<option value='" .$item->tagname    . "'>" . $item->tagname . "</option>";
								}
                            ?>
                            </select> 
                        </div> 
                    </div>  
					
					<div class="col-xs-12 col-md-6">
						<label>Know Lifestyles:</label> 
						<select data-placeholder="Select Lifestyles" name="lifestyles[]" class='chosen-select  ' multiple >
							<?php
								foreach ($lifestyles->result() as $lifestyle)
								{
									echo "<option value='" . $lifestyle->ls_name  . "'>" . $lifestyle->ls_name . "</option>";
								}
							?>
						</select>
						<small class="pull-right">(Multiple lifestyle can be selected)</small>
					</div>  
                </div>
				
			<div class='row'> 
					<div class="col-xs-12 col-md-6">
						<label>Zip:</label> 
						<input type="text" placeholder="Zip ..." class="form-control search-control" name="tbzip">  
					</div> 
				</div>
			<div class='row marg1'>	
			<div class="col-xs-12 col-md-6">
                    <button type='submit' name='btn_search' value='reverse_track' class="btn btn-primary btnblock reversetrackpartner">Search</button> 	
				</div> 
			</div>
			
			<?php echo form_close(); ?>	
</div>

 
	<?php 
	
	 
	
	if($reverse_maps != false && $reverse_maps['error'] != 10 ):
	
		$html ="<div class='profile-item marg1'>";
		$html .= "<table  " . 'id="tbl_clients" class="display" style="width:100%" >';
		$html .= "<thead><tr > <th>Know Information</th> <th>Partner/User Who Knows the Contact</th></tr> </thead><tbody> " ;
		
		foreach($reverse_maps['results']->result() as $item) 
		{
			$id= $item->id ;
			$client_name = $item->client_name ;
			$client_profession = $item->client_profession ;
			$client_phone =", <strong>Phone: </strong> " . $item->client_phone  ;
			$client_email =  $item->client_email ;
			$tags =  $item->tags ;
			if($item->ranking == null)
				$rate =   0 ;
			else 
				$rate = $item->ranking;
			
			$html .= "<tr id='$id'>".
                        "<td>"  . $client_name  . "<br/><strong>Email: </strong> " . 
                        $client_email .  "<span id='spanknowphone" . $item->knowid . "'>" . 
						$client_phone . "</span>" .
						"<span class='hidden' id='knowphone" . $item->knowid . "'><input class='inp-xs' id='tbknowphone" .$item->knowid . "' value='" . 
						$item->client_phone  . "'/>" .
						"<button class='btn-xs btnupdateknowphone' data-kid='" . $item->knowid . "' ><i class='fa fa-check'></i></button></span> " .
						"<span data-kid='" . $item->knowid . "' id='btneditphone" .$item->knowid . "' class='btn-xs btneditphone' title='Click to edit'><i class='fa fa-pencil'></i></span><br/>" .
                        "<strong>Profession:</strong> <span id='knowvocprint" . $item->knowid. "'>" . 
                        $client_profession  .  "</span> <span data-kid='" . $item->knowid . "' id='btneditvoc" . $item->knowid. "' class='btn-xs btneditvoc' title='Click to edit'><i class='fa fa-pencil'></i></span> <br/>" .
						"<div class='hidden editvoc_box' id='knowvoc" . $item->knowid . "'>" .
						"<div class='form-group'>" .
						"<label  >Select Vocation(s):</label>" .
						"<select  class='form-control reversevocs' data-placeholder='Specify Vocations ...'  multiple id='dbvoca" . $item->knowid . "' ></select>" .
						"</div>" .
						"<div class='form-group'>" .
						"<button class='btn btn-primary btnupdatevoc' data-kid='" . $item->knowid . "' >Update</button> " . 
						"<button class='btn btn-danger btn_cancelvoc' >Cancel</button>".
						"</div>" .  
						"</div>" .
						"<strong>City:</strong> <span id='knowcpr" . $item->knowid . "'>" .  $item->client_location . "</span> " .
						"<strong>ZIP:</strong> <span id='knowzpr" . $item->knowid . "'>"  .  $item->client_zip  . "</span>" . 
						"<div class='hidden edit_box' id='knowcityzip" . $item->knowid . "'>" .
						"<h5>Update City &amp; Zip</h5>" .
						"<div class='form-group'>" .
						"<label  >Select City:</label>" .
						"<select class='form-control' id='dbknowcity" . $item->knowid . "' > "  
						;
						
						$curr_grps = array_filter( explode(',', $item->client_location) ) ;
						
						$cityselected ='';
						foreach($groups->result() as $gitem)
						{
							$cityselected ='';
							if(sizeof($curr_grps ) > 0)
							{
								foreach($curr_grps as $key)
								{
									if($key == $gitem->grp_name)
									{
										$cityselected ='selected';
									}   
									$html .= '<option '. $cityselected . ' value="' . $gitem->grp_name . '">' . $gitem->grp_name . '</option>';
							
								} 
							}
							else 
							{
								$html .= '<option  value="' . $gitem->grp_name . '">' . $gitem->grp_name . '</option>';
						
							}
						
						} 
						
						$html .= "</select>" .
						"</div>" .
						"<div class='form-group'>" .
						"<label  >Zip Code:</label>" .
						"<input class='form-control' id='tbknowzip" . $item->knowid . "'   value='" .$item->client_zip   ."' />" .
						"</div>" .
						"<div class='form-group'>" .
						"<button class='btn btn-primary btnupdateknowcz' data-kid='" . $item->knowid . "' >Update</button> " . 
						"<button class='btn btn-danger btn_cancelcz' >Cancel</button>".
						"</div>" . 
						"</div>" .
						" <span data-kid='" . $item->knowid . "' id='btneditcz" . $item->knowid . "' class='btn-xs btneditcz' title='Click to edit'><i class='fa fa-pencil'></i></span>" .
						"<br/>" . 
                        "<strong>Tags:</strong> <span id='knowtagprint" . $item->knowid . "'>" .  ( $tags === null   ?  "Not Specified"   : $tags ) . "</span>" .
						"<span class='hidden' id='knowtag" . $item->knowid . "'><p>Select Tags:</p>" .
						"<select data-placeholder='Specify Tags ...'  multiple id='reversetrackedittags" . $item->knowid . "'  class='form-control reversetrackedittags'></select><br/><input type='hidden' id='oldtags" .  $item->knowid . "' value='" .  $tags ."' />" 
						." <button class='btn-xs btnupdateknowtag' data-kid='" . $item->knowid . "' >Update Tags</button></span>" .
						" <span data-kid='" . $item->knowid . "' id='btnedittag" . $item->knowid . "' class='btn-xs btnedittag' title='Click to edit'><i class='fa fa-pencil'></i></span><br/>" .   
						"<br/> <strong>User Rating:</strong> <span class='badge'>" . $rate . "</span></td>" .
                        
                        "<td style='text-align:left !important'>" . $item->username  . "<br/>"  . 
                        "<strong>Email: </strong>" . $item->user_email . " <strong>Phone:</strong> " . $item->user_phone ."<br/>" .
                        " <strong>User Package:</strong>" .   $item->user_pkg . 
						"<br/><button data-id='" . $item->id . "' class='btn btn-primary btn-sm vuconcount'>View Common Connections</button>" .
						"</td>" . 
                     "</tr> " ;
        }  
		$html .= '</tbody> </table></div>';	 
		echo $html;		

	
		$pager_config['total_rows'] = $reverse_maps['num_rows']; 
		$choice = $reverse_maps["num_rows"] / 10;
		$pager_config["num_links"] = ( round($choice) > 20 ? 20 : round($choice) )  ;  
		$this->pagination->initialize($pager_config); 
		echo $this->pagination->create_links(); 
	
	  
	if($reverse_maps["num_rows"] > 10):
	 
	?>
	  <form class="form-inline">
	  <div class="form-group">
		<label for="goto_page">Go to page: </label>
		<input type="text" class="form-control" id="goto_rvtk_page" placeholder="Page Number">
	  </div> 
	  <button type="button" data-tp='<?php echo $reverse_maps["num_rows"]; ?>' class="btn btn_rvtk_gotopage">Go</button>
	</form>
  
	<?php 
	 endif;
	 
	
	else:
	?>
	<div class='profile-item'> 
	  <h2>Reverse Tracking Result</h2>
	  <div class='hr-sm'></div>
	<?php 
	
	
	echo "<p class='alertinfofix marg1'>" .  $reverse_maps['errmsg'] . "</p>";
	
	?>
	</div>
	<?php 
	endif;
?>

<div class="modal fade" id="commonconnects" tabindex="-1" role="dialog" aria-labelledby="commonconnects" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style='height: 600px; overflow-y:none;'>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Common Connections</h4>
            </div>
            <div class="modal-body text-left "> 
              <div class='table-upper  ' style='height: 200px; overflow-y:scroll; border: 1px solid #efefef; margin-bottom: 10px'>
					<h5 class='hd-sm'>Members &amp; Common Connections Summary</h5> 
					<div class='cctable' ></div>
			  </div> 
			  <div class='table-upper  ' style='height: 220px; overflow-y:scroll; border: 1px solid #efefef; '>
					<h5 class='hd-sm'>Common Connections Summary</h5> 
					<div class='ccviewtable' ></div>
			  </div>
            </div>
            <div class="modal-footer ">
                <button class="btn btn-success" data-dismiss="modal" aria-label="Close" >Close</button>
            </div> 
          </div>
        </div>
</div>
  </div>
</div> <!-- row -->
</div> <!-- container -->  