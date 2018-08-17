<?php
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php';
$posts = $link->query("SELECT * FROM `blog_posts` WHERE post_status='publish' ORDER By ID desc");
?>
<div id="fb-root"></div>
	<div id="contact" class="about">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h4 style="margin-bottom: 65px; margin-top: 150px; font-weight: bold;">Blog </h4>
				</div> 
			</div>
		</div>
	</div>
	<div class="container">
        <div class="row">
<div class="col-xs-12 col-sm-12 col-md-8"> 
<?php 
	if($posts->num_rows > 0)
	{
		while($row = $posts->fetch_array())
		{
			$post_title = $row['post_title']; 
			$post_content = $row['post_content'];
			$post_id = $row['id'];
			$post_date = date('m/d/Y',strtotime($row['post_date'] ));
			$comment_count =  $row['comment_count'];
			$comment_status =  $row['comment_status'];
			
			echo "<div class='postbox'><div class='posttitle'><h2 data-id='$post_id' class='btn-read-more'>" . $post_title . "</h2></div><div class='postmeta'><strong>Post date:</strong>" . $post_date . " <i class='fa fa-comment-o'></i> " .
					$comment_count . "</div>" .
					"<div class='postcontent'>" .  strip_tags(substr( $post_content , 0, 300))  .
					"<p><br/><br/><button data-id='$post_id' class='btn btn-default btn-read-more'>Read More</button></p>" .
					"</div>" .
					"</div>" ; 
			 
		}  
	} 

?>  
</div></div> </div>
			
<?php include("footer.php") ?>
