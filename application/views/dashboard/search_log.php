<div class='col-md-9'> 
<div class='profile-item'> 
	<h4>Search Log</h4>
	<div class='hr-sm '></div>
	 
 <?php 
	 $html = "<table class='table table-responsive'>";
	 $html .= "<tr ><th>User Name</th><th>Vocation</th><th>Location</th><th>Date & Time</th></tr>"  ; 
	foreach($logs->result() as  $item) 
	{
		$html .= "<tr  >"  .
		"<td>" . $item->username . "</td>" .
		"<td>" . $item->vocation . "</td>" .
		"<td>" . $item->location . "</td>" .
		"<td>" . $item->created_at . "</td>" . 
		 "</tr>"; 
    }
	$html .= "</table>"; 
	
	echo $html;
	
	?>	 
 	
  </div>
	
  </div>
</div> <!-- row -->
</div> <!-- container -->  