<?php

$id =0;
$id = $helptitle =  $helpvideo = $helptext =  "";
if($faq_item != null)
{
 
	$row  = $faq_item->row(); 
	$id = $row->id;
	$helptitle = $row->helptitle;
	$helptext = $row->helptext;
	$helpvideo = $row->helpvideo;
	
	
}

?>
<div class='col-md-9'> 
   
   <div  class='profile-item '>
	<h3>Help Instructions</h3>
	<div class='hr-sm'></div> 
		<?php echo form_open() ;?>
		<div class='row'>
		 <?php
				echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";
				if($this->session->msg_error  )
				{
					echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error . "</p>";
					$this->session->unset_userdata('msg_error');
				} 
				echo "</div></div>";
			?>
			
			
			<div class="col-sm-12 marg1">
				<label>Help Title:</label> 
				<input name='title' required placeholder="Enter a title/heading for help content" class="form-control help_title"  type="text" value='<?php echo $helptitle; ?>'>
			</div> 
			<div class="col-sm-12 marg1">
				<label>?</label>
				<input name='video_url' placeholder="Enter a video Link to hyperlink it" class="form-control help_ques"  type="text" value='<?php echo $helpvideo; ?>'>
			</div> 	
			<div class="col-sm-12 marg1">
				<label>Help Content:</label>
				<textarea required name='help_text' class="form-control help_content" placeholder="Complete help explanation ..."><?php echo $helptext; ?></textarea>
			</div> 
			<div class="col-sm-12 marg1">
				<button type='submit' name='save_faq' value='save' class="btn btn-primary btnblock">Submit</button>
				<input type='hidden' name='faqid' value='<?php echo $id; ?>'/>
			</div> 
		</div>  
		<?php echo form_close() ;?>
   </div>
   
   
   <div  class='profile-item '>
	<h3>Help Instructions</h3>
	<div class='hr-sm'></div> 
	
   <div id="helptable">
	<?php 
		echo "<table class='table'>";
		echo "<tbody id='divtestimonials'>";
		 
		foreach( $allfaqs->result() as $item)
		{
			echo "<tr >" ;
			echo "<td>" . $item->helptitle . "</td>";   
			echo "<td>" . substr( $item->helptext, 0,230) . " ... </td>"; 
			echo "<td>"; 
			echo "<a class='btn-primary btn btn-xs editFaq'  href='" . $base. "faqs/edit/" . $item->id . "' ><i class='fa fa-pencil'></i></a>";
			echo  " <button class=' btn-danger btn btn-xs rmvFaq' ><i class='fa fa-times-circle'></i></button>"; 
			echo "</td>"; 
			echo "</tr>";  	 
		} 
echo "</tbody></table>";		
	?>
   </div>
   </div>
   
</div>  
</div><!-- row -->
</div><!-- container -->

 