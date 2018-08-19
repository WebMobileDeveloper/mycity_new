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
$user_email = $_SESSION['user_email'];


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

$param = array('userid' =>  $user_id, 'email' =>  $user_email  );
$connectreceived = json_decode(   curlexecute($param, $siteurl . 'api/api.php/mailbox/count/'), true);
 
?>

<div class="sidebar" data-background-color="dark" data-active-color="danger"> 
    	<div class="sidebar-wrapper">
            <div class="sidemenu"> 
            </div> 
    	</div>
    </div> 
<section class="header"  > 
<div class="container-fluid  ">
        <div class="row">
            <div class="col-xs-12 col-sm-5 col-md-3">
                <div id='logo'>
				<?php
					if(!isset($_SESSION['user_id']))
					{
						echo '<a href="index.php"><img src="/images/logo-sm.png" alt="logo"></a>';
					}
					else
					{
						echo '<a href="dashboard.php"><img src="/images/logo-sm.png" alt="logo"></a>';
					}
				?> 
				<a id="play-video" href='#watch-mycity-video' class='noborder watchvideo' data-toggle="modal" data-target="#videomodal"  data-video='zUzISiLmqMw' class='play-video-home'>
				<img src='images/bob-profile-sm.png' class='profile'  />
				</a>
        </div>
			</div>
			  <div class="col-xs-9 col-sm-7 col-md-4">
			  <div class="global-searchd">
				 
					<div class='top-search' >
					   <div class='top-search-inner'> 
      <input type="text" id="gskey"  placeholder="Name or vocation">
	   <input type="text" id='gscityorzip'  placeholder="City Name or Zip Code">
	   </div>
      <div class=''>
	  <a data-toggle="tab" href="#menu58" class=" btn-gsearch"><i class='fa fa-search'></i></a> 
	  </div>
	 <div class='clearer'></div>
	 </div> 
  
			    </div>
			 </div>
            <div class="col-xs-3 col-sm-12 col-md-5 text-right">
			 
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
				
                    <ul class="nav navbar-nav">
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
						 $connectioncount =0;
//						 if( $oldconnectreceived[0]['count'] > 0)
//							$connectioncount = $oldconnectreceived[0]['count'];
						 
						 if( $connectreceived[0]['count'] > 0)    
							$connectioncount += $connectreceived[0]['count'];
						 
						 if( $connectioncount > 8) 
						 {
							 $messagebubbletext = "<span class='bubble bubble-wide'>8+</span>";
						 }
						 else 
							  if( $connectioncount > 0) 
							  {
								  $messagebubbletext = "<span class='bubble bubble-wide'>".  $connectioncount ."</span>";
							  } 
						 
						 ?>
					<li class='dropdown'><a  href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i   class='fa fa-envelope' title='Messages'></i>
						 <span class='menu-label'>Messages</span> <span id='__mcv'><?php echo $messagebubbletext; ?></span>
						 </a>
						 <ul class='dropdown-menu'> 
						 <li class="close_drop"><a data-toggle="tab" class='loadinbox' href='#menu21b' > Inbox</a></li>  
						 <li class="close_drop"><a data-toggle="tab" class='loadoutbox' href='#menu21' > Outbox</a></li>  
<li> <a class='messagetomember' data-toggle="tab" href="#menu44">Send Message to Member</a> </li>  
						 
						  <?php if($_user_role == 'admin'  ) { ?>
						
					  <li> <a class='profileclaimmessages' data-toggle="tab" href="#menu67">Topbar Search Log</a> </li>  
					    <?php  } ?>
					  
					  
						 
						</ul>
						 </li>
						 <li class='dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i  class='fa fa-cog' title='Tools'></i>
						 <span class='menu-label'>Tools</span> </a>
							  <ul class='dropdown-menu'>  
				 <li class="close_drop"><a data-toggle="tab" class='getmypartners' href="#menu13"> My Partners</a></li>
				  <li class="close_drop"><a data-toggle="tab" class='getconnectionrequest' href="#menu59"> My Connections</a></li>
				  
				 <li class="close_drop"><a data-toggle="tab" class='getratedpartners' href="#menu16"> Highest Rated Partners</a></li>
				 <li><a class='loadprofile' data-toggle='tab' href='#menu53'> Find Businesses</a></li>
 
					<li> 
						<a class='viewperformance' data-toggle="tab" href="#menu31">Performance Report</a>  
					</li> 
					</ul></li>

					<li><a data-toggle="tab" class='getpublicfaqs' href="#menu10"><i   class='fa fa-support red' title='FAQs'></i>
						 <span class='menu-label'>FAQs</span>
						 </a>
				  
					</li>

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