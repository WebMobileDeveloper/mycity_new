
<div style='background-color:#fff;min-height: 550px; padding-top: 40px;'>

<div class='container'>
	<div class='row'>
			<div class='col-md-8'>  
			  
			  <?php 

if($faq_item == null)
{
	?>
	<h2 >MyCity FAQs</h2>   
			  <p>
     Browse our instant guide pages.
			 </p>
			 
			 <p>
			 Use the page navigator on the right side to view important details and instruction for using MyCity.com.
			 </p>
			 <?php
}

else 
{
	
	$faq = $faq_item->row();
	  
	
}

?> 		 
</div>			   
			   
			 <div class='col-md-4'> 
<?php 
	$this->load->view( $faq_menu );
?>
			</div> 
		</div>  
	</div><!-- row -->
</div><!-- container -->

</div>
 