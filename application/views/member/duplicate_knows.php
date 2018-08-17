<div class='col-md-9'> 
  
<div class='profile-item marg1'  > 
	<h2>Duplicate Referrals</h2>
	<div class='hr-sm'></div> 
	 
	<?php 
		if($duplicates['results'] !=null &&  $duplicates['results']->num_rows()  > 0 ):
		
		?>
		
		<table id="tbl_clients" class="display" style="font-size: 14px;width:100%">
		<thead>
			<tr>
				<th>Name</th>
				<th>Profession</th> 
				<th>Phone</th>
				<th>Email</th> 
				<th  >Location</th>
				<th>Group</th>
				<th  >Ranking</th>
				<th>Action</th>
			</tr>
		</thead>
	<tbody> 
		
		<?php 
		foreach($duplicates['results']->result() as $item )
		{
			$id = $item->id;
			$username = $item->client_name;
			$profession = $item->client_profession; 
			$user_phone = $item->client_phone;
			$user_email = $item->client_email;
			$package= $item->client_location; 
			echo "<tr>
				<td>$username</td>
				<td>$profession</td> 
				<td>$user_phone</td>
				<td>$user_email</td> 
				<td  >Location</td>
				<td>Group</td>
				<td  >Ranking</td>
				<td>Action</td>
				</tr>";
				
		 
		}
		
		?>
		
		
		</tbody>
			<tfoot>
				<tr>
				<th>Name</th>
				<th>Profession</th> 
				<th>Phone</th>
				<th>Email</th> 
				<th  >Location</th>
				<th>Group</th>
				<th  >Ranking</th>
				<th>Action</th>
				</tr>
			</tfoot>
		</table>
		
		<?php 
		else:
		echo "<div class='marg1'><p class='alertinfofix'>No duplicate found!</p></div>";
		endif;
		?> 
</div> 

</div> 
  
</div> <!-- row -->
</div> <!-- container -->  
 