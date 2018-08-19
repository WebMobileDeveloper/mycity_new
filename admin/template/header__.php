<?php 
 
if(  $_SERVER['HTTP_HOST'] == "localhost" || $_SERVER['HTTP_HOST'] == "mycity.test")
{
	$_SERVER['HTTPS'] = false;
    $siteurl = 'http://'. $_SERVER['HTTP_HOST'] . "/";
} 
else
{
    $siteurl =  'https://mycity.com/';
}

$user_id = $_SESSION['user_id'];


$param = array('userid' =>  $user_id );
$allreminders = json_decode(   curlexecute($param, $siteurl . 'api/api.php/reminders/get/'), true);  
 
$remindercnt = 0;
 
if(   sizeof($allreminders[0]['resultset1']) > 0)
{	
	foreach($allreminders[0]['resultset1']  as $remitem)
	{
		if($remitem ['isread'] == 0)
			$remindercnt++;  
	} 
}


$param = array('userid' =>  $user_id );
$connectreceived = json_decode(   curlexecute($param, $siteurl . 'api/api.php/member/connection/received/count/'), true);  
 


?>

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
				
				<?php
					if( isset($_SESSION['logintoken']) && isset($_SESSION['switchtoken'])  && $_SESSION['switchtoken']=='1' ) 
					{
						echo '<div class="switcher" ><a href="#" data-user="1" id="btnactswitch">Click to switch back to admin.</a></div>';
					}
				?>
				<div class="global-search">
					 <form class="form-inline">
					  <div class="form-group">
						<label class="sr-only" for="exampleInputAmount">Amount (in dollars)</label>
						<div class="input-group"> 
						  <input type="text" class="form-control" id="gskey" placeholder="Search Member">
						  <div class="input-group-addon"><a data-toggle="tab" href="#menu58" class="btn-gsearch">Go</a></div>
						</div>
					  </div> 
					</form>
			  </div>
                    <ul class="nav navbar-nav navbar-right">
						 <?php  
						 
						 $bubbletext = '';
						 if($remindercnt > 0) 
						 {
							 $bubbletext = "<span class='bubble'>".  $remindercnt ."</span>";
						 }
						  
						 echo " <li><a  data-toggle='tab' class='fetchreminder' href='#menu34'><i   class='fa fa-bell ' title='Home'></i>
						 <span class='menu-label  '>Alerts</span> ". $bubbletext . "</a></li>
						 <li><a href='dashboard.php'><i   class='fa fa-home' title='Home'></i>
						 <span class='menu-label'>Home</span>
						 </a></li>";
						 
						 
						 
						 $messagebubbletext = '';
						 if( $connectreceived[0]['count'] > 0) 
						 {
							 $messagebubbletext = "<span class='bubble bubble-wide'>".  $connectreceived[0]['count'] ."</span>";
						 } 
						 
						 ?>
						 
						 
						 
						 
						 
						 <li class='dropdown'><a  href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i   class='fa fa-envelope' title='Messages'></i>
						 <span class='menu-label'>Messages</span> <?php echo $messagebubbletext; ?>
						 </a>
						 <ul class='dropdown-menu'>   
							 <li class="close_drop"><a data-toggle="tab" class='loadmyinbox' href='#menu21' > View Messages</a></li>  
							<li class="close_drop"><a data-toggle="tab" class='getconnectionrequest' href="#menu59"> Connection Requests Sent</a></li>
							<li class="close_drop"><a data-toggle="tab" class='getconnectioninrequest' href="#menu59"> Connections Requet Received</a></li> 
						</ul>
						 </li>
						 <li class='dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i  class='fa fa-cog' title='Tools'></i>
						 <span class='menu-label'>Tools</span> </a>
							  <ul class='dropdown-menu'> ";
					  
				 <li class="close_drop"><a data-toggle="tab" class='getmypartners' href="#menu13"> Your Partners</a></li>
				 <li class="close_drop"><a data-toggle="tab" class='getratedpartners' href="#menu16"> Highest Rated Partners</a></li>
				   </ul></li>
								<?php 
							echo	"   <li><a href='logout.php'><i  class='fa fa-sign-out' title='Logout'></i>
						 <span class='menu-label'>Logout</span>
						 </a></li>  ";
						 
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