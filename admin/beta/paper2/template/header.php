<div class="sidebar" data-background-color="dark" data-active-color="danger"> 
    	<div class="sidebar-wrapper">
            <div class="sidemenu"> 
            </div> 
    	</div>
    </div> 
<section class="header "  >

<div class="container   ">
        <div class="row">
            <div class="col-xs-12 col-md-4  ">
                <div id='logo'>
				<?php
					if(!isset($_SESSION['user_id'])) {
						echo '<a href="index.php"><img src="/images/logo-sm.png" alt="logo"></a>';
					} else {
						echo '<a href="dashboard.php"><img src="/images/logo-sm.png" alt="logo"></a>';
					}
				?> 
				<a id="play-video" href='#watch-mycity-video' 
				class='noborder watchvideo' data-toggle="modal" data-target="#videomodal" 
				data-video='zUzISiLmqMw' class='play-video-home'>
				<img src='/images/bob-profile-sm.png' class='profile'  />
        </a>
        </div>
			</div>
			
            <div class="col-xs-12 col-md-8 text-right">
			 
		
		<?php
		
			if(!isset($_SESSION['user_id']))
			{
				echo "<ul><li><a data-toggle=\"modal\" data-target=\"#signin\">Sign in </a></li></ul>";
			}
			else
			{
        ?>
     <nav class="navbar ">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar bar1"></span>
                        <span class="icon-bar bar2"></span>
                        <span class="icon-bar bar3"></span>
                    </button> 
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
						 <?php 
						 
						 echo " 
						 <li><a href='index.php'><i   class='fa fa-home' title='Home'></i>
						 <span class='menu-label'>Home</span>
						 </a></li>
						 <li><a href='message.php'><i   class='fa fa-envelope' title='Messages'></i>
						 <span class='menu-label'>Messages</span>
						 </a></li>
						 <li><a href='logout.php'><i  class='fa fa-cog' title='Tools'></i>
						 <span class='menu-label'>Tools</span>
						 </a></li>
						 <li><a href='logout.php'><i  class='fa fa-sign-out' title='Logout'></i>
						 <span class='menu-label'>Logout</span>
						 </a></li> 
						 ";
						 
						 ?>
                    </ul> 
                </div>
            </div>
		</nav>  
				<?php 
					}
                ?>
            </div>
        </div>
    </div>
</section>