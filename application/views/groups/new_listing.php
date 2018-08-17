<?php 




?>
<div class='col-md-9'>   
	<div class='profile-item'>  
	   <h2>Manage New City Listing</h2>
	   <div class='hr-sm'></div>
		<?php 
		 
				echo "<div class='row'><div class='col-md-10 col-md-offset-1'>";
				if($this->session->msg_error  )
				{
					echo "<p class='  alertinfofix text-center marg1'> " . $this->session->msg_error . "</p>";
					$this->session->unset_userdata('msg_error');
				} 
				echo "</div></div>";
			 
			if(  $list_requests  != null)
			{
				echo "<table class='table table-condensed marg1'> <thead> <tr><th>City Name</th><th>Listing Requested By</th><th>Action</th>  </tr> </thead><tbody>";
            	foreach($list_requests->result() as $item) 
				{
					echo "<tr><td>" . $item->grp_name . "</td><td>" . $item->username .  "</td>";
					 
					if($item->islisted == 0)
					{
						echo "<td>New Request</td> ";
						echo "<td><a class='btn btn-primary btnlistcity' href='" . $base . "manage-groups/manage-new-listing/add/"  .  $item->id   . "' >Add</a>" ;
						echo "</td>";
						
					}
					else  if($item->islisted == 1)
					{
						echo "<td>City Listed</td> ";	
						echo "<td><a class='btn btn-danger btnlistcity' href='" . $base . "manage-groups/manage-new-listing/remove/"  .  $item->id   . "' >Remove</a>";
						echo "</td> ";
					} 
					
					echo "</tr>";
				 } 
				 echo  '</table>';
			} 
		 
		?>
	</div>  
	 
	</div>  
</div> <!-- row -->
</div> <!-- container -->  