<?php 
	
  $id=0;
  $video_url = $video_summary= '';  
  if($edit_testimonial != null)
  {
	  $row = $edit_testimonial->row();
	  $id= $row->id;
	  $video_url = $row->videolink;
	  $video_summary = $row->summary;
	  
	  
  }
?>
<div class='col-md-9'>   
	<div class='profile-item'>  
	   <h2>Manage Testimonial Videos</h2>
	   <div class='hr-sm'></div> 
	   
	   <?php
				echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";
				if($this->session->msg_error  )
				{
					echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error . "</p>";
					$this->session->unset_userdata('msg_error');
				} 
				echo "</div></div>";
			?>
			
			
	   <?php echo form_open() ;?>
	   <div class='row marg1'>
	     <div class="col-md-12">  
			<div class="form-group">
				<label for="exampleInputEmail1">Testimonial Video Link:</label>
				<input type='text' class="form-control" name='testimonial_video' id='testimonial_video' required value='<?php echo $video_url; ?>' />
			</div>
			<div class="form-group">
				<label for="exampleInputEmail1">Help Video:</label>
				<textarea type='text' class="form-control testimonial_summary"  name='testimonial_summary' id='testimonial_summary'><?php echo $video_summary; ?></textarea>
			</div>
			<div class="form-group">
				<input type='hidden' name='edit_id' value='<?php echo $id; ?>'/>
				<button type='submit' name='btn_save_tmv' value='save' class='btn btn-primary' id='btnsavetestimonial' >Save</button>
				<a    class='btn btn-danger' href='<?php echo $base; ?>testimonials/manage'  >Cancel</a>
			</div>   
		</div> </div> 
	<?php echo form_close() ;?>
	</div>  
	<div class='profile-item'>  
		   <h2>Testimonial Videos</h2>
		   <div class='hr-sm'></div> 

			<?php
				echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";
				if($this->session->msg_error_upd  )
				{
					echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error_upd . "</p>";
					$this->session->unset_userdata('msg_error_upd');
				} 
				echo "</div></div>";
			?>


		
				<table class="table table-responsive">
								<thead>
                                    <tr> 
                                       <th>Sorting Handle</th> 
                                        <th>Testimonial Video URL</th> 
                                        <th>Summary</th> 
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody id='divtestimonials'>
									<?php 
										$rowindex=1;
										foreach ($testimonials->result_array() as $item )
										{
											echo "<tr class='ui-state-default' data-id='" . $item['id'] . "'><td><i class='fa fa-arrows'></i></td><td id='tbody-" . $item['id'] . "'><span class='videolink" . $item['id'] . "'>" . $item['videolink'] ."</span>" ;
											echo "</td><td class='videosummary" . $item['id'] . "'>" .  $item['summary']  .  "</td><td>
											<a class='btn-primary btn btn-xs' href='". $base . "testimonials/manage/change/" . $item['id'] . "'><i class='fa fa-pencil'></i></a> 
											<button class='btn-danger btn btn-xs deletestimonial' data-id='" . $item['id'] . "'><i class='fa fa-trash'></i></button>

											";
											$rowindex++;
										}

										if($rowindex == 1)
										{
											echo '<tr><td colspan="4">No testimonial exists!</td></tr>'; 
										}
										else 
										{
											echo '<tr><td colspan="4"><button class="btn btn-primary btn-xs btnsavesortingorder">Save Sorting Order</button></td></tr>'; 
										}
									?>
									</tbody>
                                </table>

	</div>
	   
	</div>  
</div> <!-- row -->
</div> <!-- container -->  