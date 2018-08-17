<section class="header">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-4 logo">
                
				<?php
					if(!isset($_SESSION['user_id'])) {
						echo '<a href="index.php"><img src="/images/logo.png" alt="logo"></a>';
					} else {
						echo '<a href="dashboard.php"><img src="/images/logo.png" alt="logo"></a>';
					}
				?> 
				<a id="play-video" href='#watch-mycity-video' 
				class='noborder watchvideo' data-toggle="modal" data-target="#videomodal" 
				data-video='zUzISiLmqMw' class='play-video-home'>
				<img src='/images/bob-profile.png' class='profile'  />
				</a>
				   
			</div>
			<div class="col-xs-12  col-md-6 text-center siteTagline">
				<h5 ><?php echo $tagline[0]["page_content"] ?></h5>
			</div>
            <div class="col-xs-12 col-md-2 text-right">
                <?php
                if(!isset($_SESSION['user_id'])){
                    echo "<ul><li><a data-toggle=\"modal\" data-target=\"#signin\">Sign in </a></li></ul>";
                }else{
                    echo "<ul>
								<li><a href='dashboard.php'><i style='font-size: 36px;' class='fa fa-home' title='Home'></i></a></li>
								<li><a href='message.php'><i style='font-size: 36px;' class='fa fa-envelope' title='Messages'></i></a></li>
								<li><a href='logout.php'><i style='font-size: 36px;' class='fa fa-sign-out' title='Logout'></i></a></li>
							</ul>";
                }
                ?>
            </div>
        </div>
    </div>
</section>