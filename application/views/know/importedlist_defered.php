<div class='col-md-9'>
	<div class='profile-item'> 
				<h2>Knows Imported From Excel File</h2>
				<div class='hr-sm'></div>
                   
	
<?php 
if( isset($importedknows) ):

echo "<table class='table table-responsive'>";
foreach($importedknows as $item)
{
	echo "<tr>";
	echo "<td>"; 
	echo $item['client_name'];
	echo "</td>";
	echo "<td>"; 
	echo $item['client_email'];
	echo "</td>";
	echo "<td>"; 
	echo $item['client_profession'];
	echo "</td>";
	echo "<td>"; 
	echo ($item['isimported'] > 0   ? "Imported" : "Record Exists" ) ;
	echo "</td>";
	echo "</tr>";
	
}
echo "</table>";
else:
	echo "<p style='margin-top: 20px;' class='alert alertinfofix '>No file to import.</p>";

endif;
?> 	

  
  
  </div>                
</div>  
</div> <!-- row -->
</div> <!-- container --> 
 