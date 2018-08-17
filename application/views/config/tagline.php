<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$page_content='';
if($page_data != null)
{
	$row = $page_data->row(); 
	$page_content= $row->page_content;
}

?> 
<div class='col-md-9'>
	 <div class='profile-item'> 
				<h2>Website Tagline</h2>
				<div class='hr-sm'></div>
				<div class='row marg1'>
				
				 <?php
				echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";
				if($this->session->msg_error  )
				{
					echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error . "</p>";
					$this->session->unset_userdata('msg_error');
				} 
				echo "</div></div>";
			?>
			
			
			
				<?php echo form_open(); ?>
				 
				<div class="col-md-12">
					<div class='form-group'>
						<textarea type="text" class='tagline' name="tagline"><?php echo $page_content; ?></textarea>
					</div>
					<div class='form-group'>
					<button type="submit" name='save_tagline' value='save' class="btn btn-primary btnblock saveTagline">Save</button>
					</div>
				</div>
									
									
				<?php echo form_close(); ?>
				 </div> 
	  </div> 
	</div>  
	</div><!-- row -->
</div><!-- container -->