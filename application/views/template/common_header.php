<div class='container-fluid top-promo-light'>
	<div class='row'>
		<div class='col-md-12 text-center'>
		<?php 
		
		$ci =& get_instance();
		$ci->load->model('Pagedata');
		$pageinfo= $ci->Pagedata->get_by_name("tagline"); 
		$row = $pageinfo->row(); 
		
		if( $row->page_content !='')
		{
			echo "<h5>" . $row->page_content . "</h5>";
		}
		else 
		{
		?>
		
		<h5 >Grow relationships | Grow referral partners | Grow sales | Join MyCity for free and we will show you how!</h5>
		<?php } ?>
	   </div>  
		</div>
</div> 