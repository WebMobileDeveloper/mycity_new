<?php
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php';
echo $_POST['id'];

if(isset($_POST['id']))
{
	$posts = $link->query("SELECT * FROM `blog_posts` WHERE post_status='publish' and id='" . $_POST['id'] . "' ORDER By ID desc");
	 
	if($posts->num_rows > 0)
	{
		$link->query("UPDATE blog_posts SET read_count= (read_count+1) WHERE id='" . $_POST['id'] . "' ");
	}
	 
}
else
{ 
	?>
	<script> window.location = 'blog.php';</script>
	<?php 
}
?>
<div id="fb-root"></div>
<div  class="post-reader">
    <div class="container">
        <div class="row"> 
		<div class="col-xs-12 col-sm-12 col-md-8 "> 
		<?php 
			$row = $posts->fetch_array();
			$post_title = $row['post_title']; 
			$post_content = $row['post_content'];
			$post_id = $row['id'];
			$post_date = date('m/d/Y',strtotime($row['post_date'] ));
			$comment_count =  $row['comment_count'];
			$comment_status =  $row['comment_status'];
			echo "<div class=''><h1  >" . $post_title . "</h1></div>". 
			"<div class='dp-meta'><strong>Post date:</strong>" . $post_date . " <i class='fa fa-comment-o'></i> " .
			$comment_count . "</div>" .
			"<div  >" .    $post_content  . 
			"</div>"  ;
		
		$comments = $link->query("SELECT * FROM `blog_comment` WHERE post_id='" . $_POST['id'] . "' and status='1' and isreplyfor='0' ORDER By id DESC");
		 
		if($comments->num_rows > 0 )
		{
			echo "<hr/>";
			echo "<div class='row'><div class='col-md-6'><h4>" .$comments->num_rows . " Comments</h4></div><div class='col-md-6'><a href='#comment'>Leave a reply</a></div></div>";
			while($commentrow = $comments->fetch_array())
			{
				echo "<div class='comment-box'><p class='txt-lg'>" .$commentrow['name'] . "</p>";
				echo "<p>" . $commentrow['comment'] . "</p>";
				
				echo "</div>";
			}
		} 
		?>
		<div id='comment'><hr/>
			<h3>Leave a Reply</h3>
			<p>Your email address will not be published. Required fields are marked *</p>
			<form id='commentform'>
				<div class="form-group">
					<label for="name">Name: *</label>
					<input type="text" required class="form-control" name="name" id="name" placeholder="Your Name">
				</div>
				<div class="form-group">
					<label for="email">Email: *</label>
					<input type="email" required class="form-control" name="email" id="email" placeholder="Your Email">
				</div>
				<div class="form-group">
					<label for="comment">Comment: *</label>
					<textarea required class="form-control" name="commentbody" id="commentbody" placeholder='Comment' ></textarea>
				</div>
				<div class='row'> 
					<div class="col-xs-12 col-md-3" >
						<input type="hidden" value='<?php echo $_POST['id']; ?>' id="postid" name="postid"/>
						<button type="button" id='postcomment' class="flatbutton">Post Comment</button>
					</div>
				</div>
			</form>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 "> 
		
		</div>
 </div>
  </div>
 </div>
			
<?php include("footer.php") ?>
