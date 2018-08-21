<?php 
 
 $rem_det = get_reminder_details($this->session->id);
 $remindercnt = 0;
 
if( sizeof($rem_det[0]['resultset1']) > 0 )
{
	foreach($rem_det[0]['resultset1']->result()  as $remitem)
	{
		if($remitem->isread  == 0)
			$remindercnt++;  
	} 
} 

$mail_cnt = get_mail_count($this->session->id, $this->session->email); 

$switcher='off'; 
if ( !is_null( $this->input->cookie('_mcu') )) 
{
	$mcu =  json_decode( "[" .     $this->input->cookie('_mcu')  . "]", true ) ;
	$switcher =  ( isset($mcu[0]["switcher"]) ?  $mcu[0]["switcher"] : 'off' ) ; 
} 
  
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
						echo '<a href="'.$base .'"><img src="' .  $base .$asset. '/images/logo-sm.png" alt="logo"></a>';
					}
					else
					{
						echo '<a href="'.$base .'"><img src="' .  $base.$asset . '/images/logo-sm.png" alt="logo"></a>';
					}
				?> 
				<a id="play-video" href='#watch-mycity-video' class='noborder watchvideo' data-toggle="modal" data-target="#videomodal"  data-video='zUzISiLmqMw' class='play-video-home'>
				<img src='<?php echo $base. $asset; ?>images/bob-profile-sm.png' class='profile'  />
				</a>
			</div>
			</div>
			 <div class="col-xs-9 col-sm-7 col-md-4">
			   <div class="global-searchd"> 
					<?php 
					$form_option = array('id'=>'global_search');
					echo form_open('business/search',$form_option); ?>
					 <div class="top-search">  <div class="top-search-inner"> 
      <div class="easy-autocomplete" style="width: 183px;"><input type="text" name='gskey' id="gskey" placeholder="Name or vocation" autocomplete="off"><div class="easy-autocomplete-container" id="eac-container-gskey"><ul style="display: none;"></ul></div></div>
	   <div class="easy-autocomplete" style="width: 183px;"><input type="text"  name="gscityorzip" id="gscityorzip" placeholder="City or Zip Code" autocomplete="off"><div class="easy-autocomplete-container" id="eac-container-gscityorzip"><ul></ul></div></div>
	   </div>
      
	    <button type='submit' name='btn_global_search' value='global_search' class="btn-gsearch"><i class="fa fa-search"></i></button> 
	 
	 <div class="clearer"></div> </div> 
	 <?php echo form_close(); ?>
	 
	</div>
	</div>
	<div class="col-xs-3 col-sm-12 col-md-5 text-right"> 
		<?php 
			if( !$this->session->id)
			{
				echo "<ul><li><a class='btn btn-reg' href='" . $base ."login' >Sign in </a></li></ul>";
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
				  
				 
					if( $switcher == 'on' && $this->session->switcher == 'on' ) 
					{
						echo '<div class="switcher" ><a href="'. $base . 'login/switch-user" data-user="1" id="btnactswitch">Click to switch back to admin.</a></div>';
					}
				?> 
                    <ul class="nav navbar-nav ">
						 <?php   
						 
						 
						 $bubbletext = ''; 
						 if( $remindercnt > 0) 
						 {
							 $bubbletext = "<span class='bubble'>".  $remindercnt ."</span>";
						 } 
						 echo " <li><a    class='fetchreminder'  href='" . $base . "reminders' ><i   class='fa fa-bell ' title='Home'></i>
						 <span class='menu-label  '>Alerts</span> ". $bubbletext . "</a></li>";
						
						 if( $this->session->role =='user' || ($switcher == 'on' && $this->session->switcher == 'on') ) 
						{
							echo " <li><a href='" . BASE_URL  . "/dashboard'><i   class='fa fa-home' title='Home'></i>";
						}
						else 
						{
							echo " <li><a href='".BASE_URL."/admin/dashboard.php'><i   class='fa fa-home' title='Home'></i>";
						} 
						 echo "<span class='menu-label'>Home</span>
						 </a></li>";
						
						$messagebubbletext = '';
						$connectioncount = 0;
						 
						 if( $mail_cnt[0]['count'] > 0)    
							$connectioncount += $mail_cnt[0]['count'];
						 
						$messagebubbletext = "<span class='bubble bubble-wide bubblemsg'>".  $connectioncount ."</span>";
							 	 
					?>
					<li class='dropdown'><a  href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i   class='fa fa-envelope' title='Messages'></i>
						 <span class='menu-label'>Messages</span> <span id='__mcv'><?php echo $messagebubbletext; ?></span>
						 </a>
						 <ul class='dropdown-menu'>   
						 <li class="close_drop"><a  href='<?php echo $base;?>mails/inbox' > Inbox</a></li>  
						 <li class="close_drop"><a href='<?php echo $base;?>mails/outbox'> Outbox</a></li> 
						 <li class="close_drop"><a data-toggle="tab" class='loadmyinbox' href='#menu21' > View Messages</a></li>  
				 
					<li> <a   href="<?php echo $base; ?>nearby-members">Nearby Members</a> </li>  
					  
					    <?php if($this->session->role == 'admin'  ) { ?>
						
					  <li> <a class='profileclaimmessages' data-toggle="tab" href="#menu67">Topbar Search Log</a> </li>  
					    <?php  } ?>
					 </ul>
				</li>
				<li class='dropdown'><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i  class='fa fa-cog' title='Tools'></i> <span class='menu-label'>My Network</span> </a>
				<ul class='dropdown-menu'>
					<li class="close_drop"><a  class='getmypartners' href="<?php echo $base; ?>my-partners/"> My Partners</a></li>
				 <li class="close_drop"><a  href="<?php echo $base; ?>my-network/connections" class='getconnectionrequest' > Connections</a></li>
				 <li class="close_drop"><a  class='getratedpartners' href="<?php echo $base; ?>my-partners/highest-rated"> Highest Rated Partners</a></li>
				 <li><a class='loadprofile' data-toggle='tab' href='#menu53'> Find Businesses</a></li>
				 <li> 
					<a href="<?php echo $base;?>dashboard/performance">Performance Report</a>  
				</li> <?php if( $this->session->user_role == 'admin'  ) { ?>
				<li> 
					<a  data-toggle="tab" class='trendingsrclog' href="#menu62"> Trending Search Log</a>  
				</li>
				<?php  } ?>
					</ul></li>

					<li><a    href="<?php echo $base; ?>dashboard/help"><i   class='fa fa-support red' title='FAQs'></i>
						 <span class='menu-label'>FAQs</span>
						 </a> 
					</li>
					<?php 
						echo " <li><a href='" . $base . "logout'><i  class='fa fa-sign-out' title='Logout'></i> 
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