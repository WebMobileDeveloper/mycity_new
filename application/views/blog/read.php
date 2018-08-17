<div   class='container-fluid '>
	<div class='row marg4'> 
   <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2">  
	<?php
		$row = $posts->row();
		$post_title = $row->post_title ; 
		$post_content = $row->post_content ;
		$post_id = $row->id ;
		$post_date = date('m/d/Y',strtotime($row->post_date ));
		$comment_count =  $row->comment_count ;
		$comment_status =  $row->comment_status ;
		echo "<div class=''><h1  >" . $post_title . "</h1></div>". 
		"<div class='dp-meta'><strong>Post date:</strong>" . $post_date . " <i class='fa fa-comment-o'></i> " .
		$comment_count . "</div>" .
		"<div class='marg4' >" .    $post_content  . 
		"</div>"  ; 
	?> 
	</div>
	<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2"> 
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
		</div>
</div><!-- row -->
</div><!-- container -->

 