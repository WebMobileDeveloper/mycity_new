<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$page_content='';
$page_title ='';
if($page_data != null)
{
	$row = $page_data->row(); 
	$page_content= $row->page_content;
	$page_title = $row->page_title;
}

?> 


<div class='col-md-9'>

                <div class='profile-item'>  
			   <h2>Edit About Us Page</h2>
			   <div class='hr-sm'></div>
			   <div class='row'> 
			   <?php echo form_open(); ?>
			     <div class="col-md-12">
					<div class='form-group'>
						<label>Title:</label>
						<input type='text' class='form-control' name='title' value='<?php echo $page_title;?>' />
					</div>
					
					<div class='form-group'>
						<label>Page Content:</label>
						<textarea type="text" class='aboutcontent' name="aboutcontent"><?php echo $page_content; ?></textarea>
					</div>
					<div class='form-group'>
					<button type="submit" name='save_about' value='save' class="btn btn-primary btnblock saveTagline">Save</button>
					</div>
				</div>
			   <?php echo form_close(); ?>
			   </div>
		</div>	   

</div>  
</div><!-- row -->
</div><!-- container -->

 