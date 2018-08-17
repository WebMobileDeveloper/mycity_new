<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	$targetvoc =''; 

	if($invitelog->num_rows() > 0)
	{
		$row = $invitelog->row();  
		
		$hash_id= $row->hash_id;
		
		?>
		<div class="container">
			<div class='row'>
				<div class='col-md-12'>
	 
			<div  class="profile-summary">
		<div id="profile" class="profile">
		<div class='row'>
			<div class='col-md-8'>  		
			<h1 ><?php echo ucwords($row->client_name);?></h1> 
			</div>
			 <div class='col-md-4 text-right'>   
			 </div>
		 </div> 
	  </div>  
	<div class='row text'>
		<div class="col-md-2 col-sm-12 col-xs-12 text-center">  
		</div> 
		<div class="col-md-7 col-sm-12 col-xs-12 text-left"> 
			<p><?php echo ($row->client_profession ==''? 'Not Specified':  $row->client_profession  ) ; ?></p>
			<p class='medium'><?php echo  $row->client_location . " - " .  $row->zip ; ?></p>
			<p class='medium'><strong><?php echo  $row->client_zip ; ?></strong></p> 
		</div>  
	</div>
  </div> 
   <?php 
   
		if($answer_rs->num_rows() > 0)
		{
			$targetvocs = $answer_rs->row_array();
			$targetvoc = $targetvocs['answer']; 
			 
			?>
			
			<div class='profile-item'> 
			<h2>Who do they want to meet by vocation</h2>
			<div class='hr-sm'></div>
				<p class='content medium'>
				<?php  echo $targetvoc; ?></p> 
		  </div>
	  
			
			<?php 
		}
   ?>
  
	
	 </div>
		 
	 
	
	
	</div><!-- row -->
</div><!-- container -->
<?php 
} 
else 
{
?>
<section id="sec_sucess_msg" class="next-sections form-large" style="display: block;  height: 500px;pointer-events: auto;">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center pad8">
					<h1 class="description">Oooops ... wrong profile!</h1> 
					<h2>No matching profile found!</h2>
                    <p class="description"></p>
                </div> 
            </div>
        </div>
</section>

<?php 	
}  
?> 
			
			

	 
 
	

	
	