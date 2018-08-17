<div class='col-md-9'> 
<div class='profile-item'> 
	  <?php 
	   
	  
		$html  = '<h3 class="text-center">Weekly Performance Summary</h3>';
		$html .= '<p class="text-center">This report is for current week (from ' .   $performancedata['start_week']  . ' to '  .  $performancedata['current_week_end']   . ' ) </p><br/>';
		$html .= '<div class="row"><div class="col-md-4 col-sm-4 col-xs-6"><div class="tile_stats_count">' .
             '<span class="count_top"><i class="fa fa-user"></i> Referrals</span>' .
             '<div class="counter">'  .  $performancedata['currentweekcnt'] . '</div>' .
             '<span class="count_bottom"><i class="green">'  .  $performancedata['currentweekgrowthpc'] . ' % </i> From last Week</span>' .
             '</div></div>';   
             $html .= '<div class="col-md-4 col-sm-4 col-xs-6"><div class="tile_stats_count">' .
             '<span class="count_top"><i class="fa fa-user"></i> Referrals</span>' .
             '<div class="counter">'  .  $performancedata['currentweekrefsmailcnt'] . '</div>' .
             '<span class="count_bottom"><i class="green">'  .  $performancedata['cweekemailgrowthpc'] . ' % </i> From last Week</span>' .
             '</div></div>';  
             $html .= '<div class="col-md-4 col-sm-4 col-xs-6"><div class="tile_stats_count">' .
             ' <span class="count_top"><i class="fa fa-user"></i> Groups With Known Contacts</span>' .
             '<div class="counter">' .  $performancedata['groupcount'] .  '</div>' .
             ' <span class="count_bottom"><span class="btn btn-link pr-btn-showgroup" >Views Groups</span></span>' .
             ' </div></div>';  
             $html .= '<div class="col-md-4 col-sm-4 col-xs-6"><div class="tile_stats_count">' .
           '<span class="count_top"><i class="fa fa-user"></i> Trigger Mails Sent</span>' .
           ' <div class="counter">'  .  $performancedata['triggermailscount'] .   '</div>' .
           '<span class="count_bottom"><i class="green">This reflects total trigger mails sent so far.</span>' .
           ' </div></div></div>'; 
	echo $html ; 
		?>
</div>
  </div>
</div> <!-- row -->
</div> <!-- container -->  