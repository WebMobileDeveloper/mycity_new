<div   class='container-fluid '>
	<div class='row '>
  <div id="contact" class="about" style="pointer-events: auto;">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h4 style="margin-bottom: 65px;  font-weight: bold;">Blogs</h4>
				</div> 
			</div>
		</div>
 </div> 
 <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2"> 
<?php 
	if($posts->num_rows() > 0)
	{
		foreach($posts->result() as $row  )
		{
			$post_title = $row->post_title ; 
			$post_content = $row->post_content ;
			$post_id = $row->id  ;
			$post_date = date('m/d/Y',strtotime($row->post_date  ));
			$comment_count =  $row->comment_count ;
			$comment_status =  $row->comment_status ;
			
			echo "<div class='postbox'><div class='posttitle'><h2 data-id='$post_id' class='btn-read-more'><a href='". $base. "blog/" . $post_id . "'>"  . $post_title . "</a></h2></div><div class='postmeta'><strong>Post date:</strong>" . $post_date . " <i class='fa fa-comment-o'></i> " .
					$comment_count . "</div>" .
					"<div class='postcontent'>" .  strip_tags(substr( $post_content , 0, 300))  .
					"<p><br/><br/><a href='".$base. "blog/" . $post_id . "' class='btn btn-default btn-read-more'>Read More</a></p>" .
					"</div>" .
					"</div>" ; 
			 
		}  
	} 

?>  
</div> 
  
</div><!-- row -->
</div><!-- container -->

 