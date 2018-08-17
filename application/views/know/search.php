<?php 

  
?>
<div class='col-md-9'>
	<div class='profile-item'> 
		<h2>Search &amp; Invite Knows to MyCity</h2>
		<div class='hr-sm'></div>
        <div class="row marg1"> 
		<?php echo form_open(); ?>
			<div class="col-xs-12 col-md-3">
				<input type="text"  placeholder="Specify Name" name='src_name' class="form-control src_name">
			</div>  
			<div class="col-xs-12 col-md-5">
				<select data-placeholder="Select Vocations" name="src_vocation" class='chosen-select src_vocation'    >
					<?php
					foreach ($vocations as $vocation)
					{
						echo "<option value='" . $vocation['name'] . "'>" . $vocation['name'] . "</option>";
					}
					?>
				</select>
				<small class="pull-right">(Multiple vocations can be selected)</small>
			</div> 
			<div class="col-xs-12 col-md-4" style='padding-top: 10px;'>
				<button type='submit' name='btn_search_knows' value='search_filter' class="btn btn-primary btnblock src_all_knows">Search</button>
			</div>
			<?php echo form_close(); ?>
		</div>     
  </div>   
<?php

 
if( $result != '' ):
?>
<div class='profile-item'> 
		<h2>Search Result</h2>
		<div class='hr-sm'></div>
        <div class="row marg1"> 
		<table class="display " style="width:100%" id="tbl-reminders">
		<thead>
			<tr>
			<td>ID</td><td>Name</td><td>Profession</td><td>Ranking</td><td>Location</td>
			</tr>
		</thead>
		<tbody>
		<?php 
		$pos=1;
		foreach($result->result() as $item)
		{
			echo "<tr>";
			echo "<td>" .  $pos . "</td>";
			echo "<td>" .  $item->client_name .  "</td>";
			echo "<td>" .  $item->client_profession .  "</td>";
			echo "<td>" .  $item->rating .  "</td>";
			echo "<td>" .  $item->client_location .  "</td>"; 
			echo "</tr>";
			$pos++;			
		} 
		?>
		</tbody>
		</table>
		</div> 
</div> 
<?php
endif;
?>
</div>  
</div> <!-- row -->
</div> <!-- container --> 
 