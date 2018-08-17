<div class='col-md-9'>
 
	<?php 
		
		if($activities->num_rows() >  0) 
		{
			?>
			<div  class="profile-summary"> 
			<h2 class=''> 3 Touch Program Activities Log</h2>
			<div class="panel-body" style='max-height: 640px; overflow-y:scroll'>
			<div id='3t_activities_log'>
			<div class="tl-box" >
			<ul class='program-tl'>
			<?php 
			
			 
			foreach($activities->result() as $item)
			{
				echo "<li ><span></span>" ;
				echo "<div class='title'>Relation Name: " . $item->e . "</div>" . 
					"<div class='info'>" . $item->a ;
				echo "<br/><hr/><a data-toggle='tab'  href='#menu75' data-id='" .  $item->d . 
				"'  data-name='" . $item->e . "' data-prog='" . $pid  . 
				"' class='btn btn-primary btn-xs gotrack3tprogress'>View</a>" ;
				echo "</div>" . 
                    "<div class='time' >" .
					"<span>" . $item->c . "</span>" .
                    "</div>" .
					" </li>" ; 
				 
					 
			}   
			 
			
			?>
			</ul>
			</div>
			</div>
			</div> 
			</div> 
			
			<?php 
		}
		else 
		{
			?>
			<div  class="profile-summary"> 
			<h2 class='text-center'>You have not done any activity in the selected program.</h2>
			 
			</div> 
			</div>  
			<?
		}
 
	?>
	
 </div>  
</div><!-- row -->
</div><!-- container -->